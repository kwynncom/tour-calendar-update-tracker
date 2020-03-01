<?php

require_once('kwutils.php');
require_once('creds.php');

class kwynn_email {
    
    const smtp_server = 'email-smtp.us-east-1.amazonaws.com';
    
    function __construct() {
	$this->mailo = $this->getMailO();
	$this->setDefaultTo();
    }
    
    private function getMailO() {
	$mailo = self::getCommonO();
	$this->popCreds($mailo);
	return $mailo;
    }
    
    private function getCreds() {
	$dao = new kwynn_creds('creds');
	$c = $dao->getType('email'); kwas($c && is_array($c) && count($c) >0, 'bad creds array');
	$t = ['uname', 'pwd', 'from', 'from_name'];
	foreach($t as $i) {
	    kwas(isset($c[$i]),"bad $i in kwynn email");
	    $s = $c[$i];
	    kwas(	 isset    ($s)  
		    && 	 is_string($s)
		    &&   strlen(   $s) > 10, "bad $i in kwynn email");
	}
	
	$this->creds = $c;
	return $c;
    }
    
    private static function getCommonO() {
    $mail             = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPDebug  = 0;  // 1 = errors and messages; 2 = messages only
    $mail->SMTPAuth   = true;                  
    $mail->SMTPSecure = 'tls';                 
    $mail->Port       = 587;   
    
    return $mail;
}

private function popCreds(&$mail) {
    $creds = $this->getCreds();
    $mail->Host       = self::smtp_server;  
    $mail->Username   = $creds['uname'];
    $mail->Password   = $creds['pwd'];
    $mail->SetFrom(     $creds['from'], 
			$creds['from_name']);
}

private function setDefaultTo() {
    $this->mailo->AddAddress($this->creds['default_to'], $this->creds['default_to_name']);
}

public function smail($body, $subject, $isHTML = true) {
    $mail = $this->mailo;
    $mail->Subject    = $subject;
    if ($isHTML) {
	$mail->IsHTML(true);
	$mail->MsgHTML($body);
    }
    else {
	$mail->IsHTML(false);
	$mail->Body =  $body;

    }
    if(!$mail->Send()) return false; // $err = $mail->ErrorInfo;
    else return true;
} // func
}
