<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Rejected</title>
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
            background-color: #ef4444;
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
        .badge-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .alert-box {
            background-color: #fee2e2;
            padding: 20px;
            border-left: 4px solid #ef4444;
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
        .action-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Leave Request Update</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $mailData['employee_name'] }},</p>
        
        <div class="alert-box">
            <h3 style="margin-top: 0; color: #991b1b;">Leave Request Not Approved</h3>
            <p style="margin-bottom: 0;">
                We regret to inform you that your leave request has been <span class="badge badge-rejected">REJECTED</span>.
            </p>
        </div>
        
        <h3>Request Details:</h3>
        <table class="info-table">
            <tr>
                <td>Leave Type:</td>
                <td><strong>{{ $mailData['leave_type'] }}</strong></td>
            </tr>
            <tr>
                <td>Requested Period:</td>
                <td>
                    {{ \Carbon\Carbon::parse($mailData['leave_from'])->format('F d, Y') }} - 
                    {{ \Carbon\Carbon::parse($mailData['leave_to'])->format('F d, Y') }}
                </td>
            </tr>
            <tr>
                <td>Duration:</td>
                <td>{{ \Carbon\Carbon::parse($mailData['leave_from'])->diffInDays(\Carbon\Carbon::parse($mailData['leave_to'])) + 1 }} days</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td><span class="badge badge-rejected">Rejected</span></td>
            </tr>
        </table>

        <h3>What You Can Do:</h3>
        <ul>
            <li><strong>Contact your supervisor:</strong> Discuss the reasons for rejection and explore alternatives</li>
            <li><strong>Submit a new request:</strong> You can submit a different leave request with adjusted dates</li>
            <li><strong>Review leave policy:</strong> Ensure your request complies with company leave policies</li>
            <li><strong>Check alternative dates:</strong> Consider requesting leave during less busy periods</li>
        </ul>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/dashboard') }}" class="action-button">Go to Dashboard</a>
        </div>

        <p>
            If you have any questions or would like to discuss this decision, please don't hesitate to reach out to your supervisor or HR department.
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>For assistance, please contact your HR department.</p>
        <p>&copy; {{ date('Y') }} B-KELANA INTERNATIONAL Leave Management System. All rights reserved.</p>
    </div>
</body>
</html>
