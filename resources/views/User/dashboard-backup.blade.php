<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4 sm:mx-auto sm:max-w-7xl" style="background-color: #68D391" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4 sm:mx-auto sm:max-w-7xl" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Section -->
            @if($employee)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <!-- Profile Image -->
                        <div class="flex-shrink-0">
                            @if($employee->profile_image)
                                <img src="{{ asset('storage/' . $employee->profile_image) }}" 
                                     alt="Profile" 
                                     class="w-24 h-24 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-gray-200">
                            @else
                                <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                                    <span class="text-4xl text-gray-600 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <!-- Upload Form -->
                            <form action="{{ route('update-profile') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <input type="file" name="profile_image" id="profile_image" accept="image/*" class="hidden" onchange="this.form.submit()">
                                <label for="profile_image" class="cursor-pointer text-sm text-blue-600 hover:text-blue-800 block text-center">
                                    Change Photo
                                </label>
                            </form>
                        </div>

                        <!-- Employee Info -->
                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-gray-600 mt-1">{{ auth()->user()->email }}</p>
                            @if($employee->department)
                                <p class="text-gray-600 mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mt-2">
                                        {{ $employee->department }}
                                    </span>
                                </p>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">Member since {{ $employee->created_at->format('F Y') }}</p>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex flex-col gap-2 w-full sm:w-auto">
                            <a href="{{ route('leave-request.create') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded text-center transition">
                                Request Leave
                            </a>
                            <a href="{{ route('leave-history') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded text-center transition">
                                View History
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Balance ({{ $employee->leave_year }})</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                        @php
                            $leaveTypes = [
                                ['name' => 'Casual', 'field' => 'casual_leave', 'color' => 'blue'],
                                ['name' => 'Sick', 'field' => 'sick_leave', 'color' => 'red'],
                                ['name' => 'Emergency', 'field' => 'emergency_leave', 'color' => 'orange'],
                                ['name' => 'Study', 'field' => 'study_leave', 'color' => 'purple'],
                                ['name' => 'Maternity', 'field' => 'maternity_leave', 'color' => 'pink'],
                                ['name' => 'Paternity', 'field' => 'paternity_leave', 'color' => 'indigo'],
                                ['name' => 'Annual', 'field' => 'annual_leave', 'color' => 'green'],
                                ['name' => 'W/O Pay', 'field' => 'without_pay_leave', 'color' => 'gray'],
                            ];
                        @endphp
                        @foreach($leaveTypes as $type)
                            <div class="text-center p-4 bg-{{ $type['color'] }}-50 rounded-lg">
                                <div class="text-2xl font-bold text-{{ $type['color'] }}-700">
                                    {{ $employee->{$type['field']} ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600 mt-1">{{ $type['name'] }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <div class="text-center">
                            <span class="text-gray-700 font-semibold">Total Days Taken:</span>
                            <span class="text-3xl font-bold text-blue-600 ml-2">{{ $employee->total_leave ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p class="font-bold">Pending Approval</p>
                <p>Your employee account is pending admin approval. You'll be able to request leave once approved.</p>
            </div>
            @endif

            <!-- Leave Requests Section -->
            @if($leaveRequests)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">My Leave Requests ({{ $yearFilter }})</h3>
                        
                        <!-- Year Filter -->
                        @if($years->count() > 1)
                        <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                            <select name="year" onchange="this.form.submit()" class="p-2 border border-gray-300 rounded-md text-sm">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </form>
                        @endif
                    </div>

                    <!-- Mobile View -->
                    <div class="block sm:hidden space-y-4">
                        @forelse($leaveRequests as $request)
                            <div class="border rounded-lg p-4 
                                @if($request->status == 'Approved') bg-green-50 border-green-200
                                @elseif($request->status == 'Rejected') bg-red-50 border-red-200
                                @else bg-yellow-50 border-yellow-200
                                @endif">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-semibold text-gray-900">{{ $request->leave_type }}</span>
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        @if($request->status == 'Approved') bg-green-500 text-white
                                        @elseif($request->status == 'Rejected') bg-red-500 text-white
                                        @else bg-yellow-500 text-white
                                        @endif">
                                        {{ $request->status }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div><strong>From:</strong> {{ $request->leave_from }}</div>
                                    <div><strong>To:</strong> {{ $request->leave_to }}</div>
                                    <div><strong>Duration:</strong> {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 }} days</div>
                                    @if($request->reason)
                                        <div><strong>Reason:</strong> {{ Str::limit($request->reason, 60) }}</div>
                                    @endif
                                </div>
                                @if($request->status == 'pending')
                                    <div class="flex gap-2 mt-3">
                                        <a href="{{ route('leave-request.edit', $request->id) }}" 
                                           class="flex-1 text-center bg-blue-600 text-white py-2 px-3 rounded text-sm hover:bg-blue-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('leave-request.destroy', $request->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')" 
                                                    class="w-full bg-red-600 text-white py-2 px-3 rounded text-sm hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">No leave requests found for {{ $yearFilter }}</div>
                        @endforelse
                    </div>

                    <!-- Desktop View -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 text-left">Leave Type</th>
                                    <th class="px-4 py-2 text-left">From</th>
                                    <th class="px-4 py-2 text-left">To</th>
                                    <th class="px-4 py-2 text-center">Days</th>
                                    <th class="px-4 py-2 text-left">Reason</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                    <th class="px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveRequests as $request)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2">{{ $request->leave_type }}</td>
                                        <td class="px-4 py-2">{{ $request->leave_from }}</td>
                                        <td class="px-4 py-2">{{ $request->leave_to }}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 }}
                                        </td>
                                        <td class="px-4 py-2">{{ Str::limit($request->reason, 30) }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-1 rounded text-xs font-semibold
                                                @if($request->status == 'Approved') bg-green-500 text-white
                                                @elseif($request->status == 'Rejected') bg-red-500 text-white
                                                @else bg-yellow-500 text-white
                                                @endif">
                                                {{ $request->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if($request->status == 'pending')
                                                <div class="flex gap-2 justify-center">
                                                    <a href="{{ route('leave-request.edit', $request->id) }}" 
                                                       class="bg-blue-600 text-white py-1 px-3 rounded text-sm hover:bg-blue-700">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('leave-request.destroy', $request->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Are you sure?')" 
                                                                class="bg-red-600 text-white py-1 px-3 rounded text-sm hover:bg-red-700">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-gray-500 py-8">No leave requests found for {{ $yearFilter }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($leaveRequests->hasPages())
                        <div class="mt-4">
                            {{ $leaveRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
