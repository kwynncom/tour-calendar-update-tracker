<?php

require_once('utils/kwutils.php');

class dci {

public static function myOrd() {
    return ['BD', 'SCV', 'CC', 'BAC', 'BlC', 'PR', 'Cav', 'Sp'];
}
    
public static function corps2ArrToAb($a, $b) {
    foreach($a as $i) self::inc($t,self::corpsIDtoAb($i));
    foreach($b as $i) self::inc($t,self::corpsNameToAb($i['unitName']));
    foreach($t as $k => $v) if ($v !== 2) unset($t[$k]);
    
    $t = array_keys($t);
    
    $o = self::myOrd();
    foreach($t as $v) {
	$i = array_search($v, $o);
	if ($i === false) continue;
	$r[$i] = $v;
    }
    
    ksort($r);
    
    $s = implode(',', $r);
    
    return $s;    
}
    
private static function inc(&$a, $k) {
    
    if (!$k) return;
    
    if (!isset($a[$k])) $a[$k] = 1;
    else $a[$k]++;
}

public static function corpsNameToAb($name) {
    $a = [
	'Carolina Crown' => 'CC',
	'Bluecoats' => 'BlC',
	'Phantom Regiment' => 'PR',
	'Crossmen' => 'XM',
	'Blue Stars' => 'BlS',
	'Music City' => 'Mu',
	'Boston Crusaders' => 'BAC',
	'The Cavaliers' => 'Cav',
	'Blue Devils' => 'BD',
	'Blue Knights' => 'BK',
	'Santa Clara Vanguard' => 'SCV',
	'Spirit of Atlanta' => 'Sp'
	];

    if (isset($a[$name])) return $a[$name];
    else return '';
    
}
    
public static function corpsIDtoAb($id) { 
    $a = [
	'001j000000IWx91AAD' => 'CC',
	'001j000000IWwSrAAL' => 'BlC',
	'001j000000H3XrNAAV' => 'PR',
	'001j000000IWx9AAAT' => 'XM',
	'001j000000IWwSqAAL' => 'BlS',
	'001j000000IWxA5AAL' => 'Mu',
	'001j000000IWwSsAAL' => 'BAC',
	'001j000000IWxAFAA1' => 'Cav',
	'001j000000I6I9SAAV' => 'BD',
	'001j000000IWwSoAAL' => 'BK',
	'001j000000H3XwCAAV' => 'SCV',
	'001j000000IWxADAA1' => 'Sp'
    ];
    
    if (isset($a[$id])) return $a[$id];
    else return '';
}
    
public static function parse($alltxt) {

static $maxFileSize = 1000 * 1000 * 10;
static $maxBraces = 50;
static $maxShows = 150;
static $maxToDo = 150;

$allCnt = 0;
$jsonCnt = 0;

$off0 = 0;
$j = 0;

$bc = 0;

$shows = [];

do {
    $off0 = strpos($alltxt, '{"id":', $j);
    if (!isset($off0) || $off0 === false) {
	kwas($jsonCnt >= 1, 'no json found');
	break;
    }
    
    $j = $off0;
    
    do {
	
	if (!isset($alltxt[$j])) die('reached end of file');
	
	$char = $alltxt[$j];
	
	if ($char === '{') $bc++;
	if ($char === '}') $bc--;
	
	$j++;
	
	if ($bc > $maxBraces || $j > $maxFileSize) die('maxBrace or maxFileSize');
	
	if ($bc <= 0) {
	    
	    $json = substr($alltxt, $off0, $j - $off0);
	    
	    $o = json_decode($json);
	    $jsonCnt++;
	    
	    kwas(isset($o->locationCity), 'no city');
	    $city =    trim($o->locationCity);
	    // $city .= ', ' . $o->locationState;
	    kwas(strlen($city) > 0, 'city name too short');
	    
	    $saleURL = false;
	    if ((isset($o->buyTickets))) {
		$t =   $o->buyTickets;
		if ($t && is_string($t) && strlen(trim($t)) >= 6) $saleURL = $t;
	    }

	    $saleURL = self::normURL($saleURL);
	    
	    $reta['onsale'  ] = $saleURL ? true     : false;
	    $reta['sale_url'] = $saleURL ? $saleURL : '';
	    $reta['date'  ] = $o->startDate;
	    $reta['datets'] = strtotime($o->startDate);
	    $reta['city']   = $city;
	    $reta['state']   = $o->locationState;
	    $reta['dci_id'] = $o->id;
	    $reta['corps']   = isset($o->participants) ? $o->participants : [];
	    $reta['corps2']  = isset($o->schedules) ? $o->schedules : [];
	    $reta['slug']   = $o->slug;
	    $reta['time'] = $o->webStartTime;
	    $reta['tz']   = $o->timeZone;
	    $venue = $o->venue;
	    $venue->city = $city;
	    
	    $reta['venue'] = $venue;
	    	    
	    if ($maxToDo === 1) return $reta;

	    $shows[] = $reta;
	    
	    break; // always break because we found what we needed in the innermost loop
	} // if json
    } while ($j < $maxFileSize && $jsonCnt < $maxToDo);
} while ($jsonCnt < $maxToDo && $allCnt++ < $maxShows);

return $shows;

} // func doit

private static function normURL($uin) {
    if (!isset($uin[0])) return $uin;
    if ($uin[0] === 'H') return 'h' . substr($uin,1);
    return $uin;
}
}