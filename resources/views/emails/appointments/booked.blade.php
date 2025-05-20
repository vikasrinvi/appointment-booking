<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmation</title>
</head>
<body>
    <h2>Appointment Confirmation</h2>
    
    <p>Dear {{ $recipientName }},</p>
    
    <p>Your appointment titled "{{ $appointment->title }}" has been successfully booked.</p>
    
    <h3>Appointment Details:</h3>
    <ul>
        <li><strong>Title:</strong> {{ $appointment->title }}</li>
        <li><strong>Date & Time:</strong> {{ $appointment->formatted_start_time }} ({{ $appointment->timezone }})</li>
        @if($appointment->description)
            <li><strong>Description:</strong> {{ $appointment->description }}</li>
        @endif
    </ul>

    @if($appointment->guests->count() > 0)
        <h3>Guests:</h3>
        <ul>
            @foreach($appointment->guests as $guest)
                <li>{{ $guest->name ?? $guest->email }}</li>
            @endforeach
        </ul>
    @endif

    <p>Thank you for using our appointment booking system!</p>
</body>
</html> 