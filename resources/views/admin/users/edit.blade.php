<x-app-layout>


<x-slot name="header">

<h2 class="font-semibold text-xl text-gray-800">

Edit User

</h2>

</x-slot>




<div class="py-12">


<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


<div class="bg-white shadow-sm rounded-lg p-6">




<form action="{{ route('admin.users.update', $user->id) }}"
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



{{-- Nama --}}

<div class="mb-4">


<label class="block font-medium mb-2">

Nama

</label>


<input type="text"

name="name"

value="{{ old('name', $user->name) }}"

class="w-full border rounded p-2"

placeholder="Masukkan nama user">


</div>





{{-- Username --}}

<div class="mb-4">


<label class="block font-medium mb-2">

Username

</label>


<input type="text"

name="username"

value="{{ old('username', $user->username) }}"

class="w-full border rounded p-2"

placeholder="Masukkan username">


</div>





{{-- NIP --}}

<div class="mb-4">


<label class="block font-medium mb-2">

NIP

</label>


<input type="text"

name="nip"

value="{{ old('nip', $user->nip) }}"

class="w-full border rounded p-2"

placeholder="Masukkan NIP">


</div>





{{-- Password (opsional) --}}

<div class="mb-4">


<label class="block font-medium mb-2">

Password <span class="text-gray-400 text-sm">(kosongkan jika tidak diubah)</span>

</label>


<input type="password"

name="password"

class="w-full border rounded p-2"

placeholder="Password baru">


</div>





{{-- Role --}}

<div class="mb-4">


<label class="block font-medium mb-2">

Role

</label>


<select name="role"

class="w-full border rounded p-2">


<option value="admin"

{{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>

Admin

</option>


<option value="panelis"

{{ old('role', $user->role) == 'panelis' ? 'selected' : '' }}>

Panelis

</option>


<option value="penyelia"

{{ old('role', $user->role) == 'penyelia' ? 'selected' : '' }}>

Penyelia

</option>


</select>


</div>





{{-- Status --}}

<div class="mb-6">


<label class="block font-medium mb-2">

Status

</label>


<select name="status"

class="w-full border rounded p-2">


<option value="aktif"

{{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>

Aktif

</option>


<option value="nonaktif"

{{ old('status', $user->status) == 'nonaktif' ? 'selected' : '' }}>

Nonaktif

</option>


</select>


</div>






<button type="submit"

class="bg-blue-600 text-white px-4 py-2 rounded">


Update User


</button>




<a href="{{ route('admin.users.index') }}"

class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">


Kembali


</a>




</form>




</div>


</div>


</div>



</x-app-layout>
