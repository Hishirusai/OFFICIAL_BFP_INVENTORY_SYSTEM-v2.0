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
    <div id="logDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative w-full max-w-4xl shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all">
            
            <div class="bg-[#1e293b] p-8 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-3xl font-extrabold tracking-tight uppercase">LOG SUMMARY</h3>
                    <p class="text-gray-400 text-sm mt-1">Transaction & Activity Details</p>
                </div>
                <div class="bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-right backdrop-blur-sm">
                    <p class="text-[10px] text-gray-300 uppercase tracking-widest font-bold">Date Processed</p>
                    <p id="modal_date" class="font-mono text-xl font-bold text-white tracking-wide">--</p>
                </div>
            </div>

            <div class="p-8 bg-gray-50">
                
                <div class="flex flex-col md:flex-row justify-between mb-6 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm gap-6">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Performed By</p>
                        <p id="modal_user" class="text-2xl font-extrabold text-gray-900 leading-tight">--</p>
                    </div>
                    <div class="flex-1 md:text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Action Type</p>
                        <span id="modal_action_text" class="text-xl font-extrabold">--</span>
                    </div>
                </div>

                <div id="metadata_container" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6 hidden">
                    <div class="bg-gray-100 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Item Specifics</span>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Item Name</p>
                                <p id="meta_name" class="text-2xl font-extrabold text-gray-800">--</p>
                                <p id="meta_desc" class="text-sm text-gray-500 mt-1">--</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase">Total Cost</p>
                                <p id="meta_total" class="text-2xl font-extrabold text-emerald-600">--</p>
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

                <div class="bg-white p-4 rounded-xl border border-gray-200 mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Log Message</p>
                    <p id="modal_details" class="text-sm font-medium text-gray-800">--</p>
                </div>

                <div id="notes_container" class="bg-yellow-50 rounded-xl border border-yellow-100 p-4 hidden">
                     <p class="text-xs font-bold text-yellow-600 uppercase mb-1">Notes / Remarks</p>
                     <p id="modal_notes" class="text-sm text-yellow-800 italic">--</p>
                </div>

            </div>

            <div class="bg-white p-6 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closeModal('logDetailsModal')" class="bg-[#1e293b] hover:bg-black text-white text-sm font-bold py-3 px-8 rounded-xl shadow-lg transition">
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

            // ✅ POPULATE METADATA GRID
            const metaContainer = document.getElementById('metadata_container');
            const notesContainer = document.getElementById('notes_container');

            if (metadata) {
                metaContainer.classList.remove('hidden');
                
                // Helper to format currency
                const money = (val) => val ? '₱' + Number(val).toLocaleString(undefined, {minimumFractionDigits: 2}) : 'N/A';

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

            document.getElementById('logDetailsModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
@endsection