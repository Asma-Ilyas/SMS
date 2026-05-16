<?php

namespace Database\Seeders;
use App\Models\Section;
use App\Utils\DataSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         foreach (DataSeed::$SECTIONS as $section) {
           Section::create($section);
    }
    }
}
