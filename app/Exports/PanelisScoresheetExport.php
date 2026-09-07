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

        $rows = [];

        $assessment = $this->assessment;

        $assessment->load([
            'user',
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


        /*
        JUDUL
        */

        $rows[] = ['SCORESHEET PENILAIAN PANELIS'];

        $rows[] = [];

        /*
        INFORMASI
        */

        $rows[] = [
            'Produk : ' . ($info?->sample?->product?->nama_produk ?? '-')
        ];

        $rows[] = [
            'Nomor Sample : ' . ($info?->sample?->nomor_sample ?? '-')
        ];

        $rows[] = [
            'Tanggal Pengujian : ' . ($info?->tanggal_pengujian ?? '-')
        ];

        $rows[] = [];

        $rows[] = [
            'Nama Panelis : ' . ($assessment->user->name ?? '-'),
            '',
            '',
            'NIP : ' . ($assessment->user->nip ?? '-')
        ];

        $rows[] = [];

        /*
        HEADER
        */

        $rows[] = [
            'No',
            'Kriteria',
            'Nilai',
            'Deskripsi'
        ];


        $no = 0;

        $total = 0;

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
                '',
                '■ ' . ($section->nama_section ?? 'Section'),
                '',
                ''
            ];

            foreach ($criterias as $criteria) {

                $no++;

                $nilai = $data[$criteria->id]['nilai'] ?? null;

                $deskripsi = $criteria->options
                    ->where('nilai', $nilai)
                    ->first();

                $total += $nilai ?? 0;

                $rows[] = [
                    $no,
                    $criteria->nama_kriteria,
                    $nilai ?? '-',
                    $deskripsi?->deskripsi ?? '-'
                ];
            }
        }


        $jumlah = count($assessment->details);

        $rataRata = $jumlah > 0
            ? round($total / $jumlah, 2)
            : 0;


        $rows[] = [];

        $rows[] = [
            '',
            'Jumlah',
            $total,
            ''
        ];

        $rows[] = [
            '',
            'Rata-rata',
            number_format($rataRata, 2),
            ''
        ];

        $rows[] = [];

        /*
        TANDA TANGAN
        */

        $rows[] = [
            'Panelis',
            '',
            '',
            'Penyelia'
        ];

        $rows[] = [];
        $rows[] = [];
        $rows[] = [];
        $rows[] = [];

        $rows[] = [
            '(........................................)',
            '',
            '',
            '(........................................)'
        ];


        return $rows;
    }





    public function registerEvents(): array
    {

        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();


                $sheet
                    ->getStyle('A1:D40')
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


                /*
                WIDTH
                */

                $width = [
                    'A' => 6,
                    'B' => 35,
                    'C' => 10,
                    'D' => 60,
                ];

                foreach ($width as $col => $size) {

                    $sheet
                        ->getColumnDimension($col)
                        ->setWidth($size);
                }


                /*
                DATA PANELIS KIRI
                */

                $sheet
                    ->getStyle('A7')
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );


                $nData = count($this->assessment->details);
                $nSection = $this->assessment
                    ->details
                    ->map(fn ($d) => $d->criteria->assessmentSection)
                    ->unique('id')
                    ->count();

                $headerRow = 8;

                $lastDataRow = $headerRow + $nSection + $nData;


                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':D' . $lastDataRow
                    )
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(
                        Border::BORDER_THIN
                    );

                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':D' . $headerRow
                    )
                    ->getFont()
                    ->setBold(true);

                $sheet
                    ->getStyle(
                        'A' . $headerRow . ':D' . $lastDataRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                $sheet
                    ->getStyle(
                        'B' . $headerRow . ':B' . $lastDataRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );

                $sheet
                    ->getStyle(
                        'D' . $headerRow . ':D' . $lastDataRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );


                /*
                JUMLAH & RATA-RATA
                */

                $jumlahRow = $lastDataRow + 2;
                $rataRow = $lastDataRow + 3;

                $sheet
                    ->getStyle(
                        'A' . $jumlahRow . ':D' . $rataRow
                    )
                    ->getFont()
                    ->setBold(true);

                $sheet
                    ->getStyle(
                        'A' . $jumlahRow . ':D' . $rataRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );


                /*
                TANDA TANGAN
                */

                $ttdRow = $jumlahRow + 7;

                $sheet->mergeCells('A' . $ttdRow . ':B' . $ttdRow);
                $sheet->mergeCells('C' . $ttdRow . ':D' . $ttdRow);

                $sheet
                    ->getStyle(
                        'A' . $ttdRow . ':D' . ($ttdRow + 4)
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );
            }

        ];
    }
}