@props(['request'])

@php
    $status = $request->status;
    $assessment = $request->assessment_status;
    $mp = $request->mp_status;
@endphp

@if(strtolower($status) === 'approved')
    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
        ✓ Approved
    </span>

@elseif(strtolower($status) === 'rejected')
    <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
        ✗ Rejected
    </span>

@elseif($assessment === 'assessed_approved' && $mp === 'mp_approved')
    <span class="px-2 py-0.5 bg-teal-100 text-teal-800 text-xs font-semibold rounded-full">
        🟢 MP Approved — Awaiting Admin
    </span>

@elseif($assessment === 'assessed_approved' && is_null($mp))
    {{-- Check if this is a HOD application needing MP review --}}
    @if(in_array($request->employee->user->role, ['assessor']))
        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
            🟡 Assessed — Awaiting MP Review
        </span>
    @else
        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
            🟡 Assessed — Awaiting Admin
        </span>
    @endif

@elseif(is_null($assessment))
    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
        ⏳ Pending Assessment
    </span>

@else
    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
        {{ ucfirst($status) }}
    </span>
@endif
