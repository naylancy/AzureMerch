<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AzureMerch') - K-Pop POS & Inventory</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Enhanced Pastel Blue Ocean Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            950: '#060D19',
                            900: '#0A192F',
                            850: '#0F2342',
                            800: '#132C54',
                            700: '#1E3A8A',
                            600: '#1D4ED8',
                            500: '#2563EB',
                        },
                        cyan: {
                            300: '#7DD3FC',
                            400: '#38BDF8',
                            500: '#0EA5E9',
                            600: '#0284C7',
                        },
                        pastel: {
                            sky: '#BAE6FD',
                            ice: '#F0F9FF',
                            breeze: '#E0F2FE',
                            lavender: '#E0E7FF',
                            indigo: '#C7D2FE',
                            mint: '#CCFBF1',
                            teal: '#99F6E4',
                            rose: '#FCE7F3',
                            pink: '#FBCFE8',
                            peach: '#FEF3C7',
                            amber: '#FDE68A',
                            purple: '#EDE9FE',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    animation: {
                        'pastel-morph': 'pastelAura 12s ease infinite alternate',
                        'soft-float': 'softFloat 4s ease-in-out infinite',
                        'glow-pulse': 'glowPulse 2.5s ease-in-out infinite',
                        'shimmer-slide': 'shimmer 3s infinite linear',
                        'pop-badge': 'popBadge 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)',
                    },
                    keyframes: {
                        pastelAura: {
                            '0%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                            '100%': { backgroundPosition: '0% 50%' },
                        },
                        softFloat: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-6px)' },
                        },
                        glowPulse: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                        },
                        shimmer: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(200%)' },
                        },
                        popBadge: {
                            '0%': { transform: 'scale(0.85)' },
                            '100%': { transform: 'scale(1)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Modern Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F0F9FF;
        }
        ::-webkit-scrollbar-thumb {
            background: #BAE6FD;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #38BDF8;
        }

        /* Pastel Dream Aura Gradients */
        .pastel-ocean-aura {
            background: linear-gradient(125deg, #F0F9FF 0%, #E0F2FE 25%, #EDE9FE 50%, #E0E7FF 75%, #CCFBF1 100%);
            background-size: 300% 300%;
            animation: pastelAura 15s ease infinite alternate;
        }

        .pastel-header-gradient {
            background: linear-gradient(135deg, #0A192F 0%, #0F2342 45%, #132C54 80%, #0284C7 100%);
        }

        .pastel-glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(186, 230, 253, 0.65);
        }

        .pastel-card-hover {
            transition: all 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .pastel-card-hover:hover {
            transform: translateY(-4px);
            border-color: #38BDF8;
            box-shadow: 0 12px 28px -6px rgba(56, 189, 248, 0.3), 0 0 0 1px rgba(14, 165, 233, 0.15);
        }

        /* Animated Click Ripple for Soundboard Effect */
        .click-pop {
            animation: clickPop 0.22s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes clickPop {
            0% { transform: scale(1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Global Soundboard Audio Synthesizer (Web Audio API) -->
    <script>
        class AzureSoundboard {
            constructor() {
                this.ctx = null;
                this.muted = false;
                this.volume = 0.35;
            }

            initCtx() {
                if (!this.ctx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    this.ctx = new AudioContext();
                }
                if (this.ctx.state === 'suspended') {
                    this.ctx.resume();
                }
            }

            // 1. Playful K-Pop Chime when Product is Selected / Added
            playProductChime() {
                if (this.muted) return;
                this.initCtx();
                const now = this.ctx.currentTime;

                // Two bright pleasant harmonized sine notes (E5 -> B5 arpeggio)
                const playTone = (freq, delay, dur) => {
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now + delay);

                    gain.gain.setValueAtTime(0, now + delay);
                    gain.gain.linearRampToValueAtTime(this.volume, now + delay + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + delay + dur);

                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start(now + delay);
                    osc.stop(now + delay + dur);
                };

                playTone(659.25, 0, 0.22);       // E5
                playTone(987.77, 0.07, 0.35);     // B5
                playTone(1318.51, 0.14, 0.40);    // E6 sparkle
            }

            // 2. Barcode Optical Scanner Beep
            playBarcodeBeep() {
                if (this.muted) return;
                this.initCtx();
                const now = this.ctx.currentTime;
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(1760, now); // A6 bright beep

                gain.gain.setValueAtTime(this.volume * 0.9, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);

                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(now);
                osc.stop(now + 0.09);
            }

            // 3. Increment / Stepper Tick
            playTick(isUp = true) {
                if (this.muted) return;
                this.initCtx();
                const now = this.ctx.currentTime;
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(isUp ? 880 : 587.33, now);

                gain.gain.setValueAtTime(this.volume * 0.6, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);

                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(now);
                osc.stop(now + 0.06);
            }

            // 4. Celebratory Cash Register / Cha-ching on Checkout
            playChaChing() {
                if (this.muted) return;
                this.initCtx();
                const now = this.ctx.currentTime;

                // Coin impact chime chords
                const chords = [523.25, 659.25, 783.99, 1046.50, 1318.51]; // C major celebratory run
                chords.forEach((freq, idx) => {
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.06));

                    gain.gain.setValueAtTime(0, now + (idx * 0.06));
                    gain.gain.linearRampToValueAtTime(this.volume * 1.1, now + (idx * 0.06) + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (idx * 0.06) + 0.5);

                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start(now + (idx * 0.06));
                    osc.stop(now + (idx * 0.06) + 0.55);
                });
            }

            // 5. Warning / Out of Stock Buzz
            playError() {
                if (this.muted) return;
                this.initCtx();
                const now = this.ctx.currentTime;
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();

                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(160, now);
                osc.frequency.setValueAtTime(120, now + 0.1);

                gain.gain.setValueAtTime(this.volume * 0.7, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);

                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(now);
                osc.stop(now + 0.26);
            }
        }

        window.soundboard = new AzureSoundboard();
    </script>

    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased font-sans flex flex-col selection:bg-cyan-400 selection:text-white">

    <!-- Flash Notifications with Pastel Glassmorphism -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white px-5 py-3.5 rounded-2xl shadow-xl shadow-cyan-500/25 border border-white/20 animate-bounce-short">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span class="text-sm font-semibold tracking-wide">{{ session('success') }}</span>
            <button @click="show = false" class="text-white/80 hover:text-white ml-2">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-gradient-to-r from-rose-500 to-pink-500 text-white px-5 py-3.5 rounded-2xl shadow-xl shadow-rose-500/25 border border-white/20">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span class="text-sm font-semibold tracking-wide">{{ session('error') }}</span>
            <button @click="show = false" class="text-white/80 hover:text-white ml-2">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-gradient-to-r from-navy-900 to-navy-800 text-white px-5 py-3.5 rounded-2xl shadow-xl shadow-cyan-500/20 border border-cyan-400/30">
            <i data-lucide="info" class="w-5 h-5 text-cyan-300 flex-shrink-0"></i>
            <span class="text-sm font-semibold tracking-wide">{{ session('info') }}</span>
            <button @click="show = false" class="text-slate-400 hover:text-white ml-2">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
