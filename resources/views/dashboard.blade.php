@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-end mb-10">
        
        <div class="mb-6 md:mb-0">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h1>
            <p class="text-lg text-gray-600 mt-1">Analytics for BFP inventory status.</p>
            
            @if(session('login_success'))
                <div class="mt-4 flex items-center animate-pulse">
                    <span class="text-2xl text-gray-800">
                        Welcome back, <span class="font-bold text-red-700">{{ Auth::user()->name }}</span>! 👋
                    </span>
                </div>
            @endif
        </div>

        <div class="w-full md:w-72">
            <form method="GET" action="{{ route('dashboard') }}">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">Filter by Station</label>
                <div class="relative">
                    <select name="station_id" onchange="this.form.submit()" 
                            class="appearance-none block w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded-xl leading-tight focus:outline-none focus:bg-white focus:border-red-600 font-bold shadow-sm transition cursor-pointer hover:border-red-300">
                        
                        <option value="">All Stations</option>
                        @foreach($stations as $station)
                            <option value="{{ $station->id }}" {{ request('station_id') == $station->id ? 'selected' : '' }}>
                                {{ $station->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                        <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
        
        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-blue-600 to-blue-800 text-white transform hover:scale-105 transition duration-300">
            
            <div class="absolute top-4 right-4 p-3 bg-white/20 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>

            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Total Items</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($totalItems) }}</p>
            </div>
        </div>

        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white transform hover:scale-105 transition duration-300">
            
            <div class="absolute top-4 right-4 p-3 bg-white/20 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>

            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Serviceable</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($serviceable) }}</p>
            </div>
        </div>

        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-orange-500 to-orange-700 text-white transform hover:scale-105 transition duration-300">
            
            <div class="absolute top-4 right-4 p-3 bg-white/20 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>

            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Unserviceable</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($unserviceable) }}</p>
            </div>
        </div>

        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-red-600 to-red-800 text-white transform hover:scale-105 transition duration-300">
            
            <div class="absolute top-4 right-4 p-3 bg-white/30 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>

            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">B.E.R.</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($ber) }}</p>
            </div>
        </div>

        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-slate-700 to-slate-900 text-white transform hover:scale-105 transition duration-300">
            
            <div class="absolute top-4 right-4 h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-2xl font-bold font-sans">₱</span>
            </div>

            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Total Value</p>
                <p class="text-2xl font-semibold mt-8">
                    {{ number_format($totalValue, 2) }}
                </p>
            </div>
        </div>

    </div>

    <div class="bg-white p-10 rounded-2xl shadow-sm text-center border border-gray-200">
        <h4 class="text-gray-600 font-bold text-lg">Station Breakdown Chart</h4>
        <p class="text-gray-400 mt-2">Data visualization will appear here once items are added.</p>
        <div class="mt-8 h-40 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
            <span class="text-gray-400 font-medium">Chart Area</span>
        </div>
    </div>
@endsection