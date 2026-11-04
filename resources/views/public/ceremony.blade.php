<!DOCTYPE html>
<html lang="en" class="h-full bg-[#030712]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Closing Ceremony & Grand Presentation | KIU Sports Gala</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,900|cinzel:700,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
        .gold-glow {
            text-shadow: 0 0 25px rgba(251, 191, 36, 0.45), 0 0 50px rgba(245, 158, 11, 0.25);
        }
        .trophy-bounce {
            animation: floatTrophy 4s ease-in-out infinite;
        }
        @keyframes floatTrophy {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-12px) scale(1.03); }
        }
        /* Custom scrollbar for high-res displays */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #060b18; }
        ::-webkit-scrollbar-thumb { background: #f59e0b; border-radius: 999px; }
    </style>
</head>
<body class="h-full text-slate-100 flex flex-col justify-between overflow-x-hidden selection:bg-amber-400 selection:text-slate-950 font-sans antialiased relative">

    <!-- Celebration Canvas for Confetti -->
    <canvas id="confetti-canvas" class="fixed inset-0 pointer-events-none z-50 w-full h-full"></canvas>

    <!-- Ambient Glowing Orbs Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 right-1/4 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Top Presentation Header -->
    <header class="py-4 px-6 sm:px-10 border-b border-slate-800/80 bg-[#050a18]/90 backdrop-blur-md flex items-center justify-between z-20">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-600 p-0.5 shadow-lg shadow-amber-500/20">
                <div class="w-full h-full bg-[#050a18] rounded-[10px] flex items-center justify-center text-amber-400 font-black">
                    🏆
                </div>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400 block leading-tight">
                    Karakoram International University
                </span>
                <h1 class="text-base sm:text-lg font-black tracking-wide text-white uppercase leading-tight font-cinzel">
                    {{ $edition->title ?? 'Annual Sports Gala' }} &bull; Grand Ceremony
                </h1>
            </div>
        </div>

        <!-- Navigation Tabs / Slide Switcher -->
        <div class="flex items-center space-x-2">
            <button type="button" onclick="goToSlide(1)" id="tab-btn-1"
                    class="slide-tab-btn px-4 py-2 rounded-xl text-xs font-black transition-all bg-amber-400 text-slate-950 shadow-md">
                🥇 General Trophy
            </button>
            <button type="button" onclick="goToSlide(2)" id="tab-btn-2"
                    class="slide-tab-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-slate-800/80 hover:bg-slate-700 text-slate-300">
                🏅 Sport Champions
            </button>
            <button type="button" onclick="goToSlide(3)" id="tab-btn-3"
                    class="slide-tab-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-slate-800/80 hover:bg-slate-700 text-slate-300">
                ⭐ Highlights
            </button>
        </div>

        <!-- Right Quick Controls -->
        <div class="flex items-center space-x-3">
            <!-- Edition Selector -->
            <form method="GET" action="{{ route('ceremony') }}" class="hidden md:block">
                <select name="edition_id" onchange="this.form.submit()"
                        class="bg-slate-900 border border-slate-700 text-amber-400 text-xs font-bold rounded-xl py-1.5 px-3 focus:ring-0">
                    @foreach ($editions as $ed)
                        <option value="{{ $ed->id }}" {{ $edition?->id == $ed->id ? 'selected' : '' }}>
                            {{ $ed->title }} ({{ $ed->year }})
                        </option>
                    @endforeach
                </select>
            </form>

            <button type="button" onclick="toggleFullScreen()" title="Full Screen (F)"
                    class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>

            <a href="{{ route('home') }}"
               class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-colors">
                Exit
            </a>
        </div>
    </header>

    <!-- Main Presentation Slides Container -->
    <main class="flex-1 flex flex-col justify-center px-4 sm:px-10 py-6 max-w-7xl mx-auto w-full relative">

        <!-- ========================================== -->
        <!-- SLIDE 1: GENERAL TROPHY & CHAMPIONS PODIUM -->
        <!-- ========================================== -->
        <div id="slide-1" class="presentation-slide space-y-6">

            <!-- Title & Subtitle Banner -->
            <div class="text-center space-y-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    Annual Championship General Trophy Standings
                </span>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight font-cinzel">
                    Overall Department Champions
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto">
                    Olympic Points System: Gold (5 Pts) &bull; Silver (3 Pts) &bull; Bronze (1 Pt)
                </p>
            </div>

            @if ($champion)
                <!-- Grand Champion Spotlight Box -->
                <div class="bg-gradient-to-r from-amber-500/20 via-amber-400/10 to-amber-500/20 border-2 border-amber-400 rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden shadow-2xl shadow-amber-500/10">
                    <div class="trophy-bounce text-6xl sm:text-7xl mb-2">
                        🏆
                    </div>
                    <span class="text-xs sm:text-sm font-black uppercase tracking-widest text-amber-400 bg-[#050a18] px-4 py-1 rounded-full border border-amber-400/40 inline-block mb-2">
                        👑 OVERALL GALA CHAMPIONS 👑
                    </span>
                    <h3 class="text-3xl sm:text-5xl font-black text-white tracking-tight gold-glow font-cinzel">
                        {{ $champion['department']->name }}
                    </h3>
                    <div class="flex items-center justify-center space-x-6 mt-4 text-xs sm:text-sm font-bold">
                        <span class="text-amber-400">🥇 {{ $champion['gold'] }} Gold Medals</span>
                        <span class="text-slate-300">🥈 {{ $champion['silver'] }} Silver Medals</span>
                        <span class="text-amber-600">🥉 {{ $champion['bronze'] }} Bronze Medals</span>
                        <span class="px-3 py-1 rounded-lg bg-amber-400 text-slate-950 font-black">
                            {{ $champion['points'] }} Total Points
                        </span>
                    </div>
                </div>

                <!-- Leaderboard Table -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-[#0a1226] uppercase text-[10px] sm:text-xs font-black tracking-wider text-amber-300 border-b border-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-center w-16">Rank</th>
                                <th class="px-6 py-3">Academic Department</th>
                                <th class="px-6 py-3 text-center text-amber-400">🥇 Gold</th>
                                <th class="px-6 py-3 text-center text-slate-300">🥈 Silver</th>
                                <th class="px-6 py-3 text-center text-amber-600">🥉 Bronze</th>
                                <th class="px-6 py-3 text-center">Total Medals</th>
                                <th class="px-6 py-3 text-right">General Points</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 font-semibold">
                            @foreach ($medalTally->take(8) as $index => $row)
                                <tr class="{{ $index === 0 ? 'bg-amber-400/10 text-white font-black' : ($index === 1 ? 'bg-slate-800/40 text-slate-200' : 'hover:bg-slate-800/30 text-slate-300') }}">
                                    <td class="px-6 py-3.5 text-center font-black">
                                        @if ($index === 0) 🥇 1st
                                        @elseif ($index === 1) 🥈 2nd
                                        @elseif ($index === 2) 🥉 3rd
                                        @else #{{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-sm {{ $index === 0 ? 'text-amber-300 font-black' : 'text-white' }}">
                                        {{ $row['department']->name }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-bold text-amber-400">{{ $row['gold'] }}</td>
                                    <td class="px-6 py-3.5 text-center font-bold text-slate-300">{{ $row['silver'] }}</td>
                                    <td class="px-6 py-3.5 text-center font-bold text-amber-600">{{ $row['bronze'] }}</td>
                                    <td class="px-6 py-3.5 text-center font-mono">{{ $row['total_medals'] }}</td>
                                    <td class="px-6 py-3.5 text-right font-black text-amber-400 font-mono text-base">
                                        {{ $row['points'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-12 text-center max-w-md mx-auto">
                    <span class="text-5xl block mb-3">🏅</span>
                    <h3 class="text-xl font-black text-white">Gala In Progress</h3>
                    <p class="text-xs text-slate-400 mt-2">
                        Winners will be finalized upon the conclusion of championship matches.
                    </p>
                </div>
            @endif

        </div>

        <!-- ========================================== -->
        <!-- SLIDE 2: SPORT-BY-SPORT DISCIPLINE WINNERS -->
        <!-- ========================================== -->
        <div id="slide-2" class="presentation-slide space-y-6 hidden">
            <div class="text-center space-y-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-blue-500/20 text-blue-300 border border-blue-400/30">
                    Discipline Honors & Medalists
                </span>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight font-cinzel">
                    Sports Champions Roll of Honor
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto">
                    Gold, Silver, and Bronze medal-winning departments across official sports disciplines.
                </p>
            </div>

            @if ($winners->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($winners as $winner)
                        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-3 shadow-lg hover:border-amber-400/60 transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                                <span class="font-black text-amber-300 text-sm">
                                    {{ $winner->sport->name ?? 'Sport' }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-800 text-slate-300">
                                    {{ ucfirst($winner->sport->category ?? '') }}
                                </span>
                            </div>

                            <!-- Gold Winner -->
                            <div class="flex items-center space-x-3 bg-amber-400/10 border border-amber-400/30 rounded-xl p-2.5">
                                <span class="text-2xl">🥇</span>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block">Gold Medalist (Champion)</span>
                                    <span class="font-black text-white text-xs sm:text-sm truncate block">
                                        {{ $winner->winnerDepartment->name ?? 'TBD' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Silver Winner -->
                            @if ($winner->runnerUpDepartment)
                                <div class="flex items-center space-x-3 bg-slate-800/60 rounded-xl p-2">
                                    <span class="text-lg">🥈</span>
                                    <div class="min-w-0">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Silver Medalist</span>
                                        <span class="font-bold text-slate-200 text-xs truncate block">
                                            {{ $winner->runnerUpDepartment->name }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Bronze Winner -->
                            @if ($winner->secondRunnerUpDepartment)
                                <div class="flex items-center space-x-3 bg-slate-800/40 rounded-xl p-2">
                                    <span class="text-lg">🥉</span>
                                    <div class="min-w-0">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-amber-600 block">Bronze Medalist</span>
                                        <span class="font-bold text-slate-300 text-xs truncate block">
                                            {{ $winner->secondRunnerUpDepartment->name }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-12 text-center max-w-md mx-auto">
                    <span class="text-5xl block mb-3">🏆</span>
                    <h3 class="text-xl font-black text-white">Championship Matches Scheduled</h3>
                    <p class="text-xs text-slate-400 mt-2">
                        Official discipline winners will be announced following finals.
                    </p>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 3: GALA SPOTLIGHT & TOP ATHLETES     -->
        <!-- ========================================== -->
        <div id="slide-3" class="presentation-slide space-y-6 hidden">
            <div class="text-center space-y-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    Outstanding Achievements & Stars
                </span>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight font-cinzel">
                    Gala Tournament Spotlight
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto">
                    {{ $totalMatches }} completed tournament matches &bull; Recognizing top performers and memorable gala milestones.
                </p>
            </div>

            <!-- Spotlight Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Matches Stat Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 text-center space-y-2">
                    <span class="text-4xl">⚔️</span>
                    <h3 class="text-3xl sm:text-4xl font-black text-white font-mono">{{ $totalMatches }}</h3>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Completed Matches</span>
                </div>

                <!-- Disciplines Count Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 text-center space-y-2">
                    <span class="text-4xl">🏅</span>
                    <h3 class="text-3xl sm:text-4xl font-black text-amber-400 font-mono">{{ $winners->count() }}</h3>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Championship Titles Decided</span>
                </div>

                <!-- Departments Represented -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 text-center space-y-2">
                    <span class="text-4xl">🏛️</span>
                    <h3 class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono">{{ $medalTally->count() }}</h3>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Departments on Medal Board</span>
                </div>
            </div>

            <!-- Top Athletes of the Tournament -->
            @if ($topPerformers->isNotEmpty())
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4">
                    <h3 class="text-base font-black text-white flex items-center">
                        <span class="mr-2 text-xl">⭐</span> Most Valuable Players (Player of the Match Leaders)
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($topPerformers as $athlete)
                            <div class="bg-[#050a18] border border-slate-800 rounded-2xl p-4 flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl bg-amber-400/20 text-amber-300 font-black flex items-center justify-center text-lg shrink-0">
                                    {{ strtoupper(substr($athlete->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-black text-white text-sm truncate">{{ $athlete->name }}</h4>
                                    <span class="text-xs text-slate-400 truncate block">{{ $athlete->team->department->name ?? 'Dept' }}</span>
                                    <span class="inline-block mt-1 text-[10px] font-bold text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded-full border border-amber-400/30">
                                        ⭐ {{ $athlete->potm_count }}x MVP Awards
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </main>

    <!-- Bottom Floating Controller Bar (Can be minimized or used via remote/keyboard) -->
    <footer class="py-3 px-6 bg-[#050a18]/95 border-t border-slate-800/80 backdrop-blur-md flex items-center justify-between text-xs text-slate-400 z-20">
        <div class="flex items-center space-x-4">
            <span class="hidden sm:inline-flex items-center text-slate-500">
                <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px] mr-1.5 border border-slate-700">←</kbd>
                <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px] mr-1.5 border border-slate-700">→</kbd>
                Switch Slide
            </span>
            <span class="hidden sm:inline-flex items-center text-slate-500">
                <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px] mr-1.5 border border-slate-700">F</kbd>
                Full Screen
            </span>
            <span class="hidden sm:inline-flex items-center text-slate-500">
                <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px] mr-1.5 border border-slate-700">C</kbd>
                Confetti
            </span>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Confetti Celebration Trigger -->
            <button type="button" onclick="launchConfetti()"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 active:scale-95 transition-transform flex items-center">
                <span class="mr-1.5">🎉</span> Celebrate (Confetti)
            </button>

            <!-- Auto-Cycle Toggle -->
            <button type="button" id="auto-cycle-btn" onclick="toggleAutoCycle()"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-colors">
                ▶ Auto Cycle: Off
            </button>
        </div>
    </footer>

    <!-- Presentation Engine & Canvas Confetti Script -->
    <script>
        let currentSlide = 1;
        const totalSlides = 3;
        let autoCycleInterval = null;

        function goToSlide(slideNum) {
            currentSlide = slideNum;

            for (let i = 1; i <= totalSlides; i++) {
                const slideEl = document.getElementById(`slide-${i}`);
                const tabEl = document.getElementById(`tab-btn-${i}`);

                if (i === slideNum) {
                    slideEl.classList.remove('hidden');
                    tabEl.classList.remove('bg-slate-800/80', 'text-slate-300');
                    tabEl.classList.add('bg-amber-400', 'text-slate-950', 'shadow-md');
                } else {
                    slideEl.classList.add('hidden');
                    tabEl.classList.remove('bg-amber-400', 'text-slate-950', 'shadow-md');
                    tabEl.classList.add('bg-slate-800/80', 'text-slate-300');
                }
            }
        }

        function nextSlide() {
            let next = currentSlide + 1;
            if (next > totalSlides) next = 1;
            goToSlide(next);
        }

        function prevSlide() {
            let prev = currentSlide - 1;
            if (prev < 1) prev = totalSlides;
            goToSlide(prev);
        }

        function toggleAutoCycle() {
            const btn = document.getElementById('auto-cycle-btn');
            if (autoCycleInterval) {
                clearInterval(autoCycleInterval);
                autoCycleInterval = null;
                btn.textContent = '▶ Auto Cycle: Off';
                btn.classList.remove('bg-emerald-600', 'text-white');
                btn.classList.add('bg-slate-800', 'text-slate-300');
            } else {
                nextSlide();
                autoCycleInterval = setInterval(nextSlide, 10000); // 10s per slide
                btn.textContent = '⏸ Auto Cycle: Active';
                btn.classList.remove('bg-slate-800', 'text-slate-300');
                btn.classList.add('bg-emerald-600', 'text-white');
            }
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.warn(`Fullscreen error: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Lightweight Pure Canvas Confetti Engine (Zero external dependencies)
        function launchConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const particles = [];
            const colors = ['#f59e0b', '#fbbf24', '#3b82f6', '#10b981', '#ec4899', '#ffffff'];

            for (let i = 0; i < 220; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * -canvas.height,
                    w: Math.random() * 12 + 6,
                    h: Math.random() * 8 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    vy: Math.random() * 4 + 3,
                    vx: (Math.random() - 0.5) * 4,
                    rotation: Math.random() * 360,
                    vRot: (Math.random() - 0.5) * 8
                });
            }

            let animationFrame;
            let start = Date.now();

            function render() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                const elapsed = Date.now() - start;

                particles.forEach(p => {
                    p.y += p.vy;
                    p.x += p.vx;
                    p.rotation += p.vRot;

                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rotation * Math.PI) / 180);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });

                if (elapsed < 6000) {
                    animationFrame = requestAnimationFrame(render);
                } else {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    cancelAnimationFrame(animationFrame);
                }
            }

            render();
        }

        // Keyboard navigation bindings
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === ' ' || e.key === 'PageDown') {
                nextSlide();
            } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                prevSlide();
            } else if (e.key === 'f' || e.key === 'F') {
                toggleFullScreen();
            } else if (e.key === 'c' || e.key === 'C') {
                launchConfetti();
            }
        });

        // Auto confetti on slide 1 load if champion is present
        window.addEventListener('DOMContentLoaded', () => {
            @if($champion)
                setTimeout(launchConfetti, 1000);
            @endif
        });
    </script>
</body>
</html>

