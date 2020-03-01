<?php

function lastChange($l, $dao) {
    
    $es = $dao->getRunning($l['dci_id'], $l['ts']);
    
    $lts = $l['ts'];
    $ets = $lts;
	wipeTimeSen($l);
    foreach($es as $e) {
	$ets = $e['ts'];
	$r =  $e['r'];
		
	wipeTimeSen($e);
	$d = $e == $l;
	$t = recursive_array_diff($e, $l);
	if (!$d && $t) {

	    return date('n/j', $ets);
	}
    }
    
    return date('n/j', $ets);
}

function wipeTimeSen(&$a) {
    unset($a['ts'], $a['_id'], $a['r'], $a['sfx'], $a['resLen'], $a['venue']['googleMapsStaticMap']);
}


function recursive_array_diff($a1, $a2) { 
    $r = array(); 
    foreach ($a1 as $k => $v) {
        if (array_key_exists($k, $a2)) { 
            if (is_array($v)) { 
                $rad = recursive_array_diff($v, $a2[$k]); 
                if (count($rad)) { $r[$k] = $rad; } 
            } else { 
                if ($v != $a2[$k]) { 
                    $r[$k] = $v; 
                }
            }
        } else { 
            $r[$k] = $v; 
        } 
    } 
    return $r; 
} // https://stackoverflow.com/questions/5911067/compare-object-properties-and-show-diff-in-php
