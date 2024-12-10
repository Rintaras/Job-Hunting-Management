<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success'=>false]);
    exit;
}

$user_id = (int)$user_id;
$profile = getProfileData($user_id);
if ($profile) {
    $job_status = null;
    if ($profile['is_status_public'] == 1) {
        $job_status = getJobStatusData($user_id);
    }
    echo json_encode(['success'=>true, 'profile'=>$profile, 'job_status'=>$job_status]);
} else {
    echo json_encode(['success'=>false]);
}
