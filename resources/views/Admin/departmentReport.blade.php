<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Department Report') }} - {{ $department }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Print Button -->
            <div class="mb-4 no-print">
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-blue-700 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg print-area">
                <div class="p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">Leave Request Report</h1>
                        <h2 class="text-xl text-gray-700 mt-2">{{ $department }} Department</h2>
                        <p class="text-gray-600 mt-1">Year: {{ $year }}</p>
                        <p class="text-sm text-gray-500 mt-1">Generated on: {{ now()->format('F d, Y') }}</p>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="mb-8 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50 p-6 rounded-lg">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $leaveRequests->count() }}</div>
                            <div class="text-sm text-gray-600 mt-1">Total Requests</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $leaveRequests->where('status', 'Approved')->count() }}</div>
                            <div class="text-sm text-gray-600 mt-1">Approved</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $leaveRequests->where('status', 'Rejected')->count() }}</div>
                            <div class="text-sm text-gray-600 mt-1">Rejected</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ $leaveRequests->where('status', 'pending')->count() }}</div>
                            <div class="text-sm text-gray-600 mt-1">Pending</div>
                        </div>
                    </div>

                    <!-- Leave Type Breakdown -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Type Distribution</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @php
                                $leaveTypes = [
                                    'Casual Leave' => 'bg-blue-100 text-blue-800',
                                    'Sick Leave' => 'bg-red-100 text-red-800',
                                    'Emergency Leave' => 'bg-orange-100 text-orange-800',
                                    'Study Leave' => 'bg-purple-100 text-purple-800',
                                    'Maternity Leave' => 'bg-pink-100 text-pink-800',
                                    'Paternity Leave' => 'bg-indigo-100 text-indigo-800',
                                    'Annual Leave' => 'bg-green-100 text-green-800',
                                    'Without Pay' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            @foreach($leaveTypes as $type => $colorClass)
                                <div class="p-3 {{ $colorClass }} rounded text-center">
                                    <div class="font-bold text-lg">{{ $leaveRequests->where('leave_type', $type)->count() }}</div>
                                    <div class="text-xs">{{ $type }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detailed Leave Requests Table -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Leave Requests</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="border border-gray-300 px-4 py-2 text-left">#</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Employee</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Leave Type</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">From</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">To</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Days</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leaveRequests as $index => $request)
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $request->employee->user->name }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $request->leave_type }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $request->leave_from }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $request->leave_to }}</td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                <span class="px-2 py-1 rounded text-xs font-semibold
                                                    @if($request->status == 'Approved') bg-green-100 text-green-800
                                                    @elseif($request->status == 'Rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800
                                                    @endif">
                                                    {{ $request->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                                No leave requests found for this department in {{ $year }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
                        <p>This is a computer-generated report. No signature required.</p>
                        <p class="mt-1">For inquiries, contact the HR Department</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .print-area { box-shadow: none; }
            @page { margin: 1cm; }
        }
    </style>
</x-app-layout>
