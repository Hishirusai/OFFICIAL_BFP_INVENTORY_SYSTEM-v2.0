<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BFP Inventory System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* --- DARK THEME OVERRIDES --- */
        
        /* 1. Darken the Main Background */
        body.dark-theme main {
            background-color: #0f172a !important; /* slate-900 */
        }

        /* 2. Lighten Text that sits directly on the background (Titles, descriptions) */
        body.dark-theme main > div > div > h1,
        body.dark-theme main > div > div > h2,
        body.dark-theme main .text-gray-900 {
            color: #f8fafc !important; /* slate-50 */
        }
        
        body.dark-theme main .text-gray-600,
        body.dark-theme main .text-gray-500 {
            color: #94a3b8 !important; /* slate-400 */
        }

        /* 3. PROTECT CONTENT: Keep .bg-white elements (Cards, Tables) Light */
        body.dark-theme .bg-white {
            background-color: #ffffff !important;
            color: #1f2937 !important; /* gray-800 */
            border-color: #e5e7eb !important;
        }

        /* Ensure text inside white cards stays dark */
        body.dark-theme .bg-white h1,
        body.dark-theme .bg-white h2,
        body.dark-theme .bg-white p,
        body.dark-theme .bg-white span,
        body.dark-theme .bg-white td,
        body.dark-theme .bg-white th,
        body.dark-theme .bg-white .text-gray-600 {
            color: #374151 !important; /* gray-700 */
        }

        /* Protect Buttons so they don't lose their text color */
        body.dark-theme button, 
        body.dark-theme .btn,
        body.dark-theme a[class*="bg-"] {
            color: inherit; 
        }
    </style>
</head>
<body id="app-body" class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-20 hover:w-64 bg-red-900 text-white flex flex-col flex-shrink-0 shadow-2xl transition-all duration-300 ease-in-out group z-50 overflow-hidden">
            
            <div class="flex flex-col items-center justify-center py-6 border-b border-red-800 bg-red-950 space-y-3 px-2 transition-all duration-300">
                <img src="{{ asset('images/district-logo.png') }}"
                     alt="BFP Logo"
                     class="w-10 h-10 group-hover:w-20 group-hover:h-20 object-contain bg-white rounded-full p-1 shadow-lg transition-all duration-300">

                <div class="opacity-0 h-0 group-hover:opacity-100 group-hover:h-auto transition-all duration-300 delay-100 overflow-hidden whitespace-nowrap">
                    <span class="text-center block text-sm font-extrabold tracking-widest text-white drop-shadow-sm leading-tight">
                        INVENTORY<br>MANAGEMENT
                    </span>
                </div>
            </div>
            
            <nav class="flex-1 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
    
                <a href="{{ route('dashboard') }}" 
                class="relative flex items-center px-4 py-3 transition-all duration-200
                {{ request()->routeIs('dashboard') ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ request()->routeIs('dashboard') ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
                </a>

                @php
                    $isStationsActive = request()->routeIs('stations.*');
                @endphp
                <a href="{{ route('stations.index') }}" 
                   class="relative flex items-center px-4 py-3 transition-all duration-200
                   {{ $isStationsActive ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ $isStationsActive ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Stations & Units</span>
                </a>

                <a href="{{ route('reports.index') }}" 
                   class="relative flex items-center px-4 py-3 transition-all duration-200
                   {{ request()->routeIs('reports.*') ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ request()->routeIs('reports.*') ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Reports & Logs</span>
                </a>

                <a href="{{ route('users.index') }}" 
                   class="relative flex items-center px-4 py-3 transition-all duration-200
                   {{ request()->routeIs('users.*') ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ request()->routeIs('users.*') ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">User Management</span>
                </a>
            </nav>

            <div class="border-t border-red-800 p-4 bg-red-950 overflow-hidden">
                
                <button onclick="toggleTheme()" class="w-full flex items-center justify-center px-2 py-2 mb-3 bg-red-900/50 hover:bg-red-800 text-yellow-300 font-bold rounded transition-colors shadow-sm border border-red-800/50 group/theme">
                    <svg id="icon-sun" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg id="icon-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    
                    <span id="theme-text" class="ml-2 text-xs opacity-0 w-0 group-hover:w-auto group-hover:opacity-100 transition-all duration-300 whitespace-nowrap">Dark BG</span>
                </button>

                <div class="flex items-center mb-4 transition-all">
                    <div class="w-10 h-10 rounded-full bg-red-800 flex-shrink-0 flex items-center justify-center text-sm font-bold text-white border border-red-400 mx-auto group-hover:mx-0">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="ml-3 overflow-hidden opacity-0 w-0 group-hover:w-auto group-hover:opacity-100 transition-all duration-300 whitespace-nowrap">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-2 py-2 bg-red-800 hover:bg-red-700 text-white font-bold rounded transition-colors uppercase tracking-wider shadow-sm border border-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span class="ml-2 text-xs opacity-0 w-0 group-hover:w-auto group-hover:opacity-100 transition-all duration-300 whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-8 transition-colors duration-300">
            @yield('content')
        </main>

    </div>

    <script>
        function toggleTheme() {
            const body = document.getElementById('app-body');
            body.classList.toggle('dark-theme');
            
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('bfp_bg_theme', isDark ? 'dark' : 'light');

            updateIcons(isDark);
        }

        function updateIcons(isDark) {
            const sunIcon = document.getElementById('icon-sun');
            const moonIcon = document.getElementById('icon-moon');
            const themeText = document.getElementById('theme-text');

            if (isDark) {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                themeText.textContent = "Light BG";
            } else {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                themeText.textContent = "Dark BG";
            }
        }

        // Initialize theme on load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('bfp_bg_theme');
            if (savedTheme === 'dark') {
                document.getElementById('app-body').classList.add('dark-theme');
                updateIcons(true);
            }
        });
    </script>
</body>
</html>