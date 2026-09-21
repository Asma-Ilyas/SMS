<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Support\Facades\DB;

class SchoolTimingController extends BaseTeacherController
{
    public function index()
    {
        $this->teacher();

        $timing = DB::table('school_timings')->where('is_active', 1)->first();
        $slots  = $timing ? $this->slots() : collect();
        $breaks = $timing && $timing->breaks ? (json_decode($timing->breaks, true) ?: []) : [];

        return view('teacher.school-timings.index', compact('timing', 'slots', 'breaks'));
    }
}
