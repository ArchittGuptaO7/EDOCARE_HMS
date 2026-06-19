<?php
include("connection.php");

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

echo "<h2>CREATING UPCOMING SCHEDULES</h2>";

// Delete old schedules that are outside range but keep appointments
$database->query("DELETE FROM schedule WHERE scheduledate NOT BETWEEN '2026-04-02' AND '2026-04-30'");

// Create fresh upcoming schedules for the next 7 days
$schedules = [
    [
        'date' => date('Y-m-d', strtotime('+1 day')),
        'time' => '10:00:00',
        'title' => 'General Check-up',
        'docid' => 1,
        'nop' => 5
    ],
    [
        'date' => date('Y-m-d', strtotime('+2 days')),
        'time' => '14:30:00',
        'title' => 'Specialist Consultation',
        'docid' => 3,
        'nop' => 3
    ],
    [
        'date' => date('Y-m-d', strtotime('+3 days')),
        'time' => '11:00:00',
        'title' => 'Follow-up Visit',
        'docid' => 1,
        'nop' => 4
    ],
    [
        'date' => date('Y-m-d', strtotime('+4 days')),
        'time' => '15:00:00',
        'title' => 'Routine Examination',
        'docid' => 3,
        'nop' => 6
    ],
    [
        'date' => date('Y-m-d', strtotime('+5 days')),
        'time' => '09:30:00',
        'title' => 'Emergency Check',
        'docid' => 1,
        'nop' => 2
    ]
];

foreach($schedules as $schedule) {
    $result = $database->query("INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES ({$schedule['docid']}, '{$schedule['title']}', '{$schedule['date']}', '{$schedule['time']}', {$schedule['nop']})");
    
    if($result) {
        $newScheduleId = $database->insert_id;
        echo "<p>✓ Created Schedule ID " . $newScheduleId . ": {$schedule['title']} on {$schedule['date']} with Dr ID {$schedule['docid']}</p>";
        
        // Optionally link existing appointments to this schedule
        if($schedule['docid'] == 1) {
            // Link patient 1's appointments
            $database->query("UPDATE appointment SET scheduleid = $newScheduleId, appodate = '{$schedule['date']}' WHERE pid = 1 AND scheduleid IN (1, 11) LIMIT 1");
        } elseif($schedule['docid'] == 3) {
            // Link patient 3's appointments
            $database->query("UPDATE appointment SET scheduleid = $newScheduleId, appodate = '{$schedule['date']}' WHERE pid = 3 AND scheduleid IN (1, 11) LIMIT 1");
        }
    } else {
        echo "<p style='color:red;'>✗ Error creating schedule: " . $database->error . "</p>";
    }
}

echo "<h2>VERIFICATION</h2>";

$today = date('Y-m-d');
$nextweek = date('Y-m-d', strtotime('+1 week'));

echo "<p><strong>Today:</strong> $today</p>";
echo "<p><strong>Next Week:</strong> $nextweek</p>";

// Verify sessions
echo "<h3>Upcoming Sessions:</h3>";
$sessions = $database->query("SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime FROM schedule INNER JOIN doctor ON CAST(schedule.docid AS UNSIGNED)=doctor.docid WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' ORDER BY schedule.scheduledate ASC");

echo "<p>Count: " . $sessions->num_rows . "</p>";
if($sessions->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Doctor</th><th>Date</th><th>Time</th></tr>";
    while($row = $sessions->fetch_assoc()) {
        echo "<tr><td>" . $row['scheduleid'] . "</td><td>" . $row['title'] . "</td><td>" . $row['docname'] . "</td><td>" . $row['scheduledate'] . "</td><td>" . $row['scheduletime'] . "</td></tr>";
    }
    echo "</table>";
}

// Verify appointments
echo "<h3>Upcoming Appointments:</h3>";
$appts = $database->query("SELECT appointment.appoid, appointment.apponum, patient.pname, doctor.docname, schedule.title, schedule.scheduledate FROM schedule INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN doctor ON CAST(schedule.docid AS UNSIGNED)=doctor.docid WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' ORDER BY schedule.scheduledate ASC");

echo "<p>Count: " . $appts->num_rows . "</p>";
if($appts->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Appt#</th><th>Patient</th><th>Doctor</th><th>Session</th><th>Date</th></tr>";
    while($row = $appts->fetch_assoc()) {
        echo "<tr><td>" . $row['appoid'] . "</td><td>" . $row['apponum'] . "</td><td>" . $row['pname'] . "</td><td>" . $row['docname'] . "</td><td>" . $row['title'] . "</td><td>" . $row['scheduledate'] . "</td></tr>";
    }
    echo "</table>";
}

echo "<h2><a href='admin/index.php' style='color:blue; font-size:16px;'>→ Go to Admin Dashboard</a></h2>";

$database->close();
?>
