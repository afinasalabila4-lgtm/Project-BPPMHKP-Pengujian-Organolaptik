<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Scoresheet Panelis</title>


<style>

body {

    font-family: Arial, sans-serif;
    font-size: 12px;

}


.title {

    text-align:center;
    font-size:16px;
    font-weight:bold;
    margin-bottom:20px;

}


.info {

    margin-bottom:15px;

}


.info-table {

    width:100%;
    border-collapse:collapse;

}


.info-table td {

    padding:2px 0;

}


.result-table {

    width:100%;
    border-collapse:collapse;
    margin-top:15px;

}


.result-table th {

    background:#eeeeee;
    font-weight:bold;
    border:1px solid black;
    padding:6px;
    text-align:center;

}


.result-table td {

    border:1px solid black;
    padding:6px;
    text-align:center;

}


.result-table .section-row td {

    background:#f5f5f5;
    font-weight:bold;
    text-align:left;

}


.left {

    text-align:left;

}


.total-row td {

    font-weight:bold;

}


.ttd-table {

    margin-top:80px;
    width:100%;
    border-collapse:collapse;
    text-align:center;

}


</style>


</head>



<body>



@php

$data=[];

foreach($assessment->details as $detail)
{

    $data[$detail->criteria->nama_kriteria]
    =
    [
        'kriteria'=>$detail->criteria,
        'nilai'=>$detail->nilai
    ];

}

$total = $assessment->details->sum('nilai');

$jumlah = count($assessment->details);

$rataRata = $jumlah > 0 ? round($total/$jumlah,2) : 0;

$sections = $assessment->details
    ->map(fn($d)=>$d->criteria->assessmentSection)
    ->unique('id')
    ->values();

@endphp



<div class="title">

SCORESHEET PENILAIAN PANELIS

</div>



<table class="info-table">

<tr>

<td>

Produk :
{{ $assessment->testSession->sample->product->nama_produk ?? '-' }}

</td>

</tr>


<tr>

<td>

Nomor Sample :
{{ $assessment->testSession->sample->nomor_sample ?? '-' }}

</td>

</tr>


<tr>

<td>

Tanggal Pengujian :
{{ $assessment->testSession->tanggal_pengujian ?? '-' }}

</td>

</tr>


<tr>

<td>

Nama Panelis :
{{ $assessment->user->name ?? '-' }}
&nbsp;&nbsp;&nbsp;
NIP :
{{ $assessment->user->nip ?? '-' }}

</td>

</tr>

</table>



<table class="result-table">


<tr>

<th>No</th>

<th>Kriteria</th>

<th>Nilai</th>

<th>Deskripsi</th>

</tr>



@php $no=0; @endphp


@foreach($sections as $section)

<tr class="section-row">

<td colspan="4">

■ {{ $section->nama_section ?? 'Section' }}

</td>

</tr>


@foreach($assessment->details->filter(fn($d)=>$d->criteria->assessment_section_id===$section->id)->sortBy(fn($d)=>(int)$d->id) as $detail)

@php

$no++;

$deskripsi = $detail->criteria->options
    ->where('nilai',$detail->nilai)
    ->first();

@endphp


<tr>

<td>

{{ $no }}

</td>

<td class="left">

{{ $detail->criteria->nama_kriteria }}

</td>

<td>

{{ $detail->nilai }}

</td>

<td class="left">

{{ $deskripsi?->deskripsi ?? '-' }}

</td>

</tr>


@endforeach

@endforeach


<tr class="total-row">

<td colspan="2">

Jumlah

</td>

<td>

{{ $total }}

</td>

<td>

</td>

</tr>


<tr class="total-row">

<td colspan="2">

Rata-rata

</td>

<td>

{{ number_format($rataRata,2) }}

</td>

<td>

</td>

</tr>


</table>



<table class="ttd-table">

<tr>

<td style="width:50%">

Panelis

</td>

<td style="width:50%">

Penyelia

</td>

</tr>

<tr>

<td style="height:60px">

&nbsp;

</td>

<td>

&nbsp;

</td>

</tr>


<tr>

<td>

(........................................)

</td>

<td>

(........................................)

</td>

</tr>

</table>



</body>

</html>