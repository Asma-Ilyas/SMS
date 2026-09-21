{{-- resources/views/welcome.blade.php --}}
@php
    $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login');
    $appName  = config('app.name', 'Bluebird');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} | School Management System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    {{--
        Tailwind: this CDN build lets the page work instantly.
        If your Laravel project already compiles Tailwind with Vite,
        delete the two <script> lines below and use @vite(['resources/css/app.css', 'resources/js/app.js']) instead.
        (Add font-display / font-sans to tailwind.config.js as shown in the config below.)
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

{{-- ============ NAVBAR ============ --}}
<header class="sticky top-0 z-30 border-b border-blue-100 bg-blue-50/85 backdrop-blur dark:border-blue-950 dark:bg-navy-deep/85">
    <div class="mx-auto flex h-[72px] max-w-6xl items-center justify-between px-6">
        <a href="#top" class="flex items-center gap-2.5 font-display text-[22px] font-extrabold tracking-tight">
            <svg viewBox="0 0 40 40" class="h-9 w-9" aria-hidden="true">
                <rect width="40" height="40" rx="12" fill="#1D4ED8"/>
                <path d="M10 26c0-7 5-12 12-12h8c0 7-5 12-12 12h-2v4h-6z" fill="#fff"/>
                <circle cx="24" cy="19" r="1.7" fill="#1D4ED8"/>
            </svg>
            {{ $appName }}
        </a>

        <nav class="hidden gap-8 font-medium text-slate-500 md:flex dark:text-blue-200/70" aria-label="Main">
            <a href="#features" class="hover:text-blue-600">Features</a>
            <a href="#roles" class="hover:text-blue-600">Who it's for</a>
            <a href="#numbers" class="hover:text-blue-600">Results</a>
        </nav>

        <a href="{{ $loginUrl }}"
           class="rounded-full bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-0.5 hover:bg-blue-500 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300">
            Log in
        </a>
    </div>
</header>

<main id="top">

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden px-6 pb-24 pt-16 lg:pt-20">
        <div class="pointer-events-none absolute -right-40 -top-40 h-[640px] w-[640px] rounded-full bg-gradient-to-br from-blue-200 to-transparent opacity-80 dark:from-blue-900/50"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-14 lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <span class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-100 px-3.5 py-2 text-sm font-semibold text-blue-700 dark:bg-blue-900/50 dark:text-blue-200">
                    <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                    Built for schools of every size
                </span>

                <h1 class="font-display text-[44px] font-extrabold leading-[1.02] tracking-tight sm:text-6xl lg:text-7xl">
                    Run your whole school from one calm place.
                </h1>

                <p class="mt-6 max-w-lg text-lg text-slate-500 dark:text-blue-200/70">
                    Attendance, timetables, grades and fees, all in one system that teachers and students actually enjoy using.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $loginUrl }}"
                       class="rounded-full bg-blue-600 px-7 py-4 font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-0.5 hover:bg-blue-500 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300">
                        Log in to your school
                    </a>
                    <a href="#features"
                       class="hidden rounded-full border-2 border-blue-200 px-7 py-4 font-semibold transition hover:border-blue-600 hover:text-blue-600 sm:inline-block dark:border-blue-900">
                        See what's inside
                    </a>
                </div>

                <div class="mt-10 flex items-center gap-3.5 text-[15px] text-slate-500 dark:text-blue-200/70">
                    <div class="flex" aria-hidden="true">
                        <span class="grid h-9 w-9 place-items-center rounded-full border-[3px] border-blue-50 bg-blue-700 text-sm font-semibold text-white dark:border-navy-deep">A</span>
                        <span class="-ml-2.5 grid h-9 w-9 place-items-center rounded-full border-[3px] border-blue-50 bg-blue-500 text-sm font-semibold text-white dark:border-navy-deep">S</span>
                        <span class="-ml-2.5 grid h-9 w-9 place-items-center rounded-full border-[3px] border-blue-50 bg-navy text-sm font-semibold text-white dark:border-navy-deep">M</span>
                        <span class="-ml-2.5 grid h-9 w-9 place-items-center rounded-full border-[3px] border-blue-50 bg-blue-400 text-sm font-semibold text-white dark:border-navy-deep">R</span>
                    </div>
                    <span>Trusted by 1,200+ schools and 300,000 students</span>
                </div>
            </div>

            {{-- Dashboard preview --}}
            <div class="relative rounded-[28px] border border-blue-100 bg-white p-5 shadow-2xl shadow-blue-600/20 dark:border-blue-900 dark:bg-blue-950/60" aria-label="Preview of the school dashboard">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="font-display text-lg font-bold leading-tight">Good morning, Ms. Hadi</p>
                        <p class="text-sm text-slate-500 dark:text-blue-200/70">Monday, Grade 8 · Section B</p>
                    </div>
                    <div class="flex gap-1.5" aria-hidden="true">
                        <i class="h-2.5 w-2.5 rounded-full bg-blue-100 dark:bg-blue-900"></i>
                        <i class="h-2.5 w-2.5 rounded-full bg-blue-100 dark:bg-blue-900"></i>
                        <i class="h-2.5 w-2.5 rounded-full bg-blue-100 dark:bg-blue-900"></i>
                    </div>
                </div>

                <div class="grid gap-3.5 sm:grid-cols-2">
                    <div class="rounded-2xl bg-blue-50 p-4 dark:bg-blue-900/30">
                        <p class="mb-2.5 text-sm font-semibold text-slate-500 dark:text-blue-200/70">Today's attendance</p>
                        <div class="flex items-center gap-3.5">
                            <svg viewBox="0 0 80 80" class="h-[74px] w-[74px] shrink-0">
                                <circle cx="40" cy="40" r="32" fill="none" stroke="#DBEAFE" stroke-width="10"/>
                                <circle cx="40" cy="40" r="32" fill="none" stroke="#1D4ED8" stroke-width="10" stroke-linecap="round" stroke-dasharray="190 201" transform="rotate(-90 40 40)"/>
                            </svg>
                            <div>
                                <p class="font-display text-[26px] font-extrabold leading-none">94%</p>
                                <p class="text-[13px] text-slate-500 dark:text-blue-200/70">38 of 40 present</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-blue-50 p-4 dark:bg-blue-900/30">
                        <p class="mb-2.5 text-sm font-semibold text-slate-500 dark:text-blue-200/70">Average score this term</p>
                        <div class="flex h-16 items-end gap-2" aria-hidden="true">
                            <i class="h-[45%] flex-1 rounded-t-md bg-blue-600/35"></i>
                            <i class="h-[60%] flex-1 rounded-t-md bg-blue-600/35"></i>
                            <i class="h-[52%] flex-1 rounded-t-md bg-blue-600/35"></i>
                            <i class="h-[88%] flex-1 rounded-t-md bg-blue-600"></i>
                            <i class="h-[70%] flex-1 rounded-t-md bg-blue-600/35"></i>
                            <i class="h-[76%] flex-1 rounded-t-md bg-blue-600/35"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-3.5 rounded-2xl bg-blue-50 p-2 dark:bg-blue-900/30">
                    <div class="flex items-center gap-3 rounded-xl bg-blue-600 px-3 py-2.5 text-[15px] text-white">
                        <time class="min-w-[64px] text-sm font-semibold opacity-85">8:30</time>Mathematics
                        <span class="ml-auto text-[13px] opacity-75">Room 12 · now</span>
                    </div>
                    <div class="flex items-center gap-3 px-3 py-2.5 text-[15px]">
                        <time class="min-w-[64px] text-sm font-semibold opacity-85">9:30</time>English
                        <span class="ml-auto text-[13px] opacity-75">Room 12</span>
                    </div>
                    <div class="flex items-center gap-3 px-3 py-2.5 text-[15px]">
                        <time class="min-w-[64px] text-sm font-semibold opacity-85">11:00</time>Science Lab
                        <span class="ml-auto text-[13px] opacity-75">Lab 2</span>
                    </div>
                </div>

                <div class="absolute -bottom-6 left-3 flex items-center gap-3 rounded-2xl bg-navy px-4 py-3.5 text-sm text-white shadow-xl sm:-left-7">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-500">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
                    </span>
                    <span><b class="block text-base">Fees received</b>Term 2 · 87% collected</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FEATURES ============ --}}
    <section id="features" class="px-6 py-24">
        <div class="mx-auto max-w-6xl">
            <div class="mb-14 max-w-2xl">
                <h2 class="font-display text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">Everything a school needs, nothing it doesn't.</h2>
                <p class="mt-4 text-lg text-slate-500 dark:text-blue-200/70">Replace spreadsheets, paper registers and scattered chat groups with one connected system.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[1.1fr_1fr_1fr]">
                {{-- Big feature --}}
                <article class="flex flex-col justify-between rounded-3xl bg-blue-600 bg-[radial-gradient(circle_at_90%_8%,rgba(255,255,255,.22),transparent_45%)] p-7 text-white md:col-span-2 lg:col-span-1 lg:row-span-2">
                    <div>
                        <div class="mb-5 grid h-12 w-12 place-items-center rounded-2xl bg-white/20">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3 8-8"/><path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h9"/></svg>
                        </div>
                        <h3 class="font-display text-[34px] font-bold leading-[1.1] tracking-tight">Attendance in under a minute</h3>
                        <p class="mt-2 text-[17px] text-white/85">Mark a class in a few taps. Absences are recorded instantly and reports build themselves.</p>
                    </div>
                    <ul class="mt-7 space-y-2.5 font-medium">
                        @foreach (['Works on phones and tablets', 'Instant absence alerts', 'Monthly reports, ready to print'] as $point)
                            <li class="flex items-center gap-2.5"><span class="h-2 w-2 shrink-0 rounded-full bg-white"></span>{{ $point }}</li>
                        @endforeach
                    </ul>
                </article>

                @php
                    $features = [
                        ['Timetables', 'Build a weekly schedule without clashes for rooms or teachers.', '<rect x="3" y="4" width="18" height="17" rx="3"/><path d="M8 2v4M16 2v4M3 10h18"/>'],
                        ['Grades and report cards', 'Enter marks once and publish clear report cards for every student.', '<path d="M4 19V9M10 19V5M16 19v-8M22 19H2"/>'],
                        ['Fees and payments', 'Issue invoices, track dues and send reminders automatically.', '<rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20M6 15h4"/>'],
                        ['Notices and messages', 'Share notices, homework and events with the right class or the whole school.', '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/>'],
                    ];
                @endphp

                @foreach ($features as [$title, $text, $icon])
                    <article class="rounded-3xl border border-blue-100 bg-white p-7 dark:border-blue-900 dark:bg-blue-950/60">
                        <div class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-900/60 dark:text-blue-300">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                        </div>
                        <h3 class="mb-2 font-display text-[22px] font-bold tracking-tight">{{ $title }}</h3>
                        <p class="text-slate-500 dark:text-blue-200/70">{{ $text }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ ROLES (Admin, Teacher, Student) ============ --}}
    @php
        $roles = [
            'admin' => [
                'label' => 'Administrators', 'hint' => 'Whole school',
                'title' => 'For administrators',
                'points' => [
                    ['See the whole school at a glance', 'with live attendance, fees and staffing.'],
                    ['Manage classes, staff and admissions', 'from one place.'],
                    ['Export reports', 'for boards and inspections in a click.'],
                ],
            ],
            'teacher' => [
                'label' => 'Teachers', 'hint' => 'Classes',
                'title' => 'For teachers',
                'points' => [
                    ['Take attendance', 'in a few taps from any device.'],
                    ['Enter marks and comments', 'once, then publish report cards.'],
                    ['Share homework and notices', 'with a class instantly.'],
                ],
            ],
            'student' => [
                'label' => 'Students', 'hint' => 'Learning',
                'title' => 'For students',
                'points' => [
                    ['Check the timetable', 'and upcoming exams.'],
                    ['See homework and results', 'as soon as teachers post them.'],
                    ['Track progress', 'across every subject, term by term.'],
                ],
            ],
        ];
    @endphp

    <section id="roles" class="border-y border-blue-100 bg-white px-6 py-24 dark:border-blue-900 dark:bg-blue-950/40" x-data="{ role: 'admin' }">
        <div class="mx-auto max-w-6xl">
            <div class="mb-14 max-w-2xl">
                <h2 class="font-display text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">One login. The right view for everyone.</h2>
                <p class="mt-4 text-lg text-slate-500 dark:text-blue-200/70">Each person sees only what matters to them the moment they sign in.</p>
            </div>

            <div class="grid items-start gap-12 lg:grid-cols-[.8fr_1.2fr]">
                <div class="grid gap-2.5" role="tablist" aria-label="Choose a role">
                    @foreach ($roles as $key => $r)
                        <button type="button" role="tab"
                                @click="role = '{{ $key }}'"
                                :aria-selected="role === '{{ $key }}'"
                                :class="role === '{{ $key }}' ? 'border-blue-600 bg-blue-600 text-white' : 'border-blue-100 hover:border-blue-400 dark:border-blue-900'"
                                class="flex items-center justify-between rounded-2xl border-2 px-5 py-[18px] text-left text-lg font-semibold transition focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300">
                            {{ $r['label'] }}
                            <span class="text-sm font-normal opacity-75">{{ $r['hint'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="rounded-3xl bg-blue-50 p-8 dark:bg-blue-900/30" role="tabpanel">
                    @foreach ($roles as $key => $r)
                        <div x-show="role === '{{ $key }}'" x-cloak @if ($key !== 'admin') style="display:none" @endif>
                            <h3 class="mb-4 font-display text-[28px] font-bold tracking-tight">{{ $r['title'] }}</h3>
                            <ul class="space-y-3.5">
                                @foreach ($r['points'] as [$bold, $rest])
                                    <li class="flex items-start gap-3.5 text-slate-500 dark:text-blue-200/70">
                                        <span class="mt-1 grid h-[22px] w-[22px] shrink-0 place-items-center rounded-full bg-blue-600">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 12.5l4 4 8-9"/></svg>
                                        </span>
                                        <span><b class="font-semibold text-slate-900 dark:text-white">{{ $bold }}</b> {{ $rest }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ $loginUrl }}?role={{ $key }}"
                               class="mt-6 inline-block rounded-full bg-blue-600 px-7 py-4 font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-0.5 hover:bg-blue-500">
                                Log in as {{ \Illuminate\Support\Str::singular(strtolower($r['label'])) }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ STATS ============ --}}
    <section id="numbers" class="bg-navy px-6 py-[72px] text-white">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-6 lg:grid-cols-4">
            @foreach ([['1,200+', 'Schools on ' . $appName], ['300k', 'Students supported'], ['12 hrs', 'Saved per teacher, every month'], ['99.9%', 'Uptime during school hours']] as [$num, $label])
                <div>
                    <p class="font-display text-4xl font-extrabold leading-none tracking-tighter sm:text-6xl">{{ $num }}</p>
                    <p class="mt-2.5 text-blue-200/80">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ FINAL CTA ============ --}}
    <section class="px-6 py-24">
        <div class="relative mx-auto max-w-6xl overflow-hidden rounded-[36px] bg-gradient-to-br from-blue-700 to-blue-500 px-6 py-16 text-center text-white shadow-2xl shadow-blue-600/30 sm:px-10">
            <div class="pointer-events-none absolute -right-28 -top-40 h-[420px] w-[420px] rounded-full border-[60px] border-white/10"></div>
            <h2 class="relative mx-auto max-w-3xl font-display text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">Ready to give your school a calmer day?</h2>
            <p class="relative mx-auto mb-8 mt-4 max-w-lg text-lg text-white/90">Sign in with the account your school gave you and pick up right where you left off.</p>
            <a href="{{ $loginUrl }}" class="relative inline-block rounded-full bg-white px-8 py-4 font-semibold text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">Log in</a>
        </div>
    </section>
</main>

{{-- ============ FOOTER ============ --}}
<footer class="border-t border-blue-100 px-6 pb-11 pt-9 text-[15px] text-slate-500 dark:border-blue-900 dark:text-blue-200/70">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4">
        <span class="font-display text-lg font-extrabold text-slate-900 dark:text-white">{{ $appName }}</span>
        <span>&copy; {{ date('Y') }} {{ $appName }} School Management System</span>
    </div>
</footer>

</body>
</html>