<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Profile Image Update Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>
                    
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                        {{-- Current Profile Image --}}
                        <div class="flex-shrink-0">
                            @if(auth()->user()->employee && auth()->user()->employee->profile_image)
                                <img src="{{ asset('storage/' . auth()->user()->employee->profile_image) }}" 
                                     alt="Profile" 
                                     id="current-profile-image"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-indigo-100 shadow-lg"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'128\' height=\'128\'%3E%3Crect width=\'128\' height=\'128\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'48\' text-anchor=\'middle\' alignment-baseline=\'middle\' fill=\'%23999\'%3E{{ substr(auth()->user()->name, 0, 1) }}%3C/text%3E%3C/svg%3E';">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center border-4 border-indigo-100 shadow-lg" id="current-profile-image">
                                    <span class="text-white text-5xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Upload Form --}}
                        <div class="flex-1 w-full">
                            <form action="{{ route('user.update-profile') }}" method="POST" enctype="multipart/form-data" id="profile-image-form">
                                @csrf
                                @method('POST')
                                
                                <div class="mb-4">
                                    <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        Choose New Profile Picture
                                    </label>
                                    <input type="file" 
                                           name="profile_image" 
                                           id="profile_image" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100
                                                  cursor-pointer"
                                           onchange="previewImage(this)">
                                    <p class="mt-1 text-sm text-gray-500">
                                        Supported formats: JPG, PNG, GIF, WebP (Max 2MB)
                                    </p>
                                    @error('profile_image')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Image Preview --}}
                                <div id="image-preview" class="mb-4 hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview:</label>
                                    <img id="preview-img" src="" alt="Preview" class="w-32 h-32 rounded-full object-cover border-2 border-gray-300">
                                </div>

                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z"/>
                                    </svg>
                                    Upload Profile Picture
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account Information Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                        </div>

                        @if(auth()->user()->employee)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->employee->department ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Gender</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst(auth()->user()->gender ?? 'N/A') }}</p>
                            </div>

                            @if(auth()->user()->employee->hire_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse(auth()->user()->employee->hire_date)->format('F d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Years of Service</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ round(auth()->user()->employee->getYearsOfService(), 1) }} years</p>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Employment Status</label>
                                <p class="mt-1">
                                    @if(auth()->user()->employee->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst(auth()->user()->employee->status) }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>