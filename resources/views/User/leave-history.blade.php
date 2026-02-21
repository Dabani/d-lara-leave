<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Leave History') }}
            </h2>
            
            {{-- Filter Form in Header --}}
            <form method="GET" action="{{ route('leave-history') }}" class="flex items-center gap-3">
                <div>
                    <select name="year" id="year" class="text-sm rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <select name="status" id="status" class="text-sm rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ $statusFilter == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ $statusFilter == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                    Filter
                </button>
                
                @if($yearFilter || $statusFilter)
                    <a href="{{ route('leave-history') }}" class="px-3 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-300 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600">Total Requests</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_requests'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600">Approved</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600">Rejected</div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                </div>
            </div>

            {{-- Leave History Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">All Leave Requests</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($leaveHistory as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $request->leave_type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @php
                                                $totalDays = \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1;
                                            @endphp
                                            {{ $totalDays }} day{{ $totalDays > 1 ? 's' : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(strtolower($request->status) === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif(strtolower($request->status) === 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if(strtolower($request->status) === 'pending')
                                                <a href="{{ route('leave-request.edit', $request->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    Edit
                                                </a>
                                                <form action="{{ route('leave-request.destroy', $request->id) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">{{ ucfirst($request->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No leave requests found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($leaveHistory->hasPages())
                        <div class="mt-4">
                            {{ $leaveHistory->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>