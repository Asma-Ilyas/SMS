<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 


class CertificateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('certificate_types')->insert([
        ['title' => 'Perfect Attendance Award', 'is_active' => true],
        ['title' => 'Sports Achievement Certificate', 'is_active' => true],
        ['title' => 'Academic Excellence Award', 'is_active' => true],
        ['title' => 'Good Conduct Certificate', 'is_active' => true],
    ]);
    }
}
