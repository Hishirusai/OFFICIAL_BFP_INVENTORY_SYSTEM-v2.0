<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BFP Inventory System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-20 hover:w-64 bg-red-900 text-white flex flex-col flex-shrink-0 shadow-2xl transition-all duration-300 ease-in-out group z-50 overflow-hidden">
            
            <div class="flex flex-col items-center justify-center py-6 border-b border-red-800 bg-red-950 space-y-3 px-2 transition-all duration-300">
                <img src="{{ asset('images/district-logo.png') }}"
                     alt="BFP Logo"
                     class="w-10 h-10 group-hover:w-20 group-hover:h-20 object-contain bg-white rounded-full p-1 shadow-lg transition-all duration-300">

                <div class="opacity-0 h-0 group-hover:opacity-100 group-hover:h-auto transition-all duration-300 delay-100 overflow-hidden whitespace-nowrap">
                    <span class="text-center block text-sm font-extrabold tracking-widest text-white drop-shadow-sm leading-tight">
                        INVENTORY<br>MANAGEMENT<br>SYSTEM
                    </span>
                </div>
            </div>
            
            <nav class="flex-1 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
    
                <a href="{{ route('dashboard') }}" 
                class="relative flex items-center px-4 py-3 transition-all duration-200
                {{ request()->routeIs('dashboard') 
                    ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' 
                    : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ request()->routeIs('dashboard') ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 {{ request()->routeIs('dashboard') ? 'font-bold' : '' }}">
                        Dashboard
                    </span>
                </a>

                @php
                    $isStationsActive = request()->routeIs('stations.*');
                    $currentStation = request()->route('station'); 
                    
                    if($currentStation && !is_object($currentStation)) {
                        $currentStation = \App\Models\Station::find($currentStation);
                    }
                @endphp

                <a href="{{ route('stations.index') }}" 
                   class="relative flex items-center px-4 py-3 transition-all duration-200
                   {{ $isStationsActive 
                       ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' 
                       : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ $isStationsActive ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 {{ $isStationsActive ? 'font-bold' : '' }}">
                        Stations & Units
                    </span>
                </a>

                @if(request()->routeIs('stations.show') && $currentStation)
                    @php
                        $isMain = $currentStation->id == 1;
                        $iconBg = $isMain 
                            ? 'bg-gradient-to-br from-red-500 to-red-700' 
                            : 'bg-gradient-to-br from-orange-400 to-orange-600';
                        
                        $stations = \App\Models\Station::all();
                        $index = $stations->search(function($s) use ($currentStation) {
                            return $s->id == $currentStation->id;
                        });
                        $label = $isMain ? 'MS' : 'S' . ($index); 
                    @endphp

                    <div class="bg-red-950/30">
                        <a href="{{ route('stations.show', $currentStation->id) }}" 
                           class="relative flex items-center px-4 py-2 transition-all duration-200 text-yellow-300 font-bold bg-red-800/50 border-l-4 border-transparent">
                            
                            <div class="min-w-[3rem] flex justify-center">
                                <div class="h-8 w-8 rounded-lg flex-shrink-0 flex items-center justify-center font-extrabold text-xs shadow-md text-white border border-yellow-300 {{ $iconBg }}">
                                    {{ $label }}
                                </div>
                            </div>

                            <span class="ml-2 text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 truncate max-w-[140px]">
                                {{ $currentStation->name }}
                            </span>
                        </a>
                    </div>
                @endif
                <a href="{{ route('reports.index') }}" 
                   class="relative flex items-center px-4 py-3 transition-all duration-200
                   {{ request()->routeIs('reports.*') 
                        ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' 
                        : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ request()->routeIs('reports.*') ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 {{ request()->routeIs('reports.*') ? 'font-bold' : '' }}">
                        Reports & Logs
                    </span>
                </a>

                <a href="{{ route('users.index') }}" 
                   class="relative flex items-center px-4 py-3 transition-all duration-200
                   {{ request()->routeIs('users.*') 
                        ? 'bg-red-700 text-white border-l-4 border-yellow-300 shadow-lg' 
                        : 'hover:bg-red-800 text-red-100 hover:text-white' }}">
                    <div class="min-w-[3rem] flex justify-center">
                        <svg class="w-6 h-6 {{ request()->routeIs('users.*') ? 'text-yellow-300' : 'text-red-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="ml-2 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 {{ request()->routeIs('users.*') ? 'font-bold' : '' }}">
                        User Management
                    </span>
                </a>
            </nav>

            <div class="border-t border-red-800 p-4 bg-red-950 overflow-hidden">
                <div class="flex items-center mb-4 transition-all">
                    <div class="w-10 h-10 rounded-full bg-red-800 flex-shrink-0 flex items-center justify-center text-sm font-bold text-white border border-red-400 mx-auto group-hover:mx-0">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="ml-3 overflow-hidden opacity-0 w-0 group-hover:w-auto group-hover:opacity-100 transition-all duration-300 whitespace-nowrap">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-red-200 truncate">{{ Auth::user()->email ?? 'Email' }}</p>
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

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-8">
            @yield('content')
        </main>

    </div>
</body>
</html>