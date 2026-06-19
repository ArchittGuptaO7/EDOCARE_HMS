<?php
// Database connection
$servername = "localhost";
$username = "root";  
$password = "mysql"; 
$dbname = "edoc"; 

$database = new mysqli($servername, $username, $password, $dbname);

if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

date_default_timezone_set('Asia/Kolkata');

// Get today's date
$today = date('Y-m-d');
$nextWeek = date('Y-m-d', strtotime('+7 days'));

echo "Today: " . $today . "<br>";
echo "Next Week: " . $nextWeek . "<br><br>";

// First, check if doctor with docid=1 exists
$doctorCheck = $database->query("SELECT * FROM doctor WHERE docid=1");
if ($doctorCheck->num_rows == 0) {
    echo "No doctor found. Creating test doctor...<br>";
    $database->query("INSERT INTO doctor (docid, docemail, docname, docpassword, docnic, doctel, specialties) VALUES (1, 'doctor@edoc.com', 'Test Doctor', '123', '000000000', '0110000000', 1)");
}

// Check if patient exists
$patientCheck = $database->query("SELECT * FROM patient WHERE pid=1");
if ($patientCheck->num_rows == 0) {
    echo "No patient found. Creating test patient...<br>";
    $database->query("INSERT INTO patient (pid, pemail, pname, ppassword, paddress, pnic, pdob, ptel) VALUES (1, 'patient@edoc.com', 'Test Patient', '123', 'Sri Lanka', '0000000000', '2000-01-01', '0120000000')");
}

// Clear existing schedules for the next week
$database->query("DELETE FROM schedule WHERE scheduledate >= '$today' AND scheduledate <= '$nextWeek'");
echo "Cleared old schedules for the next week<br><br>";

// Insert upcoming schedules
$dates = [];
for ($i = 0; $i < 5; $i++) {
    $dates[] = date('Y-m-d', strtotime('+' . ($i + 1) . ' days'));
}

$sessionTitles = [
    'Cardiology Check-up',
    'General Health Examination',
    'Follow-up Consultation',
    'Specialist Review',
    'Priority Appointment'
];

foreach ($dates as $index => $date) {
    $time = '10:00:00';
    $title = $sessionTitles[$index];
    
    $insertSchedule = $database->query("INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES (1, '$title', '$date', '$time', 5)");
    
    if ($insertSchedule) {
        $scheduleid = $database->insert_id;
        echo "Created schedule ID: $scheduleid for $date at $time<br>";
        
        // Also insert an appointment for this schedule
        $appointmentInsert = $database->query("INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (1, 1, $scheduleid, '$date')");
        if ($appointmentInsert) {
            $appoid = $database->insert_id;
            echo "  └─ Created appointment ID: $appoid<br>";
        }
    }
}

echo "<br>Test data created successfully!<br>";
echo "You can now <a href='admin/index.php'>view the admin dashboard</a>";
?>
