<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criteria;
use App\Models\AssessmentSection;


class CriteriaSeeder extends Seeder
{

    public function run(): void
    {

        $sections = AssessmentSection::all();

        if ($sections->isEmpty()) {
            $this->command->info('Assessment Section belum tersedia. Jalankan AssessmentSectionSeeder terlebih dahulu.');
            return;
        }

        $criteriaList = [
            'Lapisan Es',
            'Pengeringan',
            'Perubahan Warna',
            'Kenampakan',
            'Bau',
            'Tekstur',
        ];

        foreach ($sections as $section) {

            foreach ($criteriaList as $namaKriteria) {

                Criteria::updateOrCreate(
                    [
                        'assessment_section_id' => $section->id,
                        'nama_kriteria' => $namaKriteria,
                    ],
                    [
                        'assessment_section_id' => $section->id,
                        'nama_kriteria' => $namaKriteria,
                    ]
                );

            }

        }

    }

}
