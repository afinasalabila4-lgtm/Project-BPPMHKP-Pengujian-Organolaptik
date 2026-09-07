<?php

namespace App\Http\Controllers\Penyelia;


use App\Http\Controllers\Controller;
use App\Models\TestSession;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;



class TestSessionController extends Controller
{


    /**
     * Detail hasil pengujian penyelia
     */
    public function show(TestSession $testSession)
    {


        $testSession->load([

            'sample.product',

            'assessments.user',

            'assessments.details.criteria'

        ]);


        $testSession->selesaikanOtomatisJikaLengkap();



        $jumlahPanelis = $testSession
            ->assessments
            ->count();



        $stats = \App\Support\OrganolepticStatistics::calculate($testSession);



        $nilaiMutu = $jumlahPanelis > 0
            ? $stats['p_bulat']
            : 0;



        return view(

            'penyelia.test_sessions.show',

            compact(

                'testSession',
                'nilaiMutu',
                'jumlahPanelis',
                'stats'

            )

        );


    }








    /**
     * Download PDF Penyelia
     */
    public function pdf(TestSession $testSession)
    {


        $testSession->load([

            'sample.product',

            'assessments.user',

            'assessments.details.criteria'

        ]);



        $pdf = Pdf::loadView(

            'admin.test_sessions.pdf',

            compact('testSession')

        );



        $pdf->setPaper(

            'a4',

            'landscape'

        );



        return $pdf->download(

            'Hasil_Uji_' .
            $testSession->sample->nomor_sample .
            '.pdf'

        );


    }








    /**
     * Download Excel Penyelia
     */
    public function excel(TestSession $testSession)
    {


        $testSession->load([

            'sample.product',

            'assessments.user',

            'assessments.details.criteria'

        ]);



        return Excel::download(

            new \App\Exports\TestSessionResultExport($testSession),

            'hasil_pengujian_' . $testSession->sample->nomor_sample . '.xlsx'

        );


    }



}