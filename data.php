<?
/*********************************************************************

These are 2 of the data functions in my Database Manager.

They are called in from a parent page, triggered via AJAX/JavaScript.

/*********************************************************************/		
/* [ Data -> Update ] : Updates existing database values. */
/*********************************************************************/

function data_update($table,$item){
  global $db;	
  $values = inputs($table,$item);
  $sqlValues = '';
  $outputs = array();

  foreach($values as $k => $v){
    ${$k} = $v;
    $sqlValues .= "$k=:$k,";			 
    echo "$k:$v | ";
  }

  $id = $item; //Overwrites Disabled (empty) ID
  $sqlValues = rtrim($sqlValues,',');

  try{
  //Initialize Database
  //Setup SQL string	  
    $sql = "UPDATE $table SET $sqlValues WHERE id=:id";
  //Prepare, bind and transmit Values	  
    $stmt = $db->prepare($sql);
    foreach($values as $k => $v){
      $stmt -> bindValue(":$k",${$k},PDO::PARAM_STR);
    }  
    $stmt -> execute();  
    success($table,'Update',$values);
  }
  catch(Exception $e){
    die("Update Failed");
  }
}

/*********************************************************************/		
/* [ Data -> Delete ] : Removes item from the database.  */
/*********************************************************************/

function data_delete($table,$item) {
  global $db;	
//Convert item to id	
  $id = $item;
  //Setup Database Logic/Commands
  $sql = "DELETE FROM $table WHERE id=:id";	
  $stmt = $db->prepare($sql);
  $stmt -> bindValue(':id', $id, PDO::PARAM_STR);	
  $stmt -> execute();
  //Report Success
  success($table,'Deletion','');
}

/*********************************************************************/
?>
