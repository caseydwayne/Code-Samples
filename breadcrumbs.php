<?
/*********************************************************************/	
// [ Breadcrumbs ]  :: Creates breadcrumb navigation based on URL
/*********************************************************************/			

function breadcrumbs($url){
  if($url!=='pages/home.php'){
	$url = explode('.',$url);
	$url = explode('/',$url[0]);
	
	$i = 0;
	$l = count($url);
	$count = array();
	
	echo '<div class="breadcrumbs">';
	echo '<span class="title">Breadcrumbs: </span>';
	foreach($url as $crumb){
	  $i++;	  
	  $name = str_replace('-',' ',$crumb);
	  if(strpos($crumb,'pages')!==false){ 
		$name = 'home'; 
	  }	  
	  else {		  
  	    array_push($count,$crumb);
	  }
	  if($crumb==='home'||$name==='home') $href = 'home';
	  else $href = implode('/',$count);	  
	  $name = ucwords($name);
  	  if($i===$l){
	    echo '<span class="current-page">'.$name."</span>";	
	  }
	  else echo '<a href="'.$href.'">'.$name."</a>";	
	  if($i!=($l)){ echo ' < ';}
	}
	echo "</div>\n";
  }
}

/*********************************************************************/				
?>