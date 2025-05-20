<!DOCTYPE html>
<html>
<head>
    <title>Appointment Reminder</title>
</head>
<body>
    <h2>Appointment Reminder</h2>
    
    <p>Dear {{ $recipientName }},</p>
    
    <p>This is a reminder for your upcoming appointment.</p>
    
    <h3>Appointment Details:</h3>
    <ul>
        <li><strong>Title:</strong> {{ $appointment->title }}</li>
        <li><strong>Date & Time:</strong> {{ $appointment->start_time->format('F j, Y g:i A') }} ({{ $appointment->timezone }})</li>
        @if($appointment->description)
            <li><strong>Description:</strong> {{ $appointment->description }}</li>
        @endif
    </ul>

    <p>Please make sure to join on time!</p>
</body>
</html> 