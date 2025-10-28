<!DOCTYPE html>
<html>
<head>
    <title>New Event Notification</title>
</head>
<body>
    <h2>Hello {{ $company->name }}</h2>

    <p>A new event has been created in your company.</p>

    <p><strong>Event Name:</strong> {{ $event->name }}</p>
    <p><strong>Created By:</strong> {{ $creator->first_name }} {{ $creator->last_name }} ({{ $creator->email }})</p>

    <p>Thank you</p>
    
    
</body>
</html>
