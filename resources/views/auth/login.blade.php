{{-- resources/views/auth/login.blade.php --}}
@php
    // Fixed name so the page never shows "Laravel" (the default APP_NAME).
    $appName   = 'School';
    $homeUrl   = url('/');
    $forgotUrl = \Illuminate\Support\Facades\Route::has('password.request') ? route('password.request') : null;

    $roles = [
        'admin' => [
            'label' => 'Administrators',
            'title' => 'For administrators',
            'points' => [
                ['See the whole school at a glance', 'with live attendance, fees and staffing.'],
                ['Manage classes, staff and admissions', 'from one place.'],
                ['Export reports', 'for boards and inspections in a click.'],
            ],
        ],
        'teacher' => [
            'label' => 'Teachers',
            'title' => 'For teachers',
            'points' => [
                ['Take attendance', 'in a few taps from any device.'],
                ['Enter marks and comments', 'once, then publish report cards.'],
                ['Share homework and notices', 'with a class instantly.'],
            ],
        ],
        'student' => [
            'label' => 'Students',
            'title' => 'For students',
            'points' => [
                ['Check the timetable', 'and upcoming exams.'],
                ['See homework and results', 'as soon as teachers post them.'],
                ['Track progress', 'across every subject, term by term.'],
            ],
        ],
    ];

    // The landing page links here with ?role=admin|teacher|student
    $requestedRole = (string) request('role');
    $initialRole   = isset($roles[$requestedRole]) ? $requestedRole : 'admin';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | {{ $appName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    {{--
        Same setup as the landing page.
        If your project compiles Tailwind with Vite, delete the two Tailwind <script> tags
        and use @vite(['resources/css/app.css', 'resources/js/app.js']) instead.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['"Bricolage Grotesque"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: { DEFAULT: '#0B1F4B', deep: '#050E26' },
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="bg-blue-50 font-sans text-[17px] leading-relaxed text-slate-800 antialiased dark:bg-navy-deep dark:text-blue-50">

<div class="min-h-screen lg:grid lg:grid-cols-[1fr_1.05fr]"
     x-data="{
        role: @js($initialRole),
        email: @js(old('email', '')),
        password: '',
        show: false,
        submitting: false,
        fill(email) { this.email = email; this.password = 'password123'; },
     }"
     @pageshow.window="submitting = false">

    {{-- ============ BRAND PANEL (desktop) ============ --}}
    <aside class="relative hidden overflow-hidden bg-navy p-12 text-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col lg:justify-between xl:p-16">
        <div class="pointer-events-none absolute -right-32 -top-40 h-[460px] w-[460px] rounded-full border-[64px] border-white/[.06]"></div>
        <div class="pointer-events-none absolute -bottom-48 -left-40 h-[520px] w-[520px] rounded-full bg-[radial-gradient(circle,rgba(59,130,246,.35),transparent_65%)]"></div>

        <a href="{{ $homeUrl }}" class="relative flex w-fit items-center gap-3 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg shadow-blue-600/25">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </span>
            <span class="leading-tight">
                <span class="block font-display text-xl font-extrabold tracking-tight text-white">{{ $appName }}</span>
                <span class="block text-[10px] uppercase tracking-wider text-blue-200/70">Management System</span>
            </span>
        </a>

        <div class="relative max-w-lg">
            <h2 class="font-display text-5xl font-extrabold leading-[1.02] tracking-tight xl:text-6xl">
                Pick up right where you left off.
            </h2>

            <div class="mt-9 flex flex-wrap gap-2" role="tablist" aria-label="Choose a role">
                @foreach ($roles as $key => $r)
                    <button type="button" role="tab"
                            @click="role = '{{ $key }}'"
                            :aria-selected="role === '{{ $key }}'"
                            :class="role === '{{ $key }}' ? 'border-blue-600 bg-blue-600 text-white' : 'border-white/20 text-blue-100 hover:border-white/50'"
                            class="rounded-full border-2 px-5 py-2.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300">
                        {{ $r['label'] }}
                    </button>
                @endforeach
            </div>

            <div class="mt-7" role="tabpanel">
                @foreach ($roles as $key => $r)
                    <ul x-show="role === '{{ $key }}'" x-cloak class="space-y-3.5">
                        @foreach ($r['points'] as [$bold, $rest])
                            <li class="flex items-start gap-3.5 text-blue-100/80">
                                <span class="mt-1 grid h-[22px] w-[22px] shrink-0 place-items-center rounded-full bg-blue-600">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 12.5l4 4 8-9"/></svg>
                                </span>
                                <span><b class="font-semibold text-white">{{ $bold }}</b> {{ $rest }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>

        <div class="relative flex w-fit items-center gap-4 rounded-2xl border border-white/10 bg-white/10 p-4 pr-6 backdrop-blur" aria-hidden="true">
            <svg viewBox="0 0 80 80" class="h-[64px] w-[64px] shrink-0">
                <circle cx="40" cy="40" r="32" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="10"/>
                <circle cx="40" cy="40" r="32" fill="none" stroke="#60A5FA" stroke-width="10" stroke-linecap="round" stroke-dasharray="190 201" transform="rotate(-90 40 40)"/>
            </svg>
            <div>
                <p class="font-display text-[26px] font-extrabold leading-none">94%</p>
                <p class="mt-1 text-[13px] text-blue-100/75">Attendance marked today</p>
            </div>
        </div>
    </aside>

    {{-- ============ FORM ============ --}}
    <main class="relative flex min-h-screen flex-col px-6 py-8 sm:px-10">
        <div class="pointer-events-none absolute -right-40 -top-40 h-[520px] w-[520px] rounded-full bg-gradient-to-br from-blue-200 to-transparent opacity-70 lg:hidden dark:from-blue-900/50"></div>

        <div class="relative flex items-center justify-between">
            <a href="{{ $homeUrl }}" class="flex min-w-0 items-center gap-3 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 lg:invisible">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg shadow-blue-600/25">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </span>
            <span class="leading-tight">
                <span class="block font-display text-xl font-extrabold tracking-tight ">{{ $appName }}</span>
                <span class="block text-[10px] uppercase tracking-wider text-slate-500 dark:text-blue-200/60">Management System</span>
            </span>
        </a>

            <a href="{{ route('landing') }}" aria-label="Back to home" class="ml-3 inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-2 text-[15px] font-medium text-slate-500 transition hover:text-blue-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:text-blue-200/70">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                <span class="hidden sm:inline">Back to home</span>
            </a>
        </div>

        <div class="relative my-auto w-full max-w-md self-center py-10">
            <h1 class="font-display text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">Welcome back.</h1>
            <p class="mt-3 text-lg text-slate-500 dark:text-blue-200/70">Log in with the account your school gave you.</p>

            <div class="mt-8 rounded-[28px] border border-blue-100 bg-white p-6 shadow-2xl shadow-blue-600/10 sm:p-8 dark:border-blue-900 dark:bg-blue-950/60">

                @if (session('status'))
                    <div role="status" class="mb-6 rounded-2xl bg-blue-50 px-4 py-3 text-[15px] font-medium text-blue-800 dark:bg-blue-900/40 dark:text-blue-100">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[15px] text-red-800 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">
                        <svg class="mt-0.5 shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7.5v5M12 16.5h.01"/></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" @submit="submitting = true" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-[15px] font-semibold">Email</label>
                        <input id="email" name="email" type="email" x-model="email" value="{{ old('email') }}"
                               required autocomplete="username" inputmode="email" placeholder="you@school.edu"
                               @if (! old('email')) autofocus @endif
                               @error('email') aria-invalid="true" @enderror
                               class="w-full rounded-2xl border-2 bg-white px-4 py-3.5 text-base placeholder:text-slate-400 transition focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:bg-navy-deep/60 dark:placeholder:text-blue-200/40 dark:focus:ring-blue-900 {{ $errors->has('email') ? 'border-red-400' : 'border-blue-100 dark:border-blue-900' }}">
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label for="password" class="text-[15px] font-semibold">Password</label>
                            @if ($forgotUrl)
                                <a href="{{ $forgotUrl }}" class="text-[15px] font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:text-blue-300">Forgot password?</a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" name="password" x-model="password" :type="show ? 'text' : 'password'"
                                   required autocomplete="current-password" placeholder="Enter your password"
                                   @if (old('email')) autofocus @endif
                                   @error('password') aria-invalid="true" @enderror
                                   class="w-full rounded-2xl border-2 bg-white py-3.5 pl-4 pr-14 text-base placeholder:text-slate-400 transition focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:bg-navy-deep/60 dark:placeholder:text-blue-200/40 dark:focus:ring-blue-900 {{ $errors->has('password') ? 'border-red-400' : 'border-blue-100 dark:border-blue-900' }}">
                            <button type="button" @click="show = !show"
                                    :aria-label="show ? 'Hide password' : 'Show password'" :aria-pressed="show"
                                    class="absolute inset-y-0 right-2 my-auto grid h-10 w-10 place-items-center rounded-full text-slate-400 transition hover:text-blue-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:text-blue-200/60">
                                <svg x-show="!show" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="show" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 6.1A9.7 9.7 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-3.2 3.9M6.6 6.7C3.9 8.5 2 12 2 12s3.5 6 10 6c1.6 0 3-.4 4.3-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                            </button>
                        </div>
                    </div>

                    <label for="remember" class="flex w-fit cursor-pointer items-center gap-3 text-[15px] text-slate-500 dark:text-blue-200/70">
                        <input id="remember" name="remember" type="checkbox" class="h-5 w-5 rounded-md border-2 border-blue-200 accent-blue-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300" @checked(old('remember'))>
                        Keep me logged in on this device
                    </label>

                    <button type="submit" :disabled="submitting"
                            class="w-full rounded-full bg-blue-600 px-7 py-4 text-base font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-0.5 hover:bg-blue-500 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 disabled:translate-y-0 disabled:cursor-wait disabled:opacity-70">
                        <span x-show="!submitting">Log in</span>
                        <span x-show="submitting" x-cloak>Logging in…</span>
                    </button>
                </form>
            </div>

            {{-- Test accounts: only rendered when APP_ENV=local. Remove this block before going live. --}}
            @if (app()->environment('local'))
                <div class="mt-5 rounded-2xl border-2 border-dashed border-blue-200 px-5 py-4 dark:border-blue-900">
                    <p class="text-[15px] font-semibold">Test accounts <span class="font-normal text-slate-500 dark:text-blue-200/70">(local only, fills the form)</span></p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" @click="fill('principal@school.edu.pk')"
                                class="rounded-full border-2 border-blue-200 px-4 py-2 text-sm font-semibold transition hover:border-blue-600 hover:text-blue-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-blue-900">
                            Administrator
                        </button>
                        <button type="button" @click="fill('muhammad.asif@school.edu.pk')"
                                class="rounded-full border-2 border-blue-200 px-4 py-2 text-sm font-semibold transition hover:border-blue-600 hover:text-blue-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-blue-900">
                            Teacher
                        </button>
                        <button type="button" @click="fill('imran.javed1@gmail.com')"
                                class="rounded-full border-2 border-blue-200 px-4 py-2 text-sm font-semibold transition hover:border-blue-600 hover:text-blue-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-blue-900">
                            Student
                        </button>
                    </div>
                </div>
            @endif

            <p class="mt-6 text-center text-[15px] text-slate-500 dark:text-blue-200/70">
                Can't log in? Ask your school office to reset your account.
            </p>
        </div>

        <p class="relative text-center text-sm text-slate-500 dark:text-blue-200/70">&copy; {{ date('Y') }} {{ $appName }} School Management System</p>
    </main>
</div>

</body>
</html>