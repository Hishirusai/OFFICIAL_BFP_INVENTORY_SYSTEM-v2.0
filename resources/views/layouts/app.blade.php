<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BFP Inventory System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- REMOVED: Dark Theme <style> block --}}
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

                {{-- ================= STATIONS & UNITS START ================= --}}
                @php
                    $isStationsActive = request()->routeIs('stations.*');
                    $isStationShow = request()->routeIs('stations.show');
                @endphp

                {{-- 1. Main Parent Link --}}
                <a href="{{ route('stations.index') }}" 
                class="relative flex items-center px-4 py-3 transition-all duration-200
                {{ $isStationsActive ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ $isStationsActive ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Stations & Units</span>
                </a>

                {{-- 2. Sub-Navigation: Active Station Badge --}}
                @if($isStationShow && isset($station))
                    @php
                        // LOGIC: Replicate the index.blade.php logic
                        $isMain = $station->id == 1;
                        
                        // Calculate "S1", "S2" based on how many stations are before this one
                        $stationIndex = \App\Models\Station::where('id', '<', $station->id)->count(); 
                        
                        // Define Styles exactly like index.blade.php
                        $badgeBg = $isMain 
                            ? 'bg-gradient-to-br from-red-500 to-red-700 text-white' 
                            : 'bg-gradient-to-br from-orange-400 to-orange-600 text-white';
                            
                        $badgeText = $isMain ? 'MS' : 'S' . $stationIndex;
                    @endphp

                    <div class="relative flex items-center px-4 py-2 bg-red-950/40 border-l-4 border-red-900 transition-all duration-300 group/sub">
                        
                        {{-- BADGE CONTAINER --}}
                        <div class="min-w-[3rem] flex justify-center">
                            {{-- The "Code" Badge --}}
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-extrabold shadow-md {{ $badgeBg }}">
                                {{ $badgeText }}
                            </div>
                        </div>

                        {{-- STATION NAME --}}
                        <div class="ml-2 overflow-hidden opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="block text-sm font-medium text-white truncate w-32" title="{{ $station->name }}">
                                {{ $station->name }}
                            </span>
                        </div>
                    </div>
                @endif
                {{-- ================= STATIONS & UNITS END ================= --}}

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
                
                {{-- REMOVED: Dark Theme Toggle Button was here --}}

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

        {{-- CHANGED: Background to bg-stone-100 (Dirty White/Warm Gray) --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-stone-100 p-8 transition-colors duration-300">
            @yield('content')
        </main>

    </div>

    @include('partials.flash-modal')

</body>
</html>