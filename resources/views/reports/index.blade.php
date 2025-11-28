@extends('layouts.app')

@section('content')
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
                            <td class="px-5 py-4 text-center">
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