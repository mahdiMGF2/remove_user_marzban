<?php
$url = "https://domain.com:8880"; // آدرس پنل
$username = "";// نام کاربری پنل
$password = "";// رمز عبور پنل
$status = "disable";
/**
 * وضعیت کاربرانی که میخواهند حذف شود
 * کاربر فعال active بزارید
 * کاربر غیرفعال disabled
 * کاربری که حجمش تمام شدهlimited
 * کاربری که زمانش تمام شدهexpired
 * توجه داشتید در صورت اشتباه وارد کردن وضعیت تمامی کاربران حذف خواهد شد
 * */

function token_panel($url_panel,$username_panel,$password_panel){
    $url_get_token = $url_panel.'/api/admin/token';
    $data_token = array(
        'username' => $username_panel,
        'password' => $password_panel
    );
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT_MS => 300,
        CURLOPT_POSTFIELDS => http_build_query($data_token),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'accept: application/json'
        )
    );
    $curl_token = curl_init($url_get_token);
    curl_setopt_array($curl_token, $options);
    $token = curl_exec($curl_token);
    curl_close($curl_token);

    $body = json_decode( $token, true);
    return $body;
}
$token =token_panel($url,$username,$password);
#-----------------------------#

function getuser($token,$url_panel)
{
    global $status;
    $url =  $url_panel.'/api/users?status='.$status;
    $header_value = 'Bearer ';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: ' . $header_value .  $token
    ));

    $output = curl_exec($ch);
    curl_close($ch);
    $data_useer = json_decode($output, true);
    return $data_useer;
}
function removeuser($token,$url_panel,$username)
{
    $url =  $url_panel.'/api/user/'.$username;
    $header_value = 'Bearer ';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Authorization: ' . $header_value .  $token
    ));

    $output = curl_exec($ch);
    curl_close($ch);
    $data_useer = json_decode($output, true);
    return $data_useer;
}
$users = getuser($token['access_token'],$url);
$count = count($users['users']);
for ($i = 0; $i < $count; $i++ ){
    removeuser($token['access_token'],$url,$users['users'][$i]['username']);
    echo "removed user : ".$users['users'][$i]['username'];
    echo "</br>";
}
