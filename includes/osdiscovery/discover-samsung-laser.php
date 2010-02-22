<?php

if(!$os) {
  if(preg_match("/^Samsung CLP/", $sysDescr)) { $os = "samsung-laser"; }
}

?>
