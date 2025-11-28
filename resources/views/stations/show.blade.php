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

        <div class="flex gap-3">
            <button class="bg-white text-gray-600 hover:text-gray-900 border border-gray-300 font-bold py-2 px-4 rounded-xl shadow-sm flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
            <button class="bg-white text-gray-600 hover:text-gray-900 border border-gray-300 font-bold py-2 px-4 rounded-xl shadow-sm flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </button>
            <button onclick="openModal('globalTransferModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Transfer
            </button>

            <button onclick="openModal('addItemModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Item
            </button>
        </div>
    </div>

    <form id="searchForm" method="GET" action="{{ route('stations.show', $station->id) }}" class="flex flex-col md:flex-row gap-4 mb-6 items-center">
    
        <div class="flex-1 w-full relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" 
                name="search" 
                value="{{ request('search') }}" 
                oninput="debouncedSubmit()"
                class="pl-10 w-full rounded-xl border border-gray-500 bg-gray-50 text-gray-900 placeholder-gray-500 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600 py-3 font-medium transition-colors" 
                placeholder="Search code, name, or type...">
        </div>

        <div class="w-full md:w-48">
            <select name="condition" onchange="this.form.submit()" 
                    class="w-full rounded-xl border border-gray-500 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600 py-3 font-medium cursor-pointer">
                <option value="">All Conditions</option>
                <option value="Serviceable" {{ request('condition') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                <option value="Unserviceable" {{ request('condition') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                <option value="BER" {{ request('condition') == 'BER' ? 'selected' : '' }}>B.E.R.</option>
            </select>
        </div>

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
                    <tr class="bg-red-700 text-white text-xs uppercase tracking-widest font-extrabold shadow-md">
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
                        <th class="px-4 py-4 text-center rounded-tr-3xl">
                            <div class="flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white text-sm font-bold text-gray-700">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition duration-150 group">
                        
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
                            {{ number_format($item->quantity) }} <span class="text-xs text-gray-600 uppercase ml-1 font-bold">{{ $item->unit }}</span>
                        </td>

                        <td class="px-4 py-3">
                            ₱{{ number_format($item->unit_cost, 2) }}
                        </td>

                        <td class="px-4 py-3 font-extrabold text-emerald-600">
                            ₱{{ number_format($item->total_cost, 2) }}
                        </td>

                        <td class="px-4 py-3">
                            @if($item->condition === 'Serviceable')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-500 to-emerald-700 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> Serviceable
                                </span>
                            @elseif($item->condition === 'Unserviceable')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> Unserviceable
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-500 to-red-700 text-white shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> B.E.R.
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col xl:flex-row items-center justify-center gap-2">
                                
                                <button onclick="openSingleTransferModal(
                                    '{{ $item->id }}', '{{ $item->name }}', '{{ $item->product_code }}', 
                                    '{{ $item->quantity }}', '{{ $item->unit }}'
                                )" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 text-white text-[10px] font-bold shadow-md hover:shadow-lg hover:from-blue-600 hover:to-blue-800 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    TRANSFER
                                </button>

                                <button onclick="openEditItemModal(
                                    '{{ $item->id }}', '{{ $item->product_code }}', '{{ $item->name }}', '{{ $item->type }}', 
                                    '{{ $item->quantity }}', '{{ $item->unit_cost }}', '{{ $item->date_acquired }}', 
                                    '{{ $item->date_expiry }}', '{{ $item->description }}', '{{ $item->condition }}', '{{ $item->unit }}'
                                )" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-bold shadow-md hover:shadow-lg hover:from-amber-500 hover:to-orange-600 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    EDIT
                                </button>

                                <form action="{{ route('items.destroy', ['station' => $station->id, 'item' => $item->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-red-500 to-red-700 text-white text-[10px] font-bold shadow-md hover:shadow-lg hover:from-red-600 hover:to-red-800 transition-all flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        DISPOSE
                                    </button>
                                </form>
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

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>

    <div id="addItemModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-2xl shadow-2xl rounded-3xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b pb-4">Add New Item</h3>
            
            <form action="{{ route('items.store', $station->id) }}" method="POST" autocomplete="off" novalidate>
                @csrf
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
                        <input type="number" name="quantity" id="singleTransferQtyInput" min="1" step="0.01" required 
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
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b pb-4">Edit Item: <span id="edit_product_code_display" class="font-normal text-sm text-gray-500"></span></h3>
            
            <form id="editItemForm" method="POST" autocomplete="off" novalidate>
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Product Code</label>
                        <input type="text" id="edit_product_code" name="product_code" readonly class="w-full px-4 py-3 rounded-xl border border-gray-500 bg-gray-100 shadow-inner text-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Item Name</label>
                        <input type="text" id="edit_name" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Item Type</label>
                        <input type="text" id="edit_type" name="type" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Quantity</label>
                        <input type="number" id="edit_qty" name="quantity" min="1" oninput="calculateEditTotal()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Unit Cost (₱)</label>
                        <input type="number" id="edit_unit_cost" name="unit_cost" step="0.01" min="0" oninput="calculateEditTotal()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
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
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Condition</label>
                        <select id="edit_condition" name="condition" class="w-full px-4 py-3 rounded-xl border border-gray-500 text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner">
                            <option value="Serviceable">Serviceable</option>
                            <option value="Unserviceable">Unserviceable</option>
                            <option value="BER">B.E.R.</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Date Acquired</label>
                        <input type="date" id="edit_date_acquired" name="date_acquired" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Date Expiry</label>
                        <input type="date" id="edit_date_expiry" name="date_expiry" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Description</label>
                        <textarea id="edit_description" name="description" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-500 focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeModal('editItemModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- GLOBAL MODAL CONTROLS ---
        document.addEventListener('DOMContentLoaded', function() {
        // Check if Laravel sent back any errors
        @if($errors->any())
            // We check if the errors are related to the Add Item fields
            // (Adjust these field names if yours are different)
            @if($errors->has('name') || $errors->has('product_code') || $errors->has('quantity') || $errors->has('unit_cost'))
                openModal('addItemModal');
            @endif
        @endif
        });

        // --- MODAL CONTROLS ---
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.add('hidden');
            }
        }

        // --- CALCULATIONS ---
        function calculateTotal() {
            let qty = parseFloat(document.getElementById('qty').value) || 0;
            let cost = parseFloat(document.getElementById('unit_cost').value) || 0;
            let totalDisplay = document.getElementById('total_cost_display');
            let totalHidden = document.getElementById('total_cost_hidden');

            let total = qty * cost;
            totalDisplay.value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            totalHidden.value = total;
        }

        // --- NEW: CALCULATIONS (Edit Item) ---
        function calculateEditTotal() {
            let qty = parseFloat(document.getElementById('edit_qty').value) || 0;
            let cost = parseFloat(document.getElementById('edit_unit_cost').value) || 0;
            let totalDisplay = document.getElementById('edit_total_cost_display');

            let total = qty * cost;
            totalDisplay.value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // --- UPDATED EDIT MODAL TRIGGER ---
        function openEditItemModal(itemId, productCode, name, type, quantity, unitCost, dateAcquired, dateExpiry, description, condition, unit) {
            document.getElementById('edit_product_code').value = productCode;
            document.getElementById('edit_product_code_display').innerText = productCode;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_qty').value = quantity;
            document.getElementById('edit_unit_cost').value = unitCost;

            // Date Helper
            const formatDate = (dateStr) => {
                if(!dateStr || dateStr === 'null') return '';
                return new Date(dateStr).toISOString().split('T')[0];
            };

            document.getElementById('edit_date_acquired').value = formatDate(dateAcquired);
            document.getElementById('edit_date_expiry').value = formatDate(dateExpiry);
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_unit').value = unit;
            document.getElementById('edit_condition').value = condition;

            // Calculate total immediately when modal opens
            calculateEditTotal();

            document.getElementById('editItemForm').action = `/stations/{{ $station->id }}/items/${itemId}`;
            openModal('editItemModal');
        }

        // --- ITEM DETAILS MODAL (Read Only) ---
        function openItemDetailsModal(code, name, type, description, qty, unit, unitCost, totalCost, condition, acquired, expiry) {
            document.getElementById('detail_product_code').innerText = code;
            document.getElementById('detail_name').innerHTML = `${name} <span class="text-sm font-medium text-gray-500">(${type})</span>`;
            document.getElementById('detail_quantity').innerText = `${qty} ${unit}`;
            document.getElementById('detail_unit_cost').innerText = `₱${unitCost}`;
            document.getElementById('detail_total_cost').innerText = `₱${totalCost}`;
            
            // Format Date
            const dateAcquired = new Date(acquired);
            document.getElementById('detail_acquired').innerText = !isNaN(dateAcquired) ? dateAcquired.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : acquired;
            
            const dateExpiry = new Date(expiry);
            document.getElementById('detail_expiry').innerText = (expiry === 'null' || expiry === 'N/A') ? 'N/A' : (!isNaN(dateExpiry) ? dateExpiry.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : expiry);
            
            document.getElementById('detail_description').innerText = description;
            
            let conditionClass = condition === 'Serviceable' ? 'bg-green-100 text-green-700' : 
                                 condition === 'Unserviceable' ? 'bg-orange-100 text-orange-700' : 
                                 'bg-red-100 text-red-700';
            document.getElementById('detail_condition').innerHTML = `<span class="${conditionClass} px-3 py-1 rounded-full text-xs font-bold uppercase">${condition}</span>`;
            
            openModal('itemDetailsModal');
        }

        // --- EDIT MODAL TRIGGER ---
        // ✅ FIXED: Added 'unit' parameter at the end
        function openEditItemModal(itemId, productCode, name, type, quantity, unitCost, dateAcquired, dateExpiry, description, condition, unit) {
            
            document.getElementById('edit_product_code').value = productCode;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_qty').value = quantity;
            document.getElementById('edit_unit_cost').value = unitCost.replace(/[^0-9.]/g, '');
            
            // Date Helper
            function formatDate(dateString) {
                if (!dateString || dateString === 'null' || dateString === 'N/A') return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return ''; 
                return date.toISOString().split('T')[0]; 
            }

            document.getElementById('edit_date_acquired').value = formatDate(dateAcquired);
            document.getElementById('edit_date_expiry').value = formatDate(dateExpiry);
            document.getElementById('edit_description').value = description;
            
            // ✅ FIXED: Correctly assigning unit and condition
            document.getElementById('edit_unit').value = unit; 
            document.getElementById('edit_condition').value = condition; 

            calculateEditTotal();
            document.getElementById('editItemForm').action = `/stations/{{ $station->id }}/items/${itemId}`;
            openModal('editItemModal');
        }

        // --- DISPOSE MODAL TRIGGER ---
        function openDisposeModal(itemId) {
            document.getElementById('disposeForm').action = `/stations/{{ $station->id }}/items/${itemId}`;
            openModal('disposeItemModal');
        }

        // --- SINGLE TRANSFER TRIGGER (FIXED) ---
        function openSingleTransferModal(itemId, name, code, qty, unit) {
            // 1. Set the Hidden Item ID (Input field)
            const idInput = document.getElementById('singleTransferItemId');
            if (idInput) {
                idInput.value = itemId;
            }

            // 2. Set Display Texts (These are <p> tags, so use innerText, not value)
            document.getElementById('singleTransferItemName').innerText = name;
            document.getElementById('singleTransferItemCode').innerText = code;
            
            // 3. Format and Display Quantity
            const formattedQty = Number(qty).toLocaleString();
            document.getElementById('singleTransferMaxQty').innerText = `${formattedQty} ${unit}`;

            // 4. Setup the Quantity Input
            const qtyInput = document.getElementById('singleTransferQtyInput');
            qtyInput.value = ''; // Reset input
            qtyInput.max = qty;  // Set max attribute for validation
            
            // 5. Update the "Max" text in the error message span
            const maxErrorSpan = document.getElementById('qtyErrorMax');
            if (maxErrorSpan) {
                maxErrorSpan.innerText = formattedQty;
            }

            // 6. Ensure Form Action is correct
            document.getElementById('singleTransferForm').action = "{{ route('items.transfer', $station->id) }}";

            // 7. Show Modal
            openModal('singleTransferModal');
        }

        // --- SUBMIT SINGLE TRANSFER (NEW FUNCTION) ---
        function submitSingleTransferForm() {
            const form = document.getElementById('singleTransferForm');
            const qtyInput = document.getElementById('singleTransferQtyInput');
            
            // 1. Manually trigger the quantity validation we wrote
            validateSingleQty(qtyInput);

            // 2. Check for Dropdown validation (Standard HTML5)
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return; // Stop if dropdown is empty
            }

            // 3. Check if Quantity Logic failed (is the error message visible?)
            const errorMsg = document.getElementById('singleQtyError');
            if (!errorMsg.classList.contains('hidden')) {
                return; // Stop if custom quantity validation failed
            }

            // 4. If all good, Submit
            form.action = "{{ route('items.transfer', $station->id) }}";
            form.submit();
        }
        // --- BULK TRANSFER LOGIC ---
    
        // Make sure this variable is available (passed from Blade)
        const availableItems = @json($items); 

        function addTransferRow() {
        const container = document.getElementById('transferItemsContainer');
        const index = container.children.length; 
        
        // Handle Pagination: If 'data' exists, use it. Otherwise use the array directly.
        const itemsList = availableItems.data ? availableItems.data : availableItems;

        let optionsHtml = '<option value="" disabled selected>Select Item</option>';
        itemsList.forEach(item => {
            // Keep data-qty as RAW NUMBER for logic
            optionsHtml += `<option value="${item.id}" data-qty="${item.quantity}" data-unit="${item.unit}">
                                ${item.name} - ${item.type} (${item.product_code})
                            </option>`;
        });

        const row = document.createElement('div');
        row.className = 'transfer-row flex flex-col md:flex-row gap-6 items-start bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-sm relative transition hover:shadow-md mb-4';
        
        row.innerHTML = `
            <div class="flex-1 w-full">
                <label class="text-sm font-bold text-gray-500 uppercase mb-2 block tracking-wide">
                    Item Selection <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="transfers[${index}][item_id]" required onchange="updateRowAvailability(this)"
                            class="w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none appearance-none text-base font-medium text-gray-900">
                        ${optionsHtml}
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                
                <div class="flex items-center mt-4 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                    <span class="text-sm font-bold text-gray-500 uppercase mr-3">Available Stock:</span>
                    <span class="available-qty text-xl font-extrabold text-blue-700">--</span>
                </div>
            </div>
            
            <div class="w-full md:w-56">
                <label class="text-sm font-bold text-gray-500 uppercase mb-2 block tracking-wide">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <div>
                    <input type="number" name="transfers[${index}][quantity]" min="1" required 
                        oninput="validateRowQty(this)"
                        class="qty-input w-full px-4 py-3 rounded-xl border border-gray-400 shadow-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-lg font-bold text-gray-900 text-center placeholder-gray-500" 
                        placeholder="0">
                    <p class="row-error-msg hidden mt-2 text-sm text-red-600 font-bold text-center"></p>
                </div>
            </div>

            <button type="button" onclick="this.closest('.transfer-row').remove()" class="mt-8 p-3 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Remove Row">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        `;
        
        container.appendChild(row);
    }

        // --- NEW VALIDATION FUNCTION FOR ROWS ---
        function validateRowQty(input) {
        const row = input.closest('.transfer-row');
        const errorMsg = row.querySelector('.row-error-msg');
        const max = parseFloat(input.max);
        const val = parseFloat(input.value);

        let isValid = true;
        let message = "";

        if (!input.value || val <= 0) {
            isValid = false;
            message = "Required";
        } else if (!isNaN(max) && val > max) {
            isValid = false;
            message = "Exceeds Stock";
        }

        if (!isValid) {
            errorMsg.innerText = message;
            errorMsg.classList.remove('hidden');
            input.classList.add('border-red-500', 'bg-red-50');
        } else {
            errorMsg.classList.add('hidden');
            input.classList.remove('border-red-500', 'bg-red-50');
        }
    }

        // --- SUBMIT FUNCTION (Triggers the Validation) ---
        function submitTransferForm() {
        const form = document.getElementById('bulkTransferForm');
        // Validate all inputs before submitting
        const qtyInputs = form.querySelectorAll('.qty-input');
        let isCustomValid = true;

        qtyInputs.forEach(input => {
            validateRowQty(input);
            const row = input.closest('.transfer-row');
            const errorMsg = row.querySelector('.row-error-msg');
            if (!errorMsg.classList.contains('hidden')) {
                isCustomValid = false;
            }
        });

        if (form.checkValidity() && isCustomValid) {
            form.submit();
        } else {
            // Trigger browser validation UI
            form.reportValidity();
        }
    }

        // 2. Function to Update Availability (With Commas)
        function updateRowAvailability(selectElement) {
        const row = selectElement.closest('.transfer-row');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const maxQty = selectedOption.getAttribute('data-qty');
        const unit = selectedOption.getAttribute('data-unit') || '';

        // Update Display (With Commas)
        const stockDisplay = row.querySelector('.available-qty');
        if (stockDisplay) {
            if (maxQty) {
                stockDisplay.innerText = `${parseFloat(maxQty).toLocaleString('en-US')} ${unit}`;
            } else {
                stockDisplay.innerText = '--';
            }
        }

        // Update Input (Raw Number for Validation)
        const qtyInput = row.querySelector('.qty-input');
        if (qtyInput) {
            if (maxQty) {
                qtyInput.max = maxQty;
            } else {
                qtyInput.removeAttribute('max');
            }
            // Re-validate in case user already typed a number
            validateRowQty(qtyInput);
        }
    }

        function validateSingleQty(input) {
        const errorMsg = document.getElementById('singleQtyError');
        const max = parseFloat(input.max); // Max value set by the openModal function
        const val = parseFloat(input.value);

        if (input.value === '') {
            // Field is empty
            errorMsg.innerText = "Quantity is required.";
            errorMsg.classList.remove('hidden');
            input.classList.add('border-red-500', 'bg-red-50'); // Add red styling
        } 
        else if (val > max) {
            // Value is too high
            errorMsg.innerText = `Cannot exceed available stock (Max: ${max})`;
            errorMsg.classList.remove('hidden');
            input.classList.add('border-red-500', 'bg-red-50'); // Add red styling
        } 
        else {
            // Valid
            errorMsg.classList.add('hidden');
            input.classList.remove('border-red-500', 'bg-red-50'); // Remove red styling
        }
    }
        // --- FILTER LOGIC ---
        function filterTable() {
        const searchValue = document.getElementById('tableSearch').value.toLowerCase().trim();
        const conditionValue = document.getElementById('conditionFilter').value.toLowerCase().trim();
        const unitValue = document.getElementById('unitFilter').value.toLowerCase().trim();

        const tableBody = document.querySelector('table tbody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            if (cells.length > 0) {
                const code = cells[0].innerText.toLowerCase();
                const name = cells[1].innerText.toLowerCase();
                const type = cells[2].innerText.toLowerCase();
                const qtyUnit = cells[3].innerText.toLowerCase();
                const condition = cells[6].innerText.toLowerCase();

                // 1. Loose Search (Includes)
                const matchesSearch = (searchValue === "") || 
                                      code.includes(searchValue) || 
                                      name.includes(searchValue) || 
                                      type.includes(searchValue);

                // 2. Condition Filter (Handle BER dots)
                const cleanCondition = condition.replace(/\./g, ''); 
                const matchesCondition = (conditionValue === "") || 
                                         cleanCondition.includes(conditionValue) || 
                                         condition.includes(conditionValue);

                // 3. Unit Filter
                const matchesUnit = (unitValue === "") || qtyUnit.includes(unitValue);

                if (matchesSearch && matchesCondition && matchesUnit) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
        
        // Listen to search input typing for suggestions (if you kept suggestions)
        // searchInput.addEventListener('input', updateSuggestions); // Uncomment if using suggestion box

        // --- AUTO-HIDE SUCCESS MESSAGE ---
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.classList.add('opacity-0');
                    setTimeout(function() { successMessage.remove(); }, 1000);
                }, 3000);
            }
        });

        let searchTimeout = null;
        function debouncedSubmit() {
            // Clear the previous timer
            clearTimeout(searchTimeout);
            // Set a new timer to submit the form after 600ms of inactivity
            searchTimeout = setTimeout(function() {
                document.getElementById('searchForm').submit();
            }, 600);
        }
    </script>
@endsection