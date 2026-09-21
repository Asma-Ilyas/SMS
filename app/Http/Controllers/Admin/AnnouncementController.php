<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeliverAnnouncement;
use App\Models\Announcement;
use App\Services\Notifications\PortalNotifier;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::with('creator')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.announcements.create', ['sections' => $this->sections()]);
    }

    public function store(Request $request, PortalNotifier $notifier)
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:150'],
            'message'          => ['required', 'string', 'max:2000'],
            'audience'         => ['required', Rule::in(array_keys(Announcement::AUDIENCES))],
            'class_section_id' => ['nullable', 'required_if:audience,section', 'integer'],
            'priority'         => ['required', Rule::in(array_keys(Announcement::PRIORITIES))],
            // in-app path only (e.g. /student/fees) — never an external URL
            'link'             => ['nullable', 'string', 'max:255', 'regex:#^/(?!/)\S*$#'],
        ]);

        if ($data['audience'] === 'section') {
            abort_unless($this->sections()->contains('id', (int) $data['class_section_id']), 422, 'Choose a valid class section.');
        } else {
            $data['class_section_id'] = null;
        }

        $data['created_by'] = $request->user()->getKey();
        $announcement       = Announcement::create($data);

        if (config('portal-notifications.queue')) {
            DeliverAnnouncement::dispatch($announcement->id);
            $message = 'Announcement queued. It will reach recipients as soon as the queue worker runs.';
        } else {
            $count   = $notifier->deliverAnnouncement($announcement);
            $message = "Announcement sent to {$count} " . ($count === 1 ? 'person' : 'people') . '.';
        }

        return redirect()->route('admin.announcements.index')->with('success', $message);
    }

    /** Deleting an announcement also removes it from everyone's notification list. */
    public function destroy(Announcement $announcement)
    {
        DatabaseNotification::where('data->announcement_id', $announcement->id)->delete();
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }

    protected function sections()
    {
        $class = 'App\\Models\\ClassSection';
        if (! class_exists($class)) {
            return collect();
        }

        return $class::with('class')->get()->map(fn ($s) => [
            'id'    => (int) $s->getKey(),
            'label' => trim(($s->class?->getAttributes()['name'] ?? '') . ' ' . ($s->getAttributes()['name'] ?? '')) ?: ('Section #' . $s->getKey()),
        ])->sortBy('label')->values();
    }
}