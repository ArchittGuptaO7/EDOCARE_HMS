<?php
include("connection.php");

echo "<h2>COMPLETE DATABASE STATE</h2>";

echo "<h3>All Schedules (no date filter):</h3>";
$allSchedules = $database->query("SELECT * FROM schedule");
echo "Total: " . $allSchedules->num_rows . "<br>";
echo "<table border='1'><tr><th>ID</th><th>DocID</th><th>Title</th><th>Date</th><th>Time</th><th>NOP</th></tr>";
while($row = $allSchedules->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['scheduleid'] . "</td>";
    echo "<td>" . $row['docid'] . "</td>";
    echo "<td>" . $row['title'] . "</td>";
    echo "<td>" . $row['scheduledate'] . "</td>";
    echo "<td>" . $row['scheduletime'] . "</td>";
    echo "<td>" . $row['nop'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>All Appointments:</h3>";
$allAppts = $database->query("SELECT * FROM appointment");
echo "Total: " . $allAppts->num_rows . "<br>";
echo "<table border='1'><tr><th>AppID</th><th>PID</th><th>ApptNum</th><th>ScheduleID</th><th>Date</th></tr>";
while($row = $allAppts->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['appoid'] . "</td>";
    echo "<td>" . $row['pid'] . "</td>";
    echo "<td>" . $row['apponum'] . "</td>";
    echo "<td>" . $row['scheduleid'] . "</td>";
    echo "<td>" . $row['appodate'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>All Doctors:</h3>";
$allDocs = $database->query("SELECT * FROM doctor");
while($row = $allDocs->fetch_assoc()) {
    echo "ID: " . $row['docid'] . " | Name: " . $row['docname'] . "<br>";
}

echo "<h3>All Patients:</h3>";
$allPats = $database->query("SELECT * FROM patient");
while($row = $allPats->fetch_assoc()) {
    echo "ID: " . $row['pid'] . " | Name: " . $row['pname'] . "<br>";
}

$database->close();
?>
