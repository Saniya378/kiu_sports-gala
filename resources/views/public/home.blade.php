@extends('layouts.public')

@section('title', 'KIU Sports Gala | Official Portal & Live Coverage')

@section('hero')
    @if ($latestEdition)
        <!-- Bright, Energetic Royal Blue & Athletic Gold Hero Banner -->
        <div class="relative bg-gradient-to-r from-blue-700 via-indigo-800 to-blue-900 text-white overflow-hidden border-b-4 border-amber-400 shadow-xl">
            @if ($latestEdition->banner)
                <div class="absolute inset-0 opacity-15 bg-cover bg-center mix-blend-overlay"
                     style="background-image: url('{{ $latestEdition->banner_url }}');"></div>
            @endif

            <!-- Stadium Atmosphere Mesh Light Gradients -->
            <div class="absolute inset-0 pointer-events-none opacity-25 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-amber-300/40 via-transparent to-transparent"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
                <div class="max-w-4xl mx-auto text-center space-y-6 flex flex-col items-center justify-center">
                    
                    <!-- Centered Tournament Status Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-2.5">
                        @if ($latestEdition->status === 'running')
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/25 text-emerald-200 border border-emerald-400/50 shadow-sm shadow-emerald-500/20">
                                <span class="w-2 h-2 mr-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Live Tournament in Progress
                            </span>
                        @elseif ($latestEdition->status === 'completed')
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-200/20 text-slate-200 border border-slate-300/40">
                                Tournament Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-400/25 text-amber-200 border border-amber-400/50">
                                Upcoming Edition &bull; {{ $latestEdition->year }}
                            </span>
                        @endif
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs text-amber-300 font-sports font-black uppercase tracking-wider bg-amber-400/20 border border-amber-400/40">
                            Year {{ $latestEdition->year }}
                        </span>
                    </div>

                    <!-- Grand Centered Athletic Heading -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white font-sports tracking-wide uppercase drop-shadow-[0_4px_16px_rgba(0,0,0,0.5)] text-center">
                        {{ $latestEdition->title }}
                    </h1>

                    @if ($latestEdition->description)
                        <p class="text-sm sm:text-lg text-blue-100 leading-relaxed max-w-2xl mx-auto text-center font-medium drop-shadow-sm">
                            {{ $latestEdition->description }}
                        </p>
                    @endif

                    <!-- Tournament Dates & Centered Fast Actions -->
                    <div class="pt-3 flex flex-wrap items-center justify-center gap-3.5">
                        @if ($latestEdition->start_date && $latestEdition->end_date)
                            <div class="flex items-center space-x-2 text-xs text-blue-100 bg-blue-950/70 px-4 py-3 rounded-xl border border-blue-400/30 shadow-sm">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="font-semibold">{{ $latestEdition->start_date->format('M d') }} &ndash; {{ $latestEdition->end_date->format('M d, Y') }}</span>
                            </div>
                        @endif

                        @if ($latestEdition->status === 'running')
                            <a href="{{ route('today') }}" class="inline-flex items-center px-6 py-3 rounded-xl text-xs font-sports font-black tracking-wider uppercase bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 shadow-lg shadow-amber-500/30 transition-all transform hover:-translate-y-0.5">
                                <span>Today's Matches</span> &rarr;
                            </a>
                        @endif

                        <a href="{{ route('schedule') }}" class="inline-flex items-center px-5 py-3 rounded-xl text-xs font-sports font-bold tracking-wider uppercase bg-white/15 hover:bg-white/25 text-white border border-white/30 backdrop-blur-sm transition-all transform hover:-translate-y-0.5">
                            <span>Full Schedule</span>
                        </a>

                        <a href="{{ route('results') }}" class="inline-flex items-center px-5 py-3 rounded-xl text-xs font-sports font-bold tracking-wider uppercase bg-white/15 hover:bg-white/25 text-white border border-white/30 backdrop-blur-sm transition-all transform hover:-translate-y-0.5">
                            <span>Points Table</span>
                        </a>

                        <!-- Replay Opening Intro Button -->
                        <button type="button" id="btn-replay-intro" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-xs font-sports font-bold tracking-wider uppercase bg-amber-400/20 hover:bg-amber-400/30 text-amber-300 border border-amber-400/50 backdrop-blur-sm transition-all transform hover:-translate-y-0.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 fill-current text-amber-400" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <span>Watch Opening Intro</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

<!-- ========================================================
     CINEMATIC FULLSCREEN OPENING INTRO: BATSMAN SMASH & STADIUM SOUND
     ======================================================== -->
