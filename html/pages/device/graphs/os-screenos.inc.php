<?php
      echo("<div class=graphhead>Processor Utilisation</div>");
      $graph_type = "netscreen_cpu";            include ("includes/print-device-graph.php");
      echo("<div class=graphhead>Memory Usage</div>");
      $graph_type = "netscreen_memory";        	include ("includes/print-device-graph.php");
      echo("<div class=graphhead>Firewall Sessions</div>");
      $graph_type = "netscreen_sessions";       include ("includes/print-device-graph.php");


      include("graphs/netstats.inc.php");
      include("graphs/uptime.inc.php");

?>