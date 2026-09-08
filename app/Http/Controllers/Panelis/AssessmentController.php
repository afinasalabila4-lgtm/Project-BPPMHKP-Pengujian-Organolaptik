<?php

namespace App\Http\Controllers\Panelis;

use App\Http\Controllers\Controller;
use App\Models\TestSession;
use App\Models\Assessment;
use App\Models\AssessmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AssessmentController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Form Penilaian Panelis
    |--------------------------------------------------------------------------
    */

    public function create(TestSession $testSession)
    {

        abort_unless(
            $testSession->status === 'dibuka',
            403,
            'Sesi pengujian belum dibuka atau sudah selesai.'
        );


        $testSession->load([

            'sample.product.assessmentTemplates.sections.criterias.options'

        ]);



        $template = 
            $testSession
            ->sample
            ->product
            ->assessmentTemplates
            ->first();



        return view(
                'panelis.assessments.create',
                compact(
                    'testSession',
                    'template'
                )
            );

    }






    /*
    |--------------------------------------------------------------------------
    | Simpan Penilaian
    |--------------------------------------------------------------------------
    */


    public function store(
        Request $request,
        TestSession $testSession
    )
    {

        abort_unless(
            $testSession->status === 'dibuka',
            403,
            'Sesi pengujian belum dibuka atau sudah selesai.'
        );


        $sudahDinilai = Assessment::where(
            'test_session_id',
            $testSession->id
        )
        ->where(
            'user_id',
            Auth::id()
        )
        ->exists();


        if ($sudahDinilai) {

            return redirect()
                ->route(
                    'panelis.dashboard'
                )
                ->with(
                    'error',
                    'Anda sudah melakukan penilaian untuk sesi ini'
                );

        }


        $request->validate([

            'nilai'=>[
                'required',
                'array'
            ]

        ]);




        $assessment = Assessment::create([

            'test_session_id'=>$testSession->id,

            'user_id'=>Auth::id(),

            'status'=>'selesai'

        ]);





        $total = 0;



        foreach($request->nilai as $criteriaId=>$nilai)
        {


            AssessmentDetail::create([

                'assessment_id'=>$assessment->id,

                'criteria_id'=>$criteriaId,

                'nilai'=>$nilai

            ]);


            $total += $nilai;


        }




        $jumlah = count($request->nilai);



        $assessment->update([

            'total_nilai'=>$total,

            'nilai_akhir'=>round(
                $total/$jumlah,
                2
            )

        ]);


        $this->selesaikanSesiJikaNilaiAkhirMutuKeluar(
            $testSession
        );


        return redirect()

            ->route(
                'panelis.dashboard'
            )

            ->with(
                'success',
                'Penilaian berhasil disimpan'
            );


    }


    private function selesaikanSesiJikaNilaiAkhirMutuKeluar(
        TestSession $testSession
    ) {

        $testSession->selesaikanOtomatisJikaLengkap();

    }

}