<div id="gala-cinematic-intro" class="fixed inset-0 z-[100] bg-[#020617] text-white flex flex-col justify-between overflow-hidden select-none">
    
    <!-- Top Intro Navigation Bar -->
    <div class="px-4 sm:px-8 py-3.5 bg-slate-950/90 border-b border-slate-800 flex items-center justify-between text-xs relative z-20">
        <div class="flex items-center space-x-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="font-sports font-black tracking-widest uppercase text-amber-400 text-xs sm:text-sm">
                KIU Annual Sports Gala 2026 &bull; Official Opening
            </span>
        </div>

        <div class="flex items-center space-x-2.5">
            <!-- Audio Toggle Button -->
            <button type="button" id="intro-audio-toggle" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-all cursor-pointer">
                <span id="intro-audio-icon">🔊</span>
                <span id="intro-audio-label">Sound Active</span>
            </button>

            <!-- Skip Button -->
            <button type="button" id="intro-skip-btn" class="px-4 py-1.5 rounded-full text-xs font-sports font-black uppercase tracking-wider bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-md transition-all cursor-pointer">
                Skip Intro ✕
            </button>
        </div>
    </div>

    <!-- Center Stage: Fullscreen Animated Batsman & Pitch -->
    <div class="relative flex-1 flex items-center justify-center overflow-hidden w-full cursor-pointer" id="intro-stage-clickzone" title="Click to trigger stadium crowd cheer & bat hit sound!">
        
        <!-- Ambient Stadium Spotlights & Background -->
        <div class="absolute inset-0 bg-radial from-blue-900/30 via-slate-950 to-[#01040d]"></div>
        
        <!-- Large SVG Batsman Arena -->
        <div class="w-full max-w-6xl mx-auto px-4 h-full flex items-center justify-center relative">
            <svg id="fullscreen-batsman-svg" viewBox="0 0 1000 450" class="w-full h-auto max-h-[75vh]" preserveAspectRatio="xMidYMid meet">
                <defs>
                    <linearGradient id="introSkyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#020617"/>
                        <stop offset="50%" stop-color="#0b1638"/>
                        <stop offset="100%" stop-color="#020617"/>
                    </linearGradient>
                    <linearGradient id="introPitchGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#1e293b"/>
                        <stop offset="100%" stop-color="#0f172a"/>
                    </linearGradient>
                    <linearGradient id="introBallGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ef4444"/>
                        <stop offset="60%" stop-color="#dc2626"/>
                        <stop offset="100%" stop-color="#7f1d1d"/>
                    </linearGradient>
                    <linearGradient id="introTrailGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="1"/>
                        <stop offset="40%" stop-color="#38bdf8" stop-opacity="0.9"/>
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
                    </linearGradient>
                    <radialGradient id="introBeamL" cx="0%" cy="0%" r="100%">
                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
                    </radialGradient>
                    <radialGradient id="introBeamR" cx="100%" cy="0%" r="100%">
                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.28"/>
                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0"/>
                    </radialGradient>
                    <filter id="introBallGlow" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="4" result="blur"/>
                        <feMerge>
                            <feMergeNode in="blur"/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>

                <!-- Stadium Floodlight Beams -->
                <polygon points="60,30 0,450 480,450" fill="url(#introBeamL)"/>
                <polygon points="940,30 520,450 1000,450" fill="url(#introBeamR)"/>

                <!-- Left Floodlight Tower -->
                <line x1="60" y1="20" x2="60" y2="340" stroke="#334155" stroke-width="3.5"/>
                <polygon points="45,25 75,25 60,10" fill="#64748b"/>
                <circle cx="50" cy="22" r="5" fill="#38bdf8"/>
                <circle cx="60" cy="18" r="6" fill="#ffffff"/>
                <circle cx="70" cy="22" r="5" fill="#38bdf8"/>

                <!-- Right Floodlight Tower -->
                <line x1="940" y1="20" x2="940" y2="340" stroke="#334155" stroke-width="3.5"/>
                <polygon points="925,25 955,25 940,10" fill="#64748b"/>
                <circle cx="930" cy="22" r="5" fill="#f59e0b"/>
                <circle cx="940" cy="18" r="6" fill="#ffffff"/>
                <circle cx="950" cy="22" r="5" fill="#f59e0b"/>

                <!-- Pitch Turf Ground -->
                <rect x="0" y="340" width="1000" height="110" fill="#042f2e" opacity="0.9"/>
                <rect x="50" y="325" width="900" height="50" rx="6" fill="url(#introPitchGrad)" stroke="#334155" stroke-width="1.5"/>

                <!-- Crease Lines -->
                <line x1="260" y1="325" x2="260" y2="375" stroke="#ffffff" stroke-width="3.5" opacity="0.95"/>
                <line x1="180" y1="325" x2="260" y2="325" stroke="#ffffff" stroke-width="2" opacity="0.75"/>
                <line x1="180" y1="375" x2="260" y2="375" stroke="#ffffff" stroke-width="2" opacity="0.75"/>
                <line x1="180" y1="325" x2="180" y2="375" stroke="#ffffff" stroke-width="3" opacity="0.85"/>

                <!-- Wooden Wickets & Bails -->
                <rect x="165" y="255" width="4.5" height="70" fill="#fbbf24" rx="1"/>
                <rect x="173" y="255" width="4.5" height="70" fill="#fbbf24" rx="1"/>
                <rect x="181" y="255" width="4.5" height="70" fill="#fbbf24" rx="1"/>
                <rect x="162" y="252" width="26" height="4.5" fill="#f59e0b" rx="1.5"/>

                <!-- HEROIC FULL-SCREEN BATSMAN -->
                <g id="intro-batsman-figure">
                    <!-- Back Leg & White Pad -->
                    <rect x="195" y="260" width="18" height="65" rx="5" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5"/>
                    <line x1="195" y1="275" x2="213" y2="275" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="195" y1="295" x2="213" y2="295" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="195" y1="312" x2="213" y2="312" stroke="#94a3b8" stroke-width="1.5"/>
                    <ellipse cx="200" cy="330" rx="12" ry="5.5" fill="#0f172a"/>

                    <!-- Front Leg & White Pad (Stepping into Drive) -->
                    <rect x="235" y="262" width="20" height="63" rx="5" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5"/>
                    <line x1="235" y1="278" x2="255" y2="278" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="235" y1="298" x2="255" y2="298" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="235" y1="315" x2="255" y2="315" stroke="#94a3b8" stroke-width="1.5"/>
                    <ellipse cx="250" cy="330" rx="13" ry="5.5" fill="#0f172a"/>

                    <!-- Torso & Official KIU Athletic Jersey -->
                    <path d="M205,210 L248,210 L242,265 L205,263 Z" fill="#1e3a8a" stroke="#172554" stroke-width="1.5"/>
                    <polygon points="205,210 226,232 248,210 248,218 226,240 205,218" fill="#f59e0b"/>
                    <text x="226" y="256" font-family="'Barlow Semi Condensed', sans-serif" font-size="14" font-weight="900" fill="#fef08a" text-anchor="middle">KIU</text>

                    <!-- Helmet & Grille -->
                    <circle cx="224" cy="186" r="18" fill="#0f172a" stroke="#1e3a8a" stroke-width="2.5"/>
                    <path d="M228,186 L246,188 L242,194 L228,192 Z" fill="#334155"/>
                    <line x1="228" y1="192" x2="242" y2="192" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="230" y1="197" x2="240" y2="197" stroke="#94a3b8" stroke-width="1.5"/>
                    <line x1="235" y1="189" x2="235" y2="201" stroke="#94a3b8" stroke-width="1.5"/>

                    <!-- Dynamic Bat & Arms Group (Rotates during stroke) -->
                    <g id="intro-bat-arm" style="transform-origin: 226px 215px; transform: rotate(-40deg);">
                        <path d="M210,215 Q218,228 226,228" stroke="#1e3a8a" stroke-width="9" stroke-linecap="round" fill="none"/>
                        <path d="M238,215 Q235,226 228,230" stroke="#1e3a8a" stroke-width="9" stroke-linecap="round" fill="none"/>
                        <circle cx="226" cy="230" r="7.5" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5"/>
                        <line x1="226" y1="230" x2="231" y2="246" stroke="#3b82f6" stroke-width="5" stroke-linecap="round"/>
                        <path d="M230,246 L242,243 L258,310 L244,314 Z" fill="#d97706" stroke="#b45309" stroke-width="1.5"/>
                        <line x1="235" y1="252" x2="248" y2="306" stroke="#b45309" stroke-width="1.2" opacity="0.6"/>
                        <rect x="232" y="258" width="11" height="10" fill="#ef4444" rx="1"/>
                    </g>
                </g>

                <!-- Rocket Ball Glowing Trail -->
                <path id="intro-ball-trail" d="M280,245 Q520,100 950,25" fill="none" stroke="url(#introTrailGrad)" stroke-width="7" stroke-linecap="round" opacity="0"/>

                <!-- Impact Ring & Sparks -->
                <circle id="intro-impact-ring" cx="280" cy="245" r="45" fill="none" stroke="#f59e0b" stroke-width="5" opacity="0"/>
                <g id="intro-sparks" opacity="0">
                    <line x1="280" y1="245" x2="245" y2="215" stroke="#fbbf24" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="280" y1="245" x2="315" y2="215" stroke="#fbbf24" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="280" y1="245" x2="325" y2="255" stroke="#f59e0b" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="280" y1="245" x2="300" y2="280" stroke="#fbbf24" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="280" y1="245" x2="255" y2="275" stroke="#f59e0b" stroke-width="3.5" stroke-linecap="round"/>
                    <circle cx="310" cy="230" r="4" fill="#fef08a"/>
                    <circle cx="260" cy="225" r="3.5" fill="#fef08a"/>
                    <circle cx="295" cy="270" r="3.5" fill="#fef08a"/>
                </g>

                <!-- Crimson Cricket Ball -->
                <g id="intro-cricket-ball" transform="translate(920, 150)" opacity="0">
                    <circle cx="0" cy="0" r="13" fill="url(#introBallGrad)" filter="url(#introBallGlow)"/>
                    <path d="M-8,-8 C0,-1 0,1 8,8" stroke="#ffffff" stroke-width="2.2" stroke-dasharray="2,2" fill="none" opacity="0.95"/>
                </g>
            </svg>

            <!-- Big Golden Maximum Six Banner -->
            <div id="intro-celebration-banner" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center pointer-events-none opacity-0 z-30">
                <div class="inline-block px-8 py-3.5 rounded-3xl bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 text-slate-950 font-sports font-black text-2xl sm:text-5xl uppercase tracking-wider shadow-2xl shadow-amber-500/50 border-2 border-amber-300">
                    ⚡ MAXIMUM SIX! 🏏
                </div>
                <div class="mt-3 text-base sm:text-2xl font-sports font-black uppercase tracking-widest text-white drop-shadow-[0_4px_12px_rgba(0,0,0,0.9)]">
                    Karakoram International University Sports Gala 2026
                </div>
            </div>
        </div>

        <!-- Bouncing Hint for Audio/Interaction -->
        <div id="intro-sound-prompt" class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 px-5 py-2.5 rounded-full bg-slate-900/95 text-amber-300 border border-amber-400/50 text-xs sm:text-sm font-bold flex items-center gap-2 shadow-2xl animate-bounce pointer-events-none">
            <span>🔊</span>
            <span>Click anywhere for Stadium Crowd Noise & Bat Hit Sound!</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ========================================================
    // STADIUM WEB AUDIO SYNTHESIZER (0 External Files, Instant)
    // ========================================================
    class StadiumAudioEngine {
        constructor() {
            this.ctx = null;
            this.masterGain = null;
            this.crowdGain = null;
            this.noiseSource = null;
            this.isMuted = false;
        }

        init() {
            if (this.ctx) return true;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                this.ctx = new AudioCtx();
                this.masterGain = this.ctx.createGain();
                this.masterGain.gain.setValueAtTime(0.7, this.ctx.currentTime);
                this.masterGain.connect(this.ctx.destination);
                return true;
            } catch (e) {
                console.warn('Web Audio not available:', e);
                return false;
            }
        }

        startAmbience() {
            if (!this.init()) return;
            if (this.ctx.state === 'suspended') {
                this.ctx.resume();
            }

            // Synthesize pinkish stadium crowd roar buffer (2s loop)
            const bufferSize = this.ctx.sampleRate * 2;
            const buffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
            const data = buffer.getChannelData(0);
            let b0 = 0, b1 = 0, b2 = 0, b3 = 0, b4 = 0, b5 = 0, b6 = 0;
            for (let i = 0; i < bufferSize; i++) {
                const white = Math.random() * 2 - 1;
                b0 = 0.99886 * b0 + white * 0.0555179;
                b1 = 0.99332 * b1 + white * 0.0750759;
                b2 = 0.96900 * b2 + white * 0.1538520;
                b3 = 0.86650 * b3 + white * 0.3104856;
                b4 = 0.55000 * b4 + white * 0.5329522;
                b5 = -0.7616 * b5 - white * 0.0168980;
                data[i] = (b0 + b1 + b2 + b3 + b4 + b5 + b6 + white * 0.5362) * 0.08;
                b6 = white * 0.115926;
            }

            this.noiseSource = this.ctx.createBufferSource();
            this.noiseSource.buffer = buffer;
            this.noiseSource.loop = true;

            const filter = this.ctx.createBiquadFilter();
            filter.type = 'bandpass';
            filter.frequency.setValueAtTime(750, this.ctx.currentTime);
            filter.Q.setValueAtTime(1.4, this.ctx.currentTime);

            this.crowdGain = this.ctx.createGain();
            this.crowdGain.gain.setValueAtTime(0.25, this.ctx.currentTime); // gentle stadium murmur

            this.noiseSource.connect(filter);
            filter.connect(this.crowdGain);
            this.crowdGain.connect(this.masterGain);

            this.noiseSource.start();
        }

        playBatHitCrack() {
            if (!this.init()) return;
            if (this.ctx.state === 'suspended') this.ctx.resume();

            const now = this.ctx.currentTime;

            // 1. Heavy resonant wood impact
            const osc = this.ctx.createOscillator();
            const oscGain = this.ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(800, now);
            osc.frequency.exponentialRampToValueAtTime(75, now + 0.09);

            oscGain.gain.setValueAtTime(1.0, now);
            oscGain.gain.exponentialRampToValueAtTime(0.001, now + 0.09);

            osc.connect(oscGain);
            oscGain.connect(this.masterGain);
            osc.start(now);
            osc.stop(now + 0.1);

            // 2. High snap noise (sharp bat crack)
            const snapSize = this.ctx.sampleRate * 0.06;
            const snapBuf = this.ctx.createBuffer(1, snapSize, this.ctx.sampleRate);
            const snapData = snapBuf.getChannelData(0);
            for (let i = 0; i < snapSize; i++) {
                snapData[i] = (Math.random() * 2 - 1) * Math.exp(-i / (this.ctx.sampleRate * 0.012));
            }

            const snapSource = this.ctx.createBufferSource();
            snapSource.buffer = snapBuf;

            const snapFilter = this.ctx.createBiquadFilter();
            snapFilter.type = 'highpass';
            snapFilter.frequency.setValueAtTime(2200, now);

            const snapGain = this.ctx.createGain();
            snapGain.gain.setValueAtTime(1.3, now);
            snapGain.gain.exponentialRampToValueAtTime(0.001, now + 0.06);

            snapSource.connect(snapFilter);
            snapFilter.connect(snapGain);
            snapGain.connect(this.masterGain);
            snapSource.start(now);
        }

        swellCrowdRoar() {
            if (!this.ctx || !this.crowdGain) return;
            const now = this.ctx.currentTime;

            // Crowd volume explodes high! ("awazay mazeed barhay")
            this.crowdGain.gain.cancelScheduledValues(now);
            this.crowdGain.gain.setValueAtTime(this.crowdGain.gain.value, now);
            this.crowdGain.gain.linearRampToValueAtTime(0.85, now + 0.35);

            // Stadium referee / fan whistle
            const whistle1 = this.ctx.createOscillator();
            const whistle2 = this.ctx.createOscillator();
            const whistleGain = this.ctx.createGain();

            whistle1.type = 'sine';
            whistle2.type = 'sine';
            whistle1.frequency.setValueAtTime(2600, now);
            whistle2.frequency.setValueAtTime(2640, now);

            whistleGain.gain.setValueAtTime(0.001, now);
            whistleGain.gain.linearRampToValueAtTime(0.35, now + 0.08);
            whistleGain.gain.exponentialRampToValueAtTime(0.001, now + 0.55);

            whistle1.connect(whistleGain);
            whistle2.connect(whistleGain);
            whistleGain.connect(this.masterGain);

            whistle1.start(now + 0.05);
            whistle2.start(now + 0.05);
            whistle1.stop(now + 0.6);
            whistle2.stop(now + 0.6);
        }

        fadeOut(duration = 1.0) {
            if (!this.ctx || !this.masterGain) return;
            const now = this.ctx.currentTime;
            this.masterGain.gain.cancelScheduledValues(now);
            this.masterGain.gain.setValueAtTime(this.masterGain.gain.value, now);
            this.masterGain.gain.linearRampToValueAtTime(0.001, now + duration);
            setTimeout(() => {
                if (this.noiseSource) {
                    try { this.noiseSource.stop(); } catch(e){}
                }
            }, duration * 1000);
        }

        toggleMute() {
            this.isMuted = !this.isMuted;
            if (this.masterGain && this.ctx) {
                this.masterGain.gain.setValueAtTime(this.isMuted ? 0 : 0.7, this.ctx.currentTime);
            }
            return !this.isMuted;
        }
    }

    // ========================================================
    // CINEMATIC INTRO ORCHESTRATOR
    // ========================================================
    document.addEventListener('DOMContentLoaded', function () {
        const introOverlay = document.getElementById('gala-cinematic-intro');
        if (!introOverlay) return;

        const stageZone = document.getElementById('intro-stage-clickzone');
        const skipBtn = document.getElementById('intro-skip-btn');
        const audioBtn = document.getElementById('intro-audio-toggle');
        const audioLabel = document.getElementById('intro-audio-label');
        const soundPrompt = document.getElementById('intro-sound-prompt');
        const replayBtn = document.getElementById('btn-replay-intro');

        let audioEngine = new StadiumAudioEngine();
        let hitTimer = null;
        let fadeTimer = null;
        let finishTimer = null;
        let hasInteracted = false;

        function startExperience() {
            if (!hasInteracted) {
                hasInteracted = true;
                if (soundPrompt) soundPrompt.style.display = 'none';
                audioEngine.startAmbience();
            }
        }

        function playIntroSequence() {
            introOverlay.style.display = 'flex';
            introOverlay.style.opacity = '1';
            introOverlay.style.pointerEvents = 'auto';

            introOverlay.classList.remove('intro-is-playing');
            void introOverlay.offsetWidth; // Force CSS reflow
            introOverlay.classList.add('intro-is-playing');

            // Attempt to start ambience
            try {
                audioEngine.startAmbience();
            } catch(e) {}

            // Bat connects with ball at 1.1s (40% of 2.8s)
            clearTimeout(hitTimer);
            hitTimer = setTimeout(() => {
                audioEngine.playBatHitCrack();
                audioEngine.swellCrowdRoar();
            }, 1120);

            // Intro completes, fades out gently at 3.6s
            clearTimeout(fadeTimer);
            fadeTimer = setTimeout(() => {
                introOverlay.style.transition = 'opacity 0.8s ease-out';
                introOverlay.style.opacity = '0';
                introOverlay.style.pointerEvents = 'none';
                audioEngine.fadeOut(0.9);
            }, 3600);

            // Completely remove overlay from view at 4.4s ("phir page ma nai rahay batsmen wala")
            clearTimeout(finishTimer);
            finishTimer = setTimeout(() => {
                introOverlay.style.display = 'none';
            }, 4400);
        }

        function closeIntroImmediately() {
            clearTimeout(hitTimer);
            clearTimeout(fadeTimer);
            clearTimeout(finishTimer);
            introOverlay.style.transition = 'opacity 0.3s ease-out';
            introOverlay.style.opacity = '0';
            introOverlay.style.pointerEvents = 'none';
            audioEngine.fadeOut(0.3);
            setTimeout(() => {
                introOverlay.style.display = 'none';
            }, 350);
        }

        // Click anywhere to ensure sound works and trigger hit
        if (stageZone) {
            stageZone.addEventListener('click', function () {
                startExperience();
            });
        }

        if (skipBtn) {
            skipBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                closeIntroImmediately();
            });
        }

        if (audioBtn) {
            audioBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                startExperience();
                const soundOn = audioEngine.toggleMute();
                audioLabel.textContent = soundOn ? 'Sound Active' : 'Sound Muted';
            });
        }

        if (replayBtn) {
            replayBtn.addEventListener('click', function () {
                audioEngine = new StadiumAudioEngine();
                hasInteracted = true;
                if (soundPrompt) soundPrompt.style.display = 'none';
                playIntroSequence();
            });
        }

        // Automatic run when website opens
        setTimeout(playIntroSequence, 150);
    });
