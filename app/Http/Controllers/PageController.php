<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Section;
use App\Models\School;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dynamicPage($schoolSlug, $pageSlug)
    {
        $school = School::where('slug', $schoolSlug)->firstOrFail();

        $page = Page::where('slug', $pageSlug)
                    ->where('school_id', $school->id)
                    ->firstOrFail();

        $sections = Section::with(['type', 'values.field'])
            ->where('page_id', $page->id)
            ->orderBy('sort_order')
            ->get();

        // Transform section data for each section
        foreach ($sections as $section) {
            $data = [];
            foreach ($section->values as $value) {
                if ($value->field) {
                    $data[$value->field->name] = $value->value;
                }
            }
            $section->data = $data;
        }

        // Pass BOTH $sections and $school to the view
        return view('welcome', compact('sections', 'school'));
    }
}