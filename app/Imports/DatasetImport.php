<?php

namespace App\Imports;


use App\Models\AssessmentSection;
use App\Models\AssessmentTemplate;
use App\Models\Criteria;
use App\Models\CriteriaOption;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;



class DatasetImport implements ToCollection
{


    protected $productId;



    public function __construct($productId)
    {
        $this->productId = $productId;
    }





    public function collection(Collection $rows): void
    {

        $template = AssessmentTemplate::firstOrCreate(

            [
                'product_id' => $this->productId
            ],

            [
                'nama_template' => 'Template Organoleptik'
            ]

        );



        /*
        Hapus section/kriteria/opsi lama yang belum dipakai
        penilaian, agar re-upload selalu menghasilkan data baru
        sesuai dataset (tanpa menghapus kriteria yang sudah dinilai)
        */

        $this->bersihkanSectionLama($template);



        /*
        Deteksi kolom berisi nilai ("Nilai" di baris header)
        */

        $kolomNilai = $this->deteksiKolomNilai($rows);



        /*
        Variabel aktif untuk baris berikutnya
        */

        $section = null;
        $criteria = null;
        $urutanSection = 0;
        $urutanCriteria = 0;
        $barisOpsi = [];



        foreach ($rows->skip(2) as $row)
        {

            $kolomA = trim((string) ($row[0] ?? ''));

            $kolomB = trim((string) ($row[1] ?? ''));

            $nilai  = trim((string) ($row[$kolomNilai] ?? ''));



            if ($kolomA === '' && $kolomB === '' && $nilai === '') {
                continue;
            }



            /*
            Group / section:
            - satu kolom : "A   Dalam keadaan beku"
            - dua kolom  : "A" + "Dalam keadaan beku"
            */

            $namaSection = $this->deteksiGroup($kolomA, $kolomB);


            if ($namaSection !== null) {

                $this->simpanOpsi($criteria, $barisOpsi);

                $urutanSection++;

                $section = AssessmentSection::updateOrCreate(

                    [
                        'assessment_template_id' => $template->id,
                        'nama_section' => $namaSection
                    ],

                    [
                        'urutan' => $urutanSection
                    ]

                );


                $criteria = null;
                $urutanCriteria = 0;

                continue;
            }



            /*
            Kriteria:
            - satu kolom : "1. Kenampakan" / "1    Bau"
            - dua kolom  : "1" + "Kenampakan"
            */

            $namaCriteria = $this->deteksiCriteria($kolomA, $kolomB);


            if ($namaCriteria !== null) {

                $this->simpanOpsi($criteria, $barisOpsi);

                if (!$section) {

                    $urutanSection++;

                    $section = AssessmentSection::updateOrCreate(

                        [
                            'assessment_template_id' => $template->id,
                            'nama_section' => 'Penilaian Sensori'
                        ],

                        [
                            'urutan' => $urutanSection
                        ]

                    );

                }


                $urutanCriteria++;

                $criteria = Criteria::updateOrCreate(

                    [
                        'assessment_section_id' => $section->id,
                        'nama_kriteria' => $namaCriteria
                    ],

                    [
                        'urutan' => $urutanCriteria
                    ]

                );

                continue;
            }



            /*
            Opsi nilai: kolom nilai berisi angka, keterangan di kolom A.
            Ditampung dulu agar blok keterangan yang melebar
            (beberapa baris / score) bisa dibagi per baris.
            */

            if (is_numeric($nilai) && $criteria) {

                $barisOpsi[] = [
                    'nilai' => (int) $nilai,
                    'deskripsi' => $kolomA,
                ];

            }

        }


        $this->simpanOpsi($criteria, $barisOpsi);

    }





    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */



    /**
     * Deteksi kolom yang berisi nilai, dari header "Nilai".
     */
    private function deteksiKolomNilai(Collection $rows): int
    {

        foreach ($rows->take(3) as $row) {

            foreach ($row as $i => $cell) {

                if (is_string($cell) && trim($cell) === 'Nilai') {
                    return (int) $i;
                }

            }

        }

        return 1;

    }




    /**
     * Deteksi baris group / section. Mengembalikan nama section, atau null.
     */
    private function deteksiGroup(string $kolomA, string $kolomB): ?string
    {

        // "A   Dalam keadaan beku"
        if (preg_match('/^([A-Za-z])[\s\.]+(.+)$/', $kolomA, $match)) {
            return trim($match[2]);
        }


        // "A" | "Dalam keadaan beku"
        if (preg_match('/^[A-Za-z]$/', $kolomA) && $kolomB !== '') {
            return $kolomB;
        }


        return null;

    }




    /**
     * Deteksi baris kriteria. Mengembalikan nama kriteria, atau null.
     */
    private function deteksiCriteria(string $kolomA, string $kolomB): ?string
    {

        // "1. Kenampakan" / "1    Bau"
        if (preg_match('/^([0-9]+)[\s\.]+(.+)$/', $kolomA, $match)) {
            return trim($match[2]);
        }


        // "1" | "Kenampakan"
        if (is_numeric($kolomA) && $kolomB !== '') {
            return $kolomB;
        }


        return null;

    }




    /**
     * Simpan opsi kriteria. Bila sebuah sel keterangan berisi lebih dari
     * satu baris (blok sel yang digabung memanjang), tiap baris dibagikan
     * ke baris opsi berikutnya yang keterangannya kosong.
     */
    private function simpanOpsi(?Criteria $criteria, array &$buffer): void
    {

        if (!$criteria) {
            return;
        }


        $antrian = [];


        foreach ($buffer as $baris) {

            if ($baris['deskripsi'] !== '') {
                $antrian = array_merge(
                    $antrian,
                    $this->barisDeskripsi($baris['deskripsi'])
                );
            }


            $deskripsi = $antrian ? array_shift($antrian) : '';


            if ($deskripsi === '') {
                continue;
            }


            CriteriaOption::updateOrCreate(

                [
                    'criteria_id' => $criteria->id,
                    'nilai' => $baris['nilai']
                ],

                [
                    'deskripsi' => $deskripsi
                ]

            );

        }


        $buffer = [];

    }




    /**
     * Pecah keterangan multi-baris menjadi daftar baris bersih.
     */
    private function barisDeskripsi(string $teks): array
    {

        $baris = preg_split('/\R/', $teks) ?: [];


        return array_values(array_filter(array_map(
            fn ($b) => trim($b),
            $baris
        ), fn ($b) => $b !== ''));

    }




    /**
     * Hapus section lama (cascade ke kriteria & opsi) yang belum
     * direferensikan hasil penilaian, agar re-upload tidak menumpuk.
     */
    private function bersihkanSectionLama(AssessmentTemplate $template): void
    {

        $sections = AssessmentSection::where(
            'assessment_template_id',
            $template->id
        )
        ->get();


        foreach ($sections as $section) {

            $dipakai = DB::table('assessment_details')
                ->join(
                    'criteria',
                    'criteria.id',
                    '=',
                    'assessment_details.criteria_id'
                )
                ->where(
                    'criteria.assessment_section_id',
                    $section->id
                )
                ->exists();


            if (!$dipakai) {
                $section->delete();
            }

        }

    }

}