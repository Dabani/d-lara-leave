<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #6b7280;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-warning {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        .highlight {
            background-color: #dbeafe;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Leave Request Submitted</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $mailData['employee_name'] }},</p>
        
        <p>Your leave request has been successfully submitted and is now <span class="badge badge-pending">Pending Approval</span>.</p>
        
        <table class="info-table">
            <tr>
                <td>Leave Type:</td>
                <td><strong>{{ $mailData['leave_type'] }}</strong></td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{{ \Carbon\Carbon::parse($mailData['leave_from'])->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>{{ \Carbon\Carbon::parse($mailData['leave_to'])->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Duration:</td>
                <td>
                    {{ \Carbon\Carbon::parse($mailData['leave_from'])->diffInDays(\Carbon\Carbon::parse($mailData['leave_to'])) + 1 }} calendar days
                    @if(isset($mailData['working_days']) && in_array($mailData['leave_type'], ['Annual Leave', 'Paternity Leave']))
                        <br><strong>({{ $mailData['working_days'] }} working days)</strong>
                    @endif
                </td>
            </tr>
            @if(!empty($mailData['reason']))
            <tr>
                <td>Reason:</td>
                <td>{{ $mailData['reason'] }}</td>
            </tr>
            @endif
        </table>

        {{-- Special Notices --}}
        @if($mailData['leave_type'] === 'Annual Leave')
            @php
                $leaveFrom = \Carbon\Carbon::parse($mailData['leave_from']);
                $leaveTo = \Carbon\Carbon::parse($mailData['leave_to']);
                $isInRecommendedPeriod = false;
                
                for ($date = $leaveFrom->copy(); $date->lte($leaveTo); $date->addDay()) {
                    if ($date->month >= 7 && $date->month <= 9) {
                        $isInRecommendedPeriod = true;
                        break;
                    }
                }
            @endphp
            
            @if(!$isInRecommendedPeriod)
                <div class="highlight" style="background-color: #fee2e2; border-left-color: #dc2626;">
                    <strong>⚠️ Notice:</strong> Your annual leave dates fall outside the recommended period of July-September. 
                    This may affect approval priority.
                </div>
            @endif
        @endif

        @if($mailData['leave_type'] === 'Sick Leave')
            <div class="highlight">
                <strong>📋 Important:</strong> Your sick leave request requires a medical certificate. 
                Please ensure it has been uploaded with your application.
            </div>
        @endif

        <p style="margin-top: 30px;">
            You will receive a notification once your request has been reviewed by management.
        </p>

        <p>
            <strong>What's next?</strong>
            <ul>
                <li>Your request will be reviewed by your supervisor</li>
                <li>You'll receive an email notification when a decision is made</li>
                <li>You can track your request status in the employee dashboard</li>
            </ul>
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} B-KELANA INTERNATIONAL Leave Management System. All rights reserved.</p>
    </div>
</body>
</html>