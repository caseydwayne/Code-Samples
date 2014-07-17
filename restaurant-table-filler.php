<?
/*********************************************************************

This is something I did to help someone out. 

It fills tables for events using an assoc array and some rather tricky math and methods.

Criteria was that as many employees from their respective company were seated together as much as possible.

/*********************************************************************/
?>

<!doctype html>
<html>
<head>
<meta charset="utf-$table_size">
<title>Untitled Document</title>
<style>
#table-table { max-width:600px; margin:auto; text-align:center; }
#table-table table { background:#03F; color:#FFF; float:left; margin:.4em; }
#table-table table th { background:#039; }
#table-table table td { background:#06F; padding:.2em; }
#table-table table td:first-of-type { text-align:right; }
#table-table table .open { background:rgba(255,255,0,.5); }
#table-table table .over { background:rgba(204,0,51,.5); }
#table-table #summary { clear:both; }
</style>
</head>

<body>

<?

$companies = array(  
  'Verizon' => 74,  
  'GE' => 42,
  'JP Morgan' => 38,
  'ATT' => 38,
  'Chevy' => 11,
  'Gray' => 4,
  'Individuals' => 0
);

function completeList($companies,$table_size){
  echo "<table>\n";
  echo "<tr><th>Company</th><th>Total</th></tr>\n";  
	foreach($companies as $c => $q)
	  echo "<tr><td>$c</td><td>$q</td></tr>\n";	
  echo "</table>\n\n";	  	
  fullTables($companies,$table_size);
}

function fullTables($companies,$table_size){
  $straglers = array();	
  $extras = 0;
  echo "<table>\n";
  echo "<tr><th>Company</th><th>Full Tables</th><th>Extras</th></tr>\n";
  foreach($companies as $name => $qty){
    echo "<tr><td>$name</td>";
	$tables = $qty/$table_size;
	if(!strpos($tables,'.')) $tables = number_format($tables,1);
    $totals = explode('.',$tables);	
    $extras = $qty;
	if($qty>1) $extras = (intval($totals[1])/100)*$table_size;
    if($extras>$table_size && $qty>=$table_size) $extras = $extras/10;
	if($extras<1 && $extras!=0) $extras = $extras*10;
	if($qty<=$table_size) $extras = $qty/1;
    echo "<td>$totals[0]</td><td>{$extras}</td>";	
	$straglers[$name] = $extras;
    echo "</tr>\n";
  }
  echo "</table>\n\n";
  mixRemaining($companies,$straglers,$table_size);
}

function mixRemaining($companies,$straglers,$table_size){
  $assoc = array();
  $numeric = array();
  foreach($straglers as $c => $q){
	array_push($assoc,$c);
	array_push($numeric,$q);
  }
  $i = 0;
  $l = count($straglers);
  $jokers = 0;
  $individuals = 0;

  echo "<table>\n";
  echo "<tr><th>Companies</th><th>Grouped</th><th>Supply</th></tr>\n";
  while($i<$l){	
	if($i!=$l-1 && $numeric[$i]>0){
  	  $members = $numeric[$i]+$numeric[$i+1];

	  if($members>$table_size){
		$jokers = $members-$table_size;
		$remainder = $numeric[$i+1]-$jokers;
		$members = $numeric[$i]+$remainder;
	  } else {
		$spaces_left = $table_size-$members;
		$jokers = 0;
	  }

		$mingled = "$assoc[$i] and {$assoc[$i+1]}";
	      echo "<tr><td>$mingled</td>";	
		  echo "<td>($numeric[$i]+{$numeric[$i+1]}) $members</td>";		
		  
		  if($jokers){
			echo "<td class=\"open\">$jokers extra {$assoc[$i+1]} members</td>";
		  } else echo "<td class=\"over\">$spaces_left spaces left</td>";
		
		  if($jokers && $i===($l-1)) echo "<td>[+$jokers extra {$assoc[$i+1]} member(s) in last table.]</td>";		  
	 
      $i++;
      echo "</tr>\n";  	  	
	} else {
      ($jokers>0) ? $final =  "$jokers extra people." : $final = 'all tables full!';
	  break;
	}
  }
  echo "</table>\n\n";
  summary($companies,$straglers,$table_size,$final);
}

function summary($companies,$straglers,$table_size,$final){
  $total_companies = count($companies);
  $total_people = array_sum($companies);
  $total_tables = $total_people/$table_size;
//  $total_tables = 0;
  
  echo "<h4 id=\"summary\">$total_companies companies with $total_people people using $total_tables tables leaving $final</h4>\n";
}

?>

<div id="table-table">
<h4>Assuming you have a maxium capacity of 8 per table:</h4>
<? completeList($companies,8); ?>
</div>

</body>
</html>