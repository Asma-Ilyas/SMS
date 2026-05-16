<?php

namespace Database\Seeders;
use App\Models\SectionField;
use App\Utils\DataSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         foreach (DataSeed::$SECTION_FIELDS as $sections_field) {
           SectionField::create($sections_field);
    }
    }
}
