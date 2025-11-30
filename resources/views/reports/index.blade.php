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

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Reports & Logs</h1>
            <p class="text-lg text-gray-600 mt-1">System activity history and transaction logs.</p>
        </div>
        <div class="flex gap-3">
             <button class="bg-white text-gray-600 hover:text-gray-900 border border-gray-300 font-bold py-2 px-4 rounded-xl shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Print Logs
            </button>
        </div>
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
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-5 py-4 font-bold text-gray-700">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            <td class="px-5 py-4 font-bold">{{ $log->user->name }}</td>
                            <td class="px-5 py-4 text-left">
                                @if($log->action_type == 'Item Added')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-green-200">{{ $log->action_type }}</span>
                                @elseif($log->action_type == 'Transfer')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-blue-200">{{ $log->action_type }}</span>
                                @elseif($log->action_type == 'Dispose')
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-red-200">{{ $log->action_type }}</span>
                                @else
                                    <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-orange-200">{{ $log->action_type }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-700 font-medium">{{ $log->details }}</td>
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
@endsection