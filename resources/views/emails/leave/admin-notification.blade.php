@component('mail::message')
# Leave Request Update

Dear **{{ $assessor_name }}**,

A leave request you assessed has been **{{ $action }}** by the Administrator.

| Field | Details |
|-------|---------|
| Employee | {{ $employee_name }} |
| Leave Type | {{ $leave_type }} |
| Period | {{ \Carbon\Carbon::parse($leave_from)->format('M d') }} – {{ \Carbon\Carbon::parse($leave_to)->format('M d, Y') }} |
| Admin Reason | {{ $reason }} |

@component('mail::button', ['url' => url('/assessor/dashboard')])
View Assessor Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
