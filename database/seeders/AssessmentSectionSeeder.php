<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentSection;
use App\Models\AssessmentTemplate;


class AssessmentSectionSeeder extends Seeder
{

    public function run(): void
    {

        $templates = AssessmentTemplate::all();

        if ($templates->isEmpty()) {
            $this->command->info('Template belum tersedia. Jalankan AssessmentTemplateSeeder terlebih dahulu.');
            return;
        }

        $defaultSectionName = 'Penilaian Organoleptik';

        foreach ($templates as $template) {

            // Cek apakah section untuk template ini sudah ada
            $exists = AssessmentSection::where('assessment_template_id', $template->id)->exists();

            if (!$exists) {
                AssessmentSection::create([
                    'assessment_template_id' => $template->id,
                    'nama_section' => $defaultSectionName,
                ]);
            }
        }

    }

}
