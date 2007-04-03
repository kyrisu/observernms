#!/usr/bin/php
<?

include("config.php");
include("includes/functions.php");

$search = $argv[1] . "$";

$data = trim(`cat ips-scanned.txt | grep alive | cut -d" " -f 1 | egrep $search`);

foreach( explode("\n", $data) as $ip) {
  $snmp = `snmpget -t 0.1 -v2c -c $community $ip sysName.0`;
  if(strstr($snmp, "STRING")) {
    $hostname = trim(str_replace("SNMPv2-MIB::sysName.0 = STRING: ","", $snmp));
    if(mysql_result(mysql_query("SELECT COUNT(id) FROM devices WHERE hostname = '$hostname'"),0) == '0') {
      `./add-host.php $hostname $community v2c`;
      echo("Adding $hostname \n");
    }
  }
}
?>