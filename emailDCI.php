<?php

require_once('utils/email.php');
require_once('utils/email_dao.php');
require_once('utils/utils.php');

function dci_email($a, $status = 'looping') {
    try {
    static $mbis = false;
    
    // throw new Exception('DCI test ex');

    if (PHP_SAPI !== 'cli') return;
    if ($mbis) return;
    
    if (isset($a[0])) $a = $a[0];
     
    if (isset($a['city']) && $a['city'] === 'Murfreesboro') {
	$mbis = true;
	
	if (isset($a['onsale']) && $a['onsale']) {
	    $t = 'Mboro tickets on sale!!!';
	    dciema($t);
	    return;
	}
	
	$day = date('D');
    if (time() <= strtotime('2020-02-28 23:59:59') || $day === 'Sat')  {

	
	$ht  = '';
	$ht .= '<p>';
	$ht .= '<a href="https://kwynn.com/t/20/02/dci/">my link</a>';
	$ht .= '</p>';
	
	$a['corps_cnt_1'] = count($a['corps']);
	$a['corps_cnt_2'] = count($a['corps2']); unset($a['corps'], $a['corps2']);
	$v = (array)$a['venue'];
	$a['llInGSM'] = llinGSM($v['longitude'], $v['latitude'], $v['googleMapsStaticMap']); unset($a['venue']->googleMapsStaticMap);
	
	$fs = ['slug', 'dci_id', 'datets', 'tz'];
	
	foreach($fs as $f) unset($a[$f]);
	
	$t = print_r($a,1);
	
	$ht .= '<div><pre>';
	$ht .= $t;
	$ht .= '</pre></div>';
	
	
	dciema($ht, 'Mboro ping', 1);
    }
	
    }
      
    if ($status === 'done' && !$mbis) {
	$t = 'Mboro not listed!!!';
	dciema($t);
	return;
    }
    } catch (Exception $ex) { 
	dciema('DCI exception', 'DCI exception');
    }
}

function dciema($b, $s = '', $isht = false) {
    
    static $e = false;
    static $dao = false; 
    
    if (PHP_SAPI !== 'cli') return;
    
    if (!$dao) $dao = new dci_email_dao();
    $prev = $dao->getLatest();
    
    $d = time() - $prev;
    
    if ($d < 77000 /* || !isAWS() */) return;
       
    if (!$e) $e = new kwynn_email();    
    if (!$s) $s = $b;
        
    $dao->put($s);
    $e->smail($b, $s, $isht);
}
