<?php

namespace Database\Seeders;
use App\Models\SectionType;
use App\Utils\DataSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DataSeed::$SECTION_TYPES as $type) {
           SectionType::create($type);
    }
    }
}
