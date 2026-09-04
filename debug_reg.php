<?php
require_once __DIR__ . '/includes/Config.php';
require_once __DIR__ . '/includes/Db.PHP';

echo "=== ALL COURSES AND THEIR LECTURER_NAME ===" . PHP_EOL;
$r = mysqli_query($conn, "SELECT course_id, course_code, course_name, lecturer_name FROM courses ORDER BY course_id");
while ($row = mysqli_fetch_assoc($r)) {
    echo $row['course_id'] . ' | ' . $row['course_code'] . ' | ' . $row['course_name'] . ' | lecturer:[' . ($row['lecturer_name'] ?? 'NULL') . ']' . PHP_EOL;
}

echo PHP_EOL . "=== ALL REGISTRATIONS (course + student + status) ===" . PHP_EOL;
$r = mysqli_query($conn, "SELECT reg.registration_id, reg.student_id, reg.course_id, reg.status, s.first_name, s.last_name, c.course_code, c.course_name, c.lecturer_name FROM registrations reg JOIN students s ON s.student_id = reg.student_id JOIN courses c ON c.course_id = reg.course_id ORDER BY c.course_code");
while ($row = mysqli_fetch_assoc($r)) {
    echo $row['first_name'] . ' ' . $row['last_name'] . ' -> ' . $row['course_code'] . ' (' . $row['course_name'] . ') | status:' . $row['status'] . ' | lecturer_name:[' . ($row['lecturer_name'] ?? 'NULL') . ']' . PHP_EOL;
}

echo PHP_EOL . "=== LECTURER TABLE ===" . PHP_EOL;
$r = mysqli_query($conn, "SELECT lecturer_id, user_id, first_name, last_name FROM lecturers");
while ($row = mysqli_fetch_assoc($r)) {
    echo $row['lecturer_id'] . ' | user_id:' . $row['user_id'] . ' | ' . $row['first_name'] . ' ' . $row['last_name'] . ' | full_name:[' . $row['first_name'] . ' ' . $row['last_name'] . ']' . PHP_EOL;
}
