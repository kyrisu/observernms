<?php

   if($bg == $list_colour_a) { $bg = $list_colour_b; } else { $bg = $list_colour_a; }
   if ($device['status'] == '0') { $class = "list-device-down"; } else { $class = "list-device"; }
   if ($device['ignore'] == '1') {
     $class = "list-device-ignored";
     if ($device['status'] == '1') { $class = "list-device-ignored-up"; }
   }
   $type = strtolower($device['os']);
   unset($image);

   $image = getImage($device[id]);

   echo("
            <tr bgcolor='$bg'>
             <td width='40' align=center valign=middle>$image</td>
             <td width='300'><span style='font-weight: bold; font-size: 14px;'>" . generatedevicelink($device) . "</span>
             <br />$device[purpose]</td>
             <td width='200'>$device[os]<br />
             $device[version]</td>
             <td width='200'>$device[hardware]<br />
             $device[features]</td>
             <td>" . formatUptime($device[uptime]) . "
             <br />
             $device[location]</td>
             <td width=10>
             </td>
           </tr>");

?>