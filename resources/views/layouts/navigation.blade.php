<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @if(auth()->user()->role === 'user')
                        {{-- Dashboard - Always visible to all users --}}
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        {{-- ONLY SHOW THESE IF EMPLOYEE IS APPROVED --}}
                        @if(auth()->user()->isApprovedEmployee())
                            <x-nav-link :href="route('leave-request.create')" :active="request()->routeIs('leave-request.create')">
                                {{ __('Apply for Leave') }}
                            </x-nav-link>

                            <x-nav-link :href="route('leave-history')" :active="request()->routeIs('leave-history')">
                                {{ __('Leave History') }}
                            </x-nav-link>
                        @endif

                        {{-- My Profile - Always visible to all users --}}
                        <x-nav-link :href="route('user.profile')" :active="request()->routeIs('user.profile')">
                            {{ __('My Profile') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.manage-employee')" :active="request()->routeIs('admin.manage-employee')">
                            {{ __('Manage Employees') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.manage-leave')" :active="request()->routeIs('admin.manage-leave')">
                            {{ __('Manage Leave') }}
                        </x-nav-link>
                    @endif

                    {{-- Assessor / Managing Partner dashboard link --}}
                    @if(auth()->user()->isAssessor() || auth()->user()->isManagingPartner())
                        <x-nav-link :href="route('assessor.dashboard')" :active="request()->routeIs('assessor.*')">
                            <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Assessment
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown (Single dropdown for all users) -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                {{-- Profile Image --}}
                                @if(auth()->user()->employee && auth()->user()->employee->profile_image)
                                    <img src="{{ asset('storage/' . auth()->user()->employee->profile_image) }}" 
                                         alt="Profile" 
                                         class="w-8 h-8 rounded-full object-cover mr-2"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\'%3E%3Crect width=\'32\' height=\'32\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'16\' text-anchor=\'middle\' alignment-baseline=\'middle\' fill=\'%23999\'%3E{{ substr(auth()->user()->name, 0, 1) }}%3C/text%3E%3C/svg%3E';">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                        <span class="text-gray-600 text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                
                                <div>{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- User Info --}}
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            @if(auth()->user()->employee)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ auth()->user()->employee->department ?? 'No Department' }}
                                </div>
                                @if(auth()->user()->employee->hire_date)
                                    <div class="text-xs text-gray-500">
                                        Service: {{ round(auth()->user()->employee->getYearsOfService(), 1) }} years
                                    </div>
                                @endif
                            @endif
                            @if(auth()->user()->isPendingApproval())
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending Approval
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Profile Link based on role --}}
                        @if(auth()->user()->role === 'user')
                            <x-dropdown-link :href="route('user.profile')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                        @elseif(auth()->user()->role === 'admin')
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                        @endif

                        {{-- In the dropdown content section for assessors --}}
@if(auth()->user()->isAssessor())
    <x-dropdown-link :href="route('assessor.dashboard')">
        🏢 Assessment Dashboard
        <span class="ml-1 text-xs text-gray-400">({{ auth()->user()->heads_department }})</span>
    </x-dropdown-link>
    
    {{-- View Profile (read-only) --}}
    <x-dropdown-link :href="route('user.profile')">
        {{ __('View Profile') }}
    </x-dropdown-link>
    
    {{-- Edit Profile (password change) --}}
    <x-dropdown-link :href="route('profile.edit')">
        {{ __('Edit Profile') }}
    </x-dropdown-link>
    
    {{-- Leave History --}}
    <x-dropdown-link :href="route('leave-history')">
        {{ __('Leave History') }}
    </x-dropdown-link>
@endif

@if(auth()->user()->isManagingPartner())
    <x-dropdown-link :href="route('assessor.dashboard')">
        👔 MP Review Dashboard
    </x-dropdown-link>
    
    {{-- View Profile (read-only) --}}
    <x-dropdown-link :href="route('user.profile')">
        {{ __('View Profile') }}
    </x-dropdown-link>
    
    {{-- Edit Profile (password change) --}}
    <x-dropdown-link :href="route('profile.edit')">
        {{ __('Edit Profile') }}
    </x-dropdown-link>
    
    {{-- Leave History --}}
    <x-dropdown-link :href="route('leave-history')">
        {{ __('Leave History') }}
    </x-dropdown-link>
@endif
                        {{-- Logout --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->role === 'user')
                {{-- Dashboard - Always visible --}}
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                {{-- ONLY SHOW THESE IF EMPLOYEE IS APPROVED --}}
                @if(auth()->user()->isApprovedEmployee())
                    <x-responsive-nav-link :href="route('leave-request.create')" :active="request()->routeIs('leave-request.create')">
                        {{ __('Apply for Leave') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('leave-history')" :active="request()->routeIs('leave-history')">
                        {{ __('Leave History') }}
                    </x-responsive-nav-link>
                @endif

                {{-- My Profile - Always visible --}}
                <x-responsive-nav-link :href="route('user.profile')" :active="request()->routeIs('user.profile')">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.manage-employee')" :active="request()->routeIs('admin.manage-employee')">
                    {{ __('Manage Employee') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.manage-leave')" :active="request()->routeIs('admin.manage-leave')">
                    {{ __('Manage Leave') }}
                </x-responsive-nav-link>

                {{-- Admin Profile Link in Mobile --}}
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>
            @endif

            {{-- Assessor / Managing Partner dashboard link --}}
            @if(auth()->user()->isAssessor() || auth()->user()->isManagingPartner())
                <x-responsive-nav-link :href="route('assessor.dashboard')" :active="request()->routeIs('assessor.*')">
                    <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Assessment
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center mb-2">
                    {{-- Profile Image in Mobile --}}
                    @if(auth()->user()->employee && auth()->user()->employee->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->employee->profile_image) }}" 
                             alt="Profile" 
                             class="w-10 h-10 rounded-full object-cover mr-3"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'%3E%3Crect width=\'40\' height=\'40\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'20\' text-anchor=\'middle\' alignment-baseline=\'middle\' fill=\'%23999\'%3E{{ substr(auth()->user()->name, 0, 1) }}%3C/text%3E%3C/svg%3E';">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                            <span class="text-gray-600 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                
                @if(auth()->user()->employee)
                    <div class="text-sm text-gray-600 mt-2">
                        <div>{{ auth()->user()->employee->department ?? 'No Department' }}</div>
                        @if(auth()->user()->employee->hire_date)
                            <div class="text-xs text-gray-500">
                                Service: {{ round(auth()->user()->employee->getYearsOfService(), 1) }} years
                            </div>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->isPendingApproval())
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⏳ Pending Approval
                        </span>
                    </div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                {{-- Profile Link in Mobile --}}
                @if(auth()->user()->role === 'user')
                    <x-responsive-nav-link :href="route('user.profile')">
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
