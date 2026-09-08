<?php

namespace App\Exports;

use App\Models\Assessment;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PanelisScoresheetExport implements FromArray, WithEvents
{

    protected $assessment;


    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }



    public function array(): array
    {

        $assessment = $this->assessment;

        $assessment->load([
            'user',
            'testSession.sample.product',
            'details.criteria.assessmentSection',
            'details.criteria.options'
        ]);


        $info = $assessment->testSession;

        $data = [];

        foreach ($assessment->details as $detail) {

            $data[$detail->criteria_id] = [
                'kriteria' => $detail->criteria,
                'nilai' => $detail->nilai,
            ];

        }


        $rows = [];


        /*
        JUDUL
        */

        $rows[] = ['SCORESHEET PENILAIAN PANELIS'];


        /*
        INFORMASI (labell kiri, nilai kanan)
        */

        $rows[] = [
            'Produk',
            ($info?->sample?->product?->nama_produk ?? '-')
        ];

        $rows[] = [
            'Nomor Sample',
            ($info?->sample?->nomor_sample ?? '-')
        ];

        $rows[] = [
            'Tanggal Pengujian',
            ($info?->tanggal_pengujian ?? '-')
        ];

        $rows[] = [
            'Nama Panelis',
            ($assessment->user->name ?? '-') .
                '        NIP : ' .
                ($assessment->user->nip ?? '-')
        ];


        /*
        JARAK ANTARA INFORMASI & TABEL
        */

        $rows[] = [' '];


        /*
        HEADER TABEL
        */

$rows[] = [
                'Spesifikasi',
                'Nilai (1 s.d 9)'
            ];


        $sections = $assessment
            ->details
            ->map(fn ($d) => $d->criteria->assessmentSection)
            ->unique('id')
            ->values();

        foreach ($sections as $section) {

            $criterias = $assessment->details
                ->map(fn ($d) => $d->criteria)
                ->where('assessment_section_id', $section->id)
                ->values();

            $rows[] = [
                '■ ' . ($section->nama_section ?? 'Section'),
                ' '
            ];

            foreach ($criterias as $criteria) {

                $nilai = $data[$criteria->id]['nilai'] ?? null;

                $rows[] = [
                    $criteria->nama_kriteria,
                    $nilai ?? '-'
                ];
            }
        }


        return $rows;
    }





    public function registerEvents(): array
    {

        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();


                $sheet
                    ->getStyle('A1:D60')
                    ->getFont()
                    ->setName('Times New Roman');


                /*
                JUDUL
                */

                $sheet->mergeCells('A1:D1');

                $sheet
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true)
                    ->setSize(14);

                $sheet
                    ->getStyle('A1')
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                $sheet
                    ->getRowDimension(1)
                    ->setRowHeight(28);


                /*
                WIDTH
                */

                $width = [
                    'A' => 40,
                    'B' => 14,
                    'C' => 30,
                    'D' => 15,
                ];

                foreach ($width as $col => $size) {

                    $sheet
                        ->getColumnDimension($col)
                        ->setWidth($size);
                }


                /*
                INFORMASI (LABEL KIRI, NILAI KANAN)
                */

                $sheet->mergeCells('B2:C2');
                $sheet->mergeCells('B3:C3');
                $sheet->mergeCells('B4:C4');
                $sheet->mergeCells('B5:D5');

                $sheet
                    ->getStyle('A2:A5')
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );

                $sheet
                    ->getStyle('B2:D5')
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );


                /*
                TABEL (HANYA KOLOM A:B)
                */

                $nData = count($this->assessment->details);
                $nSection = $this->assessment
                    ->details
                    ->map(fn ($d) => $d->criteria->assessmentSection)
                    ->unique('id')
                    ->count();

                $headerRow = 7;

                $lastDataRow = $headerRow + $nSection + $nData;

                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':B' . $lastDataRow
                    )
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(
                        Border::BORDER_THIN
                    );

                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':B' . $headerRow
                    )
                    ->getFont()
                    ->setBold(true);

                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':B' . $lastDataRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':A' . $lastDataRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );
            }

        ];
    }
}