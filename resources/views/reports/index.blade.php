@extends('layouts.app')

@section('content')

{{-- STYLES --}}
<style>
    .was-validated select:invalid ~ .validation-msg,
    .was-validated input:invalid ~ .validation-msg {
        display: block !important;
    }
    .was-validated select:invalid,
    .was-validated input:invalid {
        border-color: #ef4444 !important;
        background-color: #fef2f2;
    }
    nav[role="navigation"] span[aria-current="page"] span {
        background-image: linear-gradient(to right, #dc2626, #991b1b) !important;
        color: white !important;
        border: none !important;
    }
    nav[role="navigation"] a {
        background-color: white !important;
        color: #374151 !important;
        border: 1px solid #4b5563 !important;
        transition: all 0.2s ease-in-out;
    }
    nav[role="navigation"] span[aria-disabled="true"] span {
        background-color: white !important;
        color: #9ca3af !important;
        border: 1px solid #4b5563 !important;
    }
    nav[role="navigation"] a:hover {
        background-image: linear-gradient(to right, #ef4444, #b91c1c) !important;
        color: white !important;
        border-color: transparent !important;
    }
    nav[role="navigation"] a:hover svg {
        color: white !important;
    }
</style>

    {{-- HEADER & FILTER --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Reports & Logs</h1>
            <p class="text-lg text-gray-600 mt-1">System activity history and transaction logs.</p>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="w-full md:w-64">
            <label for="action_type" class="block text-xs font-bold text-gray-500 uppercase mb-1">Filter by Action</label>
            <div class="relative">
                <select name="action_type" 
                    id="action_type" 
                    onchange="this.form.submit()" 
                    class="w-full rounded-xl border border-gray-500 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600 py-3 px-4 font-medium cursor-pointer appearance-none">
                    <option value="">All Action Types</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-800 text-white text-left text-sm font-extrabold uppercase tracking-wider">
                    <th class="px-5 py-4">Date & Time</th>
                    <th class="px-5 py-4">User</th>
                    <th class="px-5 py-4">Action Type</th>
                    <th class="px-5 py-4">Details</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm">
                @forelse($logs as $log)
                    @php
                        $type = $log->action_type;
                        $badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';

                        if (in_array($type, ['User Created', 'Item Created', 'Station Created', 'Stock Added', 'Item Added', 'Item Restored'])) {
                            $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                        } elseif (str_contains($type, 'Transfer')) {
                            $badgeClass = 'bg-blue-100 text-blue-800 border-blue-200';
                        } elseif (in_array($type, ['Item Disposed', 'Station Deleted', 'User Deleted', 'Stock Deducted'])) {
                            $badgeClass = 'bg-red-100 text-red-800 border-red-200';
                        } elseif (str_contains($type, 'Updated') || str_contains($type, 'Edit')) {
                            $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                        }
                    @endphp

                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer group"
                        onclick="openLogModal(
                            '{{ $log->created_at->format('Y-m-d H:i:s') }}',
                            '{{ $log->user->name }}',
                            '{{ $log->action_type }}',
                            {{ json_encode($log->details) }},
                            {{ json_encode($log->metadata) }}, 
                            '{{ $badgeClass }}'
                        )">
                        <td class="px-5 py-4 font-bold text-gray-700 group-hover:text-blue-700 transition">
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-5 py-4 font-bold">{{ $log->user->name }}</td>
                        <td class="px-5 py-4 text-left">
                            <span class="{{ $badgeClass }} px-3 py-1 rounded-full text-xs font-bold uppercase border">
                                {{ $log->action_type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-700 font-medium truncate max-w-xs">{{ $log->details }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400 text-sm italic">No activity logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="p-4 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
        
        {{-- Showing Results Text --}}
        <div class="text-sm text-gray-500 font-medium">
            Showing <span class="font-bold text-gray-900">{{ $logs->firstItem() ?? 0 }}</span> 
            to <span class="font-bold text-gray-900">{{ $logs->lastItem() ?? 0 }}</span> 
            of <span class="font-bold text-gray-900">{{ $logs->total() }}</span> results
        </div>

        {{-- Use the custom pagination view --}}
        <div>
            {{ $logs->links('partials.pagination') }}
        </div>
    </div>


    {{-- MODAL --}}
    <div id="logDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-4xl max-h-[90vh] shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all flex flex-col">
            
            {{-- MODAL HEADER --}}
            <div class="bg-[#1e293b] p-4 md:p-8 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4 flex-shrink-0">
                <div>
                    <h3 class="text-xl md:text-3xl font-extrabold tracking-tight uppercase">LOG SUMMARY</h3>
                    <p class="text-gray-400 text-xs md:text-sm mt-1">Transaction & Activity Details</p>
                </div>
                <div class="bg-white/10 border border-white/20 rounded-lg px-3 md:px-4 py-2 text-right backdrop-blur-sm w-full md:w-auto">
                    <p class="text-[10px] text-gray-300 uppercase tracking-widest font-bold">Date Processed</p>
                    <p id="modal_date" class="font-mono text-base md:text-xl font-bold text-white tracking-wide">--</p>
                </div>
            </div>

            {{-- MODAL BODY --}}
            <div class="p-4 md:p-8 bg-gray-50 overflow-y-auto flex-1">
                
                <div class="flex flex-col md:flex-row justify-between mb-4 md:mb-6 bg-white p-4 md:p-6 rounded-2xl border border-gray-200 shadow-sm gap-4 md:gap-6">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Performed By</p>
                        <p id="modal_user" class="text-lg md:text-2xl font-extrabold text-gray-900 leading-tight">--</p>
                    </div>
                    <div class="flex-1 md:text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Action Type</p>
                        <span id="modal_action_text" class="text-base md:text-xl font-extrabold">--</span>
                    </div>
                </div>

                {{-- Transfer Stations --}}
                <div id="transfer_stations_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="p-4 md:p-6">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4 md:gap-6">
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">From (Source)</p>
                                <p id="transfer_from_name" class="text-base md:text-xl font-extrabold text-gray-800">--</p>
                                <p id="transfer_from_location" class="text-sm text-gray-500 font-medium">--</p>
                            </div>
                            <div class="flex-1 md:text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">To (Destination)</p>
                                <p id="transfer_to_name" class="text-base md:text-xl font-extrabold text-gray-800">--</p>
                                <p id="transfer_to_location" class="text-sm text-gray-500 font-medium">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Single Transfer Item --}}
                <div id="single_transfer_item_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Transferred Item</span>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="flex justify-between items-start mb-4 md:mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Item Name</p>
                                <p id="transfer_item_name" class="text-lg md:text-2xl font-extrabold text-gray-800">--</p>
                                <p id="transfer_item_code" class="text-sm text-gray-500 mt-1 font-mono">--</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase">Total Cost</p>
                                <p id="transfer_item_total" class="text-lg md:text-2xl font-extrabold text-emerald-600">--</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Qty Transferred</p><p id="transfer_item_qty" class="font-bold text-blue-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Unit</p><p id="transfer_item_unit" class="font-bold text-gray-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Unit Cost</p><p id="transfer_item_cost" class="font-bold text-gray-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Product Code</p><p id="transfer_item_product_code" class="font-mono font-bold text-gray-700 text-sm">--</p></div>
                        </div>
                    </div>
                </div>

                {{-- Bulk Transfer Table (UPDATED COLS) --}}
                <div id="bulk_transfer_items_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-100">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Transferred Items</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white border-b border-gray-100">
                                <tr>
                                    {{-- Clean 4-Column Layout based on image --}}
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Item Name / Code</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Unit Cost</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody id="bulk_transfer_items_body" class="divide-y divide-gray-50 bg-white">
                                {{-- JS fills this --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PREVIOUS STATE GRID (Gray) --}}
                <div id="previous_metadata_container" class="bg-gray-100 border border-gray-300 rounded-xl overflow-hidden shadow-sm mb-6 hidden opacity-80">
                    <div class="bg-gray-200 px-6 py-3 border-b border-gray-300 flex justify-between items-center">
                         <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Previous State (Before Action)</span>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="flex justify-between items-start mb-4 md:mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Item Name</p>
                                <p id="prev_name" class="text-lg md:text-2xl font-extrabold text-gray-700">--</p>
                                <p id="prev_desc" class="text-sm text-gray-500 mt-1">--</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase">Total Cost</p>
                                <p id="prev_total" class="text-lg md:text-2xl font-extrabold text-gray-600">--</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 pt-4 border-t border-gray-200">
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Code</p><p id="prev_code" class="font-mono font-bold text-gray-600 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Type</p><p id="prev_type" class="font-bold text-gray-600 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Quantity</p><p id="prev_qty" class="font-bold text-gray-600 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Unit Cost</p><p id="prev_cost" class="font-bold text-gray-600 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Expiry</p><p id="prev_expiry" class="font-bold text-gray-600 text-sm">--</p></div>
                        </div>
                    </div>
                </div>

                {{-- CURRENT STATE GRID (White) --}}
                <div id="metadata_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Item Specifics (New State)</span>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="flex justify-between items-start mb-4 md:mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Item Name</p>
                                <p id="meta_name" class="text-lg md:text-2xl font-extrabold text-gray-800">--</p>
                                <p id="meta_desc" class="text-sm text-gray-500 mt-1">--</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase">Total Cost</p>
                                <p id="meta_total" class="text-lg md:text-2xl font-extrabold text-emerald-600">--</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 pt-4 border-t border-gray-100">
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Product Code</p><p id="meta_code" class="font-mono font-bold text-gray-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Type</p><p id="meta_type" class="font-bold text-gray-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Quantity</p><p id="meta_qty" class="font-bold text-blue-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Unit Cost</p><p id="meta_cost" class="font-bold text-gray-700 text-sm">--</p></div>
                            <div><p class="text-[10px] uppercase font-bold text-gray-400">Expiry</p><p id="meta_expiry" class="font-bold text-gray-700 text-sm">--</p></div>
                        </div>
                    </div>
                </div>

                {{-- Stock Added Cost --}}
                <div id="stock_added_cost_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Cost Information</span>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1">Total Cost (After Addition)</p>
                                <p id="stock_added_total_after" class="text-lg md:text-2xl font-extrabold text-emerald-700">--</p>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">Cost Added</p>
                                <p id="stock_added_cost_added" class="text-lg md:text-2xl font-extrabold text-blue-700">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock Deducted Cost --}}
                <div id="stock_deducted_cost_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Cost Information</span>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1">Total Cost (After Deduction)</p>
                                <p id="stock_deducted_total_after" class="text-lg md:text-2xl font-extrabold text-emerald-700">--</p>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-red-600 uppercase tracking-wide mb-1">Cost Deducted</p>
                                <p id="stock_deducted_cost_deducted" class="text-lg md:text-2xl font-extrabold text-red-700">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Log Message --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Log Message</p>
                    <p id="modal_details" class="text-sm font-medium text-gray-800">--</p>
                </div>
                <div id="notes_container" class="bg-yellow-50 rounded-xl border border-yellow-100 p-4 hidden">
                     <p class="text-xs font-bold text-yellow-600 uppercase mb-1">Notes / Remarks</p>
                     <p id="modal_notes" class="text-sm text-yellow-800 italic">--</p>
                </div>

            </div>

            {{-- MODAL FOOTER --}}
            <div class="bg-white p-4 md:p-6 border-t border-gray-100 flex justify-end items-center gap-3 flex-shrink-0">
                <form id="restore_form" action="" method="POST" class="hidden w-full md:w-auto">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2 md:py-3 px-6 md:px-8 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restore Item
                    </button>
                </form>
                <button type="button" onclick="closeModal('logDetailsModal')" class="bg-[#1e293b] hover:bg-black text-white text-sm font-bold py-2 md:py-3 px-6 md:px-8 rounded-xl shadow-lg transition w-full md:w-auto">
                    Close Summary
                </button>
            </div>
        </div>
    </div>

    <script>
        function openLogModal(date, user, type, details, metadata, badgeClass) {
            // 1. Basic Info
            document.getElementById('modal_date').innerText = date;
            document.getElementById('modal_user').innerText = user;
            document.getElementById('modal_details').innerText = details;
            
            // 2. Action Text Styling
            const actionText = document.getElementById('modal_action_text');
            actionText.innerText = type;
            actionText.className = 'text-base md:text-xl font-extrabold'; 
            
            if(type.includes('Created') || type.includes('Added')) actionText.classList.add('text-emerald-600');
            else if(type.includes('Transfer')) actionText.classList.add('text-blue-600');
            else if(type.includes('Delete') || type.includes('Dispose') || type.includes('Deducted')) actionText.classList.add('text-red-600');
            else actionText.classList.add('text-amber-600');

            // 3. Get Containers
            const transferStationsContainer = document.getElementById('transfer_stations_container');
            const singleTransferContainer = document.getElementById('single_transfer_item_container');
            const bulkTransferContainer = document.getElementById('bulk_transfer_items_container');
            const metaContainer = document.getElementById('metadata_container');
            const stockAddedCostContainer = document.getElementById('stock_added_cost_container');
            const stockDeductedCostContainer = document.getElementById('stock_deducted_cost_container');
            const notesContainer = document.getElementById('notes_container');
            const restoreForm = document.getElementById('restore_form');
            const prevMetaContainer = document.getElementById('previous_metadata_container');

            // 4. Helpers
            const money = (val) => val ? '₱' + Number(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : 'N/A';
            const formatNum = (num) => Number(num).toLocaleString('en-US');

            // 5. Check Type
            const isTransfer = type.includes('Transfer');
            const isStockAdded = type === 'Stock Added';
            const isStockDeducted = type === 'Stock Deducted';

            // 6. RESET VISIBILITY
            transferStationsContainer.classList.add('hidden');
            singleTransferContainer.classList.add('hidden');
            bulkTransferContainer.classList.add('hidden');
            metaContainer.classList.add('hidden');
            stockAddedCostContainer.classList.add('hidden');
            stockDeductedCostContainer.classList.add('hidden');
            if(restoreForm) restoreForm.classList.add('hidden');
            if(prevMetaContainer) prevMetaContainer.classList.add('hidden');

            // ✅ 7. PREVIOUS DATA LOGIC
            let previousData = null;
            if (metadata && metadata.previous) {
                previousData = metadata.previous;
            } else if (isTransfer && type !== 'Bulk Transfer' && metadata.items && metadata.items.length > 0) {
                const item = metadata.items[0];
                if (item.previous_quantity !== undefined) {
                    previousData = {
                        name: item.name,
                        product_code: item.product_code,
                        type: item.type,
                        description: item.description,
                        quantity: item.previous_quantity,
                        unit: item.unit,
                        unit_cost: item.unit_cost,
                        total_cost: item.previous_total_cost,
                        date_expiry: item.date_expiry
                    };
                }
            }

            if (previousData && prevMetaContainer) {
                prevMetaContainer.classList.remove('hidden');
                document.getElementById('prev_name').innerText = previousData.name || 'N/A';
                document.getElementById('prev_desc').innerText = previousData.description || (previousData.type ? `Item Type: ${previousData.type}` : '');
                document.getElementById('prev_code').innerText = previousData.product_code || 'N/A';
                document.getElementById('prev_type').innerText = previousData.type || 'N/A';
                document.getElementById('prev_qty').innerText  = (previousData.quantity ? formatNum(previousData.quantity) : '0') + ' ' + (previousData.unit || '');
                document.getElementById('prev_cost').innerText = money(previousData.unit_cost);
                document.getElementById('prev_total').innerText= money(previousData.total_cost);
                document.getElementById('prev_expiry').innerText = previousData.date_expiry || 'N/A';
            }

            // 8. RESTORE BUTTON
            if(restoreForm) restoreForm.classList.add('hidden');
            if (type === 'Item Disposed' && restoreForm) {
                restoreForm.classList.remove('hidden');
                if(metadata && metadata.item_id && metadata.station_id) {
                     restoreForm.action = `/stations/${metadata.station_id}/items/${metadata.item_id}/restore`;
                }
            }

            // 9. DISPLAY LOGIC
            if (isTransfer && metadata && metadata.from_station_name) {
                // --- TRANSFER ---
                transferStationsContainer.classList.remove('hidden');
                document.getElementById('transfer_from_name').innerText = metadata.from_station_name || 'N/A';
                document.getElementById('transfer_from_location').innerText = metadata.from_station_location || '';
                document.getElementById('transfer_to_name').innerText = metadata.to_station_name || 'N/A';
                document.getElementById('transfer_to_location').innerText = metadata.to_station_location || '';

                const isBulk = type === 'Bulk Transfer' && metadata.items && metadata.items.length > 1;
                
                if (isBulk) {
                    // --- BULK TABLE (UPDATED) ---
                    bulkTransferContainer.classList.remove('hidden');
                    const tbody = document.getElementById('bulk_transfer_items_body');
                    tbody.innerHTML = ''; // Clear previous

                    if (metadata.items && metadata.items.length > 0) {
                        metadata.items.forEach(item => {
                            const unitCost = item.unit_cost ? money(item.unit_cost) : '₱0.00';
                            const totalCost = item.total_cost ? money(item.total_cost) : '₱0.00';
                            
                            tbody.innerHTML += `
                                <tr class="hover:bg-blue-50/30 transition group">
                                    {{-- 1. Name and Code --}}
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex flex-col">
                                            <span class="font-extrabold text-gray-800 text-sm group-hover:text-blue-700 transition">${item.name || 'N/A'}</span>
                                            <span class="font-mono text-[10px] text-gray-400 mt-1">${item.product_code || 'N/A'}</span>
                                        </div>
                                    </td>
                                    
                                    {{-- 2. Quantity (Blue Badge style) --}}
                                    <td class="px-6 py-4 align-middle text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200">
                                            ${formatNum(item.quantity || 0)} 
                                            <span class="ml-1 text-[9px] uppercase opacity-70">${item.unit || ''}</span>
                                        </span>
                                    </td>

                                    {{-- 3. Unit Cost --}}
                                    <td class="px-6 py-4 align-middle text-right">
                                        <span class="font-bold text-gray-500 text-sm">${unitCost}</span>
                                    </td>

                                    {{-- 4. Total Cost (Green) --}}
                                    <td class="px-6 py-4 align-middle text-right">
                                        <span class="font-extrabold text-emerald-600 text-sm">${totalCost}</span>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                } else {
                    // SINGLE TRANSFER
                    singleTransferContainer.classList.remove('hidden');
                    const item = metadata.items && metadata.items.length > 0 ? metadata.items[0] : {};
                    document.getElementById('transfer_item_name').innerText = item.name || 'N/A';
                    document.getElementById('transfer_item_code').innerText = item.product_code ? `Code: ${item.product_code}` : '';
                    document.getElementById('transfer_item_total').innerText = money(item.total_cost);
                    document.getElementById('transfer_item_qty').innerText = formatNum(item.quantity || 0);
                    document.getElementById('transfer_item_unit').innerText = item.unit || 'N/A';
                    document.getElementById('transfer_item_cost').innerText = money(item.unit_cost);
                    document.getElementById('transfer_item_product_code').innerText = item.product_code || 'N/A';
                }

            } else if (isStockAdded && metadata) {
                // Stock Added logic (kept same)
                metaContainer.classList.remove('hidden');
                stockAddedCostContainer.classList.remove('hidden');
                fillMetadata(metadata, money);
                document.getElementById('stock_added_total_after').innerText = money(metadata.total_cost_after);
                document.getElementById('stock_added_cost_added').innerText = money(metadata.cost_added);

            } else if (isStockDeducted && metadata) {
                // Stock Deducted logic (kept same)
                metaContainer.classList.remove('hidden');
                stockDeductedCostContainer.classList.remove('hidden');
                fillMetadata(metadata, money);
                document.getElementById('stock_deducted_total_after').innerText = money(metadata.total_cost_after);
                document.getElementById('stock_deducted_cost_deducted').innerText = money(metadata.cost_deducted);

            } else {
                if (metadata && !isTransfer) { 
                    metaContainer.classList.remove('hidden');
                    fillMetadata(metadata, money);
                }
            }

            // 10. Show Notes
            if(metadata && metadata.notes) {
                notesContainer.classList.remove('hidden');
                document.getElementById('modal_notes').innerText = metadata.notes;
            } else {
                notesContainer.classList.add('hidden');
            }

            document.getElementById('logDetailsModal').classList.remove('hidden');
        }

        function fillMetadata(metadata, money) {
            document.getElementById('meta_name').innerText = metadata.name || 'N/A';
            document.getElementById('meta_desc').innerText = metadata.description || (metadata.type ? `Item Type: ${metadata.type}` : '');
            document.getElementById('meta_code').innerText = metadata.product_code || 'N/A';
            document.getElementById('meta_type').innerText = metadata.type || 'N/A';
            document.getElementById('meta_qty').innerText  = (metadata.quantity ? Number(metadata.quantity).toLocaleString() : '0') + ' ' + (metadata.unit || '');
            document.getElementById('meta_cost').innerText = money(metadata.unit_cost);
            document.getElementById('meta_total').innerText= money(metadata.total_cost);
            document.getElementById('meta_expiry').innerText = metadata.date_expiry || 'N/A';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
@endsection