<!DOCTYPE html>
<html>
<head>
    <title>Failure Notification</title>
</head>
<body>


<h2>Backup Job Failure Alert</h2>
<p>The backup job has failed {{ $failures }} times.</p>
<p>Error message:</p>
<pre>{{ $exception->getMessage() }}</pre>

<p>Please investigate the issue.</p>
</body>
</html>
