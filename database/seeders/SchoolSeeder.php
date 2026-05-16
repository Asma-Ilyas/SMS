<?php

namespace Database\Seeders;
use App\Models\School;
use App\Utils\DataSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DataSeed::$SCHOOLS as $school) {
           School::create($school);
    }
    }
}
