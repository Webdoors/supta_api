<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("functions.php");
header("Content-Type: application/json; charset=UTF-8");

$referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct / Unknown';
$ip = $_SERVER['HTTP_CLIENT_IP']
    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR']
    ?? 'Unknown IP';

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown UA';

$lang = mysqli_real_escape_string($con, $_POST["lang"] ?? "");
if($lang==""){ $lang="ge"; }
@include("../lang/".$lang.".php");

$email = mysqli_real_escape_string($con, $_POST["email"] ?? "");
$password = mysqli_real_escape_string($con, $_POST["password"] ?? "");
$password = encrypt_decrypt("encrypt", $password);

$T = time();

if($email==""){
    echo json_encode([
        "status" => false,
        "message" => $W["მიუთითეთ ელფოსტა"] ?? "Email required"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$now = time();

/* attempts */
$stmt = mysqli_prepare($con, "SELECT attempts, last_attempt FROM login_attempts WHERE ip=?");
mysqli_stmt_bind_param($stmt, "s", $ip);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$maxAttempts = 10;
$lockoutTime = 15*60;

if ($row) {
    if ($row['attempts'] >= $maxAttempts && ($now - $row['last_attempt']) < $lockoutTime) {
        echo json_encode([
            "status" => false,
            "message" => "Too many attempts. Try again later."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    mysqli_query($con, "INSERT INTO login_attempts (ip, attempts, last_attempt) VALUES('$ip',0,$now)");
}

/* login */
$q1 = mysqli_query($con,"
    SELECT id 
    FROM id 
    WHERE (email='".$email."' OR mobile_phone='".$email."')
    AND pass='".$password."'
    AND deleted!=1
    LIMIT 1
");

if(mysqli_num_rows($q1) > 0){

    $r1 = mysqli_fetch_array($q1);
    $uid = $r1["id"];

    $token = json_encode([time(), $uid, time()]);
    $token = encrypt_decrypt("encrypt", $token);

    mysqli_query($con,"INSERT INTO tokens SET uid='".$uid."',token='".$token."',date='".time()."' ");

    $_SESSION["uid"] = $uid;
    $_SESSION["timeout"] = time() + 60*60*24;

    mysqli_query($con,"
        UPDATE login_attempts
        SET attempts = 0, last_attempt = $now
        WHERE ip='$ip'
    ");

    mysqli_query($con,"INSERT INTO login SET uid='".$uid."' , date='".time()."',method='Website' ");

    $ipSafe = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
    $uaSafe = htmlspecialchars($userAgent, ENT_QUOTES, 'UTF-8');

    mysqli_query($con,"UPDATE channels SET uid='".$uid."' WHERE ip='".$ipSafe."' AND ua='".$uaSafe."'");

    /* cart merge */
    if(isset($_COOKIE["cart"])){
        $cart = json_decode($_COOKIE["cart"], true);
        if(!empty($cart)){
            foreach($cart as $key=>$vl){
                $bask = mysqli_query($con,"SELECT * FROM basket WHERE owner_id='$uid' AND good_id='$key' ");
                if(mysqli_num_rows($bask)>0){
                    $rbask=mysqli_fetch_assoc($bask);
                    $quantity=intval($vl["quantity"])+intval($rbask["quantity"]);
                    mysqli_query($con,"UPDATE basket SET quantity='".$quantity."' WHERE good_id='$key' AND owner_id='$uid' ");
                } else {
                    mysqli_query($con,"INSERT INTO basket SET quantity='".$vl["quantity"]."' , good_id='$key' , owner_id='$uid', date='$T' ");
                }
            }
            setcookie("cart", "", time() + (86400 * 30), "/");
        }
    }

    // ✅ SUCCESS JSON (Laravel style)
    echo json_encode([
        "status" => true,
        "message" => "Authorized",
        "uid" => (int)$uid,
        "token" => $token
    ], JSON_UNESCAPED_UNICODE);
    exit;

} else {

    mysqli_query($con,"
        UPDATE login_attempts
        SET attempts = attempts + 1, last_attempt = $now
        WHERE ip='$ip'
    ");

    echo json_encode([
        "status" => false,
        "message" => $W["მომხმარებლის ელფოსტა ან პაროლი არასწორია"] ?? "Invalid email or password"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
