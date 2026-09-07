<?php

namespace App\Http\Controllers\Penyelia;


use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\TestSession;
use App\Models\Product;
use App\Models\Sample;
use App\Models\User;



class DashboardController extends Controller
{


public function index(Request $request)
{


$user = Auth::user();



/*
|--------------------------------------------------------------------------
| Monitoring Pengujian
|--------------------------------------------------------------------------
*/


$sessions = TestSession::with([

    'sample.product',

    'assessments'

])

->when(request('produk'), function($query){

    $query->whereHas('sample.product', function($q){

        $q->where('id', request('produk'));

    });

})


->when(request('status'), function($query){

    $query->where(
        'status',
        request('status')
    );

})


->when(request('tanggal_mulai'), function($query){

    $query->whereDate(
        'tanggal_pengujian',
        '>=',
        request('tanggal_mulai')
    );

})


->when(request('tanggal_selesai'), function($query){

    $query->whereDate(
        'tanggal_pengujian',
        '<=',
        request('tanggal_selesai')
    );

})


->latest()

->get();


foreach ($sessions as $session) {

    $session->selesaikanOtomatisJikaLengkap();

}


/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/


$totalUji = TestSession::count();



$ujiAktif = TestSession::where(

'status',

'dibuka'

)->count();




$ujiSelesai = TestSession::where(

'status',

'selesai'

)->count();


$totalProduk = Product::count();


$totalSampel = \App\Models\Sample::count();


$totalPanelis = \App\Models\User::where(
    'role',
    'panelis'
)->count();




$sessionsMutu = TestSession::with(
    'assessments'
)->get();



$daftarNilaiAkhir = [];

foreach($sessionsMutu as $session)
{


    foreach($session->assessments as $assessment)
    {

        $daftarNilaiAkhir[] = $assessment->nilai_akhir;

    }


}


$rataMutu = count($daftarNilaiAkhir) > 0

    ? array_sum($daftarNilaiAkhir) / count($daftarNilaiAkhir)

    : 0;

$nilaiMutu = round($rataMutu * 2) / 2;


if($rataMutu >= 7)
{

    $kategoriMutu = "Baik";

}
elseif($rataMutu >= 5)
{

    $kategoriMutu = "Cukup";

}
else
{

    $kategoriMutu = "Perlu Perhatian";

}








/*
|--------------------------------------------------------------------------
| RATA-RATA MUTU
|--------------------------------------------------------------------------
*/


$sessionsMutu = TestSession::with('assessments')->get();



$daftarNilaiAkhir = [];

foreach($sessionsMutu as $session)
{


foreach($session->assessments as $assessment)
{


$daftarNilaiAkhir[] = $assessment->nilai_akhir;


}

}


$rataMutu = count($daftarNilaiAkhir) > 0

    ? array_sum($daftarNilaiAkhir) / count($daftarNilaiAkhir)

    : 0;


$nilaiMutu = round($rataMutu * 2) / 2;






/*
|--------------------------------------------------------------------------
| LIST PRODUK
|--------------------------------------------------------------------------
*/


$produkList = Product::orderBy(

'nama_produk'

)->get();








/*
|--------------------------------------------------------------------------
| GRAFIK BULAN
|--------------------------------------------------------------------------
*/


$grafikPengujian = TestSession::selectRaw(

"MONTH(tanggal_pengujian) bulan,
COUNT(*) jumlah"

)

->groupBy('bulan')

->get()

->map(function($item){

return [

'bulan'=>

date('M',
mktime(
0,
0,
0,
$item->bulan,
1
)),

'jumlah'=>

$item->jumlah

];


});









/*
|--------------------------------------------------------------------------
| GRAFIK NILAI MUTU
|--------------------------------------------------------------------------
*/


$grafikMutu=[];



foreach(TestSession::with([
'assessments',
'sample.product'
])->get()

as $session)
{


$stats = \App\Support\OrganolepticStatistics::calculate($session);



if($stats['n'] > 0)
{


$grafikMutu[]=[


'produk'=>

$session
->sample
->product
->nama_produk,


'nilai'=>

$stats['p_bulat']



];


}



}







/*
|--------------------------------------------------------------------------
| GRAFIK STATUS
|--------------------------------------------------------------------------
*/


$grafikStatus=[


'draft'=>

TestSession::where(
'status',
'draft'
)->count(),


'dibuka'=>

TestSession::where(
'status',
'dibuka'
)->count(),


'selesai'=>

TestSession::where(
'status',
'selesai'
)->count()


];









return view(

'penyelia.dashboard',

compact(

'user',

'sessions',

'totalUji',

'ujiAktif',

'ujiSelesai',

'totalProduk',

'totalSampel',

'totalPanelis',

'nilaiMutu',

'grafikPengujian',

'grafikMutu',

'produkList'

)

);



}



}