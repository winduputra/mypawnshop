<x-guest-layout>
    <div class="mb-8 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-brand-gold">Harmans Gadai Syariah</p>
        <h1 class="mt-3 text-2xl font-bold text-white">Masuk ke MyPawnShop</h1>
        <p class="mt-2 text-sm text-slate-300">Gunakan akun terdaftar untuk mengelola layanan gadai.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-100">Email</label>
            <input id="email" class="mt-2 block w-full rounded-xl border border-white/10 bg-white/95 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-gold/40" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-200" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-100">Password</label>

            <input id="password" class="mt-2 block w-full rounded-xl border border-white/10 bg-white/95 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-200 focus:border-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-gold/40"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Masukkan password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-200" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-white/90 text-brand-green shadow-sm focus:ring-brand-gold" name="remember">
                <span class="ms-2 text-sm text-slate-300">{{ __('Ingat saya') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-medium text-brand-gold transition-colors hover:text-yellow-300 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 focus:ring-offset-slate-900" href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>

        <div>
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-brand-green px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-green/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 focus:ring-offset-slate-900 active:bg-brand-green">
                {{ __('Masuk') }}
            </button>
        </div>
    </form>
</x-guest-layout>
