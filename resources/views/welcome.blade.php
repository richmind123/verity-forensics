<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600&display=swap" rel="stylesheet">
    <style>
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }

        .grid-veil {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 44px 44px;
        }

        .reveal,
        .reveal-left,
        .reveal-right {
            opacity: 0;
            transition: opacity .8s ease, transform .8s ease;
        }

        .reveal {
            transform: translateY(24px);
        }

        .reveal-left {
            transform: translateX(-28px);
        }

        .reveal-right {
            transform: translateX(28px);
        }

        .reveal.in,
        .reveal-left.in,
        .reveal-right.in {
            opacity: 1;
            transform: none;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .35;
            }
        }

        .pulse-dot {
            animation: pulse-dot 1.6s ease-in-out infinite;
        }

        @keyframes scan {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(420%);
            }
        }

        .scanbeam {
            animation: scan 3.5s ease-in-out infinite;
        }

        @keyframes bounce-y {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(6px);
            }
        }

        .bounce {
            animation: bounce-y 1.8s ease-in-out infinite;
        }

        .nav-link {
            position: relative;
        }

        .nav-link.active {
            color: #ffffff;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -6px;
            height: 2px;
            background: #2FE3B4;
            transform-origin: left;
            animation: nav-flash .45s ease forwards;
        }

        @keyframes nav-flash {
            from {
                transform: scaleX(0);
                opacity: 0;
                box-shadow: 0 0 10px 1px rgba(47, 227, 180, .7);
            }

            to {
                transform: scaleX(1);
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(47, 227, 180, 0);
            }
        }
    </style>
</head>

