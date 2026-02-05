<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
require_once("functions.php");

		// $password = self::get('password', $_mail, 'user_mail');
		$email=mysqli_real_escape_string($con,$_POST["email"]??"");
		$tel=mysqli_real_escape_string($con,$_POST["tel"]??"");
		$name=mysqli_real_escape_string($con,$_POST["firstname"]??"");
		$lastname=mysqli_real_escape_string($con,$_POST["lastname"]??"");
		$address=mysqli_real_escape_string($con,$_POST["currentAddress"]??"");
		$password=mysqli_real_escape_string($con,$_POST["password"]??"");
		$pid=mysqli_real_escape_string($con,$_POST["pid"]??"");
		$company_name=mysqli_real_escape_string($con,$_POST["company_name"]??"");
		$company_id=mysqli_real_escape_string($con,$_POST["company_id"]??"");
		$pid=mysqli_real_escape_string($con,$_POST["pid"]??"");
		$industry=mysqli_real_escape_string($con,$_POST["industry"]??"");
		$wantsms=mysqli_real_escape_string($con,$_POST["wantsms"]??"");
		$pass=encrypt_decrypt("encrypt",$password);
		$email_data="";
		if($company_id!=""){
			$company_name=getcompany($company_id);
		}
		//$q1=mysqli_query($con,"SELECT * FROM id WHERE email='".$email."' OR mobile_phone='".$tel."' ");
$ua = $_SERVER['HTTP_USER_AGENT'];
$ip = $_SERVER['REMOTE_ADDR'];
		 // $q1=mysqli_query($con,"SELECT * FROM id WHERE ".($pid!=""?"personal_id='".$pid."'":"")." ".($company_id!=""?"company_id='".$company_id."'":"")." "); 
		$q1=mysqli_query($con,"SELECT * FROM id WHERE email='".$email."' OR mobile_phone='".$tel."' ");
		if(mysqli_num_rows($q1)<1){
			mysqli_query($con,"INSERT INTO id SET term='1',sms='".$wantsms."',name='".$name."',lastname='".$lastname."',mobile_phone='".$tel."',email='".$email."',pass='".$pass."',current_address='".$address."',personal_id='".$pid."',company_name='".$company_name."',company_id='".$company_id."',usertype='".($company_id!=""?2:1)."',date='".time()."',industry='".$industry."',ua='".$ua."',ip='".$ip."' ");
			$id=mysqli_insert_id($con);
			$_SESSION["uid"]=$id;
			$_SESSION["timeout"]=time()+60*60*24*7;
			$uid=$id;
$referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct / Unknown';
$ip = $_SERVER['HTTP_CLIENT_IP']
    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR']
    ?? 'Unknown IP';

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown UA';
$referrer  = htmlspecialchars($referrer, ENT_QUOTES, 'UTF-8');
$ip        = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
$userAgent = htmlspecialchars($userAgent, ENT_QUOTES, 'UTF-8');		
mysqli_query($con,"UPDATE channels SET uid='".$uid."' WHERE ip='".$ip."' AND ua='".$userAgent."'   ");			
			
			
			$email_data .= 'მოგესალმებით, თქვენ წარმატებით დარეგისტრირდით <a href="https://supta.ge" target="_blank">www.supta.ge</a>-ზე. თქვენი პირველი
	შეკვეთის მისაღებად ყველაფერი მზადაა.<br>
	არ გამოგრჩეთ „სუფთას“ სიახლეები და შეთავაზებები Facebook-ზე, გამოგვყევით:
	<a href="https://www.facebook.com/supta.ge" target="_blank">https://www.facebook.com/supta.ge</a>';	
		$email_data .= '<br>სახელი გვარი - '.($company_id!=""?$company_name:$name.' '.$lastname);
			// $email_data .= '<br><br>საუკეთესო სურვილებით,<br><strong>JSC AID Group</strong><br>ონლაინ მაღაზია<br><img src="https://supta.ge/cp/admin/uploads/logo/%E1%83%A1%E1%83%90%E1%83%98%E1%83%A2%E1%83%98%E1%83%A1%20%E1%83%9A%E1%83%9D%E1%83%92%E1%83%9D.png" style="width:100px"/>
			// <br>თბილისი, 0102, თ. მეფის გამზ. №18
			// <br>მობ: (+995) 595 99 16 26
			// <br>ელ. ფოსტა:info@supta.ge
			// <br><a href="https://supta.ge">www.supta.ge</a>';
$code="თქვენ წარმატებით დარეგისტრირდით";
			htmlMail("aidgroup2016@gmail.com,ishal10@freeuni.edu.ge", "ახალი რეგისტრაცია", $email_data);
			$url='http://bi.msg.ge/sendsms.php?username=suftage&password=VhR6EajtfynGB&client_id=631&service_id=2146&utf=1&to=+995'.$tel.'&text='.urlencode($code);

	$q22=mysqli_query($con,"SELECT * FROM admins WHERE sms='1'");
	while($r22=mysqli_fetch_array($q22)){
		// $url = 'http://bi.msg.ge/sendsms.php?username=suftage&password=VhR6EajtfynGB&client_id=631&service_id=2146&utf=1&to=+995'.$r22["tel"].'&text='.urlencode("ახალი შეკვეთა: ".date("d.m.Y H:i",$T)." თანხა ".$total." ID: ".($Zuser["company_id"]?$Zuser["company_id"]:$Zuser["personal_id"])." ".($Zuser["company_name"]?$Zuser["company_name"]:$Zuser["name"]." ".$Zuser["lastname"])." ".$olink);
		// gosms($url);		
	}			
			
	echo 1;			
		}else{
			echo 2;
		}

?>