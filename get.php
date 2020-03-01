<?php

require_once('utils/kwutils.php');
require_once('dao.php');

class dci_get {

    const urlPre2 = 'api/v1/events?locationState=';
    
    public function __construct($sfx, $dao = false) {
	$this->sfx = $sfx;
	if ($dao) $this->dao = $dao;
	else 	  $this->dao = new dci_dao();
	$cdat = $this->getI();
	$this->dat = $cdat;
    }
 
    private function getQuotaSince() { 
	$d = 28800;
	if (isAWS()) return $d;
	if (time() < strtotime('2020-02-29 21:59')) return 100;
	return $d;
    }
    
    private function getI() {
	$dbrpre = $this->getRecent(1);
	$dbr = $this->getRecent();
	if ($dbrpre || $dbr) return $dbr;
	$this->setPrefix();
	$this->dao->putCall('pre', $this->sfx);
	$res = self::realGet($this->sfx, $this->urlPrefix);
	$this->dao->putCall($res);
	return $res;
    }
    
    private function getRecent($inclPre = false, $since = false) {
	if (!$since) $since = $this->getQuotaSince();
	$res = $this->dao->getCall($this->sfx, $since , $inclPre);
	return $res;
    }
     
    public function get() { 
	if (isset($this->dat)) return $this->dat;
	else return $this->getRecent(0, 31000000);
    }
    
    private static function realGet($sfx, $prefix) {

	if (PHP_SAPI !== 'cli') die('cli only');
	
	$callSfx = $sfx;
	
	$url = $prefix . self::urlPre2 . $callSfx;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url); unset($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, kwua());
	if (isAWS()) sleep(random_int(15, 120));
	$b = microtime(1);
	$res = curl_exec($curl);
	$e = microtime(1);
	$resLen = strlen($res);
	$callElapsed = $e - $b;
	$ts = intval($e); unset($b, $e);
	curl_close($curl); unset($curl, $sfx);
	return get_defined_vars();
    }
    
    private function setPrefix() { 
	if (isset($this->urlPrefix)) return;
	$co = new kwynn_creds();
	$this->urlPrefix = $co->getType('DCI_JSON', 'url_prefix');
    }
}