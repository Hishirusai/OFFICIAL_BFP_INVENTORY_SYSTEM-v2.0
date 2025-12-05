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
                            class="appearance-none block w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-xl leading-tight focus:outline-none focus:bg-white focus:border-red-600 font-bold shadow-lg transition cursor-pointer hover:border-red-300">
                        
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

    {{-- CHART & SUMMARY SPLIT SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

        {{-- LEFT COLUMN: CHART --}}
        {{-- DESIGN RESTORED: White BG, Strong Shadow, Colored Top Border --}}
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-2xl border border-gray-300 border-t-8 border-t-blue-900 h-full flex flex-col">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <div>
                    <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        Item Statistics
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Stock History ({{ $selectedYear }})
                        @if(request('station_id'))
                            <span class="ml-1 px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-xs font-bold">Filtered</span>
                        @endif
                    </p>
                </div>
                
                <div class="flex flex-col items-end gap-3 mt-4 md:mt-0">
                    
                    <div class="flex gap-2">
                        {{-- View Toggle (Bar vs Pie) --}}
                        <div class="flex bg-gray-100 p-1 rounded-lg">
                            <button onclick="toggleChartType('bar')" id="btn-chart-bar" 
                                    class="px-3 py-1.5 rounded-md shadow-sm bg-white text-blue-600 transition-all hover:text-blue-800" title="Bar Chart">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </button>
                            <button onclick="toggleChartType('donut')" id="btn-chart-donut" 
                                    class="px-3 py-1.5 rounded-md text-gray-500 hover:text-gray-800 transition-all" title="Donut Chart">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            </button>
                        </div>

                        {{-- Parameter Toggles --}}
                        <div class="flex bg-gray-100 p-1 rounded-lg">
                            <button onclick="updateChart('conditions')" id="btn-conditions" 
                                    class="px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-gray-800 transition-all">
                                Conditions
                            </button>
                            <button onclick="updateChart('total_items')" id="btn-total-items" 
                                    class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all">
                                Items
                            </button>
                            <button onclick="updateChart('total_value')" id="btn-total-value" 
                                    class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all">
                                Value
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Month Filter (Only for Donut) --}}
                        <select id="donut-month-filter" onchange="onMonthChange()"
                                class="hidden appearance-none border border-gray-200 rounded-lg px-3 py-1 text-gray-600 text-xs font-bold focus:outline-none focus:border-blue-500 cursor-pointer hover:bg-gray-50">
                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $index => $month)
                                <option value="{{ $index }}" {{ $index == 11 ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>

                        {{-- Year Filter --}}
                        <form method="GET" action="{{ route('dashboard') }}">
                            @if(request('station_id')) <input type="hidden" name="station_id" value="{{ request('station_id') }}"> @endif
                            <select name="year" onchange="this.form.submit()" class="appearance-none border border-gray-200 rounded-lg px-3 py-1 text-gray-600 text-xs font-bold focus:outline-none focus:border-blue-500 cursor-pointer hover:bg-gray-50">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Legend (Only for Bar Chart Mode) --}}
            <div id="conditions-legend" class="flex flex-wrap items-center gap-4 text-xs font-medium mb-4 select-none">
                <div class="flex items-center gap-2 cursor-pointer transition-opacity hover:opacity-80" onclick="toggleSeries('Serviceable')">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Serviceable
                </div>
                <div class="flex items-center gap-2 cursor-pointer transition-opacity hover:opacity-80" onclick="toggleSeries('Unserviceable')">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> Unserviceable
                </div>
                <div class="flex items-center gap-2 cursor-pointer transition-opacity hover:opacity-80" onclick="toggleSeries('B.E.R.')">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> B.E.R.
                </div>
            </div>

            {{-- Chart Container --}}
            <div id="itemStatisticsChart" class="w-full h-[320px]"></div>
        </div>

        {{-- RIGHT COLUMN: ITEM SUMMARY --}}
        {{-- DESIGN RESTORED: White BG, Strong Shadow, Colored Top Border --}}
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-2xl border border-gray-300 border-t-8 border-t-blue-900 h-full flex flex-col">
            
            {{-- HEADER ROW: Title (Left) and Buttons (Right) --}}
            <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-4">
                
                {{-- Title and Subtitle (Left) --}}
                <div>
                    <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        Item Summary
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">Detailed Breakdown by Condition and Category</p>
                </div>

                {{-- FILTER BUTTONS CONTAINER (Top Right) --}}
                <div class="flex flex-col items-end gap-2">
                    
                    {{-- NEW: Condition Filter Buttons (Now Placed first/on top) --}}
                    <div class="flex bg-gray-100 p-1 rounded-lg self-end">
                        <button onclick="setConditionFilter('ALL')" id="btn-condition-ALL" 
                                class="px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-gray-800 transition-all">
                            ALL
                        </button>
                        <button onclick="setConditionFilter('Serviceable')" id="btn-condition-Serviceable" 
                                class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all">
                            SERVICEABLE
                        </button>
                        <button onclick="setConditionFilter('Unserviceable')" id="btn-condition-Unserviceable" 
                                class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all">
                            UNSERVICEABLE
                        </button>
                        <button onclick="setConditionFilter('BER')" id="btn-condition-BER" 
                                class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all">
                            B.E.R.
                        </button>
                    </div>

                    {{-- P.P.E. / F.F.E. Toggle (Now Placed second/on bottom and right-aligned) --}}
                    <div class="flex bg-gray-100 p-1 rounded-lg self-end">
                        <button onclick="setCategoryFilter('PPE')" id="btn-summary-PPE" 
                                class="px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-gray-800 transition-all">
                            P.P.E.
                        </button>
                        <button onclick="setCategoryFilter('FFE')" id="btn-summary-FFE" 
                                class="px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all">
                            F.F.E.
                        </button>
                    </div>
                </div>
            </div>

            {{-- List Container --}}
            <div class="flex-grow overflow-y-auto pr-2 custom-scrollbar" style="max-height: 300px;">
                <ul id="summary-list" class="space-y-3">
                    {{-- Dynamically populated by JavaScript --}}
                </ul>
            </div>
            
            {{-- Footer Total --}}
            <div class="mt-4 pt-4 border-t border-gray-100 bg-gray-50 p-4 rounded-xl space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Total Items (Filtered)</span>
                    <span id="category-total" class="text-lg font-extrabold text-blue-700">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase">Total Value (Filtered)</span>
                    <span id="category-total-value" class="text-lg font-extrabold text-emerald-700">₱0.00</span>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ==========================================
        // 1. CHART LOGIC (Unchanged)
        // ==========================================
        const rawData = @json($chartData);
        let currentChartType = 'bar'; // Default
        let currentDataType = 'conditions'; // Default
        window.activeSeries = null; // Track Isolated Series
        const allSeriesNames = ['Serviceable', 'Unserviceable', 'B.E.R.']; // Constant

        window.onMonthChange = function() {
            updateChart(currentDataType);
        };

        // --- UPDATED: Toggle Series Function (Isolate Mode) ---
        window.toggleSeries = function(seriesName) {
            if (!window.myChart) return;

            if (window.activeSeries === seriesName) {
                // RESET: Show all series
                allSeriesNames.forEach(s => window.myChart.showSeries(s));
                window.activeSeries = null; 
                
                // Reset Opacity
                document.querySelectorAll('#conditions-legend > div').forEach(el => el.style.opacity = '1');
            } else {
                // ISOLATE: Show only clicked
                allSeriesNames.forEach(s => {
                    if (s === seriesName) window.myChart.showSeries(s);
                    else window.myChart.hideSeries(s);
                });
                window.activeSeries = seriesName;

                // Dim others in legend
                document.querySelectorAll('#conditions-legend > div').forEach(el => {
                    if (el.innerText.trim() === seriesName) el.style.opacity = '1';
                    else el.style.opacity = '0.3';
                });
            }
        };

        const getSeriesData = (type, chartType) => {
            if (chartType === 'donut') {
                const selectedMonthIdx = document.getElementById('donut-month-filter').value;
                if (type === 'conditions') {
                    return [
                        rawData.Serviceable[selectedMonthIdx],
                        rawData.Unserviceable[selectedMonthIdx],
                        rawData.BER[selectedMonthIdx]
                    ];
                } else if (type === 'total_items') {
                    return [rawData.TotalItems[selectedMonthIdx]];
                } else {
                    return [rawData.TotalValue[selectedMonthIdx]];
                }
            } else {
                if (type === 'conditions') {
                    return [
                        { name: 'B.E.R.', data: rawData.BER },
                        { name: 'Unserviceable', data: rawData.Unserviceable },
                        { name: 'Serviceable', data: rawData.Serviceable }
                    ];
                } else if (type === 'total_items') {
                    return [{ name: 'Total Items', data: rawData.TotalItems }];
                } else {
                    return [{ name: 'Total Value', data: rawData.TotalValue }];
                }
            }
        };

        const getLabels = (type, chartType) => {
            if (chartType === 'donut') {
                if (type === 'conditions') return ['Serviceable', 'Unserviceable', 'B.E.R.'];
                if (type === 'total_items') return ['Total Items'];
                return ['Total Value'];
            }
            return [];
        };

        // --- Initial Chart Render ---
        var options = {
            series: getSeriesData('conditions', 'bar'),
            chart: { type: 'bar', height: 320, stacked: true, toolbar: { show: false }, animations: { enabled: true } },
            colors: ['#dc2626', '#f97316', '#10b981'], 
            plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], labels: { style: { colors: '#4b5563', fontSize: '11px' } } },
            yaxis: { labels: { style: { colors: '#4b5563', fontSize: '11px' }, formatter: (val) => val.toLocaleString() } },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
            legend: { show: false },
            tooltip: { y: { formatter: (val) => val.toLocaleString() + " Units" } }
        };

        window.myChart = new ApexCharts(document.querySelector("#itemStatisticsChart"), options);
        window.myChart.render();

        window.toggleChartType = function(type) {
            currentChartType = type;
            
            const btnBar = document.getElementById('btn-chart-bar');
            const btnDonut = document.getElementById('btn-chart-donut');
            const activeClass = "px-3 py-1.5 rounded-md shadow-sm bg-white text-blue-600 transition-all hover:text-blue-800";
            const inactiveClass = "px-3 py-1.5 rounded-md text-gray-500 hover:text-gray-800 transition-all";
            const monthFilter = document.getElementById('donut-month-filter');

            if(type === 'bar') {
                btnBar.className = activeClass;
                btnDonut.className = inactiveClass;
                monthFilter.classList.add('hidden'); 
            } else {
                btnBar.className = inactiveClass;
                btnDonut.className = activeClass;
                monthFilter.classList.remove('hidden'); 
            }

            // RESET SERIES VISIBILITY WHEN SWITCHING CHARTS
            if (window.activeSeries) {
                allSeriesNames.forEach(s => window.myChart.showSeries(s));
                window.activeSeries = null;
                document.querySelectorAll('#conditions-legend > div').forEach(el => el.style.opacity = '1');
            }

            updateChart(currentDataType);
        }

        window.updateChart = function(type) {
            currentDataType = type;

            const activeBtnClass = "px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-gray-800 transition-all";
            const inactiveBtnClass = "px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all";
            ['btn-conditions', 'btn-total-items', 'btn-total-value'].forEach(id => {
                document.getElementById(id).className = inactiveBtnClass;
            });
            let activeId = (type === 'conditions') ? 'btn-conditions' : (type === 'total_items' ? 'btn-total-items' : 'btn-total-value');
            document.getElementById(activeId).className = activeBtnClass;

            const legendEl = document.getElementById('conditions-legend');
            if (currentChartType === 'bar') {
                if (type === 'conditions') legendEl.style.display = 'flex';
                else legendEl.style.display = 'none';
            } else {
                legendEl.style.display = 'none';
            }

            let newOptions = {
                chart: { type: currentChartType, stacked: (type === 'conditions' && currentChartType === 'bar') },
                series: getSeriesData(type, currentChartType),
            };

            if (currentChartType === 'donut') {
                newOptions.labels = getLabels(type, 'donut');
                newOptions.plotOptions = { 
                    pie: { 
                        donut: { 
                            size: '70%', 
                            labels: {
                                show: true,
                                name: { show: true },
                                value: { 
                                    show: true,
                                    fontSize: '20px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 'bold',
                                    color: undefined,
                                    offsetY: 5,
                                    formatter: function (val) {
                                        return (type === 'total_value' ? "₱ " : "") + Number(val).toLocaleString();
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: false,
                                    label: 'Total',
                                    color: '#374151',
                                    fontWeight: 'bold',
                                    formatter: function (w) {
                                        let total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return (type === 'total_value' ? "₱ " : "") + total.toLocaleString();
                                    }
                                }
                            }
                        } 
                    } 
                };
                
                newOptions.colors = (type === 'conditions') 
                    ? ['#10b981', '#f97316', '#dc2626'] 
                    : ['#3b82f6'];
                
                newOptions.dataLabels = { 
                    enabled: true,
                    style: { fontSize: '14px', fontWeight: 'bold' },
                    dropShadow: { enabled: false },
                    formatter: function (val, opts) {
                        return val.toFixed(1) + "%"; 
                    }
                };

                newOptions.legend = {
                    show: true,
                    position: 'bottom',
                    fontSize: '13px',
                    markers: { width: 12, height: 12, radius: 12 },
                    itemMargin: { horizontal: 10, vertical: 5 }
                };

                newOptions.xaxis = { categories: [], labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } };
                newOptions.yaxis = { show: false };
                newOptions.grid = { show: false };

            } else {
                // FIXED: Explicitly restore X-Axis categories for Bar Chart
                newOptions.legend = { show: false }; 
                newOptions.dataLabels = { enabled: false };
                newOptions.xaxis = { 
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 
                    labels: { show: true, style: { colors: '#4b5563', fontSize: '11px' } },
                    axisBorder: { show: true },
                    axisTicks: { show: true }
                };
                newOptions.grid = { borderColor: '#f3f4f6', strokeDashArray: 4, show: true };
                
                if (type === 'total_value') {
                    newOptions.colors = ['#1e293b'];
                    newOptions.tooltip = { y: { formatter: (val) => "₱ " + val.toLocaleString() } };
                    newOptions.yaxis = { show: true, labels: { formatter: (val) => "₱ " + val.toLocaleString() } };
                } else {
                    newOptions.colors = (type === 'conditions') ? ['#dc2626', '#f97316', '#10b981'] : ['#3b82f6'];
                    newOptions.tooltip = { y: { formatter: (val) => val.toLocaleString() + " Units" } };
                    newOptions.yaxis = { show: true, labels: { formatter: (val) => val.toLocaleString() } };
                }
            }
            
            window.myChart.updateOptions(newOptions);
        }
        
        // ==========================================
        // 2. ITEM SUMMARY LOGIC (Updated)
        // ==========================================

        const allInventoryItems = @json($allItems ?? []); 
        let currentCategory = 'PPE'; // New state variable for category
        let currentCondition = 'ALL'; // New state variable for condition

        const targets = {
            'PPE': ['HOOD', 'COAT', 'HELMET', 'TROUSERS', 'GLOVES', 'BOOTS', 'SCBA'],
            'FFE': ['HOSE 1 1/2', 'HOSE 2 1/2', 'NOZZLE 1 1/2', 'NOZZLE 2 1/2']
        };

        // Define the exact list of normalized DB strings for each filter key
        const dbConditionMap = {
            'serviceable': ['serviceable'],
            'unserviceable': ['unserviceable'],
            'ber': ['ber'], // Assumes the item controller saves 'BER' which normalizes to 'ber'
        };
        // NOTE: The PHP controller (ItemController) only saves 'Serviceable' or 'Unserviceable' based on expiry, or 'BER' is handled by the user/system. We'll use these core terms.

        // Function to update the active state of the condition buttons (Updated styling logic)
        const updateConditionButtons = () => {
            const conditions = ['ALL', 'Serviceable', 'Unserviceable', 'BER'];
            const activeClass = "px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-gray-800 transition-all";
            const inactiveClass = "px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all";
            
            conditions.forEach(condition => {
                const btn = document.getElementById(`btn-condition-${condition}`);
                if (!btn) return;
                
                if (condition === currentCondition) {
                    btn.className = activeClass;
                } else {
                    btn.className = inactiveClass;
                }
            });
        };

        // Function to update the active state of the category buttons (Unchanged)
        const updateCategoryButtons = () => {
            const categories = ['PPE', 'FFE'];
            const activeClass = "px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-gray-800 transition-all";
            const inactiveClass = "px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-800 transition-all";

            categories.forEach(category => {
                const btn = document.getElementById(`btn-summary-${category}`);
                if (!btn) return;

                if (category === currentCategory) {
                    btn.className = activeClass;
                } else {
                    btn.className = inactiveClass;
                }
            });
        };

        window.setConditionFilter = function(condition) {
            currentCondition = condition;
            updateConditionButtons();
            updateSummary();
        };

        window.setCategoryFilter = function(category) {
            currentCategory = category;
            updateCategoryButtons();
            updateSummary();
        };

        // Consolidated updateSummary function - Logic modified to always render rows
        window.updateSummary = function() {
            const listContainer = document.getElementById('summary-list');
            listContainer.innerHTML = ''; 
            let grandTotalQty = 0;
            let grandTotalValue = 0;
            let hasContent = false;

            const categoryTargets = targets[currentCategory];

            categoryTargets.forEach(targetName => {
                
                // 1. FILTER BY ITEM NAME/CATEGORY (PRIMARY CHECK)
                const categoryMatches = allInventoryItems.filter(item => {
                    if (!item.name) return false;

                    // FFE Type/Size matching logic (This part is robust as verified by item name searches working)
                    if (currentCategory === 'FFE') {
                        let requiredName = '';
                        let requiredType = '';

                        if (targetName.toUpperCase().includes('HOSE')) requiredName = 'HOSE';
                        else if (targetName.toUpperCase().includes('NOZZLE')) requiredName = 'NOZZLE';

                        if (targetName.includes('1 1/2')) requiredType = '1 1/2';
                        else if (targetName.includes('2 1/2')) requiredType = '2 1/2';

                        const nameMatches = item.name.toUpperCase().includes(requiredName);
                        const dbTypeClean = (item.type ? String(item.type) : '').replace(/\s+/g, ''); 
                        const reqTypeClean = requiredType.replace(/\s+/g, '');
                        return nameMatches && dbTypeClean.includes(reqTypeClean);
                    } else {
                        // PPE simple name matching (This part is robust)
                        return item.name.toLowerCase().includes(targetName.toLowerCase());
                    }
                });

                // 2. FILTER BY CONDITION (SECONDARY CHECK - THE DEFINITIVE FIX)
                const matches = categoryMatches.filter(item => {
                    
                    // Normalize the item's condition string: lowercase and trim.
                    // This is the clean, official database value.
                    const itemConditionNormalized = (item.condition || '').toLowerCase().trim();
                    
                    let currentFilterKey = currentCondition.toLowerCase();
                    
                    if (currentFilterKey === 'all') {
                        return true;
                    } 
                    
                    // --- Get the expected normalized filter string/key ---
                    // IMPORTANT: Check 'unserviceable' FIRST because it contains 'serviceable' as a substring!
                    let expectedFilterKey = '';
                    if (currentFilterKey.includes('unserviceable')) {
                        expectedFilterKey = 'unserviceable';
                    } else if (currentFilterKey.includes('serviceable')) {
                        expectedFilterKey = 'serviceable';
                    } else if (currentFilterKey.includes('ber')) {
                        expectedFilterKey = 'ber';
                    } else {
                        return false; // Should not happen
                    }
                    
                    // --- Check for exact match against acceptable normalized DB strings ---

                    // Check for BER (BER or B.E.R. in DB, both normalize to 'ber' after cleaning non-alpha chars)
                    if (expectedFilterKey === 'ber') {
                         // The actual DB string might be "B.E.R." or "BER". Normalize both item and expected values simply.
                         // Using .replace(/[^a-z]/g, '') is the safest for BER/B.E.R.
                         const itemConditionCleaned = itemConditionNormalized.replace(/[^a-z]/g, '');
                         return itemConditionCleaned === 'ber';
                    }

                    // Check for Serviceable/Unserviceable (Assume the DB stores "serviceable" or "unserviceable")
                    // Use equality here, because these are predetermined conditions.
                    return itemConditionNormalized === expectedFilterKey;
                });

                // Calculate Row Totals
                const totalQty = matches.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                const totalVal = matches.reduce((sum, item) => sum + (parseFloat(item.total_cost) || 0), 0);
                
                grandTotalQty += totalQty;
                grandTotalValue += totalVal;

                // --- MODIFIED LOGIC: Always render the row ---
                // The row must always be created, even if totalQty is 0
                hasContent = true; // Mark as having content if we iterate over targets

                const li = document.createElement('li');
                li.className = "grid grid-cols-12 gap-2 items-center p-3 pl-4 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100/50";
                
                // Determine text color based on quantity
                const qtyTextColor = totalQty > 0 ? 'text-gray-800' : 'text-gray-400';
                
                li.innerHTML = `
                    <div class="col-span-5 flex flex-col">
                        <p class="text-base font-extrabold text-gray-700 truncate" title="${targetName}">${targetName}</p>
                        <p class="text-[11px] text-gray-600 font-bold uppercase tracking-wider mt-0.5">${matches.length} Records</p>
                    </div>
                    
                    <div class="col-span-4 text-center">
                        <span class="block text-base font-extrabold text-emerald-700 truncate">
                            ₱${totalVal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                        </span>
                        <span class="text-[11px] text-gray-600 uppercase font-bold tracking-wide mt-1 block">Total Value</span>
                    </div>

                    <div class="col-span-3 text-right">
                        <span class="block text-base font-extrabold ${qtyTextColor}">
                            ${totalQty.toLocaleString()}
                        </span>
                        <span class="text-[11px] text-gray-600 uppercase font-bold tracking-wide mt-1 block">Qty</span>
                    </div>
                `;
                listContainer.appendChild(li);
            });

            // If there were no targets (which shouldn't happen with the hardcoded lists), show message.
            if (!hasContent) {
                 listContainer.innerHTML = `
                    <div class="p-6 text-center text-gray-500 bg-gray-50 rounded-xl border-dashed border-2 border-gray-200">
                        <p class="font-semibold">No item types defined for the '${currentCategory}' category.</p>
                        <p class="text-sm">Please check the configuration.</p>
                    </div>
                 `;
            }

            document.getElementById('category-total').innerText = grandTotalQty.toLocaleString();
            document.getElementById('category-total-value').innerText = "₱" + grandTotalValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        };
        
        // Initialize the summary list on load
        updateCategoryButtons();
        updateConditionButtons();
        updateSummary();
    });
</script>
@endsection