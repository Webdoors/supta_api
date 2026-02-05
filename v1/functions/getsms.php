<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$tel=mysqli_real_escape_string($con,$_REQUEST["tel"]);
$optype='';
$uid='';
$insuid='';
// if(ISSET($_SESSION['UID']))
// {
	// $uid=(int)$_SESSION['UID'];
	// $insuid=" ,uid='$uid' " ;
// }
// $subj="ახალი რეგისტრაცია SMS კოდი";
// if(isset($_REQUEST['c']))
// {
	// $optype=mysqli_real_escape_string($con,$_REQUEST["c"]);
	// $subj="SMS კოდი";
// }

$code=rand(1111,9999);
$T=time();
$ip = $_SERVER['HTTP_CLIENT_IP']
    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR']
    ?? 'Unknown IP';
if($tel=="591505031"||$tel=="597233040"||$tel=="551156577"){
	die("Go somewhere else :)");
}
$sql = "
    SELECT COUNT(*) AS cnt
    FROM sms
    WHERE tel = '$tel'
      AND FROM_UNIXTIME(date) >= (NOW() - INTERVAL 30 MINUTE)
";

$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 4) {
	mysqli_query($con,"INSERT INTO blocked SET  ip='".$ip."',date='".$T."' ");
    die("Too many requests. Try again later.");
}
$sql = "
    SELECT COUNT(*) AS cnt
    FROM sms
    WHERE tel = '$tel'
      AND FROM_UNIXTIME(date) >= (NOW() - INTERVAL 24 HOUR)
";

$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 9) {
	mysqli_query($con,"INSERT INTO blocked SET  ip='".$ip."',date='".$T."' ");
    die("Too many requests. Try again later.");
}
// echo $ip;
$q1=mysqli_query($con,"SELECT * FROM blocked WHERE ip='".$ip."'");
if(mysqli_num_rows($q1)>0){
	die("Too many requests");
}
// echo "INSERT INTO sms SET tel='".$tel."',date='".$T."',code='".$code."' $insuid";
mysqli_query($con,"INSERT INTO sms SET  ip='".$ip."',tel='".$tel."',date='".$T."',code='".$code."' $insuid");


$url='http://212.72.155.180:2375/api/sendmsg.php?username=9mVN8u993tPo06K&password=M5Bi17ObaN1ll_9XMnJl&num=995'.$tel.'&utf=1&msg='.urlencode($code);
 
$response = file_get_contents($url);
 var_dump($response);


?>