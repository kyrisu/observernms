<?

include("unix-graphing.php");

function pollDeviceUnix() {
   global $device;
   global $community;
   global $rrdtool;
   $id = $device['id'];
   $hostname = $device['hostname'];
   $hardware = $device['hardware'];
   $version = $device['version'];
   $features = $device['features'];
   if($device['apache'] == "1") { $apache = "yes"; }
   $loadrrd  = "rrd/" . $hostname . "-load.rrd";
   $loadgraph = "public_html/graphs/" . $hostname . "-load.png";
   $cpurrd   = "rrd/" . $hostname . "-cpu.rrd";
   $cpugraph = "public_html/graphs/" . $hostname . "-cpu.png";   
   $memrrd   = "rrd/" . $hostname . "-mem.rrd";
   $memgraph = "public_html/graphs/" . $hostname . "-mem.png";
   $sysrrd   = "rrd/" . $hostname . "-sys.rrd";
   $sysgraph = "public_html/graphs/" . $hostname . "-sys.png";

   ## Check Disks
   $dq = mysql_query("SELECT * FROM storage WHERE host_id = '$id'");
   while ($dr = mysql_fetch_array($dq)) {
     $hrStorageIndex = $dr['hrStorageIndex'];
     $hrStorageAllocationUnits = $dr['hrStorageAllocationUnits'];
     $hrStorageSize = $dr['hrStorageAllocationUnits'] * $dr['hrStorageSize']; 
     $hrStorageDescr = $dr['hrStorageDescr'];
     $used = `snmpget -O qv -v2c -c $community $hostname hrStorageUsed.$hrStorageIndex`;
     $used = $used * $hrStorageAllocationUnits;
     $perc = $used / $hrStorageSize * 100;

     $filedesc = str_replace("\"", "", str_replace("/", "_", $hrStorageDescr));
     $storerrd  = "rrd/" . $hostname . "-storage-" . $filedesc . ".rrd";
     if (!is_file($storerrd)) {
       `rrdtool create $storerrd \
        --step 300 \
        DS:size:GAUGE:600:0:U \
        DS:used:GAUGE:600:0:U \
        DS:perc:GAUGE:600:0:U \
        RRA:AVERAGE:0.5:1:800 \
        RRA:AVERAGE:0.5:6:800 \
        RRA:AVERAGE:0.5:24:800 \
        RRA:AVERAGE:0.5:288:800 \
        RRA:MAX:0.5:1:800 \
        RRA:MAX:0.5:6:800 \
        RRA:MAX:0.5:24:800 \
        RRA:MAX:0.5:288:800`;
     }  
     rrd_update($storerrd, "N:$hrStorageSize:$used:$perc");
   }

   ## Set OIDs
   $oid_ssCpuRawUser         = ".1.3.6.1.4.1.2021.11.50.0";
   $oid_ssCpuRawNice         = ".1.3.6.1.4.1.2021.11.51.0";
   $oid_ssCpuRawSystem       = ".1.3.6.1.4.1.2021.11.52.0";
   $oid_ssCpuRawIdle         = ".1.3.6.1.4.1.2021.11.53.0";
   $oid_hrSystemProcesses    = ".1.3.6.1.2.1.25.1.6.0";
   $oid_hrSystemNumUsers     = ".1.3.6.1.2.1.25.1.5.0";

   $s = `snmpget -O qv -v2c -c $community $hostname $oid_ssCpuRawUser $oid_ssCpuRawSystem $oid_ssCpuRawNice $oid_ssCpuRawIdle $oid_hrSystemProcesses $oid_hrSystemNumUsers .1.3.6.1.4.1.2021.1.101.1`;
   list ($cpuUser, $cpuSystem, $cpuNice, $cpuIdle, $procs, $users, $cputemp) = explode("\n", $s);

   ## Create CPU RRD if it doesn't already exist
   if (!is_file($cpurrd)) {
      `rrdtool create $cpurrd \
        --step 300 \
        DS:user:COUNTER:600:0:U \
        DS:system:COUNTER:600:0:U \
        DS:nice:COUNTER:600:0:U \
        DS:idle:COUNTER:600:0:U \
        RRA:AVERAGE:0.5:1:800 \
        RRA:AVERAGE:0.5:6:800 \
        RRA:AVERAGE:0.5:24:800 \
        RRA:AVERAGE:0.5:288:800 \
        RRA:MAX:0.5:1:800 \
        RRA:MAX:0.5:6:800 \
        RRA:MAX:0.5:24:800 \
        RRA:MAX:0.5:288:800`;
   }
   rrd_update($cpurrd,  "N:$cpuUser:$cpuSystem:$cpuNice:$cpuIdle");


 ## If the device isn't monowall or pfsense, monitor all the pretty things
 if($device[os] != "m0n0wall" && $device[os] != "Voswall" && $device[os] != "pfSense" ) {
   if (!is_file($sysrrd)) {
      `rrdtool create $sysrrd \
        --step 300 \
        DS:users:GAUGE:600:0:U \
        DS:procs:GAUGE:600:0:U \
        RRA:AVERAGE:0.5:1:800 \
        RRA:AVERAGE:0.5:6:800 \
        RRA:AVERAGE:0.5:24:800 \
        RRA:AVERAGE:0.5:288:800 \
        RRA:MAX:0.5:1:800 \
        RRA:MAX:0.5:6:800 \
        RRA:MAX:0.5:24:800 \
        RRA:MAX:0.5:288:800`;
   }

   if (!is_file($memrrd)) {
      `rrdtool create $memrrd \
       --step 300 \
       DS:totalswap:GAUGE:600:0:10000000000 \
       DS:availswap:GAUGE:600:0:10000000000 \
       DS:totalreal:GAUGE:600:0:10000000000 \
       DS:availreal:GAUGE:600:0:10000000000 \
       DS:totalfree:GAUGE:600:0:10000000000 \
       DS:shared:GAUGE:600:0:10000000000 \
       DS:buffered:GAUGE:600:0:10000000000 \
       DS:cached:GAUGE:600:0:10000000000 \
       RRA:AVERAGE:0.5:1:800 \
       RRA:AVERAGE:0.5:6:800 \
       RRA:AVERAGE:0.5:24:800 \
       RRA:AVERAGE:0.5:288:800 \
       RRA:MAX:0.5:1:800 \
       RRA:MAX:0.5:6:800 \
       RRA:MAX:0.5:24:800 \
       RRA:MAX:0.5:288:800`;
   }

   if(!is_file($loadrrd)) {
    `$rrdtool create $loadrrd \
    --step 300 \
    DS:1min:GAUGE:600:0:5000 \
    DS:5min:GAUGE:600:0:5000 \
    DS:15min:GAUGE:600:0:5000 \
    RRA:AVERAGE:0.5:1:800 \
    RRA:AVERAGE:0.5:6:800 \
    RRA:AVERAGE:0.5:24:800 \
    RRA:AVERAGE:0.5:288:800 \
    RRA:MAX:0.5:1:800 \
    RRA:MAX:0.5:6:800 \
    RRA:MAX:0.5:24:800 \
    RRA:MAX:0.5:288:800`;
   }

   $mem_get = "memTotalSwap.0 memAvailSwap.0 memTotalReal.0 memAvailReal.0 memTotalFree.0 memShared.0 memBuffer.0 memCached.0";
   $mem_raw = `snmpget -O qv -v2c -c $community $hostname $mem_get`;
   list($memTotalSwap, $memAvailSwap, $memTotalReal, $memAvailReal, $memTotalFree, $memShared, $memBuffer, $memCached) = explode("\n", $mem_raw); 

   $load_get = "laLoadInt.1 laLoadInt.2 laLoadInt.3";
   $load_raw = `snmpget -O qv -v2c -c $community $hostname $load_get`;
   list ($load1, $load5, $load10) = explode ("\n", $load_raw);

   rrd_update($sysrrd,  "N:$users:$procs");
   rrd_update($loadrrd, "N:$load1:$load5:$load10");
   rrd_update($memrrd,  "N:$memTotalSwap:$memAvailSwap:$memTotalReal:$memAvailReal:$memTotalFree:$memShared:$memBuffer:$memCached");

  if($device['temp'] = 1) {
    $temprrd = "rrd/" . $hostname . "-temp.rrd";   
    $cputemp = str_replace("\"", "", $cputemp);
    if (!is_file($temprrd)) {
      $rrdcreate = `rrdtool create $temprrd --step 300 \
                    DS:cputemp:GAUGE:600:-25:125 \
                    DS:systemp:GAUGE:600:-25:125 \
                    RRA:AVERAGE:0.5:1:2000 \
                    RRA:AVERAGE:0.5:6:2000 \
                    RRA:AVERAGE:0.5:24:2000 \
                    RRA:AVERAGE:0.5:288:2000 \
                    RRA:MAX:0.5:1:2000 \
                    RRA:MAX:0.5:6:2000 \
                    RRA:MAX:0.5:24:2000 \
                    RRA:MAX:0.5:288:2000`;
      }
    rrd_update($temprrd, "N:$cputemp:0");
  }


  if($apache) {
   $apacherrd = "rrd/" . $hostname . "-apache.rrd";
   if(!is_file($apacherrd)) {
   $woo= `rrdtool create $apacherrd         \
           DS:bits:COUNTER:600:U:10000000   \
           DS:hits:COUNTER:600:U:10000000  \
           RRA:AVERAGE:0.5:1:800      \
           RRA:AVERAGE:0.5:6:700      \
           RRA:AVERAGE:0.5:24:775     \
           RRA:AVERAGE:0.5:288:797    \
           RRA:MAX:0.5:1:800          \
           RRA:MAX:0.5:6:700          \
           RRA:MAX:0.5:24:775         \
           RRA:MAX:0.5:288:797`;
  }

  list($ahits,$abits) = explode("\n", `./get-apache.sh $hostname`);  
  $abits = $abits * 8;

  rrd_update($apacherrd,"N:$abits:$ahits");
 }
} // End Monowall Test

}
