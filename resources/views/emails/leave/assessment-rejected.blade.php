@component('mail::message')
# Leave Request Rejected

Dear **{{ $employee_name }}**,

We regret to inform you that your **{{ $leave_type }}** request
({{ \Carbon\Carbon::parse($leave_from)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($leave_to)->format('M d, Y') }})
has been **rejected** by your Head of Department.

## Reason
{{ $reason }}

@if(!empty($suggestion))
## Suggested Way Forward
{{ $suggestion }}
@endif

If you have questions, please speak with **{{ $assessor_name }}** or HR.

@component('mail::button', ['url' => url('/dashboard')])
View Your Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent