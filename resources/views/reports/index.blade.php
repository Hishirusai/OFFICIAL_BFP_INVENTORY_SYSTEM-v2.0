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

    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Reports & Logs</h1>
            <p class="text-lg text-gray-600 mt-1">System activity history and transaction logs.</p>
        </div>

        {{-- Dynamic Filter Form --}}
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
                
                {{-- Arrow Icon --}}
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

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

                        if (in_array($type, ['User Created', 'Item Created', 'Station Created', 'Stock Added', 'Item Added'])) {
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
                            {{ json_encode($log->metadata) }}, {{-- ✅ Pass Metadata --}}
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

    <div class="p-4 border-t border-gray-200">
        {{ $logs->links() }}
    </div>

    {{-- MODAL --}}
    <div id="logDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl max-h-[90vh] shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all flex flex-col">
            
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

                {{-- Transfer Section (From/To Stations) --}}
                <div id="transfer_stations_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="p-4 md:p-6">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4 md:gap-6">
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">From (Source)</p>
                                <p id="transfer_from_name" class="text-base md:text-xl font-extrabold text-gray-800">--</p>
                                <p id="transfer_from_location" class="text-sm text-gray-500 font-medium">--</p>
                            </div>
                            
                            <div class="hidden md:flex items-center justify-center px-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>

                            <div class="flex-1 md:text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">To (Destination)</p>
                                <p id="transfer_to_name" class="text-base md:text-xl font-extrabold text-gray-800">--</p>
                                <p id="transfer_to_location" class="text-sm text-gray-500 font-medium">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Single Transfer Item Info --}}
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
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Quantity</p>
                                <p id="transfer_item_qty" class="font-bold text-blue-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Unit</p>
                                <p id="transfer_item_unit" class="font-bold text-gray-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Unit Cost</p>
                                <p id="transfer_item_cost" class="font-bold text-gray-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Product Code</p>
                                <p id="transfer_item_product_code" class="font-mono font-bold text-gray-700 text-sm">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bulk Transfer Items Table --}}
                <div id="bulk_transfer_items_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Transferred Items</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="p-4 border-b border-gray-200">Item Name / Code</th>
                                    <th class="p-4 border-b border-gray-200 text-center">Qty</th>
                                    <th class="p-4 border-b border-gray-200 text-right">Unit Cost</th>
                                    <th class="p-4 border-b border-gray-200 text-right">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody id="bulk_transfer_items_body" class="divide-y divide-gray-100 bg-white">
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Non-Transfer Metadata Container (for other action types) --}}
                <div id="metadata_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Item Specifics</span>
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
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Product Code</p>
                                <p id="meta_code" class="font-mono font-bold text-gray-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Type</p>
                                <p id="meta_type" class="font-bold text-gray-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Quantity</p>
                                <p id="meta_qty" class="font-bold text-blue-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Unit Cost</p>
                                <p id="meta_cost" class="font-bold text-gray-700 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Expiry</p>
                                <p id="meta_expiry" class="font-bold text-gray-700 text-sm">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock Added Cost Display --}}
                <div id="stock_added_cost_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Cost Information</span>
                    </div>
                    
                    <div class="p-4 md:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1">Total Cost (After Addition)</p>
                                <p id="stock_added_total_after" class="text-lg md:text-2xl font-extrabold text-emerald-700">--</p>
                                <p class="text-xs text-emerald-600 mt-1">Current total cost of all items</p>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">Cost Added</p>
                                <p id="stock_added_cost_added" class="text-lg md:text-2xl font-extrabold text-blue-700">--</p>
                                <p class="text-xs text-blue-600 mt-1">Amount being added to inventory</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock Deducted Cost Display --}}
                <div id="stock_deducted_cost_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Cost Information</span>
                    </div>
                    
                    <div class="p-4 md:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1">Total Cost (After Deduction)</p>
                                <p id="stock_deducted_total_after" class="text-lg md:text-2xl font-extrabold text-emerald-700">--</p>
                                <p class="text-xs text-emerald-600 mt-1">Remaining total cost after deduction</p>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <p class="text-xs font-bold text-red-600 uppercase tracking-wide mb-1">Cost Deducted</p>
                                <p id="stock_deducted_cost_deducted" class="text-lg md:text-2xl font-extrabold text-red-700">--</p>
                                <p class="text-xs text-red-600 mt-1">Amount being deducted from inventory</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-200 mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Log Message</p>
                    <p id="modal_details" class="text-sm font-medium text-gray-800">--</p>
                </div>

                <div id="notes_container" class="bg-yellow-50 rounded-xl border border-yellow-100 p-4 hidden">
                     <p class="text-xs font-bold text-yellow-600 uppercase mb-1">Notes / Remarks</p>
                     <p id="modal_notes" class="text-sm text-yellow-800 italic">--</p>
                </div>

            </div>

            <div class="bg-white p-4 md:p-6 border-t border-gray-100 flex justify-end flex-shrink-0">
                <button type="button" onclick="closeModal('logDetailsModal')" class="bg-[#1e293b] hover:bg-black text-white text-sm font-bold py-2 md:py-3 px-6 md:px-8 rounded-xl shadow-lg transition w-full md:w-auto">
                    Close Summary
                </button>
            </div>
        </div>
    </div>

    <script>
        function openLogModal(date, user, type, details, metadata, badgeClass) {
            document.getElementById('modal_date').innerText = date;
            document.getElementById('modal_user').innerText = user;
            document.getElementById('modal_details').innerText = details;
            
            const actionText = document.getElementById('modal_action_text');
            actionText.innerText = type;
            actionText.className = 'text-xl font-extrabold'; // Reset
            
            if(type.includes('Created') || type.includes('Added')) actionText.classList.add('text-emerald-600');
            else if(type.includes('Transfer')) actionText.classList.add('text-blue-600');
            else if(type.includes('Delete') || type.includes('Dispose') || type.includes('Deducted')) actionText.classList.add('text-red-600');
            else actionText.classList.add('text-amber-600');

            // Get all containers
            const transferStationsContainer = document.getElementById('transfer_stations_container');
            const singleTransferContainer = document.getElementById('single_transfer_item_container');
            const bulkTransferContainer = document.getElementById('bulk_transfer_items_container');
            const metaContainer = document.getElementById('metadata_container');
            const stockAddedCostContainer = document.getElementById('stock_added_cost_container');
            const stockDeductedCostContainer = document.getElementById('stock_deducted_cost_container');
            const notesContainer = document.getElementById('notes_container');

            // Helper to format currency
            const money = (val) => val ? '₱' + Number(val).toLocaleString(undefined, {minimumFractionDigits: 2}) : 'N/A';
            const formatNum = (num) => Number(num).toLocaleString('en-US');

            // Check if this is a transfer action
            const isTransfer = type.includes('Transfer');
            const isStockAdded = type === 'Stock Added';
            const isStockDeducted = type === 'Stock Deducted';

            if (isTransfer && metadata && metadata.from_station_name) {
                // ✅ TRANSFER ACTION: Show transfer-specific layout
                
                // Show transfer stations
                transferStationsContainer.classList.remove('hidden');
                document.getElementById('transfer_from_name').innerText = metadata.from_station_name || 'N/A';
                document.getElementById('transfer_from_location').innerText = metadata.from_station_location || '';
                document.getElementById('transfer_to_name').innerText = metadata.to_station_name || 'N/A';
                document.getElementById('transfer_to_location').innerText = metadata.to_station_location || '';

                // Hide non-transfer metadata
                metaContainer.classList.add('hidden');

                // Check if single or bulk transfer
                const isBulk = type === 'Bulk Transfer' && metadata.items && metadata.items.length > 1;
                
                if (isBulk) {
                    // ✅ BULK TRANSFER: Show items table
                    singleTransferContainer.classList.add('hidden');
                    bulkTransferContainer.classList.remove('hidden');
                    
                    const tbody = document.getElementById('bulk_transfer_items_body');
                    tbody.innerHTML = '';
                    
                    if (metadata.items && metadata.items.length > 0) {
                        metadata.items.forEach(item => {
                            const unitCost = item.unit_cost ? money(item.unit_cost) : '-';
                            const totalCost = item.total_cost ? money(item.total_cost) : '-';
                            
                            const row = `
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4">
                                        <span class="block font-bold text-gray-800 text-base">${item.name || 'N/A'}</span>
                                        <span class="block font-mono text-xs text-gray-400 mt-1">${item.product_code || 'N/A'}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-lg font-bold text-sm">
                                            ${formatNum(item.quantity || 0)} <span class="text-[10px] uppercase text-blue-600">${item.unit || ''}</span>
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
                    }
                } else {
                    // ✅ SINGLE TRANSFER: Show single item info
                    bulkTransferContainer.classList.add('hidden');
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

                // Show notes if available
                if(metadata.notes) {
                    notesContainer.classList.remove('hidden');
                    document.getElementById('modal_notes').innerText = metadata.notes;
                } else {
                    notesContainer.classList.add('hidden');
                }

                // Hide stock cost containers for transfers
                stockAddedCostContainer.classList.add('hidden');
                stockDeductedCostContainer.classList.add('hidden');
            } else if (isStockAdded && metadata) {
                // ✅ STOCK ADDED: Show cost information
                transferStationsContainer.classList.add('hidden');
                singleTransferContainer.classList.add('hidden');
                bulkTransferContainer.classList.add('hidden');
                stockDeductedCostContainer.classList.add('hidden');
                
                // Show metadata and stock added cost container
                metaContainer.classList.remove('hidden');
                stockAddedCostContainer.classList.remove('hidden');
                
                // Populate metadata
                document.getElementById('meta_name').innerText = metadata.name || 'N/A';
                document.getElementById('meta_desc').innerText = metadata.description || (metadata.type ? `Item Type: ${metadata.type}` : '');
                document.getElementById('meta_code').innerText = metadata.product_code || 'N/A';
                document.getElementById('meta_type').innerText = metadata.type || 'N/A';
                document.getElementById('meta_qty').innerText  = (metadata.quantity ? Number(metadata.quantity).toLocaleString() : '0') + ' ' + (metadata.unit || '');
                document.getElementById('meta_cost').innerText = money(metadata.unit_cost);
                document.getElementById('meta_total').innerText= money(metadata.total_cost);
                document.getElementById('meta_expiry').innerText = metadata.date_expiry || 'N/A';

                // Populate stock added costs
                document.getElementById('stock_added_total_after').innerText = money(metadata.total_cost_after);
                document.getElementById('stock_added_cost_added').innerText = money(metadata.cost_added);

                if(metadata.notes) {
                    notesContainer.classList.remove('hidden');
                    document.getElementById('modal_notes').innerText = metadata.notes;
                } else {
                    notesContainer.classList.add('hidden');
                }
            } else if (isStockDeducted && metadata) {
                // ✅ STOCK DEDUCTED: Show cost information
                transferStationsContainer.classList.add('hidden');
                singleTransferContainer.classList.add('hidden');
                bulkTransferContainer.classList.add('hidden');
                stockAddedCostContainer.classList.add('hidden');
                
                // Show metadata and stock deducted cost container
                metaContainer.classList.remove('hidden');
                stockDeductedCostContainer.classList.remove('hidden');
                
                // Populate metadata
                document.getElementById('meta_name').innerText = metadata.name || 'N/A';
                document.getElementById('meta_desc').innerText = metadata.description || (metadata.type ? `Item Type: ${metadata.type}` : '');
                document.getElementById('meta_code').innerText = metadata.product_code || 'N/A';
                document.getElementById('meta_type').innerText = metadata.type || 'N/A';
                document.getElementById('meta_qty').innerText  = (metadata.quantity ? Number(metadata.quantity).toLocaleString() : '0') + ' ' + (metadata.unit || '');
                document.getElementById('meta_cost').innerText = money(metadata.unit_cost);
                document.getElementById('meta_total').innerText= money(metadata.total_cost);
                document.getElementById('meta_expiry').innerText = metadata.date_expiry || 'N/A';

                // Populate stock deducted costs
                document.getElementById('stock_deducted_total_after').innerText = money(metadata.total_cost_after);
                document.getElementById('stock_deducted_cost_deducted').innerText = money(metadata.cost_deducted);

                if(metadata.notes) {
                    notesContainer.classList.remove('hidden');
                    document.getElementById('modal_notes').innerText = metadata.notes;
                } else {
                    notesContainer.classList.add('hidden');
                }
            } else {
                // ✅ OTHER NON-TRANSFER ACTION: Show regular metadata grid
                transferStationsContainer.classList.add('hidden');
                singleTransferContainer.classList.add('hidden');
                bulkTransferContainer.classList.add('hidden');
                stockAddedCostContainer.classList.add('hidden');
                stockDeductedCostContainer.classList.add('hidden');

                if (metadata) {
                    metaContainer.classList.remove('hidden');
                    
                    document.getElementById('meta_name').innerText = metadata.name || 'N/A';
                    document.getElementById('meta_desc').innerText = metadata.description || (metadata.type ? `Item Type: ${metadata.type}` : '');
                    document.getElementById('meta_code').innerText = metadata.product_code || 'N/A';
                    document.getElementById('meta_type').innerText = metadata.type || 'N/A';
                    document.getElementById('meta_qty').innerText  = (metadata.quantity ? Number(metadata.quantity).toLocaleString() : '0') + ' ' + (metadata.unit || '');
                    document.getElementById('meta_cost').innerText = money(metadata.unit_cost);
                    document.getElementById('meta_total').innerText= money(metadata.total_cost);
                    document.getElementById('meta_expiry').innerText = metadata.date_expiry || 'N/A';

                    if(metadata.notes) {
                        notesContainer.classList.remove('hidden');
                        document.getElementById('modal_notes').innerText = metadata.notes;
                    } else {
                        notesContainer.classList.add('hidden');
                    }
                } else {
                    // Hide grid if this is an old log without metadata
                    metaContainer.classList.add('hidden');
                    notesContainer.classList.add('hidden');
                }
            }

            document.getElementById('logDetailsModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
@endsection