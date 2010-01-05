<?php

$community = $device['community'];

echo("CISCO-CDP-MIB: ");
   
unset($cdp_array);
$cdp_array = snmpwalk_cache_twopart_oid("cdpCache", $device, $cdp_array, "CISCO-CDP-MIB");
$cdp_array = $cdp_array[$device[device_id]];
if($cdp_array) {
  unset($cdp_links);
  foreach( array_keys($cdp_array) as $key) { 
    $interface = mysql_fetch_array(mysql_query("SELECT * FROM `interfaces` WHERE device_id = '".$device['device_id']."' AND `ifIndex` = '".$key."'"));
    $cdp_if_array = $cdp_array[$key]; 
    foreach( array_keys($cdp_if_array) as $entry_key) {
      $cdp_entry_array = $cdp_if_array[$entry_key];
      if($device['hostname'] && $interface['ifIndex'] && $cdp_entry_array['cdpCacheDeviceId'] && $cdp_entry_array['cdpCacheDevicePort']){
        if(strpos($cdp_entry_array['cdpCacheDeviceId'], ")")) { list(,$cdp_entry_array['cdpCacheDeviceId']) = explode("(", $cdp_entry_array['cdpCacheDeviceId']); echo($cdp_entry_array['cdpCacheDeviceId']); 
                                                               list($cdp_entry_array['cdpCacheDeviceId'],) = explode(")", $cdp_entry_array['cdpCacheDeviceId']); echo($cdp_entry_array['cdpCacheDeviceId']); }

        $cdp_links .= $device['hostname'] . "," . $interface['ifIndex'] . "," . $cdp_entry_array['cdpCacheDeviceId'] . "," . $cdp_entry_array['cdpCacheDevicePort'] . "\n";
      }
    }     
  }
}
if($debug) {echo("$cdp_links");}

echo("\nLLDP-MIB: ");

