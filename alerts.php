#!/usr/bin/php
<?php

include("config.php");
include("includes/functions.php");

$alert_query = mysql_query("SELECT *, A.id as id FROM `alerts` as A, `devices` as D where A.device_id = D.id AND alerted = '0'");
while ($alert = mysql_fetch_array($alert_query)) {
  $id = $alert['id'];
  $host = $alert['hostname'];
  $date = $alert['time_logged'];
  $msg = $alert['message'];
  $alert_text .= "$date $host $msg";

  mysql_query("UPDATE `alerts` SET alerted = '1' WHERE `id` = '$id'");

}

if($alert_text) {
  echo("$alert_text");
#  `echo '$alert_text' | gnokii --sendsms 07885729982`;
}


?>