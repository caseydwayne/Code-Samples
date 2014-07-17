<? 
/*********************************************************************

This code receives either POST or GET data from a serialized form.

It then 

 - sanitizes
 - filters out bots
 - sends an HTML email
 - returns a thank you message on success or error on halt.

 ( Script is validated clientside via JavaScript as well )

/*********************************************************************/

/*********************************************************************/
// [ Email Parameters ]
/*********************************************************************/      

global $server,$ip,$logfile;
$server = 'server@creativedesigninfluence.com';

$to = 'caseydwayne@creativedesigninfluence.com';
$title = 'New Website Submission';
$receipt = <<<EOT
<h4>Thank you!</h4>
<blockquote class='swoosh'>Your submission has been received.</blockquote>
<p class='closer'>We will review your message as soon as possible.</p>
EOT;

/*********************************************************************/
// [ System Parameters ] :: do not edit
/*********************************************************************/      

$error = ''; //leave blank
$ip = $_SERVER['REMOTE_ADDR'];
$logfile = 'email-log.txt';

//Check for bot submission

	if(!empty($_REQUEST['hun'])){
	  echo "Bot Detected. IP Address $ip blocked.";
	  logger($logfile,"Ban: $ip");
	  die;
	}

	$is_empty = 0;	
	$inputs = !empty($HTTP_GET_VARS) ? $HTTP_GET_VARS : $_POST;
	if(empty($inputs)) $inputs = $_REQUEST;
	foreach($inputs as $r) if(empty($r) || strlen($r)<2) $is_empty++;	
	if($is_empty===count($inputs)){ 	
	  echo 'Empty form detected. Submission failed.';
	  logger($logfile,"Empty: $ip");
	  die; 
	}

/*********************************************************************/
// [ Receive and Sanitize Input ]
/*********************************************************************/

	! empty($_REQUEST['name']) 
	? $name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING) 
	: $error .= 'no name supplied' && $name = 'None';
	
	! empty($_REQUEST['email']) 
	? $email = filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) 
	: $error .= 'no name supplied' && $email = $server;
	
	! empty($_REQUEST['phone']) 
	? $phone = filter_var($_REQUEST['phone'], FILTER_SANITIZE_STRING)
	: $error .= 'no phone supplied' && $phone = 'No valid phone supplied';
	
	! empty($_REQUEST['message']) 
	? $message = filter_var($_REQUEST['message'], FILTER_SANITIZE_STRING)
	: $message = '[Message Blank]';


/*********************************************************************/
// [ Form email body ]
/*********************************************************************/

$message = <<<EOT
<!doctype html>
<html>
<head>
<style type="text/css">
* { margin:0; border:0; }
body { background:#009; font-size:10pt; font-size:1.2em; }
h1,h2,h3,h4,h5,h6 { margin:.1em auto .2em; }
.content { background:rgba(255,255,255,.6); border-radius:.2em; padding:1em .6em 0; margin:.6em; }
.logo { width:120px; float:left; margin-right:1em; }
.swoosh { background:rgba(0,0,153,.4); color: white; padding:.5em 2%; border:.2em ridge rgba(153,153,153,.1); border-radius:30px;	margin:1.2em; }
h5 { margin:.2em auto; opacity:.6; transition:.8s all ease-in-out; padding-bottom:.4em; text-align:right; }
h5 a { text-decoration:none; color:#00C;; }
h5:hover { opacity:1; }
blockquote { text-indent:25px; }
</style>
</head>
<body>
<div class="content">
<img src="http://www.creativedesigninfluence.com/files/media/assets/logo.png" class="logo" />
<h2>You have received a new site submission!</h2>
<p><strong>Name:</strong> $name</p>
<p><strong>Phone Number</strong>: $phone</p>
<p><strong>Email:</strong> $email</p>
<blockquote class="swoosh">$message</blockquote>
<h5><a href="http://www.creativedesigninfluence.com">Powered by Creative</a></h5>
</div>
</body>
</html>
EOT;

/*********************************************************************/
// [ Logging Function ]
/*********************************************************************/

function logger($logfile,$string){
  ini_set('date.timezone', 'America/New_York');
  $timestamp = date("Y/m/d").' at '.date("h:i:sa");
  $output = "[$timestamp] : $string \n";
  $log = fopen($logfile,'a+');
  fwrite($log,$output);
  fclose($log);	
}

/*********************************************************************/
// [ Email Function ]
/*********************************************************************/

function emailer($to,$title,$message,$receipt,$from=''){
  global $server,$ip,$logfile;
  $sender = empty($from) ? $ip : $from;
  $subject = !empty($title) ? $title : 'Form Submission';
//Clean Message
  $message = wordwrap($message, 70, "\r\n");
  $message = stripslashes($message);
//Establish Headers
  $headers = "From: $server \r\n";
  $headers .= 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//Send email
  mail($to,$subject,$message,$headers); //disable in localhost
//Log fail/success
  logger($logfile,"Success: $sender");
//Receipt
  echo $receipt;
}//end of function

/*********************************************************************/
// [ Send actual email ]
/*********************************************************************/

emailer($to,$title,$message,$receipt,$email);

/*********************************************************************/
?> 