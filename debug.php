<?php
date_default_timezone_set('Asia/Kolkata');

// Database connection
include("connection.php");

$today = date('Y-m-d');
$nextweek = date('Y-m-d', strtotime('+1 week'));

echo "<h2>DEBUG INFORMATION</h2>";
echo "<p><strong>Today's Date:</strong> $today</p>";
echo "<p><strong>Next Week Date:</strong> $nextweek</p>";

echo "<h3>Database Records:</h3>";

// Check schedule records
echo "<h4>All Schedule Records:</h4>";
$schedules = $database->query("SELECT * FROM schedule");
echo "<p>Total schedules: " . $schedules->num_rows . "</p>";
echo "<table border='1'><tr><th>scheduleid</th><th>docid</th><th>title</th><th>scheduledate</th><th>scheduletime</th></tr>";
while($row = $schedules->fetch_assoc()) {
    echo "<tr><td>" . $row['scheduleid'] . "</td><td>" . $row['docid'] . "</td><td>" . $row['title'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['scheduletime'] . "</td></tr>";
}
echo "</table>";

// Check if upcoming schedules exist
echo "<h4>Upcoming Schedules (within 7 days - filtered query):</h4>";
$upcoming = $database->query("SELECT * FROM schedule WHERE scheduledate >= '$today' AND scheduledate <= '$nextweek' ORDER BY scheduledate DESC");
echo "<p>Upcoming schedules: " . $upcoming->num_rows . "</p>";
if($upcoming->num_rows > 0) {
    echo "<table border='1'><tr><th>scheduleid</th><th>docid</th><th>title</th><th>scheduledate</th><th>scheduletime</th></tr>";
    while($row = $upcoming->fetch_assoc()) {
        echo "<tr><td>" . $row['scheduleid'] . "</td><td>" . $row['docid'] . "</td><td>" . $row['title'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['scheduletime'] . "</td></tr>";
    }
    echo "</table>";
}

// Check appointment records
echo "<h4>All Appointment Records:</h4>";
$appointments = $database->query("SELECT * FROM appointment");
echo "<p>Total appointments: " . $appointments->num_rows . "</p>";

// Check doctors
echo "<h4>All Doctors:</h4>";
$doctors = $database->query("SELECT * FROM doctor");
echo "<p>Total doctors: " . $doctors->num_rows . "</p>";
while($row = $doctors->fetch_assoc()) {
    echo "<p>Doctor ID: " . $row['docid'] . " - Name: " . $row['docname'] . "</p>";
}

// Test the JOIN query with CAST
echo "<h3>Testing JOIN Query with CAST:</h3>";
$testQuery = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime FROM schedule INNER JOIN doctor ON CAST(schedule.docid AS UNSIGNED)=doctor.docid WHERE schedule.scheduledate>='$today' AND schedule.scheduledate<='$nextweek' ORDER BY schedule.scheduledate DESC";
echo "<p><strong>Query:</strong> $testQuery</p>";

$result = $database->query($testQuery);
if($result) {
    echo "<p>Result rows: " . $result->num_rows . "</p>";
    if($result->num_rows > 0) {
        echo "<table border='1'><tr><th>Session ID</th><th>Title</th><th>Doctor</th><th>Date</th><th>Time</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['scheduleid'] . "</td><td>" . $row['title'] . "</td><td>" . $row['docname'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['scheduletime'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p><strong style='color:red;'>No results from JOIN query</strong></p>";
    }
} else {
    echo "<p><strong style='color:red;'>Query Error: " . $database->error . "</strong></p>";
}

echo "<h3>Testing Appointments JOIN Query:</h3>";
$testQueryAppt = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum FROM schedule INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN doctor ON CAST(schedule.docid AS UNSIGNED)=doctor.docid WHERE schedule.scheduledate>='$today' AND schedule.scheduledate<='$nextweek' ORDER BY schedule.scheduledate DESC";

$result2 = $database->query($testQueryAppt);
if($result2) {
    echo "<p>Appointment Result rows: " . $result2->num_rows . "</p>";
    if($result2->num_rows > 0) {
        echo "<table border='1'><tr><th>Appt ID</th><th>Patient</th><th>Doctor</th><th>Title</th><th>Date</th><th>Appt Num</th></tr>";
        while($row = $result2->fetch_assoc()) {
            echo "<tr><td>" . $row['appoid'] . "</td><td>" . $row['pname'] . "</td><td>" . $row['docname'] . "</td><td>" . $row['title'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['apponum'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p><strong style='color:red;'>No results from Appointments JOIN query</strong></p>";
    }
} else {
    echo "<p><strong style='color:red;'>Query Error: " . $database->error . "</strong></p>";
}

$database->close();
?>
