<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center py-2"> <!-- Reduced padding here -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Employee') }}
            </h2>
            
            <!-- Search and Export Container - aligned to middle -->
            <div class="flex items-center gap-2">
                <!-- Search Box Component -->
                <div class="relative">
                    <x-search-box 
                        route="{{ route('admin.manage-employee') }}" 
                        placeholder="Search by name or email..."
                        :value="request('search')" />
                </div>
                
                <!-- Export Button -->
                <a href="{{ route('admin.export-employees', ['type' => 'all']) }}" 
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 whitespace-nowrap h-[42px]"> <!-- Fixed height to match search button -->
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export to Excel
                </a>
            </div>
        </div>
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

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4 sm:mx-auto sm:max-w-7xl" role="alert">
            <strong class="font-bold">Validation Error!</strong>
            <ul class="mt-2 ml-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Export Button -->
    <!-- <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row gap-2 justify-end">
                <a href="{{ route('admin.export-employees', ['type' => 'all']) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export to Excel
                </a>
            </div>
        </div>
    </div> -->

    <!-- Tab Navigation -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex px-6" aria-label="Tabs">
                        <button onclick="showTab('approved')" 
                                id="tab-approved"
                                class="employee-tab border-b-2 border-indigo-500 text-indigo-600 py-4 px-6 text-sm font-medium">
                            ✅ Approved Employees
                            <span class="ml-2 bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $activeEmployees->total() }}
                            </span>
                        </button>
                        <button onclick="showTab('pending')" 
                                id="tab-pending"
                                class="employee-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 text-sm font-medium">
                            ⏳ Pending Requests
                            <span class="ml-2 bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $pendingUsers->total() }}
                            </span>
                        </button>
                        <button onclick="showTab('blocked')" 
                                id="tab-blocked"
                                class="employee-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 text-sm font-medium">
                            🔴 Blocked Employees
                            <span class="ml-2 bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                {{ $blockedEmployees->total() }}
                            </span>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content: Approved Employees -->
                <div id="content-approved" class="employee-tab-content p-6">
                    <div class="bg-white rounded-lg">
                        <!-- Mobile View -->
                        <div class="block sm:hidden space-y-4">
                            @if($activeEmployees->isEmpty())
                                <div class="text-center py-4 text-gray-500">No active employees</div>
                            @else
                                @foreach($activeEmployees as $employee)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Name:</span>
                                            <span class="text-gray-900">{{ $employee->user->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Email:</span>
                                            <span class="text-gray-900 text-sm">{{ $employee->user->email }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Gender:</span>
                                            <span class="text-gray-900">{{ ucfirst($employee->user->gender ?? 'Not set') }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Department:</span>
                                            <span class="text-gray-900">{{ $employee->department ?? 'No Department' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Hire Date:</span>
                                            <span class="text-gray-900">{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : 'Not set' }}</span>
                                        </div>
                                        <div class="flex gap-2 mt-3">
                                            <button onclick="openEditModal(
                                                {{ $employee->id }},
                                                '{{ addslashes($employee->user->name) }}',
                                                '{{ $employee->user->gender ?? '' }}',
                                                '{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '' }}',
                                                '{{ addslashes($employee->department ?? '') }}',
                                                '{{ $employee->profile_image ?? '' }}'
                                            )" 
                                                    style="background-color:#3773B8" 
                                                    class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                Edit
                                            </button>
                                            <a href="{{ route('admin.block-employee', $employee->id) }}" 
                                               onclick="return confirm('Are you sure you want to block {{ $employee->user->name }}?')" 
                                               class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Block
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 text-center">#Sl No</th>
                                    <th class="px-4 py-2 text-start">Name</th>
                                    <th class="px-4 py-2 text-start">Email</th>
                                    <th class="px-4 py-2 text-center">Gender</th>
                                    <th class="px-4 py-2 text-center">Hire Date</th>
                                    <th class="px-4 py-2 text-center">Department</th>
                                    <th class="px-4 py-2 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($activeEmployees->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No active employees</td>
                                    </tr>
                                @endif
                                @foreach($activeEmployees as $employee)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2 text-center">{{ ($activeEmployees->currentPage()-1) * $activeEmployees->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{ $employee->user->name }}</td>
                                        <td class="px-4 py-2">{{ $employee->user->email }}</td>
                                        <td class="px-4 py-2 text-center">{{ ucfirst($employee->user->gender ?? 'Not set') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : 'Not set' }}
                                        </td>
                                        <td class="px-4 py-2 text-center">{{ $employee->department }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex gap-2 justify-center">
                                                {{-- Role Management Button --}}
                                                <button onclick="openRoleModal(
                                                    {{ $employee->user->id }},
                                                    '{{ addslashes($employee->user->name) }}',
                                                    '{{ $employee->user->role }}',
                                                    '{{ addslashes($employee->department ?? '') }}'
                                                )" class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 font-medium">
                                                    Role: {{ ucfirst(str_replace('_', ' ', $employee->user->role)) }}
                                                </button>
                                                <button onclick="openEditModal(
                                                        {{ $employee->id }},
                                                        '{{ addslashes($employee->user->name) }}',
                                                        '{{ $employee->user->gender ?? '' }}',
                                                        '{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '' }}',
                                                        '{{ addslashes($employee->department ?? '') }}',
                                                        '{{ $employee->profile_image ?? '' }}'
                                                    )"
                                                    style="background-color:#3773B8"
                                                    class="text-xs text-white px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 font-medium">
                                                    Edit Profile
                                                </button>

                                                <a href="{{ route('admin.block-employee', $employee->id) }}" 
                                                   onclick="return confirm('Are you sure you want to block {{ $employee->user->name }}?')" 
                                                   class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-2 px-4 rounded">
                                                    Block
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $activeEmployees->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Pending Requests -->
                <div id="content-pending" class="employee-tab-content hidden p-6">
                    <div class="bg-white rounded-lg">
                        <!-- Mobile View -->
                        <div class="block sm:hidden space-y-4">
                            @if($pendingUsers->isEmpty())
                                <div class="text-center py-4 text-gray-500">No pending request</div>
                            @else
                                @foreach($pendingUsers as $pendingUser)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Name:</span>
                                            <span class="text-gray-900">{{ $pendingUser->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Email:</span>
                                            <span class="text-gray-900 text-sm">{{ $pendingUser->email }}</span>
                                        </div>
                                        <form action="{{ route('admin.approve-employee', $pendingUser->id) }}" method="POST" class="mt-3">
                                            @csrf
                                            
                                            <div class="mb-3">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                    Gender <span class="text-red-500">*</span>
                                                </label>
                                                <select name="gender" required class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                    Hire Date <span class="text-red-500">*</span>
                                                </label>
                                                <input type="date" 
                                                    name="hire_date" 
                                                    required 
                                                    max="{{ date('Y-m-d') }}"
                                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                                <p class="text-xs text-gray-500 mt-1">Used to determine annual leave eligibility</p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                                    Department <span class="text-red-500">*</span>
                                                </label>
                                                <select name="department" required class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                                    <option value="">Select Department</option>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <button type="submit"
                                                    style="background-color: #68D391" 
                                                    class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                Approve Employee
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 text-center">#Sl No</th>
                                    <th class="px-4 py-2 text-start">Name</th>
                                    <th class="px-4 py-2 text-start">Email</th>
                                    <th class="px-4 py-2 text-center">Gender</th>
                                    <th class="px-4 py-2 text-center">Hire Date</th>
                                    <th class="px-4 py-2 text-center">Department</th>
                                    <th class="px-4 py-2 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($pendingUsers->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No pending request</td>
                                    </tr>
                                @endif
                                @foreach($pendingUsers as $pendingUser)
                                    <tr class="hover:bg-gray-100">
                                        <form action="{{ route('admin.approve-employee', $pendingUser->id) }}" method="POST">
                                            @csrf
                                            <td class="px-4 py-2 text-center">{{ ($pendingUsers->currentPage()-1) * $pendingUsers->perPage() + $loop->iteration }}</td>
                                            <td class="px-4 py-2">{{ $pendingUser->name }}</td>
                                            <td class="px-4 py-2">{{ $pendingUser->email }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <select name="gender" 
                                                        required 
                                                        class="p-1 border border-gray-300 rounded-md text-sm w-28">
                                                    <option value="">Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <input type="date" 
                                                    name="hire_date" 
                                                    required 
                                                    max="{{ date('Y-m-d') }}"
                                                    class="p-1 border border-gray-300 rounded-md text-sm w-36">
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <select name="department" 
                                                        required 
                                                        class="p-1 border border-gray-300 rounded-md text-sm">
                                                    <option value="">Select</option>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition">
                                                    ✓ Approve
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $pendingUsers->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Blocked Employees -->
                <div id="content-blocked" class="employee-tab-content hidden p-6">
                    <div class="bg-white rounded-lg">
                        <!-- Mobile View -->
                        <div class="block sm:hidden space-y-4">
                            @if($blockedEmployees->isEmpty())
                                <div class="text-center py-4 text-gray-500">No blocked employees</div>
                            @else
                                @foreach($blockedEmployees as $employee)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Name:</span>
                                            <span class="text-gray-900">{{ $employee->user->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Email:</span>
                                            <span class="text-gray-900 text-sm">{{ $employee->user->email }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Department:</span>
                                            <span class="text-gray-900">{{ $employee->department }}</span>
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('admin.unblock-employee', $employee->id) }}" 
                                               onclick="return confirm('Are you sure you want to unblock {{ $employee->user->name }}?')" 
                                               style="background-color: #68D391" 
                                               class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition inline-block">
                                                Unblock
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 text-center">#Sl No</th>
                                    <th class="px-4 py-2 text-start">Name</th>
                                    <th class="px-4 py-2 text-start">Email</th>
                                    <th class="px-4 py-2 text-center">Department</th>
                                    <th class="px-4 py-2 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($blockedEmployees->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No blocked employees</td>
                                    </tr>
                                @endif
                                @foreach($blockedEmployees as $employee)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2 text-center">{{ ($blockedEmployees->currentPage()-1) * $blockedEmployees->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{ $employee->user->name }}</td>
                                        <td class="px-4 py-2">{{ $employee->user->email }}</td>
                                        <td class="px-4 py-2 text-center">{{ $employee->department }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('admin.unblock-employee', $employee->id) }}" 
                                               onclick="return confirm('Are you sure you want to unblock {{ $employee->user->name }}?')" 
                                               style="background-color: #68D391" 
                                               class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                Unblock
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $blockedEmployees->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4 pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Edit Employee Profile</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="editEmployeeForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_employee_id" name="employee_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column - Form Fields --}}
                <div class="space-y-4">
                    {{-- Employee Name --}}
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">
                            Employee Name
                        </label>
                        <div class="w-2/3">
                            <input type="text" id="edit_employee_name" disabled
                                   class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700">
                        </div>
                    </div>

                    {{-- Gender --}}
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <div class="w-2/3">
                            <select id="edit_gender" name="gender" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    {{-- Hire Date --}}
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">
                            Hire Date
                        </label>
                        <div class="w-2/3">
                            <input type="date" id="edit_hire_date" name="hire_date"
                                   max="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                        </div>
                    </div>

                    {{-- Department --}}
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <div class="w-2/3">
                            <select id="edit_department" name="department" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Profile Image --}}
                <div class="space-y-4">
                    {{-- Current Profile Image Preview --}}
                    <div class="flex items-start">
                        <label class="w-1/3 text-sm font-medium text-gray-700">
                            Profile Image
                        </label>
                        <div class="w-2/3">
                            {{-- Image Preview Container --}}
                            <div id="current_image_preview" class="mb-3 flex justify-center">
                                {{-- Current image will be shown here via JavaScript --}}
                            </div>
                            
                            {{-- File Input --}}
                            <input type="file" id="edit_profile_image" name="profile_image"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   onchange="previewNewImage(event)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300 text-sm">
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF, WebP (Max 2MB)</p>
                            
                            {{-- New image preview --}}
                            <div id="new_image_preview" class="mt-3 hidden">
                                <p class="text-xs text-gray-500 mb-1">New image preview:</p>
                                <div class="flex justify-center">
                                    <img id="new_image" src="" alt="New profile preview"
                                         class="w-24 h-24 rounded-full object-cover border-2 border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 justify-end mt-6 pt-4 border-t">
                <button type="button" onclick="closeEditModal()"
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-semibold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Default user SVG for when no profile image is available
     */
    function getDefaultUserSVG() {
        return `
            <div class="flex justify-center">
                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        `;
    }

    /**
     * Open the edit modal and pre-fill with employee data
     */
    function openEditModal(employeeId, name, gender, hireDate, department, profileImage) {
        // Set form action URL
        document.getElementById('editEmployeeForm').action = `/admin/update-employee-profile/${employeeId}`;
        
        // Pre-fill fields
        document.getElementById('edit_employee_id').value = employeeId;
        document.getElementById('edit_employee_name').value = name;
        document.getElementById('edit_gender').value = gender ? gender.toLowerCase() : '';
        document.getElementById('edit_hire_date').value = hireDate || '';
        document.getElementById('edit_department').value = department || '';
        
        // Handle current profile image
        const currentImagePreview = document.getElementById('current_image_preview');
        
        if (profileImage) {
            // Show actual image if available
            currentImagePreview.innerHTML = `
                <div class="flex justify-center">
                    <img src="/storage/${profileImage}" alt="Current profile"
                        class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                </div>
            `;
        } else {
            // Show default SVG if no image
            currentImagePreview.innerHTML = getDefaultUserSVG();
        }
        
        // Reset new image preview
        document.getElementById('new_image_preview').classList.add('hidden');
        document.getElementById('edit_profile_image').value = '';
        
        // Show modal
        document.getElementById('editEmployeeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close the edit modal
     */
    function closeEditModal() {
        document.getElementById('editEmployeeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    /**
     * Preview new image before upload
     */
    function previewNewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('new_image').src = e.target.result;
                document.getElementById('new_image_preview').classList.remove('hidden');
                
                // Optionally hide the current image preview when new image is selected
                // Uncomment the line below if you want to hide the current image
                // document.getElementById('current_image_preview').innerHTML = '';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('new_image_preview').classList.add('hidden');
        }
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });

    // Close modal on outside click
    document.getElementById('editEmployeeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>

    {{-- Role Management Modal --}}
    <div id="roleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4 pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Update Employee Role</h3>
                <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <p id="roleModalName" class="text-sm text-gray-600 mb-4 font-medium"></p>

            <form id="roleForm" method="GET">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="roleSelect" name="role" required
                                onchange="toggleHeadsDept(this.value)"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300">
                            <option value="user">User (Regular Employee)</option>
                            <option value="assessor">Assessor (Head of Department)</option>
                            <option value="managing_partner">Managing Partner</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div id="headsDeptDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Heads Department <span class="text-red-500">*</span>
                        </label>
                        <select name="heads_department" id="headsDeptSelect"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            This person will assess all leave requests from this department.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 justify-end mt-6 pt-4 border-t">
                    <button type="button" onclick="closeRoleModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md text-sm font-semibold hover:bg-purple-700">
                        Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.employee-tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.employee-tab').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Set active state on selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');
        }
    </script>
    @endpush    

    <script>
        function openRoleModal(userId, userName, currentRole, currentDept) {
            document.getElementById('roleModalName').textContent = 'Employee: ' + userName;
            document.getElementById('roleForm').action = '/admin/manage-employee/role/' + userId;
            document.getElementById('roleSelect').value = currentRole;
            document.getElementById('headsDeptSelect').value = currentDept;
            toggleHeadsDept(currentRole);
            document.getElementById('roleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeRoleModal() {
            document.getElementById('roleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        function toggleHeadsDept(role) {
            const div = document.getElementById('headsDeptDiv');
            if (role === 'assessor') {
                div.classList.remove('hidden');
                document.getElementById('headsDeptSelect').required = true;
            } else {
                div.classList.add('hidden');
                document.getElementById('headsDeptSelect').required = false;
            }
        }
        document.getElementById('roleModal').addEventListener('click', function(e) {
            if (e.target === this) closeRoleModal();
        });
    </script>
</x-app-layout>