/*
LLDP-MIB::lldpRemChassisIdSubtype.0.25.1 = INTEGER: macAddress(4)
LLDP-MIB::lldpRemChassisIdSubtype.0.26.1 = INTEGER: macAddress(4)
LLDP-MIB::lldpRemChassisId.0.25.1 = Hex-STRING: 00 30 48 74 88 A8
LLDP-MIB::lldpRemChassisId.0.26.1 = Hex-STRING: 00 30 48 73 C9 7C
LLDP-MIB::lldpRemPortIdSubtype.0.25.1 = INTEGER: interfaceName(5)
LLDP-MIB::lldpRemPortIdSubtype.0.26.1 = INTEGER: interfaceName(5)
LLDP-MIB::lldpRemPortId.0.25.1 = STRING: "eth0"
LLDP-MIB::lldpRemPortId.0.26.1 = STRING: "eth0"
LLDP-MIB::lldpRemPortDesc.0.25.1 = STRING:
LLDP-MIB::lldpRemPortDesc.0.26.1 = STRING:
LLDP-MIB::lldpRemSysName.0.25.1 = STRING: tequila.powersource.cx
LLDP-MIB::lldpRemSysName.0.26.1 = STRING: terminator.powersource.cx
LLDP-MIB::lldpRemSysDesc.0.25.1 = STRING: Linux 2.6.18-6-xen-vserver-686 #1 SMP Tue May 5 05:22:28 UTC 2009 i686
LLDP-MIB::lldpRemSysDesc.0.26.1 = STRING: Linux 2.6.26-2-xen-686 #1 SMP Wed Nov 4 23:23:33 UTC 2009 i686
LLDP-MIB::lldpRemSysCapSupported.0.25.1 = BITS: 01 stationOnly(7)
LLDP-MIB::lldpRemSysCapSupported.0.26.1 = BITS: 20 bridge(2)
LLDP-MIB::lldpRemSysCapEnabled.0.25.1 = BITS: 01 stationOnly(7)
LLDP-MIB::lldpRemSysCapEnabled.0.26.1 = BITS: 20 bridge(2)
LLDP-MIB::lldpRemManAddrIfSubtype.0.25.1.1.4.195.160.167.65 = INTEGER: ifIndex(2)
LLDP-MIB::lldpRemManAddrIfSubtype.0.25.1.2.16.32.1.6.124.0.92.2.0.2.48.72.255.254.116.136.168 = INTEGER: ifIndex(2)
LLDP-MIB::lldpRemManAddrIfSubtype.0.26.1.1.4.195.160.167.97 = INTEGER: ifIndex(2)
LLDP-MIB::lldpRemManAddrIfSubtype.0.26.1.2.16.32.1.6.124.0.92.2.0.2.48.72.255.254.115.201.124 = INTEGER: ifIndex(2)
LLDP-MIB::lldpRemManAddrIfId.0.25.1.1.4.195.160.167.65 = INTEGER: 2
LLDP-MIB::lldpRemManAddrIfId.0.25.1.2.16.32.1.6.124.0.92.2.0.2.48.72.255.254.116.136.168 = INTEGER: 2
LLDP-MIB::lldpRemManAddrIfId.0.26.1.1.4.195.160.167.97 = INTEGER: 2
LLDP-MIB::lldpRemManAddrIfId.0.26.1.2.16.32.1.6.124.0.92.2.0.2.48.72.255.254.115.201.124 = INTEGER: 2
LLDP-MIB::lldpRemManAddrOID.0.25.1.1.4.195.160.167.65 = OID: SNMPv2-SMI::zeroDotZero
LLDP-MIB::lldpRemManAddrOID.0.25.1.2.16.32.1.6.124.0.92.2.0.2.48.72.255.254.116.136.168 = OID: SNMPv2-SMI::zeroDotZero
LLDP-MIB::lldpRemManAddrOID.0.26.1.1.4.195.160.167.97 = OID: SNMPv2-SMI::zeroDotZero
LLDP-MIB::lldpRemManAddrOID.0.26.1.2.16.32.1.6.124.0.92.2.0.2.48.72.255.254.115.201.124 = OID: SNMPv2-SMI::zeroDotZero
LLDP-MIB::lldpRemOrgDefInfo.0.25.1.0.18.15.4.1 = Hex-STRING: 05 F2
LLDP-MIB::lldpRemOrgDefInfo.0.26.1.0.18.15.4.1 = Hex-STRING: 05 F2
*/
   
unset($lldp_array);
$lldp_array = snmpwalk_cache_threepart_oid("lldpRemoteSystemsData", $device, $lldp_array, "LLDP-MIB");
$lldp_array = $lldp_array[$device[device_id]];
if($lldp_array) {
  unset($lldp_links);
  foreach( array_keys($lldp_array) as $key) { 
    $lldp_if_array = $lldp_array[$key]; 
    foreach( array_keys($lldp_if_array) as $entry_key) {
      $interface = mysql_fetch_array(mysql_query("SELECT * FROM `interfaces` WHERE device_id = '".$device['device_id']."' AND `ifIndex` = '".$entry_key."'"));
      $lldp_entry_array = $lldp_if_array[$entry_key];
      foreach ( array_keys($lldp_entry_array) as $entry_instance) {
      $lldp_entry_instance_array = $lldp_entry_array[$entry_instance];
        if($device['hostname'] && $interface['ifIndex'] && $lldp_entry_instance_array['lldpRemSysName'] && $lldp_entry_instance_array['lldpRemPortId']){
          if(strpos($lldp_entry_instance_array['lldpRemSysName'], ")")) { list(,$lldp_entry_instance_array['lldpRemSysName']) = explode("(", $lldp_entry_instance_array['lldpRemSysName']); echo($lldp_entry_instance_array['lldpRemSysName']); 
                                                               list($lldp_entry_instance_array['lldpRemSysName'],) = explode(")", $lldp_entry_instance_array['lldpRemSysName']); echo($lldp_entry_instance_array['lldpRemSysName']); }

          $lldp_links .= $device['hostname'] . "," . $interface['ifIndex'] . "," . $lldp_entry_instance_array['lldpRemSysName'] . "," . str_replace('"','',$lldp_entry_instance_array['lldpRemPortId']) . "\n";
        }
      }
    }     
  }
}
if($debug) {echo("$lldp_links");}

