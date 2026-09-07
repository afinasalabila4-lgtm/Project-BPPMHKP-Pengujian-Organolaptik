<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800">
            Edit Sesi Pengujian
        </h2>

    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <div class="bg-white shadow rounded-lg p-6">



                <form action="{{ route('admin.test_sessions.update',$testSession->id) }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif



                    <div class="mb-4">

                        <label class="block font-medium mb-2">
                            Pilih Sample
                        </label>


                        <select name="sample_id"
                            class="w-full border rounded p-2">


                            @foreach($samples as $sample)


                            <option value="{{ $sample->id }}"

                                @if($sample->id == $testSession->sample_id)
                                selected
                                @endif

                                >

                                {{ $sample->product->nama_produk }}
                                -
                                {{ $sample->nomor_sample }}

                            </option>


                            @endforeach


                        </select>


                    </div>




                    <div class="mb-4">


                        <label class="block font-medium mb-2">

                            Tanggal Pengujian

                        </label>


                        <input type="date"

                            name="tanggal_pengujian"

                            value="{{ $testSession->tanggal_pengujian }}"

                            class="w-full border rounded p-2">


                    </div>






                    <div class="mb-4">


                        <label class="block font-medium mb-2">

                            Status

                        </label>


                        <select name="status"

                            class="w-full border rounded p-2">


                            <option value="draft"
                                @if($testSession->status=='draft')
                                selected
                                @endif
                                >
                                Draft
                            </option>



                            <option value="dibuka"
                                @if($testSession->status=='dibuka')
                                selected
                                @endif
                                >
                                Dibuka
                            </option>



                            <option value="selesai"
                                @if($testSession->status=='selesai')
                                selected
                                @endif
                                >
                                Selesai
                            </option>



                        </select>


                    </div>






                    <div class="mb-4">


                        <label class="block font-medium mb-2">

                            Catatan

                        </label>


                        <textarea

                            name="catatan"

                            class="w-full border rounded p-2">{{ $testSession->catatan }}</textarea>


                    </div>





                    {{-- PANELIS --}}
                    <div class="mb-6">

                        <label class="block font-medium mb-3">
                            Panelis
                        </label>

                        <div id="panelis-wrapper" class="space-y-4">

                            @php
                            $panelisCount = max(count($existingPanelis), 1);
                            @endphp

                            @foreach($existingPanelis as $index => $panelisId)
                            <div class="panelis-row flex items-center gap-4">
                                <label class="w-32 font-medium">
                                    Panelis {{ $index + 1 }}
                                </label>
                                <select name="panelis[]"
                                    class="flex-1 border rounded p-2">
                                    <option value="">-- Pilih Panelis --</option>
                                    @foreach($panelis as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $user->id == $panelisId ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    onclick="hapusPanelis(this)"
                                    class="bg-red-600 text-white px-3 py-2 rounded">
                                    Hapus
                                </button>
                            </div>
                            @endforeach

                            @if(count($existingPanelis) == 0)
                            <div class="panelis-row flex items-center gap-4">
                                <label class="w-32 font-medium">
                                    Panelis 1
                                </label>
                                <select name="panelis[]"
                                    class="flex-1 border rounded p-2">
                                    <option value="">-- Pilih Panelis --</option>
                                    @foreach($panelis as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    onclick="hapusPanelis(this)"
                                    class="bg-red-600 text-white px-3 py-2 rounded">
                                    Hapus
                                </button>
                            </div>
                            @endif

                        </div>

                        <button type="button"
                            onclick="tambahPanelis()"
                            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                            + Tambah Panelis
                        </button>

                    </div>




                    <button

                        class="bg-blue-600 text-white px-4 py-2 rounded">

                        Update

                    </button>



                    <a href="{{ route('admin.test_sessions.index') }}"

                        class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">

                        Kembali

                    </a>


                </form>



            </div>


        </div>

    </div>

    <script>
        let jumlahPanelis = {{ $panelisCount }};

        function tambahPanelis()
        {
            jumlahPanelis++;

            let wrapper = document.getElementById('panelis-wrapper');

            let div = document.createElement('div');
            div.className = "panelis-row flex items-center gap-4";

            div.innerHTML = `
                <label class="w-32 font-medium">Panelis ${jumlahPanelis}</label>
                <select name="panelis[]" class="flex-1 border rounded p-2">
                    <option value="">-- Pilih Panelis --</option>
                    @foreach($panelis as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="hapusPanelis(this)" class="bg-red-600 text-white px-3 py-2 rounded">Hapus</button>
            `;

            wrapper.appendChild(div);
        }

        function hapusPanelis(button)
        {
            let row = button.closest('.panelis-row');
            row.remove();
        }
    </script>

</x-app-layout>
