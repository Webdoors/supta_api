<?php
function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'qJB0rGtIn5UB1xG03efyCp1xG03efyCp';
    $secret_iv = 'This is my secret iv';
    // hash
    $key = hash('sha512', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha512', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function price($proid){
	include("../db_open.php");

	include("../db_close.php");
	
}

	 function gosms($url){			
		$ch = curl_init();
$json=
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $json );
		curl_setopt($ch, CURLOPT_POST, false );
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json', 
			'Content-Length: 0' )
		);
		$result = curl_exec($ch);
		curl_close($ch);			
	}
	function generateSignature($params, $secret_key, $version = '1.0', $encoded = true)
{
    $signatureSeparator = '|';  // Assuming this is defined somewhere in your class
    
    if ($version == '2.0') {
        if ($encoded) {
            if (is_array($params)) {
                $params = base64_encode(json_encode(['order' => $params]));
                $signature = sha1($secret_key . $signatureSeparator . $params);
            } else {
                $signature = sha1($secret_key . $signatureSeparator . $params);
            }
        } else {
            $signature = $secret_key . $signatureSeparator . $params;
        }
    } else {
        $data = array_filter($params, function ($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);
        
        $sign_str = $secret_key;
        foreach ($data as $v) {
            $sign_str .= $signatureSeparator . $v;
        }
        
        if ($encoded) {
            $signature = sha1($sign_str);
        } else {
            $signature = $sign_str;
        }
    }

    return strtolower($signature);
}
function getperson($id="01008045610"){
	$request_url = "https://services.rs.ge/WayBillService/WayBillService.asmx?op=get_name_from_tin";

	$request_xml = '<?xml version="1.0" encoding="utf-8"?>
						<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
						  <soap12:Body>
							<get_name_from_tin xmlns="http://tempuri.org/">
							  <su>QCASH:405281216</su>
							  <sp>#Tsertila#123</sp>
							  <tin>'.trim($id).'</tin>
							</get_name_from_tin>
						  </soap12:Body>
						</soap12:Envelope>';
   
	$request_headers = array(
					"POST /WayBillService/WayBillService.asmx HTTP/1.1",
					"Host: services.rs.ge",
					"Content-Type: application/soap+xml; charset=utf-8",
					"Content-Length: ".strlen($request_xml)."",
					"SOAPAction: http://tempuri.org/get_name_from_tin"
					);
   
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_URL, $request_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request_xml);
	$xml_result = curl_exec($ch);
	$xml = simplexml_load_string($xml_result);
	$doc = new DOMDocument();  
	$str = $xml->asXML();
	$doc->loadXML($str);
	$result = $doc->getElementsByTagName('get_name_from_tinResult')->item(0)->nodeValue;
	// var_dump($result);
	return $result;
}
function getcompany($cid="405281216"){
	$url = "https://enreg.reestri.gov.ge/main.php";

	$postFields = [
		"c" => "search",
		"m" => "find_legal_persons",
		"s_legal_person_idnumber" => $cid,
		"s_legal_person_name" => "",
		"s_legal_person_form" => "0",
		"s_legal_person_email" => ""
	];
	// IMPORTANT: copy your browser cookie
	$cookie = "MMR_PUBLIC=8hjjgov7sm2vt3l6jorab1ffs0";

	$headers = [
		"X-Requested-With: XMLHttpRequest",
		"Origin: https://enreg.reestri.gov.ge",
		"Referer: https://enreg.reestri.gov.ge/main.php?m=new_index",
		"User-Agent: Mozilla/5.0",
		"Content-Type: application/x-www-form-urlencoded"
	];

	$ch = curl_init();

	curl_setopt_array($ch, [
		CURLOPT_URL => $url,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($postFields),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_COOKIE => $cookie,
		CURLOPT_SSL_VERIFYPEER => false
	]);

	$response = curl_exec($ch);
	// $response = mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8');
	curl_close($ch);
libxml_use_internal_errors(true);
$dom = new DOMDocument();

// Prepend XML declaration to force UTF-8
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $response);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

$tds = $xpath->query("//td");

$data = [];
foreach ($tds as $td) {
    // Use htmlspecialchars to safely handle UTF-8
    $data[] = htmlspecialchars(trim($td->nodeValue), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


$name = $data[3] ?? "";

	return $name;
}
function htmlMail($_to, $_subject, $_text, $lang="ka",$trin="")
	{
		global $W;
		if($trin!=""){
			$_to=$_to.",invite-fd173913958g9060@trustindex.io";
		}
		$_text =$_text;
	$_from="Supta <noreply@Supta.ge>";
	// message
	$message = '
	<html>
	<head>
	  <title>Supta</title>
	</head>
	<body>
	'.($trin!=""?'<script type="text/javascript">{"Trustindex.io":{"name":"'.$trin.'"}}</script>':'').'
	  <table border = "0">
		<tr>
			<td>
				'.$_text.'
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
<br><br>'.ucfirst($W["საუკეთესო სურვილებით"]).',<br><strong>JSC AID Group</strong><br>'.ucfirst($W["ონლაინ მაღაზია"]).'<br><img src="https://supta.ge/img/sitelogo.png" style="width:100px"/>
		<br>'.ucfirst($W["address"]).' : '.ucfirst($W["თბილისი, 0112, მ. წინამძღვრიშვილის ქ. №162"]).'
		<br>'.ucfirst($W["tel"]).' : (+995) 0322110626
		<br>'.ucfirst($W["email"]).' : info@supta.ge
		<br>'.ucfirst($W["web"]).' : <a href="https://supta.ge">www.supta.ge</a>
			</td>
		</tr>

	  </table>
	</body>
	</html>
	';
/*
		<tr >
			<td>
				შეკითხვებისთვის მოგვმართეთ შემდეგ ელ.ფოსტაზე: info@Supta.ge
			</td>
		</tr>
		<tr>
			<td>
				გისურვებთ წარმატებულ დღეს!
			</td>
		</tr>
		<tr>
		  <td>
				<a href="https://www.Supta.ge/">www.Supta.ge</a>
		  </td>
		</tr>
*/
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

	//$headers .= 'To: '.$_to. "\r\n";
	$headers .= 'From:  '.$_from.'' . "\r\n";
	/*if($trustpilot==1){
		$headers .= "BCC: supta.ge+a1258ae7d1@invite.trustpilot.com,ishal10@freeuni.edu.ge\r\n";	
			
	}*/


	// Mail it
	mail($_to, $_subject, $message, $headers);

	}