</script>
@endpush

@section('content')
<div class="space-y-12">

    @if ($latestEdition && $latestEdition->status === 'running')
        <!-- RUNNING STATE: Today's Action -->
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h2 class="text-2xl font-black text-slate-900">Today's Live & Scheduled Matches</h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ now()->format('l, F j, Y') }} &bull; Live updates from KIU grounds
                    </p>
                </div>
                <a href="{{ route('today') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                    View full board &rarr;
                </a>
            </div>

            @if ($todayMatches->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($todayMatches as $match)
                        <div data-fixture-card="{{ $match->id }}" class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)] hover:shadow-[0_12px_28px_-6px_rgba(15,23,42,0.14)] hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col justify-between group">
                            <!-- Premier League / FIFA Header Bar (Unified Athletic Navy/Blue) -->
                            <div class="bg-gradient-to-r from-blue-950 via-indigo-950 to-blue-900 px-4 py-2.5 border-b border-blue-800/80 flex items-center justify-between text-xs text-white">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span class="font-sports font-bold uppercase tracking-wider text-amber-300 text-xs">
                                        {{ $match->sport->name }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $match->sport->category === 'girls' ? 'bg-pink-500/20 text-pink-300 border border-pink-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' }}">
                                        {{ ucfirst($match->sport->category) }}
                                    </span>
                                </div>
                                <div data-status-badge="{{ $match->id }}">
                                    @if ($match->status === 'live')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs shadow-rose-950/40 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            LIVE
                                        </span>
                                    @elseif ($match->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-amber-400 border border-slate-700">
                                            FINAL
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-950 text-indigo-300 border border-indigo-800/60">
                                            {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('g:i A') : 'Scheduled' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Matchup Body with Athletic Department Crests -->
                            <div class="p-4 sm:p-5 space-y-3.5 bg-gradient-to-b from-white via-white to-slate-50/50">
                                <!-- Team 1 -->
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <!-- Athletic Department Crest Badge -->
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-900 to-indigo-900 border border-blue-700/60 text-amber-400 font-sports font-black flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ $match->team1->department->code ?? substr($match->team1->name, 0, 3) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-sports font-extrabold text-base text-slate-900 group-hover:text-blue-700 transition-colors truncate uppercase tracking-tight">
                                                {{ $match->team1->name }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 font-medium truncate">{{ $match->team1->department->name ?? 'Dept' }}</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span data-team1-score="{{ $match->id }}" class="px-3 py-1 rounded-xl bg-blue-950 text-amber-400 font-sports font-black text-xl min-w-[48px] inline-block text-center shadow-xs tracking-tight border border-blue-800/60 transition-transform duration-300">
                                            {{ $match->result ? $match->formatTeamScore(1) : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="border-t border-slate-100 flex items-center justify-center -my-1">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded-full text-[9px] font-sports font-black uppercase text-slate-400 tracking-widest border border-slate-200">VS</span>
                                </div>

                                <!-- Team 2 -->
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <!-- Athletic Department Crest Badge -->
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-950 to-slate-900 border border-blue-800/60 text-amber-400 font-sports font-black flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ $match->team2->department->code ?? substr($match->team2->name, 0, 3) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-sports font-extrabold text-base text-slate-900 group-hover:text-blue-700 transition-colors truncate uppercase tracking-tight">
                                                {{ $match->team2->name }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 font-medium truncate">{{ $match->team2->department->name ?? 'Dept' }}</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span data-team2-score="{{ $match->id }}" class="px-3 py-1 rounded-xl bg-blue-950 text-amber-400 font-sports font-black text-xl min-w-[48px] inline-block text-center shadow-xs tracking-tight border border-blue-800/60 transition-transform duration-300">
                                            {{ $match->result ? $match->formatTeamScore(2) : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="bg-slate-100/80 px-4 py-3 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-600">
                                <div class="flex items-center space-x-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate font-medium text-[11px]">{{ $match->venue->name ?? 'Venue TBD' }} @if ($match->round) &bull; {{ $match->round }} @endif</span>
                                </div>
                                <a href="{{ route('matches.show', $match) }}" class="inline-flex items-center gap-1 font-sports font-bold text-indigo-700 hover:text-indigo-900 uppercase tracking-wide text-xs shrink-0 ml-2">
                                    <span>Match Center</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No Matches Scheduled for Today</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">
                        Games resume tomorrow. View upcoming fixtures or previous results on the schedule.
                    </p>
                    <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-[#0a1226] hover:bg-[#060b18] text-amber-300 font-bold text-xs rounded-xl transition-colors">
                        Browse Full Schedule
                    </a>
                </div>
            @endif
        </section>

        @if($topMedalTally->isNotEmpty())
            <!-- General Trophy Leaderboard Preview (Bright Royal Blue & Athletic Gold Theme) -->
            <section class="bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-blue-800/80 pb-4">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-300 bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-400/40">
                            Tournament Leaderboard
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
                            <svg class="w-6 h-6 fill-current text-amber-400 shrink-0" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            <span>General Trophy Championship Race</span>
                        </h3>
                        <p class="text-xs text-blue-200 mt-0.5">
                            Leading academic departments competing for the ultimate University Sports Trophy.
                        </p>
                    </div>
                    <a href="{{ route('results') }}#general-trophy" class="inline-flex items-center text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors">
                        View Full Standings &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($topMedalTally as $item)
                        <div class="bg-blue-950/90 border {{ $item['rank'] === 1 ? 'border-amber-400 bg-amber-950/40 ring-1 ring-amber-400/50' : 'border-blue-800/80' }} rounded-xl p-4 flex flex-col justify-between shadow-sm">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black uppercase tracking-wider {{ $item['rank'] === 1 ? 'text-amber-400' : 'text-blue-200' }}">
                                        @if($item['rank'] === 1) 🥇 Rank #1 @elseif($item['rank'] === 2) 🥈 Rank #2 @else 🥉 Rank #3 @endif
                                    </span>
                                    <span class="text-sm font-black text-amber-400 font-mono">{{ $item['points'] }} PTS</span>
                                </div>
                                <h4 class="font-bold text-white text-base">{{ $item['department']->name }}</h4>
                            </div>
                            <div class="mt-4 pt-3 border-t border-blue-900 flex items-center justify-between text-xs text-blue-100">
                                <span>🥇 {{ $item['gold'] }}</span>
                                <span>🥈 {{ $item['silver'] }}</span>
                                <span>🥉 {{ $item['bronze'] }}</span>
                                <span class="font-bold text-blue-200">{{ $item['total_medals'] }} Medals</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    @else
        <!-- NON-RUNNING STATE: "Next Gala Coming Soon" & Past Highlights -->
        <section class="space-y-10">

            <!-- "Next Gala Coming Soon" Banner Card (Bright Royal Blue Theme) -->
            <div class="bg-gradient-to-r from-blue-800 via-indigo-900 to-blue-900 border-2 border-amber-400 rounded-2xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 max-w-2xl space-y-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/40">
                        Stay Tuned
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        Next Gala Coming Soon
                    </h2>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Preparations for the upcoming Karakoram International University Sports Gala are actively underway! Check back soon for official announcements, participating department rosters, and complete match schedules.
                    </p>
                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ route('schedule') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-sm transition-colors">
                            Explore Schedule
                        </a>
                        <a href="{{ route('results') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-colors">
                            View Past Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- Highlights of the Latest Completed Edition -->
            @if ($completedEdition)
                <div class="space-y-6">
                    <div class="border-b border-slate-200 pb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.789l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-2xl font-black text-slate-900">
                                Highlights of {{ $completedEdition->title }}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Relive the championship records and memorable moments from the {{ $completedEdition->year }} gala.
                        </p>
                    </div>

                    <!-- Champions Grid -->
                    @if ($completedEdition->winners->count() > 0)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Tournament Champions</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($completedEdition->winners as $winner)
                                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex items-center justify-between">
                                        <div>
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                                {{ $winner->sport->name }} ({{ ucfirst($winner->sport->category) }})
                                            </span>
                                            <div class="font-extrabold text-slate-900 text-sm mt-0.5 flex items-center space-x-1.5">
                                                <span class="text-amber-500 font-black">🏆</span>
                                                <span>{{ $winner->winnerDepartment->name ?? 'Champion Dept' }}</span>
                                            </div>
                                            @if ($winner->runnerUpDepartment)
                                                <div class="text-[11px] text-slate-500 mt-1">
                                                    Runner-Up: <span class="font-medium text-slate-700">{{ $winner->runnerUpDepartment->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if ($winner->final_score)
                                            <span class="font-mono text-xs font-bold px-2 py-1 bg-slate-100 rounded text-slate-800">
                                                {{ $winner->final_score }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Photo Gallery Highlights -->
                    @if ($completedEdition->photos->count() > 0)
                        <div class="pt-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Photo Moments</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($completedEdition->photos->take(8) as $photo)
                                    <div class="group relative rounded-xl overflow-hidden aspect-video bg-slate-900 border border-slate-200 shadow-xs">
                                        <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Edition Highlight' }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        @if ($photo->caption)
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-2 text-white text-[11px] font-medium truncate">
                                                {{ $photo->caption }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </section>
    @endif

    <!-- ========================================================
         SECTION 1: ABOUT KIU ANNUAL SPORTS GALA (ID: ABOUT)
         ======================================================== -->
    <section id="about" class="pt-8 space-y-6 scroll-mt-24">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-sports font-black uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200 mb-1">
                    <span>Tournament Overview &amp; Legacy</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black font-sports tracking-wide uppercase text-slate-900">
                    About KIU Annual Sports Gala
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Uniting academic faculties, athletes, and students across Gilgit-Baltistan
                </p>
            </div>
            <a href="{{ route('about') }}" class="inline-flex items-center gap-1.5 text-xs font-sports font-bold tracking-wider uppercase text-blue-700 hover:text-blue-900 transition-colors">
                <span>Full About Details</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
            <!-- Left 2 Cols: Story & 4 Core Pillars -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">University Athletic Legacy</h3>
                        <p class="text-xs text-slate-500">Established by Directorate of Sports &amp; Physical Education</p>
                    </div>
                </div>

                <p class="text-sm text-slate-600 leading-relaxed">
                    The <strong>KIU Annual Sports Gala</strong> is organized each year by the Directorate of Sports at Karakoram International University. It stands as the flagship university-wide sporting celebration in the region, bringing together student-athletes and academic departments across Cricket, Football, Volleyball, Basketball, Badminton, Table Tennis, and Athletics.
                </p>

                <!-- 4 Pillars -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-blue-800 mb-1">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Athletic Excellence</span>
                        </div>
                        <p class="text-xs text-slate-600">Scouting talent to represent KIU at national collegiate championships.</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-blue-800 mb-1">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Campus Harmony</span>
                        </div>
                        <p class="text-xs text-slate-600">Fostering lifelong sportsmanship across all academic disciplines.</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-blue-800 mb-1">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Equal Opportunity</span>
                        </div>
                        <p class="text-xs text-slate-600">Structured Boys &amp; Girls sports divisions with official refereeing.</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-blue-800 mb-1">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span>Physical Wellness</span>
                        </div>
                        <p class="text-xs text-slate-600">Promoting active lifestyles and mental wellness through regular sports.</p>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Facilities & Grounds Highlights -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-950 border-2 border-amber-400 text-white rounded-2xl p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-sports font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                        Tournament Venues
                    </span>
                    <h3 class="text-xl font-black font-sports tracking-wide uppercase text-white">
                        Campus Sports Facilities
                    </h3>
                    <p class="text-xs text-blue-200 leading-relaxed">
                        Host to over 10 sports disciplines with international standard pitches, court floodlights, and grandstands.
                    </p>

                    <div class="space-y-3 pt-2 text-xs text-blue-100">
                        <div class="flex items-start gap-2.5">
                            <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></div>
                            <div>
                                <strong class="text-white block font-bold">Main Cricket Ground:</strong>
                                Turf pitch, commentary box &amp; ceremonies
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></div>
                            <div>
                                <strong class="text-white block font-bold">Football &amp; Futsal Arena:</strong>
                                Dedicated pitch for departmental matches
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 shrink-0"></div>
                            <div>
                                <strong class="text-white block font-bold">Indoor Gymnasium:</strong>
                                Badminton, Table Tennis &amp; Volleyball courts
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-blue-800">
                    <a href="{{ route('schedule') }}" class="w-full py-2.5 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-sports font-black text-xs uppercase tracking-wider transition-colors shadow-sm text-center block">
                        Browse Match Schedule &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================
         SECTION 2: CONTACT DIRECTORATE & HELPDESK (ID: CONTACT)
         ======================================================== -->
    <section id="contact" class="pt-10 space-y-6 scroll-mt-24">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-sports font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300 mb-1">
                    <span>Official Organizing Committee</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black font-sports tracking-wide uppercase text-slate-900">
                    Contact Us &amp; Directorate Helpdesk
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Reach out for fixture timings, athlete registration, venue access, or emergency inquiries
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 text-xs font-sports font-bold tracking-wider uppercase text-blue-700 hover:text-blue-900 transition-colors">
                <span>Dedicated Contact Page</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Office Location -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Office Location</h4>
                        <p class="text-xs text-slate-600 leading-relaxed mt-1">
                            Directorate of Sports &amp; Physical Education, Main Campus, University Road, Gilgit
                        </p>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4">
                    <span class="text-[11px] font-semibold text-blue-700">Gilgit-Baltistan, Pakistan</span>
                </div>
            </div>

            <!-- 2. Email Address -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Official Email</h4>
                        <p class="text-xs text-slate-600 leading-relaxed mt-1">
                            For team rosters, scheduling queries &amp; official correspondence
                        </p>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4">
                    <a href="mailto:sports@kiu.edu.pk" class="text-xs font-bold text-blue-700 hover:underline">
                        sports@kiu.edu.pk &rarr;
                    </a>
                </div>
            </div>

            <!-- 3. Phone Line -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Helpline / Phone</h4>
                        <p class="text-xs text-slate-600 leading-relaxed mt-1">
                            Direct telephone extension to the Directorate helpdesk
                        </p>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4">
                    <span class="text-xs font-mono font-bold text-slate-900">+92 (05811) 960010 &bull; Ext. 234</span>
                </div>
            </div>

            <!-- 4. Desk Hours & Action -->
            <div class="bg-gradient-to-br from-blue-950 via-indigo-950 to-blue-900 border-2 border-amber-400 rounded-2xl p-5 shadow-sm text-white flex flex-col justify-between">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-sports font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                        Helpdesk Open
                    </span>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Desk Operating Hours</h4>
                        <p class="text-xs text-blue-200 mt-1 leading-relaxed">
                            Mon &ndash; Fri: 08:30 AM &ndash; 04:30 PM<br>
                            Match days: 07:30 AM &ndash; End of Play
                        </p>
                    </div>
                </div>
                <div class="pt-4 border-t border-blue-800 mt-4">
                    <a href="{{ route('contact') }}" class="w-full py-2 px-3 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-sports font-black text-xs uppercase tracking-wider transition-colors text-center block">
                        Send Message &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

@push('scripts')
<script>
    (function() {
        const endpoint = "{{ route('api.live-scores.index') }}";

        function flashUpdate(el) {
            if (!el) return;
            el.classList.add('scale-125', 'text-amber-500', 'font-black');
            setTimeout(() => {
                el.classList.remove('scale-125', 'text-amber-500', 'font-black');
            }, 600);
        }

        async function pollHomeScores() {
            try {
                const response = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) return;
                const data = await response.json();
                if (!data.matches || !Array.isArray(data.matches)) return;

                data.matches.forEach(match => {
                    // Update Score 1
                    const s1El = document.querySelector(`[data-team1-score="${match.id}"]`);
                    if (s1El) {
                        const newT1 = match.team1_score !== null ? String(match.team1_score) : '-';
                        if (s1El.textContent.trim() !== newT1) {
                            s1El.textContent = newT1;
                            flashUpdate(s1El);
                        }
                    }

                    // Update Score 2
                    const s2El = document.querySelector(`[data-team2-score="${match.id}"]`);
                    if (s2El) {
                        const newT2 = match.team2_score !== null ? String(match.team2_score) : '-';
                        if (s2El.textContent.trim() !== newT2) {
                            s2El.textContent = newT2;
                            flashUpdate(s2El);
                        }
                    }

                    // Update Status Badge
                    const badgeContainer = document.querySelector(`[data-status-badge="${match.id}"]`);
                    if (badgeContainer) {
                        if (match.status === 'live') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    LIVE
                                </span>
                            `;
                        } else if (match.status === 'completed') {
                            badgeContainer.innerHTML = `
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-amber-400 border border-slate-700">
                                    FINAL
                                </span>
                            `;
                        }
                    }
                });
            } catch (err) {
                console.error("Home scores poll error:", err);
            }
        }

        // Poll every 8 seconds if there are running matches on the page
        if (document.querySelector('[data-team1-score]')) {
            setInterval(pollHomeScores, 8000);
        }
    })();
</script>
@endpush
@endsection

