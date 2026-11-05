<x-guest-layout>
    <div class="space-y-6">

        <!-- Form Header -->
        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-sports font-black uppercase tracking-wider bg-blue-50 text-blue-800 border border-blue-200">
                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Authorized Personnel Access</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Sign In to Match Control
            </h1>

            <p class="text-sm text-slate-500 leading-relaxed">
                Welcome back. Enter your official university credentials to enter live scores, update fixtures, and manage tournaments.
            </p>
        </div>

        <!-- Session Status Alert -->
        <x-auth-session-status class="mb-2" :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Official Email Address
                </label>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="admin@kiu.edu.pk or scorer@kiu.edu.pk"
                           class="block w-full pl-10 pr-3.5 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-700 hover:text-blue-900 font-semibold transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <div class="relative rounded-xl shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••••••"
                           class="block w-full pl-10 pr-3.5 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="w-4 h-4 rounded border-slate-300 text-blue-700 focus:ring-blue-500 cursor-pointer">
                    <span class="ms-2 text-xs font-medium text-slate-600">Remember this device</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-sports font-black text-sm uppercase tracking-wider bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 shadow-md shadow-amber-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                    <span>Sign In to Match Control</span>
                    <span class="text-base font-bold">&rarr;</span>
                </button>
            </div>
        </form>

        <!-- Official Security & Credentials Notice -->
        <div class="pt-4 border-t border-slate-100">
            <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-md bg-blue-900 text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 fill-current text-amber-400" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <div class="text-xs text-slate-600 space-y-1">
                        <span class="font-bold text-slate-900 uppercase tracking-wide block text-[11px]">
                            Directorate of Sports Authorization
                        </span>
                        <p class="leading-relaxed text-[11px] text-slate-600">
                            Public registration is closed. Match scorer, coordinator, and administrator credentials are exclusively provisioned by the Directorate of Sports.
                        </p>
                        <p class="text-[11px] text-blue-700 font-semibold pt-1">
                            Official support desk: <a href="mailto:sports@kiu.edu.pk" class="underline hover:text-blue-900">sports@kiu.edu.pk</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-guest-layout>
