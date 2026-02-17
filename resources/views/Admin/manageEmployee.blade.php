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
                                        <form id="approve-form-{{ $pendingUser->id }}" action="{{ route('admin.approve-employee', $pendingUser->id) }}" method="POST" class="mt-3">
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
                                            
                                            <button onclick="return validateAndSubmit({{ $pendingUser->id }});" 
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
                                        <td colspan="7" class="text-center py-4">No pending request</td>
                                    </tr>
                                @endif
                                @foreach($pendingUsers as $pendingUser)
                                    {{-- Hidden form for this row --}}
                                    <form id="approve-form-{{ $pendingUser->id }}" action="{{ route('admin.approve-employee', $pendingUser->id) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-4 py-2 text-center">{{ ($pendingUsers->currentPage()-1) * $pendingUsers->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{ $pendingUser->name }}</td>
                                        <td class="px-4 py-2">{{ $pendingUser->email }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <select name="gender" 
                                                    form="approve-form-{{ $pendingUser->id }}"
                                                    required 
                                                    class="p-1 border border-gray-300 rounded-md text-sm w-28" 
                                                    title="Select Gender">
                                                <option value="">Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="date" 
                                                name="hire_date" 
                                                form="approve-form-{{ $pendingUser->id }}"
                                                required 
                                                max="{{ date('Y-m-d') }}"
                                                class="p-1 border border-gray-300 rounded-md text-sm w-36" 
                                                title="Hire Date">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <select name="department" 
                                                    form="approve-form-{{ $pendingUser->id }}"
                                                    required 
                                                    class="p-1 border border-gray-300 rounded-md text-sm">
                                                <option value="">Select</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button onclick="return validateAndSubmit({{ $pendingUser->id }});" 
                                                    type="button"
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
                    </div>

                    <div class="mt-4">
                        {{ $pendingUsers->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Employees -->
    <div class="py-6">
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
                                <div class="text-center py-4 text-gray-500">No active employees</div>
                            @else
                                @foreach($activeEmployees as $employee)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Name:</span>
                                            <span class="text-gray-900">{{ $employee->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Email:</span>
                                            <span class="text-gray-900 text-sm">{{ $employee->email }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Gender:</span>
                                            <span class="text-gray-900">{{ ucfirst($employee->gender ?? 'Not set') }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Department:</span>
                                            <span class="text-gray-900">{{ $employee->employee->department }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Hire Date:</span>
                                            <span class="text-gray-900">{{ $employee->employee->hire_date ? \Carbon\Carbon::parse($employee->employee->hire_date)->format('M d, Y') : 'Not set' }}</span>
                                        </div>
                                        <div class="flex gap-2 mt-3">
                                            <button onclick="openEditModal({{ $employee->employee->id }}, '{{ $employee->name }}', '{{ $employee->employee->department }}', '{{ $employee->gender ?? '' }}', '{{ $employee->employee->hire_date ?? '' }}')" 
                                                    style="background-color:#3773B8" 
                                                    class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                Edit
                                            </button>
                                            <a href="{{ route('admin.block-employee', $employee->id) }}" 
                                               onclick="return confirm('Are you sure you want to block {{ $employee->name }}?')" 
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
                                        <td class="px-4 py-2">{{ $employee->name }}</td>
                                        <td class="px-4 py-2">{{ $employee->email }}</td>
                                        <td class="px-4 py-2 text-center">{{ ucfirst($employee->gender ?? 'Not set') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{ $employee->employee->hire_date ? \Carbon\Carbon::parse($employee->employee->hire_date)->format('M d, Y') : 'Not set' }}
                                        </td>
                                        <td class="px-4 py-2 text-center">{{ $employee->employee->department }}</td>
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

                                                <button onclick="openEditModal({{ $employee->employee->id }}, '{{ $employee->name }}', '{{ $employee->employee->department }}', '{{ $employee->gender ?? '' }}', '{{ $employee->employee->hire_date ?? '' }}')" 
                                                        style="background-color:#3773B8" 
                                                        class="text-white font-bold py-2 px-4 rounded hover:opacity-90 transition">
                                                    Edit
                                                </button>
                                                <a href="{{ route('admin.block-employee', $employee->id) }}" 
                                                   onclick="return confirm('Are you sure you want to block {{ $employee->name }}?')" 
                                                   class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                    Block
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $activeEmployees->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocked Employees -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="mb-4">
                        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                            {{ __('Blocked Employees') }}
                        </h2>
                    </div>

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
                                            <span class="text-gray-900">{{ $employee->name }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Email:</span>
                                            <span class="text-gray-900 text-sm">{{ $employee->email }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700">Department:</span>
                                            <span class="text-gray-900">{{ $employee->employee->department }}</span>
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('admin.unblock-employee', $employee->id) }}" 
                                               onclick="return confirm('Are you sure you want to unblock {{ $employee->name }}?')" 
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
                                        <td class="px-4 py-2">{{ $employee->name }}</td>
                                        <td class="px-4 py-2">{{ $employee->email }}</td>
                                        <td class="px-4 py-2 text-center">{{ $employee->employee->department }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('admin.unblock-employee', $employee->id) }}" 
                                               onclick="return confirm('Are you sure you want to unblock {{ $employee->name }}?')" 
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
                    </div>

                    <div class="mt-4">
                        {{ $blockedEmployees->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                {{-- HEADER --}}
                <div class="flex justify-between items-center pb-3 mb-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Employee Profile</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">
                        &times;
                    </button>
                </div>

                {{-- FORM --}}
                <form id="editEmployeeForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- FORM CONTENT --}}
                    <div class="space-y-4">
                        <div>
                            <label for="employeeName" class="block text-gray-700 text-sm font-bold mb-2">Employee Name</label>
                            <input type="text" 
                                id="employeeName" 
                                readonly 
                                class="w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="editGender" class="block text-gray-700 text-sm font-bold mb-2">Gender</label>
                                <select name="gender" 
                                    id="editGender" 
                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="editHireDate" class="block text-gray-700 text-sm font-bold mb-2">Hire Date</label>
                                <input type="date" 
                                    name="hire_date" 
                                    id="editHireDate" 
                                    max="{{ date('Y-m-d') }}"
                                    class="w-full p-2 border border-gray-300 rounded-md text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="editDepartment" class="block text-gray-700 text-sm font-bold mb-2">Department</label>
                            <select name="department" 
                                id="editDepartment" 
                                class="w-full p-2 border border-gray-300 rounded-md text-sm">
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

        function validateAndSubmit(userId) {
            // Get the form
            const form = document.getElementById('approve-form-' + userId);
            
            if (!form) {
                console.error('Form not found for user ID:', userId);
                alert('Error: Form not found. Please refresh the page and try again.');
                return false;
            }
            
            // Get form elements using the form attribute
            const genderSelect = document.querySelector('select[name="gender"][form="approve-form-' + userId + '"]') || 
                                form.querySelector('select[name="gender"]');
            const hireDateInput = document.querySelector('input[name="hire_date"][form="approve-form-' + userId + '"]') || 
                                 form.querySelector('input[name="hire_date"]');
            const departmentSelect = document.querySelector('select[name="department"][form="approve-form-' + userId + '"]') || 
                                    form.querySelector('select[name="department"]');
            
            // Check if elements exist
            if (!genderSelect || !hireDateInput || !departmentSelect) {
                console.error('Required form elements not found');
                console.error('Gender:', genderSelect);
                console.error('HireDate:', hireDateInput);
                console.error('Department:', departmentSelect);
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
                // Submit the form
                form.submit();
                return true;
            }
            
            return false;
        }
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