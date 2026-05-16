<?php

namespace Database\Seeders;
use App\Models\SectionFieldValue;
use App\Utils\DataSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionFieldValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DataSeed::$SECTION_FIELD_VALUES as $field_values) {
           SectionFieldValue::create($field_values);
    }
    }
}
