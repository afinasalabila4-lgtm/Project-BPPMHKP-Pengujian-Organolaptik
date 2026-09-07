<x-app-layout>


    <x-slot name="header">

        <h2 class="font-bold text-xl text-[#003B5C]">
            Hasil Penilaian Organoleptik
        </h2>

    </x-slot>



    <div class="py-10">


        <div class="max-w-7xl mx-auto px-6">



            <div class="bg-white rounded-2xl shadow p-8">



                {{-- INFORMASI --}}

                <div class="flex justify-between mb-8">


                    <div>

                        <h1 class="text-2xl font-bold">

                            {{ $testSession->sample->product->nama_produk }}

                        </h1>


                        <p class="mt-3">
                            Nomor Sample :
                            {{ $testSession->sample->nomor_sample }}
                        </p>


                        <p>
                            Tanggal :
                            {{ $testSession->tanggal_pengujian }}
                        </p>


                    </div>




                    <div class="text-right">


                        <p class="font-semibold">

                            Jenis Produk :

                            {{ $testSession->sample->product->nama_produk }}

                        </p>


                    </div>


                </div>







                {{-- TABEL NILAI --}}


                <div class="overflow-x-auto">


                    <table class="w-full border-collapse border">


                        <thead>


                            <tr class="bg-gray-100">


                                <th class="border p-3">
                                    No
                                </th>


                                <th class="border p-3">
                                    Panelis
                                </th>


                                @foreach($criteriaList as $criteria)

                                <th class="border p-3">
                                    {{ $criteria->nama_kriteria }}
                                </th>

                                @endforeach



                                <th class="border p-3">
                                    Jumlah
                                </th>


                                <th class="border p-3">
                                    Rata-rata
                                </th>


                            </tr>


                        </thead>




                        <tbody>



                            @foreach($testSession->assessments as $index=>$assessment)


                            @php

                            $data=[];

                            foreach($assessment->details as $detail)
                            {

                            $data[$detail->criteria->nama_kriteria]
                            =
                            $detail->nilai;

                            }

                            @endphp



                            <tr>


                                <td class="border p-3 text-center">

                                    {{ $index+1 }}

                                </td>



                                <td class="border p-3">

                                    {{ $assessment->user->name }}

                                </td>




                                @foreach($criteriaList as $criteria)


                                <td class="border p-3 text-center">

                                    {{ $data[$criteria->nama_kriteria] ?? '-' }}

                                </td>


                                @endforeach




                                <td class="border p-3 text-center">

                                    {{ $assessment->total_nilai }}

                                </td>



                                <td class="border p-3 text-center">

                                    {{ number_format($assessment->nilai_akhir,2) }}

                                </td>


                            </tr>


                            @endforeach





                            {{-- JUMLAH --}}

                            <tr class="font-bold bg-gray-50">


                                <td colspan="2"
                                    class="border p-3 text-center">

                                    Jumlah

                                </td>




                                @foreach($criteriaList as $criteria)


                                <td class="border p-3 text-center">


                                    {{
$testSession
->assessments
->flatMap->details
->where('criteria_id',$criteria->id)
->sum('nilai')
}}


                                </td>


                                @endforeach





                                <td class="border p-3 text-center">

                                    {{ $testSession->assessments->sum('total_nilai') }}

                                </td>




                                <td class="border p-3 text-center">

                                    {{ number_format($rataRata,2) }}

                                </td>


                            </tr>



                        </tbody>


                    </table>


                </div>







                {{-- STATISTIK --}}


                <div class="grid grid-cols-2 gap-8 mt-8">



                    <div>


                        <table class="w-full border">


                            <tr>

                                <td class="border p-3">
                                    Konstanta
                                </td>

                                <td class="border p-3">
                                    {{ number_format($stats['konstanta'],2) }}
                                </td>

                            </tr>



                            <tr>

                                <td class="border p-3">
                                    n (Jumlah Panelis)
                                </td>

                                <td class="border p-3">

                                    {{ $stats['n'] }}

                                </td>

                            </tr>



                            <tr>

                                <td class="border p-3">
                                    √n
                                </td>

                                <td class="border p-3">

                                    {{ number_format($stats['akar_n'],4) }}

                                </td>

                            </tr>



                            <tr>

                                <td class="border p-3">
                                    s²
                                </td>

                                <td class="border p-3">

                                    {{ number_format($stats['s2'],4) }}

                                </td>

                            </tr>



                            <tr>

                                <td class="border p-3">
                                    s
                                </td>

                                <td class="border p-3">

                                    {{ number_format($stats['s'],4) }}

                                </td>

                            </tr>



                            <tr>

                                <td class="border p-3">
                                    P min
                                </td>

                                <td class="border p-3">

                                    {{ number_format($stats['p_min'],2) }}

                                </td>

                            </tr>



                            <tr>

                                <td class="border p-3">
                                    P max
                                </td>

                                <td class="border p-3">

                                    {{ number_format($stats['p_max'],2) }}

                                </td>

                            </tr>



                            <tr class="font-bold">

                                <td class="border p-3">
                                    P (Skor Akhir Mutu)
                                </td>

                                <td class="border p-3">

                                    {{ number_format($stats['p'],2) }}

                                </td>

                            </tr>


                        </table>


                    </div>







                    {{-- NILAI AKHIR --}}


                    <div class="bg-blue-50 rounded-xl flex items-center justify-center">


                        <div class="text-center">


                            <p class="text-blue-700 font-bold text-xl">

                                NILAI AKHIR MUTU (P)

                            </p>



                            <p class="text-6xl font-bold text-[#003B5C] mt-4">

                                {{ number_format($stats['p_bulat'],1) }}

                            </p>


                            <p class="font-semibold">

                                (DIBULATKAN 0.5)

                            </p>


                        </div>


                    </div>



                </div>

                {{-- DOKUMEN HASIL PENGUJIAN --}}

                <div class="mt-10 border-t pt-8">


                    <h3 class="text-lg font-bold text-[#003B5C] mb-5">
                        Dokumen Hasil Pengujian
                    </h3>



                    <div class="grid md:grid-cols-3 gap-5">


                                {{-- PDF --}}

                            <a

        href="{{ route('admin.test_sessions.exportPdf',$testSession->id) }}"

        class="
        bg-red-600
        hover:bg-red-700
        text-white
        font-bold
        px-6
        py-3
        rounded-xl
        shadow
        inline-flex
        items-center
        gap-2
        "

        >

        📄 Download PDF

        </a>




                        {{-- EXCEL --}}

                        <a href="{{ route('admin.test_sessions.exportExcel',$testSession->id) }}"
                            class="
