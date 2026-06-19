<?php
date_default_timezone_set('Asia/Kolkata');

// Database connection
include("connection.php");

echo "<h2>FIXING DATABASE INCONSISTENCIES</h2>";

// First, get valid doctor IDs
$doctors = $database->query("SELECT docid FROM doctor");
$validDoctorIds = [];
while($row = $doctors->fetch_assoc()) {
    $validDoctorIds[] = $row['docid'];
}

echo "<p><strong>Valid Doctor IDs:</strong> " . implode(", ", $validDoctorIds) . "</p>";

if(empty($validDoctorIds)) {
    echo "<p style='color:red;'><strong>ERROR: No doctors found in database!</strong></p>";
} else {
    // Update all schedules with invalid docid to use the first valid doctor
    $validDocId = $validDoctorIds[0];
    
    // Check schedules with invalid docid
    $invalidSchedules = $database->query("SELECT scheduleid, docid FROM schedule WHERE docid NOT IN (" . implode(",", $validDoctorIds) . ")");
    
    if($invalidSchedules->num_rows > 0) {
        echo "<p><strong>Found " . $invalidSchedules->num_rows . " schedules with invalid doctor IDs. Fixing...</strong></p>";
        
        $update = $database->query("UPDATE schedule SET docid = $validDocId WHERE docid NOT IN (" . implode(",", $validDoctorIds) . ")");
        
        if($update) {
            echo "<p style='color:green;'><strong>✓ Updated " . $database->affected_rows . " schedules to use valid doctor ID: " . $validDocId . "</strong></p>";
        } else {
            echo "<p style='color:red;'><strong>Error updating schedules: " . $database->error . "</strong></p>";
        }
    } else {
        echo "<p><strong>All schedules have valid doctor IDs.</strong></p>";
    }
    
    // Now verify the upcoming appointments/sessions
    $today = date('Y-m-d');
    $nextweek = date('Y-m-d', strtotime('+1 week'));
    
    echo "<h3>Verified Upcoming Sessions and Appointments:</h3>";
    
    // Sessions
    $sessions = $database->query("SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime FROM schedule INNER JOIN doctor ON CAST(schedule.docid AS UNSIGNED)=doctor.docid WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' ORDER BY schedule.scheduledate DESC");
    
    echo "<h4>Upcoming Sessions:</h4>";
    echo "<p>Count: " . $sessions->num_rows . "</p>";
    if($sessions->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color:#ccc;'><th>Session ID</th><th>Title</th><th>Doctor</th><th>Date</th><th>Time</th></tr>";
        while($row = $sessions->fetch_assoc()) {
            echo "<tr><td>" . $row['scheduleid'] . "</td><td>" . $row['title'] . "</td><td>" . $row['docname'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['scheduletime'] . "</td></tr>";
        }
        echo "</table>";
    }
    
    // Appointments
    $appointments = $database->query("SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum FROM schedule INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN doctor ON CAST(schedule.docid AS UNSIGNED)=doctor.docid WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' ORDER BY schedule.scheduledate DESC");
    
    echo "<h4>Upcoming Appointments:</h4>";
    echo "<p>Count: " . $appointments->num_rows . "</p>";
    if($appointments->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color:#ccc;'><th>Appt ID</th><th>Patient</th><th>Doctor</th><th>Session Title</th><th>Date</th><th>Appt Num</th></tr>";
        while($row = $appointments->fetch_assoc()) {
            echo "<tr><td>" . $row['appoid'] . "</td><td>" . $row['pname'] . "</td><td>" . $row['docname'] . "</td><td>" . $row['title'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['apponum'] . "</td></tr>";
        }
        echo "</table>";
    }
}

echo "<h3><a href='admin/index.php' style='color:blue;'>← Go to Admin Dashboard</a></h3>";

$database->close();
?>
