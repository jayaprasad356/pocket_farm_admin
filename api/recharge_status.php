<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');
include_once('../includes/crud.php');

$db = new Database();
$db->connect();

$response = array(); // Initialize response array

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is empty";
    echo json_encode($response);
    return;
}

if (empty($_POST['txn_id'])) {
    $response['success'] = false;
    $response['message'] = "Transaction ID is empty";
    echo json_encode($response);
    return;
}

if (empty($_POST['date'])) {
    $response['success'] = false;
    $response['message'] = "Date is empty";
    echo json_encode($response);
    return;
}

if (empty($_POST['key'])) {
    $response['success'] = false;
    $response['message'] = "Key is empty";
    echo json_encode($response);
    return;
}

$user_id = $db->escapeString($_POST['user_id']);
$txn_id = $db->escapeString($_POST['txn_id']);
$date = $db->escapeString($_POST['date']);
$key = $db->escapeString($_POST['key']);

$client_txn_id = "20240326231306"; // Assuming this value
$txn_date = "26-03-2024"; // Assuming this value
$key = "707029bb-78d4-44b6-9f72-0d7fe80e338b"; // Assuming this value

$api_url = 'https://api.ekqr.in/api/check_order_status';
$api_data = array(
    'client_txn_id' => $client_txn_id,
    'txn_date' => $txn_date,
    'key' => $key
);
$api_response = json_decode(file_get_contents($api_url, false, stream_context_create(array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/json',
        'content' => json_encode($api_data)
    )
))), true);

if (isset($api_response['success'])) {
    $datetime = date('Y-m-d H:i:s');
    $type = 'recharge';
    $amount = 300;

    $sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$user_id', '$amount', '$datetime', '$type')";
    $db->sql($sql);

    $sql_query = "UPDATE users SET recharge = recharge + $amount, total_recharge = total_recharge + $amount WHERE id = '$user_id'";
    $db->sql($sql_query);

    $response['success'] = true;
    $response['message'] = "Transaction completed successfully";
} else {
    // Transaction failed due to unsuccessful API response
    $response['success'] = false;
    $response['message'] = "Transaction failed";
}

echo json_encode($response);
?>
