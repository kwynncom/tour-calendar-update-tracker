<?php

require_once('dao.php');
require_once('shows_2020.php');
require_once('changes.php');
require_once('dci.php');

function getHT() {
    $dao = new dci_dao();
    $ht  = '';
    
    $shows = ['Mboro', 'Col', 'ATL'];

    
    foreach($shows as $show) {
    
    $shid = getShowID($show);
    $sho  = $dao->getShowsLatest($shid);

    $ht .=  '<div>';
    
    $v = $sho['venue'];
    
    $ht .= '<h3>';
    
    $slug = getShowURL($sho);
    
    if ($slug) $ht .= "<a href='$slug'>";
    $ht .= $sho['city'];
    if ($slug) $ht .= "</a>";

    $vnm = substr($v['name'], 0, 13);
    $ht .= " <span class='vname'>$vnm</span></h3>\n";
    $ht .= '<p>';
    
    $ds = date('D, F j ', $sho['datets']);
    
    if ($show === 'ATL') $ht .= $ds;
    
    if ($show !== 'ATL') {
    
    if (isset($sho['time']) && $sho['time']) $ds .= $sho['time'] . ' ';
    if ((isset($sho['time']) && isset($sho['tz'])) && $sho['tz'])   $ds .= $sho['tz'] . ' ';    
    
    $ht .= $ds;
    
    // $sho['onsale'] = true;
    
    if ($show === 'Mboro') $class = ' class="Mbonsale" ';
    else                   $class = '';
    
    $ht .= '<br/>on sale : ' . ($sho['onsale'] ? ("<span $class>Y</span>") : 'N');

    $corps = dci::corps2ArrToAb($sho['corps'], $sho['corps2']);
    $ht .= '<br/>' . $corps;
    
    }
    $ht .= '<br/>';
    $lc = lastChange($sho, $dao);
    
    $ht .= 'asof ' . date('n/j H:i', $sho['ts']) . ', since ' . $lc;
    $ht .= '</p>';
    $ht .= '</div>';
    }
    
    return $ht;
}

function getSaleURL($o) {
    if (!isset($o['onsale']) || !$o['onsale']) return 'N';
    if (!isset($o['sale_url'])) return 'N';
    
    $ht = "<a href='$o[sale_url]'>Y</a>";
    return $ht;
    
}

function getShowURL($s) {
	$slug = isset($s['slug']) ? $s['slug'] : '';
	
	if ($slug) $slug = 'https://www.dci.org/events/' . $slug;  
	
	if ($slug) return $slug;
	else       return '';
	
}
