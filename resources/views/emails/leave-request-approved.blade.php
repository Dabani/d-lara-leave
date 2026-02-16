<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Approved</title>
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
            background-color: #10b981;
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
        .success-icon {
            text-align: center;
            font-size: 48px;
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: white;
        }
        .info-table td {
            padding: 12px;
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
        .badge-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        .highlight-box {
            background-color: #d1fae5;
            padding: 20px;
            border-left: 4px solid #10b981;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Leave Request Approved</h1>
    </div>
    
    <div class="content">
        <div class="success-icon">✓</div>
        
        <p>Dear {{ $mailData['employee_name'] }},</p>
        
        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #065f46;">Good News!</h3>
            <p style="margin-bottom: 0;">
                Your leave request has been <span class="badge badge-approved">APPROVED</span>. 
                You can proceed with your leave as planned.
            </p>
        </div>
        
        <h3>Leave Details:</h3>
        <table class="info-table">
            <tr>
                <td>Leave Type:</td>
                <td><strong>{{ $mailData['leave_type'] }}</strong></td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{{ \Carbon\Carbon::parse($mailData['leave_from'])->format('F d, Y (l)') }}</td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>{{ \Carbon\Carbon::parse($mailData['leave_to'])->format('F d, Y (l)') }}</td>
            </tr>
            <tr>
                <td>Duration:</td>
                <td>
                    @if(isset($mailData['duration']))
                        <strong>{{ $mailData['duration'] }} days</strong>
                    @else
                        <strong>{{ \Carbon\Carbon::parse($mailData['leave_from'])->diffInDays(\Carbon\Carbon::parse($mailData['leave_to'])) + 1 }} days</strong>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Status:</td>
                <td><span class="badge badge-approved">Approved</span></td>
            </tr>
        </table>

        <h3>Important Reminders:</h3>
        <ul>
            <li><strong>Prepare for handover:</strong> Ensure all pending tasks are delegated or completed before your leave</li>
            <li><strong>Set out-of-office:</strong> Update your email auto-responder and voicemail</li>
            <li><strong>Emergency contact:</strong> Leave contact information if you need to be reached urgently</li>
            @if($mailData['leave_type'] === 'Sick Leave')
                <li><strong>Medical certificate:</strong> Keep your medical certificate for records</li>
            @endif
            @if($mailData['leave_type'] === 'Annual Leave')
                <li><strong>Enjoy your time off:</strong> Make the most of your well-deserved break!</li>
            @endif
        </ul>

        <p style="margin-top: 30px;">
            Have a great time during your leave! We look forward to having you back.
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} B-KELANA INTERNATIONAL Leave Management System. All rights reserved.</p>
    </div>
</body>
</html>
