@extends('layouts.app')

@section('content')

<style>
    .was-validated select:invalid ~ .validation-msg,
    .was-validated input:invalid ~ .validation-msg {
        display: block !important;
    }

    /* Red borders for invalid fields */
    .was-validated select:invalid,
    .was-validated input:invalid {
        border-color: #ef4444 !important;
        background-color: #fef2f2;
    }
    /* 1. ACTIVE PAGE: Gradient Red (Fixed) */
    nav[role="navigation"] span[aria-current="page"] span {
        background-image: linear-gradient(to right, #dc2626, #991b1b) !important; /* Red-600 to Red-800 */
        color: white !important;
        border: none !important;
    }

    /* 2. ALL LINKS (Arrows & Inactive Numbers): White BG + Dark Gray Border */
    nav[role="navigation"] a {
        background-color: white !important;
        color: #374151 !important; /* Gray-700 Text */
        border: 1px solid #4b5563 !important; /* Gray-600 Border (Dark Gray) */
        transition: all 0.2s ease-in-out;
    }

    /* 3. DISABLED ARROWS (e.g. Previous on Page 1): White BG + Dark Gray Border */
    /* This ensures even the unclickable arrow matches the design */
    nav[role="navigation"] span[aria-disabled="true"] span {
        background-color: white !important;
        color: #9ca3af !important; /* Lighter Gray Text to indicate disabled */
        border: 1px solid #4b5563 !important; /* Same Dark Gray Border */
    }

    /* 4. HOVER STATE: Turn Gradient Red + White Text */
    nav[role="navigation"] a:hover {
        background-image: linear-gradient(to right, #ef4444, #b91c1c) !important; /* Red-500 to Red-700 */
        color: white !important;
        border-color: transparent !important;
    }

    /* 5. ARROW ICONS: Turn White on Hover */
    nav[role="navigation"] a:hover svg {
        color: white !important;
    }
</style>

    <div class="flex flex-col md:flex-row justify-between items-center mb-2 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('stations.index') }}" 
                   class="bg-white text-gray-700 hover:text-gray-900 border border-gray-300 font-bold py-2 px-4 rounded-xl shadow-sm flex items-center transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    BACK
                </a>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $station->name }}</h1>
            </div>
            <p class="text-gray-600 mt-1 ml-30 text-lg">{{ $station->location ?? 'No Location' }}</p>
        </div>

        <div class="flex items-center gap-3"> 
            
            <div class="relative z-50">
                <button onclick="toggleNotificationDropdown()" class="bg-white text-gray-600 hover:text-blue-600 border border-gray-300 font-bold p-2.5 rounded-xl shadow-sm transition-all relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span id="notif-badge" class="{{ $station->unreadNotifications->count() > 0 ? '' : 'hidden' }} absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-md animate-pulse">
                        {{ $station->unreadNotifications->count() }}
                    </span>
                </button>

                <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-visible">
                    <div class="absolute -top-2 right-4 w-4 h-4 bg-white border-t border-l border-gray-200 transform rotate-45 z-10"></div>
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex justify-between items-center rounded-t-2xl relative z-20">
                        <h3 class="font-bold text-gray-700 text-base">Notifications</h3>
                        @if($station->notifications->count() > 0)
                            <form action="{{ route('stations.notifications.clear', $station->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-700 hover:underline font-bold uppercase tracking-wide">Clear All</button>
                            </form>
                        @endif
                    </div>
                    <div class="max-h-[400px] overflow-y-auto relative z-20">
                        @forelse($station->notifications as $notification)
                            <div id="notif-item-{{ $notification->id }}" 
                                class="group p-5 border-b border-gray-100 cursor-pointer transition duration-200 
                                {{ $notification->read_at ? 'bg-white opacity-60 hover:opacity-100' : 'bg-blue-50/50 border-l-4 border-blue-500' }} hover:bg-gray-50"
                                onclick="openReceiptModal('{{ $notification->id }}', {{ json_encode($notification->data) }})">
                                <div class="flex justify-between items-start mb-1">
                                    <p class="font-extrabold text-gray-800 text-sm group-hover:text-blue-600 transition">{{ $notification->data['title'] }}</p>
                                    <span class="text-[10px] text-gray-400 font-mono whitespace-nowrap ml-2">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 leading-snug">{{ $notification->data['message'] }}</p>
                                <div class="mt-2 flex items-center text-[11px] text-gray-400 uppercase font-bold tracking-wider">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $notification->data['user_name'] ?? 'System' }}
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center flex flex-col items-center text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <span class="text-sm font-medium">No notifications yet.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <button class="bg-white text-gray-600 hover:text-gray-900 border border-gray-300 font-bold py-2 px-4 rounded-xl shadow-sm flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
            <button class="bg-white text-gray-600 hover:text-gray-900 border border-gray-300 font-bold py-2 px-4 rounded-xl shadow-sm flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </button>
            <button onclick="openModal('globalTransferModal')" class="bg-gradient-to-r from-blue-700 to-blue-900 hover:from-blue-500 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Transfer
            </button>
            <button onclick="openModal('addItemModal')" class="bg-gradient-to-r from-emerald-700 to-emerald-900 hover:from-emerald-500 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Item
            </button>
        </div> 
    </div>

    <form id="searchForm" method="GET" action="{{ route('stations.show', $station->id) }}" class="flex flex-col md:flex-row gap-4 mb-6 items-center">
    
        {{-- LIVE SEARCH INPUT --}}
        <div class="flex-1 w-full relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" 
                id="liveSearchInput" 
                name="search" 
                value="{{ request('search') }}" 
                autocomplete="off"
                class="pl-10 w-full rounded-xl border border-gray-500 bg-gray-50 text-gray-900 placeholder-gray-500 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600 py-3 font-medium transition-colors" 
                placeholder="Search Item (Name, Code, Type)...">
        </div>

        {{-- CONDITION FILTER (Unchanged) --}}
        <div class="w-full md:w-48">
            <select name="condition" onchange="this.form.submit()" 
                    class="w-full rounded-xl border border-gray-500 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600 py-3 font-medium cursor-pointer">
                <option value="">All Conditions</option>
                <option value="Serviceable" {{ request('condition') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                <option value="Unserviceable" {{ request('condition') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                <option value="BER" {{ request('condition') == 'BER' ? 'selected' : '' }}>B.E.R.</option>
            </select>
        </div>

        {{-- UNIT FILTER (Unchanged) --}}
        <div class="w-full md:w-48">
            <select name="unit" onchange="this.form.submit()" 
                    class="w-full rounded-xl border border-gray-500 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600 py-3 font-medium cursor-pointer">
                <option value="">All Units</option>
                <option value="pieces" {{ request('unit') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                <option value="pairs" {{ request('unit') == 'pairs' ? 'selected' : '' }}>Pairs</option>
                <option value="sets" {{ request('unit') == 'sets' ? 'selected' : '' }}>Sets</option>
                <option value="rolls" {{ request('unit') == 'rolls' ? 'selected' : '' }}>Rolls</option>
                <option value="box" {{ request('unit') == 'box' ? 'selected' : '' }}>Boxes</option>
            </select>
        </div>

        {{-- CLEAR BUTTON (Unchanged) --}}
        @if(request('search') || request('condition') || request('unit'))
            <a href="{{ route('stations.show', $station->id) }}" 
            class="whitespace-nowrap px-6 py-3 bg-red-100 text-red-700 border border-red-300 font-bold rounded-xl shadow-sm hover:bg-red-200 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Clear Filters
            </a>
        @endif
    </form>

    <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                
                <thead>
                    <tr class="bg-gray-800 text-white text-xs uppercase tracking-widest font-extrabold shadow-md">
                        <th class="px-4 py-4 rounded-tl-3xl">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                Product Code
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                Item Name
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Type
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Date Acquired
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Quantity
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Unit Cost
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Total Cost
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Condition
                            </div>
                        </th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Date Expiry
                            </div>
                        </th>

                        <th class="px-4 py-4 text-center rounded-tr-3xl">
                            <div class="flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody id="tableBody" class="divide-y divide-gray-100 bg-white text-sm font-bold text-gray-700">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition duration-150 group cursor-pointer"
                        onclick="openItemDetailsModal(
                            '{{ $item->product_code }}', 
                            '{{ $item->name }}', 
                            '{{ $item->type }}', 
                            '{{ $item->date_acquired }}', 
                            '{{ $item->quantity }}', 
                            '{{ $item->unit }}', 
                            '{{ $item->unit_cost }}', 
                            '{{ $item->total_cost }}',
                            '{{ $item->condition }}', 
                            '{{ $item->date_expiry }}',
                            '{{ $item->description }}'
                        )">
                        
                        <td class="px-4 py-3 font-mono">
                            {{ $item->product_code }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->name }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="inline-block bg-gradient-to-r from-blue-500 to-blue-700 text-white px-3 py-1 rounded-full shadow-sm text-xs tracking-wide font-bold">
                                {{ $item->type }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($item->date_acquired)->format('M d, Y') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ number_format($item->quantity) }} <span class="text-xs text-gray-600 uppercase ml-1 font-bold">{{ $item->unit }}</span>
                        </td>

                        <td class="px-4 py-3">
                            ₱{{ number_format($item->unit_cost, 2) }}
                        </td>

                        <td class="px-4 py-3 font-extrabold text-emerald-800">
                            ₱{{ number_format($item->quantity * $item->unit_cost, 2) }}
                        </td>

                        <td class="px-4 py-3">
                            @if($item->condition === 'Serviceable')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-700 to-emerald-900 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> Serviceable
                                </span>
                            @elseif($item->condition === 'Unserviceable')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-700 to-orange-900 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> Unserviceable
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-700 to-red-900 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> B.E.R.
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->date_expiry ? \Carbon\Carbon::parse($item->date_expiry)->format('M d, Y') : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col xl:flex-row items-center justify-center gap-2">
                                
                                <button onclick="openSingleTransferModal(
                                    '{{ $item->id }}', '{{ $item->name }}', '{{ $item->product_code }}', 
                                    '{{ $item->quantity }}', '{{ $item->unit }}'
                                )" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-700 to-blue-900 text-white text-[10px] font-bold shadow-md hover:shadow-lg hover:from-blue-400 hover:to-blue-700 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    TRANSFER
                                </button>

                                <button onclick="openEditItemModal(
                                    '{{ $item->id }}', '{{ $item->product_code }}', '{{ $item->name }}', '{{ $item->type }}', 
                                    '{{ $item->quantity }}', '{{ $item->unit_cost }}', '{{ $item->date_acquired }}', 
                                    '{{ $item->date_expiry }}', '{{ $item->description }}', '{{ $item->condition }}', '{{ $item->unit }}'
                                )" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-amber-700 to-orange-900 text-white text-[10px] font-bold shadow-md hover:shadow-lg hover:from-amber-500 hover:to-orange-700 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    EDIT
                                </button>

                                <button type="button" 
                                        onclick="openDisposeModal('{{ $item->id }}')" 
                                        class="px-3 py-1.5 rounded-full bg-gradient-to-r from-red-700 to-red-900 text-white text-[10px] font-bold shadow-md hover:shadow-lg hover:from-red-400 hover:to-red-700 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    DISPOSE
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <p class="text-lg font-bold">No items found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>

    @if ($errors->addItem->any())
    <script>
        openModal('addItemModal');
    </script>
    @endif
    <div id="addItemModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-2xl shadow-2xl rounded-3xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b pb-4">Add New Item</h3>
            
            <form action="{{ route('items.store', $station->id) }}" method="POST" autocomplete="off" novalidate>
                @csrf
                <input type="hidden" name="form_type" value="add_item">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Product Code (Identifier)</label>
                        <input type="text" name="product_code" autocomplete="off" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" placeholder="e.g. BFP-2025-001">
                        @error('product_code') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Item Name</label>
                        <input list="item_names" name="name" autocomplete="off" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" placeholder="Select or Type Item Name">
                        
                        <datalist id="item_names">
                            @foreach($itemNames as $name) <option value="{{ $name }}"> @endforeach
                        </datalist>
                        @error('name') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Item Type</label>
                        <input list="item_types" name="type" autocomplete="off" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" placeholder="Select or Type Item Type">
                        
                        <datalist id="item_types">
                            @foreach($itemTypes as $type) <option value="{{ $type }}"> @endforeach
                        </datalist>
                        @error('type') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Quantity</label>
                        <input type="number" id="qty" name="quantity" min="1" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" placeholder="0" oninput="calculateTotal()">
                        @error('quantity') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Unit Cost (₱)</label>
                        <input type="number" id="unit_cost" name="unit_cost" step="0.01" min="0" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" placeholder="0.00" oninput="calculateTotal()">
                        @error('unit_cost') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Unit of Measure</label>
                        <div class="relative"> <select name="unit" class="w-full px-4 py-3 rounded-xl border border-gray-500 text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition appearance-none cursor-pointer bg-white shadow-inner">
                                <option value="" disabled selected>Select Unit</option>
                                <option value="Pieces">Pieces</option>
                                <option value="Boxes">Boxes</option>
                                <option value="Rolls">Rolls</option>
                                <option value="Pairs">Pairs</option>
                                <option value="Sets">Sets</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        @error('unit') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Total Cost (₱)</label>
                        <input type="text" id="total_cost_display" readonly class="w-full px-4 py-3 rounded-xl bg-gray-100 border border-gray-500 text-emerald-700 font-bold text-lg shadow-inner" placeholder="0.00">
                        <input type="hidden" id="total_cost_hidden" name="total_cost">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Date Acquired</label>
                        <input type="date" name="date_acquired" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" max="9999-12-31" value="{{ now()->format('Y-m-d') }}">
                        @error('date_acquired') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Date Expiry</label>
                        <input type="date" name="date_expiry" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" max="9999-12-31">
                        @error('date_expiry') <p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Description (Optional)</label>
                        <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" placeholder="Additional details..."></textarea>
                    </div>

                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeModal('addItemModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg">Save Item</button>
                </div>
            </form>
        </div>
    </div>

    <div id="itemDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-xl shadow-2xl rounded-2xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b pb-4">Item Details</h3>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                
                <div class="col-span-2 mb-4 p-3 bg-gray-50 rounded-lg border-l-4 border-red-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Product Code</p>
                    <p class="text-lg font-mono text-gray-900" id="detail_product_code"></p>
                </div>

                <div class="col-span-2">
                    <p class="text-xs font-bold text-gray-500 uppercase">Item Name / Type</p>
                    <p class="text-xl font-extrabold text-gray-900" id="detail_name"></p>
                </div>
                
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Quantity / Unit</p>
                    <p class="text-base font-bold text-gray-800" id="detail_quantity"></p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Unit Cost</p>
                    <p class="text-base font-bold text-gray-800" id="detail_unit_cost"></p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Date Acquired</p>
                    <p class="text-base font-medium text-gray-700" id="detail_acquired"></p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Date Expiry</p>
                    <p class="text-base font-medium text-gray-700" id="detail_expiry"></p>
                </div>

                <div class="col-span-2 border-t pt-4 mt-2 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Total Value</p>
                        <p class="text-xl font-extrabold text-emerald-600" id="detail_total_cost"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase text-right">Condition</p>
                        <p class="text-lg font-bold text-right" id="detail_condition"></p>
                    </div>
                </div>

                <div class="col-span-2 pt-4">
                    <p class="text-xs font-bold text-gray-500 uppercase">Description</p>
                    <p class="text-sm text-gray-700 mt-1" id="detail_description"></p>
                </div>

            </div>
            
            <div class="flex justify-end mt-8">
                <button type="button" onclick="closeModal('itemDetailsModal')" class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-800 text-white rounded-lg font-bold hover:from-red-700 hover:to-red-900 transition shadow-lg transform hover:-translate-y-0.5">Close</button>
            </div>
        </div>
    </div>

    <div id="disposeItemModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirm Disposal</h3>
            <p class="text-gray-500 mb-6">Are you sure you want to dispose of this item? It will be moved to archives but can be restored.</p>
            
            <form id="disposeForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeModal('disposeItemModal')" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-lg">Yes, Dispose It</button>
                </div>
            </form>
        </div>
    </div>

    <div id="globalTransferModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    
        <div class="relative p-8 border w-full max-w-5xl shadow-2xl rounded-3xl bg-white max-h-[90vh] overflow-y-auto">
            
            <div class="mb-8 border-b pb-4">
                <h3 class="text-3xl font-extrabold text-gray-900">Bulk Transfer</h3>
                <p class="text-base text-gray-500 mt-1">Move multiple items to another station at once.</p>
            </div>
            
            <form id="bulkTransferForm" action="{{ route('items.transfer', $station->id) }}" method="POST" autocomplete="off" novalidate>
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 uppercase mb-2 tracking-wide">From Station (Source)</label>
                        <div class="w-full px-4 py-3 rounded-xl bg-gray-100 border border-gray-300 text-gray-800 text-lg font-bold shadow-sm">
                            {{ $station->name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-600 uppercase mb-2 tracking-wide">
                            Transfer To Station <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="to_station_id" required class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white text-lg font-medium text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none appearance-none cursor-pointer">
                                <option value="" disabled selected>Select Destination Station</option>
                                @foreach(App\Models\Station::all() as $destStation)
                                    @if($destStation->id != $station->id)
                                        <option value="{{ $destStation->id }}">{{ $destStation->name }} ({{ $destStation->location }})</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <p class="validation-msg hidden mt-2 text-sm text-red-600 font-bold">Please select a destination station.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-600 uppercase mb-3 tracking-wide">Items to Transfer</label>
                    <div id="transferItemsContainer" class="space-y-4">
                        </div>
                    <button type="button" onclick="addTransferRow()" class="mt-6 flex items-center text-base font-bold text-blue-700 hover:text-blue-900 transition py-2 px-3 hover:bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Another Item
                    </button>
                </div>

                <div class="mt-8">
                    <label class="block text-sm font-bold text-gray-600 uppercase mb-2 tracking-wide">Notes (Optional):</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white text-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-gray-500" placeholder="Reason for transfer..."></textarea>
                </div>

                <div class="flex justify-end space-x-4 mt-10 border-t pt-6">
                    <button type="button" onclick="closeModal('globalTransferModal')" class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl text-base font-bold hover:bg-gray-200 transition">Cancel</button>
                    <button type="button" onclick="submitTransferForm()" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl text-base font-bold hover:from-blue-700 hover:to-blue-900 transition shadow-lg">
                        Confirm Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="singleTransferModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-xl shadow-2xl rounded-3xl bg-white max-h-[90vh] overflow-y-auto">
            
            <div class="mb-6 border-b pb-4">
                <h3 class="text-3xl font-extrabold text-gray-900">Transfer Item</h3>
                <p class="text-base text-gray-500 mt-1">Move a single item to another station.</p>
            </div>
            
            <form id="singleTransferForm" method="POST" class="space-y-6" novalidate>
                @csrf
                <input type="hidden" name="item_id" id="singleTransferItemId">
                
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Item Name</label>
                        <p id="singleTransferItemName" class="text-xl font-bold text-gray-900">--</p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Product Code</label>
                            <p id="singleTransferItemCode" class="text-lg font-semibold text-gray-700 font-mono">--</p>
                        </div>
                        <div class="text-right">
                            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Available Stock</label>
                            <p id="singleTransferMaxQty" class="text-lg font-bold text-blue-600">--</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 uppercase mb-2 tracking-wide">From Station</label>
                        <div class="w-full px-4 py-3 rounded-xl bg-gray-100 border border-gray-300 text-gray-800 text-lg font-bold shadow-sm flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $station->name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-600 uppercase mb-2 tracking-wide">
                            To Station <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="to_station_id" required class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white text-lg font-medium text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none appearance-none">
                                <option value="" disabled selected>Select Destination</option>
                                @foreach(App\Models\Station::all() as $destStation)
                                    @if($destStation->id != $station->id)
                                        <option value="{{ $destStation->id }}">{{ $destStation->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <p class="validation-msg hidden mt-2 text-sm text-red-600 font-bold">Please select a destination.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Quantity to transfer</label>
                    <div class="relative">
                        <input type="number" name="quantity" id="singleTransferQtyInput" min="1" step="1" required 
                            oninput="validateSingleQty(this)"
                            class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white text-lg font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-gray-500" 
                            placeholder="Enter quantity...">
                        
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-bold text-sm">
                            Qty
                        </div>
                    </div>
                    
                    <p id="singleQtyError" class="hidden mt-2 text-sm text-red-600 font-bold"></p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-600 uppercase mb-2 tracking-wide">Notes (Optional):</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white text-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-gray-500" placeholder="Reason for transfer..."></textarea>
                </div>

                <div class="flex justify-end space-x-4 pt-4 border-t mt-4">
                    <button type="button" onclick="closeModal('singleTransferModal')" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl text-base font-bold hover:bg-gray-200 transition">Cancel</button>
                    <button type="button" onclick="submitSingleTransferForm()" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-base font-bold hover:bg-blue-700 transition shadow-lg">Confirm Transfer</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editItemModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-2xl shadow-2xl rounded-3xl bg-white max-h-[90vh] overflow-y-auto">
            
            {{-- HEADER --}}
            <div class="mb-6 border-b pb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-2xl font-extrabold text-gray-900">Edit Item:</h3>
                    {{-- DISPLAY CODE ONLY --}}
                    <span id="edit_product_code_display" class="bg-blue-100 text-blue-800 text-lg font-mono font-bold px-3 py-1 rounded-lg border border-blue-200">
                    </span>
                </div>
            </div>
            
            <form id="editItemForm" method="POST" autocomplete="off" novalidate>
                @csrf
                @method('PUT')
                
                <input type="hidden" name="form_type" value="edit_item">
                <input type="hidden" name="item_id" id="edit_item_id_hidden">
                
                {{-- ❌ REMOVED PRODUCT CODE INPUT. IT IS NOT SENT TO SERVER ANYMORE. --}}
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Item Name</label>
                        <input type="text" id="edit_name" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                        @error('name') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Item Type</label>
                        <input type="text" id="edit_type" name="type" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                        @error('type') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Quantity</label>
                        <input type="number" id="edit_qty" name="quantity" min="1" oninput="calculateEditTotal()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                        @error('quantity') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Unit Cost (₱)</label>
                        <input type="number" id="edit_unit_cost" name="unit_cost" step="0.01" min="0" oninput="calculateEditTotal()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                        @error('unit_cost') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Total Cost (₱)</label>
                        <input type="text" id="edit_total_cost_display" readonly 
                            class="w-full px-4 py-3 rounded-xl bg-gray-100 border border-gray-500 text-emerald-700 font-bold text-lg shadow-inner" 
                            placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Unit of Measure</label>
                        <select id="edit_unit" name="unit" class="w-full px-4 py-3 rounded-xl border border-gray-500 text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner">
                            <option value="Pieces">Pieces</option>
                            <option value="Boxes">Boxes</option>
                            <option value="Rolls">Rolls</option>
                            <option value="Pairs">Pairs</option>
                            <option value="Sets">Sets</option>
                        </select>
                        @error('unit') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Condition</label>
                        <select id="edit_condition" name="condition" class="w-full px-4 py-3 rounded-xl border border-gray-500 text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner">
                            <option value="Serviceable">Serviceable</option>
                            <option value="Unserviceable">Unserviceable</option>
                            <option value="BER">B.E.R.</option>
                        </select>
                        @error('condition') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Date Acquired</label>
                        <input type="date" id="edit_date_acquired" name="date_acquired" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                        @error('date_acquired') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Date Expiry</label>
                        <input type="date" id="edit_date_expiry" name="date_expiry" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner">
                        @error('date_expiry') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Description</label>
                        <textarea id="edit_description" name="description" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner"></textarea>
                        @error('description') <p class="text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeModal('editItemModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    <div id="receiptModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-[60] flex items-center justify-center">
        <div class="relative p-0 border w-full max-w-3xl shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all">
            
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-8 text-white flex justify-between items-center shadow-lg">
                <div>
                    <h3 class="text-3xl font-extrabold tracking-tight">TRANSFER SUMMARY</h3> <p class="text-gray-300 text-base mt-1">Stock Movement Details</p>
                </div>
                <div class="text-right bg-white/10 p-3 rounded-lg border border-white/20 backdrop-blur-sm">
                    <p class="text-[10px] text-gray-300 uppercase tracking-widest mb-1">Date Processed</p>
                    <p class="font-mono text-xl font-bold" id="receipt_date">--</p>
                </div>
            </div>

            <div class="p-8">
                <div class="flex flex-col md:flex-row justify-between mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100 gap-6">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">From (Source)</p>
                        <p class="text-xl font-extrabold text-gray-800" id="receipt_from">--</p>
                        <p class="text-sm text-gray-500 font-medium" id="receipt_location">--</p>
                    </div>
                    
                    <div class="hidden md:flex items-center justify-center px-4">
                        <svg class="w-8 h-8 text-gray-300 transform rotate-90 md:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>

                    <div class="flex-1 md:text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">To (Destination)</p>
                        <p class="text-xl font-extrabold text-gray-800">{{ $station->name }}</p>
                        <p class="text-sm text-gray-500 font-medium">{{ $station->location }}</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden mb-8 shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs tracking-wider">
                            <tr>
                                <th class="p-4 border-b border-gray-200">Item Name / Code</th>
                                <th class="p-4 border-b border-gray-200 text-center">Qty</th>
                                <th class="p-4 border-b border-gray-200 text-right">Unit Cost</th>
                                <th class="p-4 border-b border-gray-200 text-right">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody id="receipt_items_body" class="divide-y divide-gray-100 bg-white">
                            </tbody>
                    </table>
                </div>

                <div class="mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Notes / Remarks</p>
                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 text-gray-700 text-sm italic leading-relaxed" id="receipt_notes">
                        No notes provided.
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100">
                    <button type="button" onclick="closeModal('receiptModal')" class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-lg text-sm">
                        Close Summary
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- GLOBAL ON LOAD ---
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. SETUP LIVE SERVER SEARCH (FETCH)
            const liveInput = document.getElementById('liveSearchInput');
            let searchTimeout = null;

            if (liveInput) {
                liveInput.addEventListener('input', function() {
                    const query = this.value;
                    // Get the current URL (e.g., stations/1)
                    const currentUrl = "{{ route('stations.show', $station->id) }}";

                    // Clear previous timer (Debounce)
                    clearTimeout(searchTimeout);

                    // Wait 400ms after typing stops, then fetch from server
                    searchTimeout = setTimeout(() => {
                        fetchResults(currentUrl, query);
                    }, 400);
                });
            }

            // 2. CHECK ERRORS (For Add Item Modal)
            @if($errors->addItem->any())
                @if(
                    $errors->addItem->has('name') ||
                    $errors->addItem->has('product_code') ||
                    $errors->addItem->has('quantity') ||
                    $errors->addItem->has('unit_cost')
                )
                    <script>
                        openModal('addItemModal');
                    </script>
                @endif
            @endif
            
            // 3. AUTO-HIDE SUCCESS MESSAGE
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.classList.add('opacity-0');
                    setTimeout(function() { successMessage.remove(); }, 1000);
                }, 3000);
            }
        });

        // --- NEW: FETCH RESULTS FUNCTION ---
        function fetchResults(url, query) {
            // Build the URL with the search query
            const fetchUrl = `${url}?search=${encodeURIComponent(query)}`;
            const tbody = document.getElementById('tableBody');

            // Optional: Visual loading state
            if (tbody) tbody.style.opacity = '0.5';

            fetch(fetchUrl)
                .then(response => response.text())
                .then(html => {
                    // Convert text response to HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // A. Swap the Table Rows
                    const newBody = doc.getElementById('tableBody');
                    if (newBody && tbody) {
                        tbody.innerHTML = newBody.innerHTML;
                    }

                    // B. Swap the Pagination
                    const currentPagination = document.getElementById('paginationContainer');
                    const newPagination = doc.getElementById('paginationContainer');
                    if (newPagination && currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }
                    
                    // Restore Opacity
                    if (tbody) tbody.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Search failed:', err);
                    if (tbody) tbody.style.opacity = '1';
                });
        }

        // --- MODAL CONTROLS ---
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) modal.classList.remove('hidden');
        }
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) modal.classList.add('hidden');
        }

        // --- CALCULATIONS (Add Item) ---
        function calculateTotal() {
            let qty = parseFloat(document.getElementById('qty').value) || 0;
            let cost = parseFloat(document.getElementById('unit_cost').value) || 0;
            let totalDisplay = document.getElementById('total_cost_display');
            let totalHidden = document.getElementById('total_cost_hidden');

            let total = qty * cost;
            totalDisplay.value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            totalHidden.value = total;
        }

        // --- CALCULATIONS (Edit Item) ---
        function calculateEditTotal() {
            let qty = parseFloat(document.getElementById('edit_qty').value) || 0;
            let cost = parseFloat(document.getElementById('edit_unit_cost').value) || 0;
            let totalDisplay = document.getElementById('edit_total_cost_display');

            let total = qty * cost;
            totalDisplay.value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // --- EDIT MODAL TRIGGER ---
        // --- EDIT MODAL TRIGGER ---
        // --- EDIT MODAL TRIGGER ---
        function openEditItemModal(itemId, productCode, name, type, quantity, unitCost, dateAcquired, dateExpiry, description, condition, unit) {
        
            // 1. Show Visual Text Only (No hidden input needed for this field)
            document.getElementById('edit_product_code_display').innerText = productCode;
            
            // 2. Fill the rest of the form
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_qty').value = quantity;
            document.getElementById('edit_unit_cost').value = unitCost;
            document.getElementById('edit_item_id_hidden').value = itemId;

            const formatDate = (dateStr) => {
                if(!dateStr || dateStr === 'null') return '';
                return new Date(dateStr).toISOString().split('T')[0];
            };
            document.getElementById('edit_date_acquired').value = formatDate(dateAcquired);
            document.getElementById('edit_date_expiry').value = formatDate(dateExpiry);
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_unit').value = unit;
            document.getElementById('edit_condition').value = condition;

            calculateEditTotal(); 
            // Fix URL
            document.getElementById('editItemForm').action = `/stations/{{ $station->id }}/items/${itemId}`;
            openModal('editItemModal');
        }

        // --- REOPEN ON ERROR ---
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any())
                const formType = "{{ old('form_type') }}";
                if (formType === 'edit_item') {
                    // Restore Text (Visual only)
                    // Note: We use the old('item_id') to fetch the item again if needed, 
                    // but usually just showing the modal is enough.
                    // Ideally we should pass the code back from old input, but since we didn't submit it, 
                    // we rely on the user cancelling or trying again.
                    // For now, let's just re-open the modal.
                    
                    const oldItemId = "{{ old('item_id') }}";
                    document.getElementById('edit_item_id_hidden').value = oldItemId;
                    
                    // Repopulate other fields
                    document.getElementById('edit_name').value = "{{ old('name') }}";
                    document.getElementById('edit_type').value = "{{ old('type') }}";
                    document.getElementById('edit_qty').value = "{{ old('quantity') }}";
                    document.getElementById('edit_unit_cost').value = "{{ old('unit_cost') }}";
                    document.getElementById('edit_unit').value = "{{ old('unit') }}";
                    document.getElementById('edit_condition').value = "{{ old('condition') }}";
                    document.getElementById('edit_date_acquired').value = "{{ old('date_acquired') }}";
                    document.getElementById('edit_date_expiry').value = "{{ old('date_expiry') }}";
                    document.getElementById('edit_description').value = "{{ old('description') }}";

                    document.getElementById('editItemForm').action = `/stations/{{ $station->id }}/items/${oldItemId}`;

                    calculateEditTotal();
                    openModal('editItemModal');
                }
            @endif
        });

        // --- FORMAT HELPERS ---
        function formatCurrency(number) {
            const num = parseFloat(number);
            return isNaN(num) ? number : num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function formatNumber(number) {
            const num = parseFloat(number);
            return isNaN(num) ? number : num.toLocaleString('en-PH');
        }

        // --- DETAILS MODAL ---
        // Note: parameter order must match the values passed in the row's onclick
        function openItemDetailsModal(code, name, type, acquired, qty, unit, unitCost, totalCost, condition, expiry, description) {
            document.getElementById('detail_product_code').innerText = code;
            document.getElementById('detail_name').innerHTML = `${name} <span class="text-sm font-medium text-gray-500">(${type})</span>`;
            document.getElementById('detail_quantity').innerText = `${formatNumber(qty)} ${unit}`;
            document.getElementById('detail_unit_cost').innerText = `₱${formatCurrency(unitCost)}`;
            document.getElementById('detail_total_cost').innerText = `₱${formatCurrency(totalCost)}`;

            const dateAcquired = new Date(acquired);
            document.getElementById('detail_acquired').innerText = !isNaN(dateAcquired) ? dateAcquired.toLocaleDateString('en-US') : acquired;

            const dateExpiry = new Date(expiry);
            document.getElementById('detail_expiry').innerText = (expiry === 'null' || expiry === 'N/A') ? 'N/A' : (!isNaN(dateExpiry) ? dateExpiry.toLocaleDateString('en-US') : expiry);

            document.getElementById('detail_description').innerText = description;

            let conditionClass = condition === 'Serviceable' ? 'bg-green-100 text-green-700' : 
                                condition === 'Unserviceable' ? 'bg-orange-100 text-orange-700' : 
                                'bg-red-100 text-red-700';
            document.getElementById('detail_condition').innerHTML = `<span class="${conditionClass} px-3 py-1 rounded-full text-xs font-bold uppercase">${condition}</span>`;
            openModal('itemDetailsModal');
        }


        // --- DISPOSE MODAL ---
        function openDisposeModal(itemId) {
            document.getElementById('disposeForm').action = `/stations/{{ $station->id }}/items/${itemId}`;
            openModal('disposeItemModal');
        }

        // --- SINGLE TRANSFER MODAL ---
        function openSingleTransferModal(itemId, name, code, qty, unit) {
            document.getElementById('singleTransferItemId').value = itemId;
            document.getElementById('singleTransferItemName').innerText = name;
            document.getElementById('singleTransferItemCode').innerText = code;
            
            const formattedQty = Number(qty).toLocaleString();
            document.getElementById('singleTransferMaxQty').innerText = `${formattedQty} ${unit}`;

            const qtyInput = document.getElementById('singleTransferQtyInput');
            qtyInput.value = ''; 
            qtyInput.max = qty;  
            
            const maxErrorSpan = document.getElementById('qtyErrorMax');
            if (maxErrorSpan) maxErrorSpan.innerText = formattedQty;

            document.getElementById('singleTransferForm').action = "{{ route('items.transfer', $station->id) }}";
            openModal('singleTransferModal');
        }

        // --- SUBMIT SINGLE TRANSFER ---
        function submitSingleTransferForm() {
            const form = document.getElementById('singleTransferForm');
            const qtyInput = document.getElementById('singleTransferQtyInput');
            
            validateSingleQty(qtyInput);

            if (!form.checkValidity() || !document.getElementById('singleQtyError').classList.contains('hidden')) {
                form.classList.add('was-validated');
                return; 
            }
            form.submit();
        }

        // --- BULK TRANSFER LOGIC ---
        const availableItems = @json($allStationItems); // Note: Use ->items() if paginated, or just $items if all

        function addTransferRow() {
            const container = document.getElementById('transferItemsContainer');
            const index = container.children.length; 
            
            // Use the JS array passed from Blade
            let optionsHtml = '<option value="" disabled selected>Select Item</option>';
            availableItems.forEach(item => {
                optionsHtml += `<option value="${item.id}" data-qty="${item.quantity}" data-unit="${item.unit}">
                                    ${item.name} - ${item.type} (${item.product_code})
                                </option>`;
            });

            const row = document.createElement('div');
            row.className = 'transfer-row flex flex-col md:flex-row gap-6 items-start bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-sm relative transition hover:shadow-md mb-4';
            
            row.innerHTML = `
                <div class="flex-1 w-full">
                    <label class="text-sm font-bold text-gray-500 uppercase mb-2 block tracking-wide">Item Selection <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="transfers[${index}][item_id]" required onchange="updateRowAvailability(this)"
                                class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none appearance-none text-base font-medium text-gray-900">
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="flex items-center mt-4 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                        <span class="text-sm font-bold text-gray-500 uppercase mr-3">Available:</span>
                        <span class="available-qty text-xl font-extrabold text-blue-700">--</span>
                    </div>
                </div>
                <div class="w-full md:w-56">
                    <label class="text-sm font-bold text-gray-500 uppercase mb-2 block tracking-wide">Quantity <span class="text-red-500">*</span></label>
                    <div>
                        <input type="number" name="transfers[${index}][quantity]" min="1" required oninput="validateRowQty(this)"
                            class="qty-input w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none text-lg font-bold text-center placeholder-gray-500" placeholder="0">
                        <p class="row-error-msg hidden mt-2 text-sm text-red-600 font-bold text-center"></p>
                    </div>
                </div>
                <button type="button" onclick="this.closest('.transfer-row').remove()" class="mt-8 p-3 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Remove Row">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            container.appendChild(row);
        }

        function updateRowAvailability(selectElement) {
            const row = selectElement.closest('.transfer-row');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const maxQty = selectedOption.getAttribute('data-qty');
            const unit = selectedOption.getAttribute('data-unit') || '';

            const stockDisplay = row.querySelector('.available-qty');
            if (stockDisplay) stockDisplay.innerText = maxQty ? `${parseFloat(maxQty).toLocaleString()} ${unit}` : '--';

            const qtyInput = row.querySelector('.qty-input');
            if (qtyInput) {
                maxQty ? qtyInput.max = maxQty : qtyInput.removeAttribute('max');
                validateRowQty(qtyInput);
            }
        }

        function validateRowQty(input) {
            const row = input.closest('.transfer-row');
            const errorMsg = row.querySelector('.row-error-msg');
            const max = parseFloat(input.max);
            const val = parseFloat(input.value);

            let message = "";
            if (!input.value || val <= 0) message = "Required";
            else if (!isNaN(max) && val > max) message = "Exceeds Stock";

            if (message) {
                errorMsg.innerText = message;
                errorMsg.classList.remove('hidden');
                input.classList.add('border-red-500', 'bg-red-50');
            } else {
                errorMsg.classList.add('hidden');
                input.classList.remove('border-red-500', 'bg-red-50');
            }
        }

        function validateSingleQty(input) {
            const errorMsg = document.getElementById('singleQtyError');
            const max = parseFloat(input.max);
            const val = parseFloat(input.value);

            let message = "";
            if (!input.value) message = "Quantity is required.";
            else if (val > max) message = `Cannot exceed available stock (Max: ${max})`;

            if (message) {
                errorMsg.innerText = message;
                errorMsg.classList.remove('hidden');
                input.classList.add('border-red-500', 'bg-red-50');
            } else {
                errorMsg.classList.add('hidden');
                input.classList.remove('border-red-500', 'bg-red-50');
            }
        }

        function submitTransferForm() {
            const form = document.getElementById('bulkTransferForm');
            const qtyInputs = form.querySelectorAll('.qty-input');
            let isValid = true;

            qtyInputs.forEach(input => {
                validateRowQty(input);
                if (!input.closest('.transfer-row').querySelector('.row-error-msg').classList.contains('hidden')) isValid = false;
            });

            if (form.checkValidity() && isValid) form.submit();
            else form.reportValidity();
        }
        // Toggle Notification Dropdown
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    function openReceiptModal(notificationId, data) {
        // 1. Mark as Read in Backend (Background Fetch)
        fetch(`/stations/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if(response.ok) {
                // ✅ DYNAMIC UI UPDATE (Fixes Point 2)
                
                // A. Visually mark the specific list item as read immediately
                const listItem = document.getElementById(`notif-item-${notificationId}`);
                if(listItem) {
                    listItem.classList.remove('bg-blue-50/50', 'border-l-4', 'border-blue-500');
                    listItem.classList.add('bg-white', 'opacity-60');
                }

                // B. Decrement the Badge Count (Don't hide it unless it hits 0)
                const badge = document.getElementById('notif-badge');
                if(badge) {
                    let currentCount = parseInt(badge.innerText);
                    if(currentCount > 0) {
                        currentCount--;
                        badge.innerText = currentCount;
                        // Hide if 0
                        if(currentCount === 0) {
                            badge.classList.add('hidden');
                        }
                    }
                }
            }
        });

        // 2. Populate Modal Data
        document.getElementById('receipt_date').innerText = data.transfer_date;
        document.getElementById('receipt_from').innerText = data.from_station_name;
        document.getElementById('receipt_location').innerText = data.from_station_location;
        
        const notesDiv = document.getElementById('receipt_notes');
        notesDiv.innerText = data.notes || "No notes provided.";
        // Style notes if empty
        if(!data.notes) notesDiv.classList.add('text-gray-400');
        else notesDiv.classList.remove('text-gray-400');

        // 3. Populate Items Table
        const tbody = document.getElementById('receipt_items_body');
        tbody.innerHTML = ''; 
        
        data.items.forEach(item => {
            // Helper for Number Formatting (Commas + Decimals)
            const formatNum = (num) => Number(num).toLocaleString('en-US');
            const formatMoney = (num) => '₱' + Number(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Handle cases where old notifications might not have cost data yet
            const unitCost = item.unit_cost ? formatMoney(item.unit_cost) : '-';
            const totalCost = item.total_cost ? formatMoney(item.total_cost) : '-';

            const row = `
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4">
                        <span class="block font-bold text-gray-800 text-base">${item.name}</span>
                        <span class="block font-mono text-xs text-gray-400 mt-1">${item.product_code}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-lg font-bold text-sm">
                            ${formatNum(item.quantity)} <span class="text-[10px] uppercase text-blue-600">${item.unit}</span>
                        </span>
                    </td>
                    <td class="p-4 text-right font-medium text-gray-600">
                        ${unitCost}
                    </td>
                    <td class="p-4 text-right font-extrabold text-emerald-700">
                        ${totalCost}
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        // 4. Close Dropdown & Open Modal
        toggleNotificationDropdown(); 
        openModal('receiptModal');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notificationDropdown');
        const btn = dropdown.previousElementSibling; 
        if (!dropdown.classList.contains('hidden') && !dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
        
    </script>
@endsection