<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();

        $certificates = DB::table('certificate_distributions as cd')
            ->join('certificate_types as ct', 'ct.id', '=', 'cd.certificate_type_id')
            ->where('cd.student_id', $s->id)
            ->orderByDesc('cd.issue_date')
            ->select('cd.*', 'ct.title', 'ct.description')->get();

        $transferCertificates = DB::table('transfer_certificates')
            ->where('student_id', $s->id)->orderByDesc('issued_date')->get()
            ->map(function ($t) {
                $t->types = json_decode($t->certificate_types, true) ?: [];
                return $t;
            });

        return view('student.certificates.index', compact('s', 'certificates', 'transferCertificates'));
    }

    public function download($distribution)
    {
        $this->student();
        $row = DB::table('certificate_distributions')->where('id', $distribution)->first();
        $this->ensureOwner($row);

        abort_if(!$row->certificate_file || !Storage::disk('public')->exists($row->certificate_file), 404, 'Certificate file not available.');

        return Storage::disk('public')->download($row->certificate_file);
    }
}
