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


if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = " User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['wallet_type'])) {
    $response['success'] = false;
    $response['message'] = " Wallet Type is Empty";
    print_r(json_encode($response));
    return false;
}
$datetime = date('Y-m-d H:i:s');
$user_id=$db->escapeString($_POST['user_id']);
$wallet_type = $db->escapeString($_POST['wallet_type']);

$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num == 1) {
    $veg_wallet = $res[0]['veg_wallet']; 
    $fruit_wallet = $res[0]['fruit_wallet'];

    if($wallet_type == 'veg_wallet'){
       
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'veg_wallet','$datetime',$veg_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET veg_wallet= veg_wallet - $veg_wallet,balance = balance + $veg_wallet  WHERE id=" . $user_id;
        $db->sql($sql);
    }
    if($wallet_type == 'fruit_wallet'){
        
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'fruit_wallet','$datetime',$fruit_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET fruit_wallet= fruit_wallet - $fruit_wallet,balance = balance + $fruit_wallet  WHERE id=" . $user_id;
        $db->sql($sql);
    }


    $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Added to Main Balance Successfully";
    $response['data'] = $res;



}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
}

print_r(json_encode($response));
?>