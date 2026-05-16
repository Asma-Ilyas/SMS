<?php

namespace Database\Seeders;
use App\Models\Page;
use App\Utils\DataSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
   
        foreach (DataSeed::$PAGES as $page) {
           Page::create($page);
    }
    }
}