<body class="min-h-screen bg-verity-bg text-white antialiased overflow-x-hidden">

    <nav class="sticky top-0 z-30 backdrop-blur bg-verity-bg/80 border-b border-verity-border">
        <div class="max-w-6xl mx-auto px-6 lg:px-10 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <x-app-logo-icon class="w-5 h-5 text-accent" />
                Verity
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm text-verity-muted">
                <a href="#ledger" data-nav="ledger" class="nav-link hover:text-white transition-colors">Live ledger</a>
                <a href="#modules" data-nav="modules" class="nav-link hover:text-white transition-colors">Modules</a>
                <a href="#cases" data-nav="cases" class="nav-link hover:text-white transition-colors">Case studies</a>
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-block px-4 py-1.5 bg-transparent text-white border border-verity-border hover:border-accent/50 rounded-sm transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block px-4 py-1.5 bg-transparent text-verity-muted hover:text-white transition-colors">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block px-4 py-1.5 bg-transparent text-white border border-verity-border hover:border-accent/50 rounded-sm transition-colors">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </nav>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 grid-veil pointer-events-none"></div>
            <div class="absolute -top-40 -left-20 w-[700px] h-[700px] rounded-full bg-accent/5 blur-3xl pointer-events-none"></div>

            <div class="hidden lg:block absolute top-28 right-10 xl:right-24 w-72">
                <div class="relative rotate-[4deg] bg-verity-panel border border-verity-border rounded-lg p-5">
                    <div class="absolute -top-2.5 left-6 w-5 h-5 rounded-full bg-verity-bg border border-verity-border"></div>
                    <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none">
                        <div class="scanbeam absolute left-0 right-0 h-14 bg-gradient-to-b from-transparent via-accent/10 to-transparent"></div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="font-mono text-[10px] tracking-wide text-amber-400 uppercase">Exhibit A-047</span>
                        <x-severity-badge severity="fatal" />
                    </div>
                    <div class="text-sm mb-1">File Download</div>
                    <div class="font-mono text-xs text-verity-muted">203.0.113.55 · 08:39:15</div>
                </div>
                <div class="relative -rotate-[3deg] -mt-6 ml-10 bg-verity-panel border border-verity-border rounded-lg p-4 opacity-90">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono text-[10px] tracking-wide text-verity-muted uppercase">Exhibit A-032</span>
                        <x-severity-badge severity="warning" />
                    </div>
                    <div class="font-mono text-xs text-verity-muted">185.220.101.47</div>
                </div>
            </div>

            <div class="relative max-w-6xl mx-auto px-6 lg:px-10 pt-24 pb-20">
                <div class="reveal inline-flex items-center gap-2 px-3 py-1 text-xs uppercase tracking-wide border rounded-full text-accent border-accent/30 bg-accent/10 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent pulse-dot"></span>
                    Digital forensics platform
                </div>

                <h1 class="reveal font-display text-[52px] sm:text-7xl lg:text-8xl leading-[0.95] font-medium mb-8 max-w-4xl">
                    Trace evidence.<br>
                    <span class="text-accent">Score</span> risk.<br>
                    See the truth.
                </h1>

                <p class="reveal text-xl text-verity-muted max-w-xl mb-10 leading-relaxed">
                    Upload audit logs, correlate events by IP, and surface the highest-risk activity in your evidence — all in one place.
                </p>

                @if (Route::has('login'))
                    <div class="reveal flex flex-wrap gap-4 mb-16">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-7 py-3.5 rounded-sm bg-accent text-accent-foreground font-medium hover:brightness-110 transition">
                                Go to dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-sm bg-accent text-accent-foreground font-medium hover:brightness-110 transition">
                                    Get started
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="px-7 py-3.5 rounded-sm border border-verity-border hover:border-accent/50 transition-colors">
                                Log in
                            </a>
                        @endauth
                    </div>
                @endif

                <div class="reveal flex flex-wrap gap-10 font-mono text-sm text-verity-muted border-t border-verity-border pt-8 max-w-2xl">
                    <div><span class="text-white text-base">2,537</span> events indexed</div>
                    <div><span class="text-coral text-base">6</span> fatal flags</div>
                    <div><span class="text-white text-base">&lt;4min</span> avg. detection</div>
                </div>
            </div>

            <div class="reveal flex justify-center pb-10">
                <svg class="bounce text-verity-muted" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M12 4v16M6 14l6 6 6-6" />
                </svg>
            </div>
        </section>

        <section id="ledger" class="border-t border-verity-border">
            <div class="max-w-6xl mx-auto px-6 lg:px-10 py-24">
                <div class="reveal flex flex-wrap items-end justify-between gap-4 mb-10">
                    <div>
                        <div class="font-mono text-xs text-accent tracking-widest uppercase mb-3">Real-time</div>
                        <h2 class="font-display text-3xl sm:text-4xl">Every event, logged and scored</h2>
                    </div>
                    <span class="font-mono text-xs px-3 py-1.5 rounded bg-verity-panel border border-verity-border text-white">2,537 events</span>
                </div>

                <div class="reveal border border-verity-border rounded-lg overflow-hidden" id="ledger-list">
                    <div class="flex items-center gap-6 px-5 py-3.5 font-mono text-xs border-b border-verity-border hover:bg-white/[0.02] transition-colors">
                        <span class="text-verity-muted w-16">08:39:15</span>
                        <span class="text-white flex-1">File Download</span>
                        <span class="text-verity-muted w-32">203.0.113.55</span>
                        <x-severity-badge severity="fatal" />
                    </div>
                    <div class="flex items-center gap-6 px-5 py-3.5 font-mono text-xs border-b border-verity-border hover:bg-white/[0.02] transition-colors">
                        <span class="text-verity-muted w-16">08:37:00</span>
                        <span class="text-white flex-1">Login Failed</span>
                        <span class="text-verity-muted w-32">203.0.113.55</span>
                        <x-severity-badge severity="critical" />
                    </div>
                    <div class="flex items-center gap-6 px-5 py-3.5 font-mono text-xs border-b border-verity-border hover:bg-white/[0.02] transition-colors">
                        <span class="text-verity-muted w-16">08:27:00</span>
                        <span class="text-white flex-1">Login Failed</span>
                        <span class="text-verity-muted w-32">66.114.201.9</span>
                        <x-severity-badge severity="warning" />
                    </div>
                    <div class="flex items-center gap-6 px-5 py-3.5 font-mono text-xs hover:bg-white/[0.02] transition-colors">
                        <span class="text-verity-muted w-16">08:24:10</span>
                        <span class="text-white flex-1">Session start</span>
                        <span class="text-verity-muted w-32">10.0.4.12</span>
                        <span class="px-2 py-0.5 rounded bg-accent/15 text-accent uppercase text-xs">Ok</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="modules" class="border-t border-verity-border">
            <div class="max-w-6xl mx-auto px-6 lg:px-10 py-24">
                <div class="reveal mb-20 max-w-xl">
                    <div class="font-mono text-xs text-accent tracking-widest uppercase mb-3">How it works</div>
                    <h2 class="font-display text-3xl sm:text-4xl">Four modules, one investigation</h2>
                </div>

                <div class="grid lg:grid-cols-2 gap-12 items-center mb-24">
                    <div class="reveal-left">
                        <div class="font-mono text-xs text-accent mb-3">01 — INGEST</div>
                        <h3 class="font-display text-2xl mb-3">Upload raw evidence</h3>
                        <p class="text-verity-muted leading-relaxed">Drop in a CSV of server logs. A queued job parses, hashes, and stores every row without blocking the UI — and rejects an exact file you've already processed.</p>
                    </div>
                    <div class="reveal-right bg-verity-panel border border-dashed border-verity-border rounded-lg p-8 text-center">
                        <svg class="mx-auto mb-4 text-accent" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 3v10m0 0l-4-4m4 4l4-4" />
                            <path d="M4 15v3a2 2 0 002 2h12a2 2 0 002-2v-3" />
                        </svg>
                        <div class="font-mono text-sm mb-3">Alpha_Corp_Raw_Evidence.csv</div>
                        <div class="h-1.5 bg-verity-border rounded-full overflow-hidden">
                            <div class="h-full bg-accent" style="width: 72%"></div>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-2 gap-12 items-center mb-24">
                    <div class="reveal-left lg:order-2">
                        <div class="font-mono text-xs text-accent mb-3">02 — TIMELINE</div>
                        <h3 class="font-display text-2xl mb-3">See activity take shape</h3>
                        <p class="text-verity-muted leading-relaxed">Events aggregate into a stacked, color-coded chart by day and severity, so a spike in failed logins or downloads is visible at a glance.</p>
                    </div>
                    <div class="reveal-right lg:order-1 bg-verity-panel border border-verity-border rounded-lg p-6">
                        <div class="flex items-end gap-1.5 h-32">
                            <div class="flex-1 bg-accent/40 rounded-t" style="height:35%"></div>
                            <div class="flex-1 bg-accent/40 rounded-t" style="height:55%"></div>
                            <div class="flex-1 bg-accent/40 rounded-t" style="height:40%"></div>
                            <div class="flex-1 bg-coral/70 rounded-t" style="height:90%"></div>
                            <div class="flex-1 bg-accent/40 rounded-t" style="height:48%"></div>
                            <div class="flex-1 bg-accent/40 rounded-t" style="height:30%"></div>
                            <div class="flex-1 bg-accent/40 rounded-t" style="height:60%"></div>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-2 gap-12 items-center mb-24">
                    <div class="reveal-left">
                        <div class="font-mono text-xs text-accent mb-3">03 — SEARCH</div>
                        <h3 class="font-display text-2xl mb-3">Filter down to one actor</h3>
                        <p class="text-verity-muted leading-relaxed">Narrow by IP, severity, or date range with a live-updating table — 2,500+ rows, paginated, with a dirty-state indicator so you always know what's applied.</p>
                    </div>
                    <div class="reveal-right bg-verity-panel border border-verity-border rounded-lg p-5">
                        <div class="flex gap-2 mb-4">
                            <span class="px-2.5 py-1 rounded bg-verity-bg border border-verity-border font-mono text-xs text-white">ip: 203.0.113.55</span>
                            <span class="px-2.5 py-1 rounded bg-verity-bg border border-verity-border font-mono text-xs text-verity-muted">severity: fatal</span>
                        </div>
                        <div class="space-y-2">
                            <div class="h-8 bg-verity-bg border border-verity-border rounded"></div>
                            <div class="h-8 bg-verity-bg border border-verity-border rounded"></div>
                            <div class="h-8 bg-verity-bg border border-verity-border rounded"></div>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="reveal-left lg:order-2">
                        <div class="font-mono text-xs text-accent mb-3">04 — INSPECT</div>
                        <h3 class="font-display text-2xl mb-3">Confirm what actually happened</h3>
                        <p class="text-verity-muted leading-relaxed">Click any row to open its full JSON payload — user agent, file name, session — in a slide-over panel. No guessing.</p>
                    </div>
                    <div class="reveal-right lg:order-1 bg-verity-panel border border-verity-border rounded-lg p-5 font-mono text-xs leading-relaxed">
                        <div><span class="text-verity-muted">"user_agent"</span>: <span class="text-coral">"python-requests/2.31.0"</span></div>
                        <div><span class="text-verity-muted">"file"</span>: <span class="text-accent">"customer_credit_cards_150k.csv"</span></div>
                        <div><span class="text-verity-muted">"status"</span>: <span class="text-white">200</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="cases" class="border-t border-verity-border bg-verity-panel/30">
            <div class="max-w-6xl mx-auto px-6 lg:px-10 py-24">
                <div class="reveal mb-16 max-w-xl">
                    <div class="font-mono text-xs text-accent tracking-widest uppercase mb-3">Advanced detection</div>
                    <h2 class="font-display text-3xl sm:text-4xl">Two ways Verity catches what humans miss</h2>
                </div>
                <div class="grid lg:grid-cols-2 gap-8">
                    <div class="reveal-left bg-verity-bg border border-verity-border rounded-lg p-8">
                        <div class="font-mono text-xs text-coral tracking-widest uppercase mb-4">Case 01 — Brute force</div>
                        <div class="font-display text-3xl mb-2">185.220.101.47</div>
                        <p class="text-verity-muted mb-6 leading-relaxed">Six failed logins inside a five-minute window — flagged automatically, no manual review needed.</p>
                        <div class="font-mono text-xs text-verity-muted border-t border-verity-border pt-4">Time-bucketed · threshold-based detection</div>
                    </div>
                    <div class="reveal-right bg-verity-bg border border-verity-border rounded-lg p-8">
                        <div class="font-mono text-xs text-accent tracking-widest uppercase mb-4">Case 02 — Risk scoring</div>
                        <div class="flex items-center gap-4 mb-4">
                            <span class="font-display text-3xl text-coral w-14">100</span>
                            <div class="flex-1 h-2 bg-verity-border rounded-full overflow-hidden">
                                <div class="bar h-full bg-coral rounded-full" style="width:0%" data-target="100"></div>
                            </div>
                            <span class="font-mono text-xs text-verity-muted">attacker</span>
                        </div>
                        <div class="flex items-center gap-4 mb-6">
                            <span class="font-display text-3xl text-accent w-14">20</span>
                            <div class="flex-1 h-2 bg-verity-border rounded-full overflow-hidden">
                                <div class="bar h-full bg-accent rounded-full" style="width:0%" data-target="20"></div>
                            </div>
                            <span class="font-mono text-xs text-verity-muted">legit IP</span>
                        </div>
                        <p class="text-verity-muted text-sm leading-relaxed">Weighted signals — user agent, download volume, severity — separate real threats from noisy false positives.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-verity-border">
            <div class="max-w-6xl mx-auto px-6 lg:px-10 py-20 grid sm:grid-cols-4 gap-8">
                <div class="reveal">
                    <div class="font-display text-4xl mb-1"><span data-count="2537">0</span></div>
                    <div class="font-mono text-xs text-verity-muted uppercase tracking-wide">Events indexed</div>
                </div>
                <div class="reveal">
                    <div class="font-display text-4xl mb-1 text-coral"><span data-count="6">0</span></div>
                    <div class="font-mono text-xs text-verity-muted uppercase tracking-wide">Threats flagged</div>
                </div>
                <div class="reveal">
                    <div class="font-display text-4xl mb-1"><span data-count="100">0</span></div>
                    <div class="font-mono text-xs text-verity-muted uppercase tracking-wide">Peak risk score</div>
                </div>
                <div class="reveal">
                    <div class="font-display text-4xl mb-1 text-accent"><span data-count="33">0</span></div>
                    <div class="font-mono text-xs text-verity-muted uppercase tracking-wide">Tests passing</div>
                </div>
            </div>
        </section>

        <section class="border-t border-verity-border">
            <div class="max-w-6xl mx-auto px-6 lg:px-10 py-24">
                <div class="reveal mb-14 max-w-xl">
                    <div class="font-mono text-xs text-accent tracking-widest uppercase mb-3">Built for chain of custody</div>
                    <h2 class="font-display text-3xl sm:text-4xl">Evidence you can stand behind</h2>
                </div>
                <div class="grid sm:grid-cols-3 gap-10">
                    <div class="reveal">
                        <svg class="text-accent mb-4" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <rect x="4" y="10" width="16" height="10" rx="1.5" />
                            <path d="M8 10V7a4 4 0 018 0v3" />
                        </svg>
                        <h3 class="font-medium mb-2">Stays on your infrastructure</h3>
                        <p class="text-sm text-verity-muted leading-relaxed">Logs are processed and stored where you host Verity — nothing is sent to a third party.</p>
                    </div>
                    <div class="reveal">
                        <svg class="text-accent mb-4" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 3" />
                        </svg>
                        <h3 class="font-medium mb-2">Every event hash-verified</h3>
                        <p class="text-sm text-verity-muted leading-relaxed">Uploaded files are hashed on ingest, so tampering after the fact is immediately detectable.</p>
                    </div>
                    <div class="reveal">
                        <svg class="text-accent mb-4" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M9 4h6a2 2 0 012 2v14l-5-3-5 3V6a2 2 0 012-2z" />
                        </svg>
                        <h3 class="font-medium mb-2">Immutable audit trail</h3>
                        <p class="text-sm text-verity-muted leading-relaxed">Every upload, search, and inspection is itself logged — a record of who looked at what, and when.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-verity-border">
            <div class="reveal max-w-6xl mx-auto px-6 lg:px-10 py-28">
                <h2 class="font-display text-4xl sm:text-5xl max-w-2xl mb-6">Your logs already hold the truth.</h2>
                <p class="text-verity-muted mb-8 text-lg">Verity just makes it visible.</p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block px-7 py-3.5 rounded-sm bg-accent text-accent-foreground font-medium hover:brightness-110 transition">
                        Get started free
                    </a>
                @endif
            </div>
        </section>
    </main>

    <footer class="border-t border-verity-border py-10">
        <div class="max-w-6xl mx-auto px-6 lg:px-10 flex flex-wrap items-center justify-between gap-4 font-mono text-xs text-verity-muted">
            <span>VERITY</span>
            <span>Internal forensics tooling</span>
            <span>© 2026</span>
        </div>
    </footer>

    <script>
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: 0.15 });
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => io.observe(el));

        document.querySelectorAll('[data-count]').forEach(el => {
            const target = parseInt(el.dataset.count, 10);
            const io2 = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        const dur = 1200, t0 = performance.now();
                        function tick(t) {
                            const p = Math.min((t - t0) / dur, 1);
                            el.textContent = Math.floor(p * target).toLocaleString();
                            if (p < 1) requestAnimationFrame(tick); else el.textContent = target.toLocaleString();
                        }
                        requestAnimationFrame(tick);
                        io2.unobserve(el);
                    }
                });
            }, { threshold: 0.4 });
            io2.observe(el);
        });

        document.querySelectorAll('.bar').forEach(bar => {
            const io3 = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { bar.style.width = bar.dataset.target + '%'; io3.unobserve(bar); } });
            }, { threshold: 0.4 });
            io3.observe(bar);
        });

        const ledgerList = document.getElementById('ledger-list');
        const sampleEvents = [
            { ip: '198.51.100.23', ev: 'Session start', sev: 'ok' },
            { ip: '203.0.113.55', ev: 'File Download', sev: 'fatal' },
            { ip: '192.0.2.14', ev: 'Login attempt', sev: 'warn' },
            { ip: '10.0.4.12', ev: 'CSV Upload', sev: 'ok' },
        ];
        function addRow() {
            const s = sampleEvents[Math.floor(Math.random() * sampleEvents.length)];
            const time = new Date().toTimeString().slice(0, 8);
            const sevClass = s.sev === 'fatal' ? 'bg-coral/15 text-coral' : s.sev === 'warn' ? 'bg-amber-400/15 text-amber-400' : 'bg-accent/15 text-accent';
            const row = document.createElement('div');
            row.className = 'flex items-center gap-6 px-5 py-3.5 font-mono text-xs border-b border-verity-border hover:bg-white/[0.02] transition-colors duration-500';
            row.style.opacity = '0';
            row.style.transform = 'translateY(-8px)';
            row.style.transition = 'opacity .5s ease, transform .5s ease';
            row.innerHTML = `<span class="text-verity-muted w-16">${time}</span><span class="text-white flex-1">${s.ev}</span><span class="text-verity-muted w-32">${s.ip}</span><span class="px-2 py-0.5 rounded ${sevClass} uppercase">${s.sev}</span>`;
            ledgerList.prepend(row);
            requestAnimationFrame(() => { row.style.opacity = '1'; row.style.transform = 'none'; });
            if (ledgerList.children.length > 6) ledgerList.lastElementChild.remove();
        }
        setInterval(addRow, 4000);

        const navLinks = document.querySelectorAll('.nav-link');
        const navSections = ['ledger', 'modules', 'cases']
            .map(id => document.getElementById(id))
            .filter(Boolean);

        function setActiveNav(id) {
            navLinks.forEach(link => link.classList.toggle('active', link.dataset.nav === id));
        }

        const spy = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) setActiveNav(entry.target.id);
            });
        }, { rootMargin: '-45% 0px -50% 0px', threshold: 0 });

        navSections.forEach(section => spy.observe(section));
    </script>

</body>

</html>