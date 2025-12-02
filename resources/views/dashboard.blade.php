@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-end mb-10">
        
        <div class="mb-6 md:mb-0">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Dashboard Overview</h1>
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
            {{-- Station Filter Form --}}
            <form method="GET" action="{{ route('dashboard') }}">
                {{-- Preserve Year selection when changing Station --}}
                @if(request('year'))
                    <input type="hidden" name="year" value="{{ request('year') }}">
                @endif

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

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
        
        {{-- Total Items --}}
        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-blue-600 to-blue-800 text-white transform hover:scale-105 transition duration-300">
            <div class="absolute top-4 right-4 p-3 bg-white/20 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Total Items</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($totalItems) }}</p>
            </div>
        </div>

        {{-- Serviceable --}}
        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white transform hover:scale-105 transition duration-300">
            <div class="absolute top-4 right-4 p-3 bg-white/20 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Serviceable</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($serviceable) }}</p>
            </div>
        </div>

        {{-- Unserviceable --}}
        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-orange-500 to-orange-700 text-white transform hover:scale-105 transition duration-300">
            <div class="absolute top-4 right-4 p-3 bg-white/20 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">Unserviceable</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($unserviceable) }}</p>
            </div>
        </div>

        {{-- B.E.R. --}}
        <div class="relative rounded-2xl shadow-xl p-6 pb-10 bg-gradient-to-br from-red-600 to-red-800 text-white transform hover:scale-105 transition duration-300">
            <div class="absolute top-4 right-4 p-3 bg-white/30 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold uppercase opacity-80 tracking-wider">B.E.R.</p>
                <p class="text-2xl font-semibold mt-8">{{ number_format($ber) }}</p>
            </div>
        </div>

        {{-- Total Value --}}
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

    {{-- CHART SECTION --}}
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 mb-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    Item Statistics
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </h4>
                <p class="text-sm text-gray-500 mt-1">
                    Acquisition per Month ({{ $selectedYear }})
                    @if(request('station_id'))
                        <span class="ml-1 px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-xs font-bold">Filtered</span>
                    @endif
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 text-sm font-medium mt-4 md:mt-0">
                {{-- Legend --}}
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Serviceable
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-orange-500"></span> Unserviceable
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-600"></span> B.E.R.
                </div>
                
                {{-- Year Filter Form --}}
                <div class="ml-4">
                    <form method="GET" action="{{ route('dashboard') }}">
                        {{-- Preserve Station selection when changing Year --}}
                        @if(request('station_id'))
                            <input type="hidden" name="station_id" value="{{ request('station_id') }}">
                        @endif
                        
                        <div class="relative">
                            <select name="year" onchange="this.form.submit()" class="appearance-none border border-gray-200 rounded-lg pl-3 pr-8 py-1 text-gray-600 text-sm focus:outline-none focus:border-blue-500 cursor-pointer hover:bg-gray-50">
                                @if($availableYears->isEmpty())
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                @else
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- The Chart Container --}}
        <div id="itemStatisticsChart" class="w-full min-h-[350px]"></div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data passed from Controller
            const chartData = @json($chartData);

            // STACKING ORDER LOGIC NG ARRAY FOR ITEMS
            // 1. Serviceable (Green) -> TOP (Last in array)
            // 2. Unserviceable (Orange) -> MIDDLE
            // 3. B.E.R. (Red) -> BOTTOM (First in array)

            var options = {
                series: [
                    { name: 'B.E.R.', data: chartData.BER },
                    { name: 'Unserviceable', data: chartData.Unserviceable },
                    { name: 'Serviceable', data: chartData.Serviceable }
                ],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true, 
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'inherit'
                },
                // COLORS MUST MATCH THE SERIES ORDER ABOVE PARA PAAYOS AND AVOID SIRA
                // 1. Red (for BER)
                // 2. Orange (for Unserviceable)
                // 3. Green (for Serviceable)
                colors: ['#dc2626', '#f97316', '#10b981'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4,
                        columnWidth: '40%',
                    },
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
                },
                grid: {
                    show: true,
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4,
                    padding: { top: 0, right: 0, bottom: 0, left: 10 } 
                },
                legend: { show: false },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Units"
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#itemStatisticsChart"), options);
            chart.render();
        });
    </script>
@endsection