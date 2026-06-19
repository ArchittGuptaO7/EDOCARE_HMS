<?php
session_start();

// Temporarily bypass login for testing
if(!isset($_SESSION["user"])){
    $_SESSION["user"] = "admin@edoc.com";
    $_SESSION["usertype"] = "a";
}

// Database connection
include("connection.php");

date_default_timezone_set('Asia/Kolkata');

$today = date('Y-m-d');
$nextweek = date('Y-m-d', strtotime('+1 week'));

echo "<h2>ADMIN DASHBOARD TEST - Checking Appointments & Sessions Display</h2>";
echo "<p><strong>Today's Date:</strong> $today</p>";
echo "<p><strong>Next Week Date:</strong> $nextweek</p>";

// Test Query 1: Sessions
echo "<h3>1. Upcoming Sessions Query Test</h3>";
$sqlsessions = "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on CAST(schedule.docid AS UNSIGNED)=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";

echo "<p><strong>Query:</strong> $sqlsessions</p>";
$result_sessions = $database->query($sqlsessions);

if($result_sessions === false) {
    echo "<p style='color:red;'><strong>ERROR:</strong> " . $database->error . "</p>";
} else {
    echo "<p><strong>Result count: " . $result_sessions->num_rows . "</strong></p>";
    
    if($result_sessions->num_rows == 0){
        echo "<p style='color:orange;'>No sessions found - showing \"not found\" message</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color:#ccc;'><th>Title</th><th>Doctor</th><th>Date</th><th>Time</th></tr>";
        while($row = $result_sessions->fetch_assoc()){
            echo "<tr>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['docname'] . "</td>";
            echo "<td>" . $row['scheduledate'] . "</td>";
            echo "<td>" . $row['scheduletime'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Test Query 2: Appointments
echo "<h3>2. Upcoming Appointments Query Test</h3>";
$sqlappts = "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on CAST(schedule.docid AS UNSIGNED)=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";

echo "<p><strong>Query:</strong> $sqlappts</p>";
$result_appts = $database->query($sqlappts);

if($result_appts === false) {
    echo "<p style='color:red;'><strong>ERROR:</strong> " . $database->error . "</p>";
} else {
    echo "<p><strong>Result count: " . $result_appts->num_rows . "</strong></p>";
    
    if($result_appts->num_rows == 0){
        echo "<p style='color:orange;'>No appointments found - showing \"not found\" message</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color:#ccc;'><th>Appt#</th><th>Patient</th><th>Doctor</th><th>Session</th><th>Date</th></tr>";
        while($row = $result_appts->fetch_assoc()){
            echo "<tr>";
            echo "<td>" . $row['apponum'] . "</td>";
            echo "<td>" . $row['pname'] . "</td>";
            echo "<td>" . $row['docname'] . "</td>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['scheduledate'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

echo "<h3><a href='admin/index.php'>Go to Admin Dashboard</a></h3>";

$database->close();
?>
