<?php
// Simple server-side script to handle counter value
header('Content-Type: application/json');

// Get the counter value from POST request
$counter = isset($_POST['counter']) ? intval($_POST['counter']) : 0;

// Prepare response
$response = [
    'status' => 'success',
    'message' => 'Counter value received: ' . $counter,
    'counter' => $counter,
    'timestamp' => time()
];

// Send JSON response
echo json_encode($response);
?> 