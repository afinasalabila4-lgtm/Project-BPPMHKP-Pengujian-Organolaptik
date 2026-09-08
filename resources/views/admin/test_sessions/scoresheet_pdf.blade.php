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

<th>Spesifikasi</th>

<th>Nilai (1 s.d 9)</th>

</tr>



@foreach($sections as $section)

<tr class="section-row">

<td colspan="2">

■ {{ $section->nama_section ?? 'Section' }}

</td>

</tr>


@foreach($assessment->details->filter(fn($d)=>$d->criteria->assessment_section_id===$section->id)->sortBy(fn($d)=>(int)$d->id) as $detail)

<tr>

<td class="left">

{{ $detail->criteria->nama_kriteria }}

</td>

<td>

{{ $detail->nilai }}

</td>

</tr>


@endforeach

@endforeach

</table>



</body>

</html>