flex
items-center
justify-center
gap-3

bg-green-600
hover:bg-green-700

text-white
font-bold

px-6
py-4

rounded-xl

shadow
transition
">

                            📊 Export Excel

                        </a>






                        {{-- PRINT --}}

                        <button
                            onclick="window.print()"

                            class="
flex
items-center
justify-center
gap-3

bg-[#003B5C]

hover:bg-[#002B45]

text-white

font-bold

px-6
py-4

rounded-xl

shadow

transition
">

                            🖨️ Cetak Laporan

                        </button>


                    </div>


                </div>


                {{-- SCORESHEET PANELIS --}}

                <div class="mt-10 border-t pt-8">

                    <h3 class="text-lg font-bold text-[#003B5C] mb-2">
                        Scoresheet Panelis
                    </h3>

                    <p class="text-sm text-slate-500 mb-6">
                        Rincian penilaian masing-masing panelis yang sudah memberikan penilaian. Klik Download Excel untuk mengunduh scoresheet per panelis.
                    </p>


                    @if($testSession->assessments->isEmpty())

                    <p class="text-sm text-slate-500">
                        Belum ada panelis yang memberikan penilaian.
                    </p>

                    @else

                    <div class="grid md:grid-cols-2 gap-6">

                        @foreach($testSession->assessments as $index=>$assessment)

                        @php

                        $scData=[];

                        foreach($assessment->details as $detail)
                        {

                        $scData[$detail->criteria->nama_kriteria]
                        =
                        $detail->nilai;

                        }

                        @endphp

                        <div class="border border-slate-200 rounded-2xl p-6">

                            <div class="flex justify-between items-start mb-4">

                                <div>

                                    <p class="font-bold text-slate-800">
                                        {{ $assessment->user->name }}
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        NIP : {{ $assessment->user->nip ?? '-' }}
                                    </p>

                                </div>


                                <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                                    Panelis {{ $index+1 }}
                                </span>

                            </div>


                            <table class="w-full border-collapse text-sm mb-4">

                                @foreach($scData as $kriteria=>$nilai)

                                <tr>

                                    <td class="border px-3 py-2">
                                        {{ $kriteria }}
                                    </td>

                                    <td class="border px-3 py-2 text-center font-bold">
                                        {{ $nilai }}
                                    </td>

                                </tr>

                                @endforeach


                                <tr class="bg-slate-50 font-bold">

                                    <td class="border px-3 py-2">
                                        Jumlah
                                    </td>

                                    <td class="border px-3 py-2 text-center">
                                        {{ $assessment->total_nilai }}
                                    </td>

                                </tr>


                                <tr class="bg-slate-50 font-bold">

                                    <td class="border px-3 py-2">
                                        Rata-rata
                                    </td>

                                    <td class="border px-3 py-2 text-center">
                                        {{ number_format($assessment->nilai_akhir,2) }}
                                    </td>

                                </tr>

                            </table>


                            <div class="flex items-center gap-2">

                                <a
                                    href="{{ route('admin.test_sessions.scoresheet.excel',[$testSession->id,$assessment->id]) }}"
                                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold px-5 py-2.5 rounded-lg shadow text-sm transition"
                                >
                                    📥 Download Excel
                                </a>

                                <a
                                    href="{{ route('admin.test_sessions.scoresheet.pdf',[$testSession->id,$assessment->id]) }}"
                                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-lg shadow text-sm transition"
                                >
                                    📄 Download PDF
                                </a>

                            </div>

                        </div>

                        @endforeach

                    </div>

                    @endif

                </div>


            </div>






        </div>


    </div>


    </div>



</x-app-layout>