<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}
$user_id = $_SESSION['user_id'];
$schedules = getSchedules($user_id);
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="schedule.csv"');
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['Date', 'Time', 'Description']);
foreach ($schedules as $sch) {
    fputcsv($output, [
        $sch['event_date'],
        $sch['event_time'],
        $sch['event_description']
    ]);
}

fclose($output);
exit;
