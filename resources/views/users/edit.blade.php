<x-app-layout>
    @section('header_title', 'Edit User')
    @section('content')
    <div class="max-w-2xl mx-auto bg-white border border-slate-200 rounded-xl p-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            @include('users._form', ['user' => $user])
            <button class="w-full bg-[#cf9e50] text-white font-semibold rounded-xl py-3">Simpan User</button>
        </form>
    </div>
    @endsection
</x-app-layout>
