<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class DatabaseSeeder extends Seeder
{

    use WithoutModelEvents;


    public function run(): void
    {

        $this->call([

            UserSeeder::class,

            ProductSeeder::class,

            AssessmentTemplateSeeder::class,

            AssessmentSectionSeeder::class,

            CriteriaSeeder::class,

            CriteriaOptionSeeder::class,

        ]);

    }

}