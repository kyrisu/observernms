<?php print_optionbar_start(28); ?>

<table cellpadding=0 cellspacing=0 class=devicetable width=100%>
  <tr>
  <form method='post' action=''>
    <td width='200'>
      <select name='device_id' id='device_id'>
      <option value=''>All Devices</option>
      <?php
        $query = mysql_query("SELECT `device_id`,`hostname` FROM `devices` GROUP BY `hostname` ORDER BY `hostname`");
        while($data = mysql_fetch_array($query)) {
          echo("<option value='".$data['device_id']."'");
          if($data['device_id'] == $_POST['device_id']) { echo("selected"); }
          echo(">".$data['hostname']."</option>");
        }
      ?>
      </select>
    </td>
    <td width='200'>
      <select name='interface' id='interface'>
      <option value=''>All Interfaces</option>
      <option value='Loopback%' <?php if($_POST['interface'] == "Loopback%"){ echo("selected");} ?> >Loopbacks</option>
      <option value='Vlan%' <?php if($_POST['interface'] == "Vlan%"){ echo("selected");} ?> >VLANs</option>
      </select>
    </td>
    <td>
    </td>
    <td width=400>
     <input type="text" name="address" id="address" size=40 value="<?php  echo($_POST['address']); ?>" />
     <input style="align:right;" type=submit class=submit value=Search></div>
    </td>
  </form>

  </tr>
</table>

<?php print_optionbar_end(); ?>


<?php

echo("<table width=100% cellpadding=2 cellspacing=0>");

if($_POST['device_id']) { $where .= " AND I.device_id = '".$_POST['device_id']."'"; }
if($_POST['interface']) { $where .= " AND I.ifDescr LIKE '".$_POST['interface']."'"; }

$sql = "SELECT * FROM `ipv6_addresses` AS A, `ports` AS I, `devices` AS D, `ipv6_networks` AS N WHERE I.interface_id = A.interface_id AND I.device_id = D.device_id AND N.ipv6_network_id = A.ipv6_network_id $where ORDER BY A.ipv6_address";

$query = mysql_query($sql);

echo("<tr class=tablehead><td width=0></td><th>Device</a></th><th>Interface</th><th>Address</th><th>Description</th></tr>");

$row = 1;

while($interface = mysql_fetch_array($query)) {

  if($_POST['address']) { 
    list($addy, $mask) = explode("/", $_POST['address']);
    if(!$mask) { $mask = "128"; }
     if(!Net_IPv6::isInNetmask ( $interface['ipv6_address'], $addy, $mask )) { $ignore = 1; } else { $ignore = 0;}
  }

if(!$ignore) {

  if(is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

  $speed = humanspeed($interface['ifSpeed']);
  $type = humanmedia($interface['ifType']);

  list($prefix, $length) = explode("/", $interface['ipv6_network']);

    if($interface['in_errors'] > 0 || $interface['out_errors'] > 0) {
    $error_img = generateiflink($interface,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
  } else { $error_img = ""; }

  if( interfacepermitted($interface['interface_id']) )
  {
    echo("<tr bgcolor=$row_colour><td></td>
          <td class=list-bold>" . generatedevicelink($interface) . "</td>
          <td class=list-bold>" . generateiflink($interface, makeshortif(fixifname($interface['ifDescr']))) . " $error_img</td>
          <td>" . Net_IPv6::compress($interface['ipv6_address']) . "/".$length."</td>
          <td>" . $interface['ifAlias'] . "</td>
        </tr>\n");

    $row++;

  }

}

unset($ignore);

}

echo("</table>");


?>
