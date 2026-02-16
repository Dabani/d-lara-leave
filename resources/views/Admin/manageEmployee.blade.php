<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Employee') }}
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
    <div class="py-4">
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
    </div>

    <!-- Pending Requests -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="mb-4">
                        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                            {{ __('Pending Request') }}
                        </h2>
                    </div>

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
                                            
                                            {{-- Gender Selection --}}
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
                                            
                                            {{-- Hire Date Input --}}
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
                                            
                                            {{-- Department Selection --}}
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
                                            
                                            <button onclick="validateAndSubmit({{ $pendingUser->id }}); return false;" 
                                                    type="button"
                                                    style="background-color: #68D391" 
                                                    class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                Approve
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
                                        <td colspan="7" class="text-center py-4">No pending request</td>  {{-- UPDATE colspan to 7 --}}
                                    </tr>
                                @endif
                                @foreach($pendingUsers as $pendingUser)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2 text-center">{{ ($pendingUsers->currentPage()-1) * $pendingUsers->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{ $pendingUser->name }}</td>
                                        <td class="px-4 py-2">{{ $pendingUser->email }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <form id="approve-form-{{ $pendingUser->id }}" action="{{ route('admin.approve-employee', $pendingUser->id) }}" method="POST" class="inline">
                                                @csrf
                                                {{-- Gender Selection --}}
                                                <select name="gender" required class="p-1 border border-gray-300 rounded-md text-sm w-28" title="Select Gender">
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
                                                    class="p-1 border border-gray-300 rounded-md text-sm w-36" 
                                                    title="Hire Date">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                                {{-- Department Selection --}}
                                                <select name="department" required class="p-1 border border-gray-300 rounded-md text-sm">
                                                    <option value="">Select</option>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button onclick="validateAndSubmit({{ $pendingUser->id }})" 
                                                    style="background-color: #68D391" 
                                                    class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                Approve
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $pendingUsers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Employees -->
    <div class="pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="mb-4">
                        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                            {{ __('Active Employees') }}
                        </h2>
                    </div>

                    <div class="bg-white rounded-lg">
                        <!-- Mobile View -->
                        <div class="block sm:hidden space-y-4">
                            @if($activeEmployees->isEmpty())
                                <div class="text-center py-4 text-gray-500">No Active Employee</div>
                            @else
                                @foreach($activeEmployees as $activeEmployee)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center mb-3">                                            
                                            @if($activeEmployee->employee && $activeEmployee->employee->profile_image)
                                                <img src="{{ asset('storage/' . $activeEmployee->employee->profile_image) }}" 
                                                    alt="Profile" 
                                                    class="w-12 h-12 rounded-full mr-3 object-cover"
                                                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'48\' text-anchor=\'middle\' alignment-baseline=\'middle\' fill=\'%23999\'%3E{{ substr(auth()->user()->name, 0, 1) }}%3C/text%3E%3C/svg%3E';">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 text-3xl font-bold">{{ substr($activeEmployee->name ?? 'U', 0, 1) }}</span>
                                                </div>
                                            @endif                                             
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $activeEmployee->name }}</div>
                                                <div class="text-sm text-gray-600">{{ $activeEmployee->email }}</div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Department:</span>
                                            <span class="text-gray-900">{{ $activeEmployee->employee->department ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex gap-2 mt-3">
                                            <button onclick="openEditModal({{ $activeEmployee->employee->id }}, '{{ $activeEmployee->name }}', '{{ $activeEmployee->employee->department ?? '' }}')" 
                                                    style="background-color:#3773B8" 
                                                    class="flex-1 text-center text-white font-bold py-2 px-4 rounded">
                                                Edit
                                            </button>
                                            <a href="{{ route('admin.block-employee', $activeEmployee->id) }}" 
                                               style="background-color:#ca2323" 
                                               class="flex-1 text-center text-white font-bold py-2 px-4 rounded">
                                                Block
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden sm:block overflow-x-auto">
                            <!-- Active Employees - Desktop Table -->
                            <table class="w-full table-auto">
                                <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 text-center">#Sl No</th>
                                    <th class="px-4 py-2 text-center">Profile</th>
                                    <th class="px-4 py-2 text-start">Name</th>
                                    <th class="px-4 py-2 text-start">Email</th>
                                    <th class="px-4 py-2 text-center">Gender</th>
                                    <th class="px-4 py-2 text-center">Hire Date</th>
                                    <th class="px-4 py-2 text-center">Service Years</th>
                                    <th class="px-4 py-2 text-center">Department</th>
                                    <th class="px-4 py-2 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($activeEmployees as $activeEmployee)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2 text-center">{{ ($activeEmployees->currentPage()-1) * $activeEmployees->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($activeEmployee->employee && $activeEmployee->employee->profile_image)
                                                {{-- FIX: Use asset helper properly --}}
                                                <img src="{{ asset('storage/' . $activeEmployee->employee->profile_image) }}" 
                                                    alt="Profile" 
                                                    class="w-10 h-10 rounded-full object-cover mx-auto"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mx-auto">
                                                    <span class="text-gray-600 font-bold">{{ substr($activeEmployee->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $activeEmployee->name }}</td>
                                        <td class="px-4 py-2">{{ $activeEmployee->email }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($activeEmployee->gender)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $activeEmployee->gender === 'male' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $activeEmployee->gender === 'female' ? 'bg-pink-100 text-pink-800' : '' }}
                                                    {{ $activeEmployee->gender === 'other' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                    {{ ucfirst($activeEmployee->gender) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">Not set</span>
                                            @endif
                                        </td>
                                        {{-- Hire Date Display --}}
                                        <td class="px-4 py-2 text-center text-sm">
                                            @if($activeEmployee->employee && $activeEmployee->employee->hire_date)
                                                {{ \Carbon\Carbon::parse($activeEmployee->employee->hire_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">Not set</span>
                                            @endif
                                        </td>
                                        {{-- Service Years Display --}}
                                        <td class="px-4 py-2 text-center">
                                            @if($activeEmployee->employee && $activeEmployee->employee->hire_date)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ round($activeEmployee->employee->getYearsOfService(), 1) }} years
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">{{ $activeEmployee->employee->department ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-center space-x-2">
                                            <button onclick="openEditModal({{ $activeEmployee->employee->id }}, '{{ $activeEmployee->name }}', '{{ $activeEmployee->employee->department }}', '{{ $activeEmployee->gender }}', '{{ $activeEmployee->employee->hire_date }}')" 
                                                    style="background-color:#3773B8" 
                                                    class="text-white font-bold py-2 px-3 rounded text-sm hover:opacity-90 transition">
                                                Edit
                                            </button>
                                            <a href="{{ route('admin.block-employee', $activeEmployee->id) }}" 
                                            onclick="return confirm('Are you sure you want to block this employee?')"
                                            style="background-color:#cd3952" 
                                            class="inline-block text-white font-bold py-2 px-3 rounded text-sm hover:opacity-90 transition">
                                                Block
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $activeEmployees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocked Employees -->
    <div class="pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="mb-4">
                        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                            {{ __('Blocked Employee') }}
                        </h2>
                    </div>

                    <div class="bg-white rounded-lg">
                        <!-- Mobile View -->
                        <div class="block sm:hidden space-y-4">
                            @if($blockedEmployees->isEmpty())
                                <div class="text-center py-4 text-gray-500">No Blocked Employee</div>
                            @else
                                @foreach($blockedEmployees as $blockedEmployee)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center mb-3">
                                            @if($blockedEmployee->employee && $blockedEmployee->employee->profile_image)
                                                <img src="{{ asset('storage/' . $blockedEmployee->employee->profile_image) }}" 
                                                     alt="Profile" 
                                                     class="w-12 h-12 rounded-full mr-3 object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-full mr-3 bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-bold">{{ substr($blockedEmployee->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $blockedEmployee->name }}</div>
                                                <div class="text-sm text-gray-600">{{ $blockedEmployee->email }}</div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Department:</span>
                                            <span class="text-gray-900">{{ $blockedEmployee->employee->department ?? 'N/A' }}</span>
                                        </div>
                                        <a href="{{ route('admin.unblock-employee', $blockedEmployee->id) }}" 
                                           style="background-color:#68D391" 
                                           class="block text-center text-white font-bold py-2 px-4 rounded mt-3">
                                            Unblock
                                        </a>
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
                                    <th class="px-4 py-2 text-start">Profile</th>
                                    <th class="px-4 py-2 text-start">Name</th>
                                    <th class="px-4 py-2 text-start">Email</th>
                                    <th class="px-4 py-2 text-start">Department</th>
                                    <th class="px-4 py-2 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($blockedEmployees->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No Blocked Employee</td>
                                    </tr>
                                @endif
                                @foreach($blockedEmployees as $blockedEmployee)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2 text-center">{{ ($blockedEmployees->currentPage()-1) * $blockedEmployees->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2">
                                            @if($blockedEmployee->employee && $blockedEmployee->employee->profile_image)
                                                <img src="{{ asset('storage/' . $blockedEmployee->employee->profile_image) }}" 
                                                     alt="Profile" 
                                                     class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-bold">{{ substr($blockedEmployee->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $blockedEmployee->name }}</td>
                                        <td class="px-4 py-2">{{ $blockedEmployee->email }}</td>
                                        <td class="px-4 py-2">{{ $blockedEmployee->employee->department ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('admin.unblock-employee', $blockedEmployee->id) }}" 
                                               style="background-color:#68D391" 
                                               class="text-white font-bold py-2 px-4 rounded">
                                                Unblock
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $blockedEmployees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white my-10">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4 pb-3 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Employee</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="editEmployeeForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- TWO COLUMN LAYOUT --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- LEFT COLUMN --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Employee Name</label>
                                <input type="text" id="employeeName" class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                            </div>

                            <div>
                                <label for="editGender" class="block text-gray-700 text-sm font-bold mb-2">
                                    Gender <span class="text-red-500">*</span>
                                </label>
                                <select name="gender" id="editGender" required class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="editHireDate" class="block text-gray-700 text-sm font-bold mb-2">
                                    Hire Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                    name="hire_date" 
                                    id="editHireDate" 
                                    required 
                                    max="{{ date('Y-m-d') }}"
                                    class="w-full p-2 border border-gray-300 rounded-md">
                                <p class="text-xs text-gray-500 mt-1">Used to calculate annual leave entitlement</p>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN --}}
                        <div class="space-y-4">
                            <div>
                                <label for="editDepartment" class="block text-gray-700 text-sm font-bold mb-2">
                                    Department <span class="text-red-500">*</span>
                                </label>
                                <select name="department" id="editDepartment" required class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="profileImage" class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
                                <input type="file" 
                                    name="profile_image" 
                                    id="profileImage" 
                                    accept="image/*,.webp" 
                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF, WebP (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex gap-3 justify-end mt-6 pt-4 border-t">
                        <button type="button" 
                                onclick="closeEditModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                style="background-color:#3773B8" 
                                class="px-4 py-2 text-white rounded-md hover:opacity-90 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(employeeId, employeeName, currentDepartment, currentGender, currentHireDate) {
            const form = document.getElementById('editEmployeeForm');
            form.action = `/admin/update-employee-profile/${employeeId}`;
            
            document.getElementById('employeeName').value = employeeName;
            document.getElementById('editGender').value = currentGender || '';
            document.getElementById('editHireDate').value = currentHireDate || '';
            document.getElementById('editDepartment').value = currentDepartment;
            
            document.getElementById('editModal').classList.remove('hidden');
            
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editEmployeeForm').reset();
            
            // Re-enable body scroll
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('editModal');
                if (!modal.classList.contains('hidden')) {
                    closeEditModal();
                }
            }
        });
    </script>

    @push('scripts')
    <script>
        function validateAndSubmit(userId) {
            const form = document.getElementById('approve-form-' + userId);
            
            if (!form) {
                console.error('Form not found for user ID:', userId);
                alert('Error: Form not found. Please refresh the page and try again.');
                return false;
            }
            
            const genderSelect = form.querySelector('select[name="gender"]');
            const hireDateInput = form.querySelector('input[name="hire_date"]');
            const departmentSelect = form.querySelector('select[name="department"]');
            
            // Check if elements exist
            if (!genderSelect || !hireDateInput || !departmentSelect) {
                console.error('Required form elements not found');
                alert('Error: Form is incomplete. Please refresh the page.');
                return false;
            }
            
            const gender = genderSelect.value;
            const hireDate = hireDateInput.value;
            const department = departmentSelect.value;
            
            // Validate gender
            if (!gender || gender === '') {
                alert('Please select a gender before approving.');
                genderSelect.focus();
                return false;
            }
            
            // Validate hire date
            if (!hireDate || hireDate === '') {
                alert('Please select a hire date before approving.');
                hireDateInput.focus();
                return false;
            }
            
            // Validate hire date is not in future
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(hireDate);
            selectedDate.setHours(0, 0, 0, 0);
            
            if (selectedDate > today) {
                alert('Hire date cannot be in the future.');
                hireDateInput.focus();
                return false;
            }
            
            // Validate department
            if (!department || department === '') {
                alert('Please select a department before approving.');
                departmentSelect.focus();
                return false;
            }
            
            // Confirm approval
            if (confirm('Are you sure you want to approve this employee?\n\nGender: ' + gender + '\nHire Date: ' + hireDate + '\nDepartment: ' + department)) {
                // Show loading state
                const button = event.target;
                button.disabled = true;
                button.innerHTML = 'Approving...';
                
                // Submit the form
                form.submit();
                return true;
            }
            
            return false;
        }
    </script>
    @endpush
</x-app-layout>