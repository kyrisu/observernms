<?php

$check = `checkers/check_simap -H $service[hostname]`;

list($check, $time) = split("\|", $check);

if(strstr($check, "SIMAP OK")) { 
  $status = '1'; 
} else { 
  $status = '0'; 
}

?>