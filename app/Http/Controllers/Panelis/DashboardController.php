<?php

namespace App\Http\Controllers\Panelis;


use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Models\SessionUser;



class DashboardController extends Controller
{


public function index()
{

    $user = auth()->user();


    $sessions = \App\Models\SessionUser::where(
        'user_id',
        $user->id
    )
    ->with([
        'testSession.sample.product.assessmentTemplates.sections.criterias',
        'testSession.assessments'
    ])
    ->get();

    $sessionsMenunggu = $sessions->filter(function ($session) use ($user) {

        $sudahDinilai = $session
            ->testSession
            ->assessments
            ->contains('user_id', $user->id);

        return !$sudahDinilai;

    });

    $totalParameter = $sessionsMenunggu->sum(function ($session) {

        $template = $session
            ->testSession
            ->sample
            ?->product
            ?->assessmentTemplates
            ?->first();

        if (!$template) {

            return 0;

        }

        return $template
            ->sections
            ->sum(fn($section) => $section->criterias->count());

    });



    return view(
        'panelis.dashboard',
        compact('sessions', 'sessionsMenunggu', 'totalParameter')
    );

}


}