<!DOCTYPE html>
<html>
<head>
    <title>Appointment Cancellation</title>
</head>
<body>
    <h2>Appointment Cancellation</h2>
    
    <p>Dear {{ $recipientName }},</p>
    
    <p>The appointment titled "{{ $appointment->title }}" scheduled for {{ $appointment->start_time->format('F j, Y g:i A') }} ({{ $appointment->timezone }}) has been cancelled.</p>
    
    @if($appointment->description)
        <p><strong>Description:</strong> {{ $appointment->description }}</p>
    @endif

    <p>We regret any inconvenience caused.</p>
    
    <p>Thank you for your understanding.</p>
</body>
</html> 