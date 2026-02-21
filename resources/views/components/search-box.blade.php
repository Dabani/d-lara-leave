{{-- 
    Reusable Search Box Component
    
    Usage:
    <x-search-box 
        route="{{ route('admin.manage-employee') }}" 
        placeholder="Search by name or email..."
        :value="request('search')" />
--}}

@props([
    'route', 
    'placeholder' => 'Search...', 
    'value' => ''
])

<form method="GET" action="{{ $route }}" class="m-0 p-0"> <!-- Remove margins -->
    <div class="flex items-center gap-2">
        <div class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $value }}"
                   placeholder="{{ $placeholder }}"
                   class="w-64 pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[42px]"> <!-- Fixed height -->
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <button type="submit" 
                class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition inline-flex items-center justify-center h-[42px] whitespace-nowrap"> <!-- Fixed height -->
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search
        </button>
        @if($value)
            <a href="{{ $route }}" 
               class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition inline-flex items-center justify-center h-[42px] whitespace-nowrap"> <!-- Fixed height -->
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Clear
            </a>
        @endif
    </div>
</form>
