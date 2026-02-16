<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
{{--                <div class="p-6 text-gray-900">--}}
{{--                    {{ __("You're logged in!") }}--}}
{{--                </div>--}}

            </div>
        </div>
    </div>


    <div class="container mx-auto py-8 px-4">
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.manage-employee') }}" class="card bg-white shadow-md rounded-lg overflow-hidden transition-transform hover:translate-y-1 hover:shadow-lg">
                <div class="card-content p-4">
                    <h2 class="text-lg font-semibold">Manage Employee</h2>
                    <p class="text-gray-600">You have {{ $pendingReq }} pending request(s)</p>
                </div>
            </a>
            <a href="{{route('admin.manage-leave')}}" class="card bg-white shadow-md rounded-lg overflow-hidden transition-transform hover:translate-y-1 hover:shadow-lg">
                <div class="card-content p-4">
                    <h2 class="text-lg font-semibold">Manage Leave</h2>
                    <p class="text-gray-600">You have {{ $leaveRequests }} pending request(s)</p>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
