<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.css' />
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/jquery.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/moment.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.js'></script>
</head>
<body>
<div id='calendar'></div>
<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        events: 'load_events.php'
    });
});
</script>
</body>
</html>