$discovered_links = $cdp_links . $lldp_links;

echo "\n";

if($discovered_links != "\n") {
  foreach ( explode("\n" ,$discovered_links) as $link ) {
    if ($link == "") { break; }
    list($src_host,$src_if, $dst_host, $dst_if) = explode(",", $link);
    $dst_host = strtolower($dst_host);
    $dst_if = strtolower($dst_if);
    $src_host = strtolower($src_host);
    $src_if = strtolower($src_if);
    $ip = gethostbyname($dst_host);
    if ( match_network($config['nets'], $ip) ) {
      if ( mysql_result(mysql_query("SELECT COUNT(*) FROM `devices` WHERE `sysName` = '$dst_host' OR `hostname`='$dst_host'"), 0) == '0' ) {
        if($config['lldp_autocreate']) {
          echo("++ Creating: $dst_host \n");
          createHost ($dst_host, $community, "v2c");
        }
      } else {
        echo(".");
      }
    } else {
      echo("!($dst_host)");
    }

    $dst_if_id   = @mysql_result(mysql_query("SELECT I.interface_id FROM `interfaces` AS I, `devices` AS D WHERE (`ifDescr` = '$dst_if' OR `ifName`='$dst_if') AND (sysName = '$dst_host' OR hostname='$dst_host') AND D.device_id = I.device_id"), 0);

    if ( mysql_result(mysql_query("SELECT COUNT(*) FROM `devices` WHERE `sysName` = '$dst_host' OR `hostname`='$dst_host'"), 0) == '1' && 
      mysql_result(mysql_query("SELECT COUNT(*) FROM `devices` WHERE `hostname` = '$src_host'"), 0) == '1' &&
      $dst_if_id && 
      mysql_result(mysql_query("SELECT COUNT(*) FROM `interfaces` AS I, `devices` AS D WHERE `ifIndex` = '$src_if' AND hostname = '$src_host' AND D.device_id = I.device_id"), 0) == '1')
    {
      $src_if_id   = mysql_result(mysql_query("SELECT I.interface_id FROM `interfaces` AS I, `devices` AS D WHERE `ifIndex` = '$src_if' AND hostname = '$src_host' AND D.device_id = I.device_id"), 0);
      $link_exists[] = $src_if_id . "," . $dst_if_id;
      if ( mysql_result(mysql_query("SELECT COUNT(*) FROM `links` WHERE `dst_if` = '$dst_if_id' AND `src_if` = '$src_if_id'"),0) == '0') 
      { 
        $sql = "INSERT INTO `links` (`src_if`, `dst_if`) VALUES ('$src_if_id', '$dst_if_id')";
        mysql_query($sql);
        echo("\n++($src_host $src_if -> $dst_host $dst_if)");
      } else { 
        echo(".."); 
      }
    } else {

    } 
  }
}


$sql = "SELECT * FROM `links` AS L, `interfaces` AS I, `devices` AS D WHERE L.src_if = I.interface_id AND I.device_id = D.device_id AND D.device_id = '".$device['device_id']."'";
$query = mysql_query($sql);

while ($test_link = mysql_fetch_array($query)) {
  unset($exists);
  $i = 0;
  while ($i < count($link_exists) && !$exists) {
    $this_link = $test_link['src_if'] . ",". $test_link['dst_if'];
    if ($link_exists[$i] == $this_link) { $exists = 1; }
    $i++;
  }
  if(!$exists) {
    echo("-");
    mysql_query("DELETE FROM `links` WHERE `src_if` = '".$test_link['src_if']."' AND `dst_if` = '".$test_link['dst_if']."'");
    if($debug) { echo($link_exists[$i] . " REMOVED \n"); }
  } else {
    if($debug) { echo($link_exists[$i] . " VALID \n"); }
  }
}

echo("\n");

?>
