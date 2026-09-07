<table border="1">


    <tr>

        <th>No</th>
        <th>Panelis</th>
        <th>Kenampakan</th>
        <th>Bau</th>
        <th>Rasa</th>
        <th>Tekstur</th>
        <th>Jumlah</th>
        <th>Rata-rata</th>

    </tr>


    @php

    $stats = \App\Support\OrganolepticStatistics::calculate($testSession);

    $jKenampakan=0;
    $jBau=0;
    $jRasa=0;
    $jTekstur=0;
    $jTotal=0;

    @endphp


    @foreach($testSession->assessments as $index=>$assessment)


    @php

    $data=[];

    foreach($assessment->details as $detail){

    $data[$detail->criteria->nama_kriteria]
    =
    $detail->nilai;

    }

    $jKenampakan += $data['Kenampakan'] ?? 0;
    $jBau += $data['Bau'] ?? 0;
    $jRasa += $data['Rasa'] ?? 0;
    $jTekstur += $data['Tekstur'] ?? 0;
    $jTotal += $assessment->total_nilai;

    @endphp



    <tr>

        <td>
            {{ $index+1 }}
        </td>


        <td>
            {{ $assessment->user->name }}
        </td>


        <td>
            {{ $data['Kenampakan'] ?? '-' }}
        </td>


        <td>
            {{ $data['Bau'] ?? '-' }}
        </td>


        <td>
            {{ $data['Rasa'] ?? '-' }}
        </td>


        <td>
            {{ $data['Tekstur'] ?? '-' }}
        </td>


        <td>
            {{ $assessment->total_nilai }}
        </td>


        <td>
            {{ number_format($assessment->nilai_akhir,2) }}
        </td>


    </tr>



    @endforeach


    <tr>

        <td colspan="2"><b>Jumlah</b></td>
        <td>{{ $jKenampakan }}</td>
        <td>{{ $jBau }}</td>
        <td>{{ $jRasa }}</td>
        <td>{{ $jTekstur }}</td>
        <td>{{ $jTotal }}</td>
        <td>{{ number_format($stats['p'],2) }}</td>

    </tr>


</table>


<br><br>


<table border="1">

    <tr><td><b>Konstanta</b></td><td>{{ number_format($stats['konstanta'],2) }}</td></tr>
    <tr><td><b>n (Jumlah Panelis)</b></td><td>{{ $stats['n'] }}</td></tr>
    <tr><td><b>√n</b></td><td>{{ number_format($stats['akar_n'],4) }}</td></tr>
    <tr><td><b>s²</b></td><td>{{ number_format($stats['s2'],4) }}</td></tr>
    <tr><td><b>s</b></td><td>{{ number_format($stats['s'],4) }}</td></tr>
    <tr><td><b>P min</b></td><td>{{ number_format($stats['p_min'],2) }}</td></tr>
    <tr><td><b>P max</b></td><td>{{ number_format($stats['p_max'],2) }}</td></tr>
    <tr><td><b>P (Skor Akhir Mutu)</b></td><td>{{ number_format($stats['p'],2) }}</td></tr>

</table>


<br><br>


<table border="1">

    <tr>

        <td><b>NILAI AKHIR MUTU (P)</b></td>

    </tr>

    <tr>

        <td>{{ number_format($stats['p_bulat'],1) }}</td>

    </tr>

    <tr>

        <td>(DIBULATKAN 0.5)</td>

    </tr>

</table>