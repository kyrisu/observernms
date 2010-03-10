<?php

  $descr = $_POST['descr'];
  $ignore = $_POST['ignore'];
  $type = $_POST['type'];
  $disabled = $_POST['disabled'];
  $community = $_POST['community'];
  $snmpver = $_POST['snmpver'];
  $mgmnt_link = $_POST['mgmnt_link'];

if($_POST['editing']){
#FIXME needs more sanity checking!
  $sql = "UPDATE `devices` SET `purpose` = '" . mysql_escape_string($descr) . "', `community` = '" . mysql_escape_string($community) . "', `type` = '$type'";
  $sql .= ", `snmpver` = '" . mysql_escape_string($snmpver) . "', `ignore` = '$ignore',  `disabled` = '$disabled',
  `mgmnt_link` = '$mgmnt_link' WHERE `device_id` = '$_GET[id]'";
  $query = mysql_query($sql);

  $rows_updated = mysql_affected_rows();

  if($rows_updated > 0) {
    $update_message = mysql_affected_rows() . " Device record updated.";
    $updated = 1;
  } elseif ($rows_updated == '0') {
    $update_message = "Device record unchanged. No update necessary.";
    $updated = -1;
  } else {
    $update_message = "Device record update error (".$rows_updated.")";
    $updated = 0;
  }
}

else if ($_POST['sediting']){
 $suquery = mysql_query("SELECT `temp_id`, `user_descr` FROM `temperature` WHERE `device_id`='".$_GET['id']."'");
 $rows_updated = 0;
 $sql = "";
 while($usensor = mysql_fetch_array($suquery)){
     $sql = "UPDATE `temperature` SET `user_descr` = '".mysql_escape_string($_POST['user_descr'.$usensor['temp_id']])."' WHERE `temp_id` = '".$usensor['temp_id']."'";
     $query = mysql_query($sql);
     $rows_updated += mysql_affected_rows();
  }
  
  if($rows_updated > 0) {
    $update_message = $rows_updated . " Device record updated.";
    $updated = 1;
  } elseif ($rows_updated == '0') {
    $update_message = "Device record unchanged. No update necessary.";
    $updated = -1;
  } else {
    $update_message = "Device record update error (".$rows_updated.")";
    $updated = 0;
  }
}
?>
