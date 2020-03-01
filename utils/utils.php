<?php

function llInGSM($l1, $l2, $g) { 
    $s1 = strpos($g, (string)$l1);
    $s2 = strpos($g, (string)$l2);
    
    return $s1 && $s2;
    
}