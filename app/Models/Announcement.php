<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'message', 'audience', 'class_section_id', 'priority', 'link', 'created_by', 'recipients_count',
    ];

    public const AUDIENCES = [
        'all'      => 'Everyone (admins, teachers, students)',
        'admins'   => 'Admins only',
        'teachers' => 'All teachers',
        'students' => 'All students',
        'section'  => 'Students of one class section',
    ];

    public const PRIORITIES = ['normal' => 'Normal', 'important' => 'Important', 'urgent' => 'Urgent'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', User::class), 'created_by');
    }

    public function audienceLabel(): string
    {
        return self::AUDIENCES[$this->audience] ?? ucfirst($this->audience);
    }
}