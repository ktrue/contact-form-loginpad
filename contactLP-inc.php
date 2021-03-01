<?php
# contact-inc.php for standalone and Saratoga templates
/*
PHP script originally by Mike Challis, www.642weather.com/weather
Version 1.00 - 28-May-2008 initial release
Version 1.01 - 29-May-2008 fixed session_start warning "headers already sent"
Version 1.02 - 30-May-2008 added Lat/Lon fields, fixed "TRIM here" comment syntax
Version 1.03 - 07-Jun-2008 added config setting for printing $thank_you message after form is sent
Version 1.04 - 07-Jun-2008 added setting for captch library [path]/[folder]

Version 2.00 - 07-Apr-2018 rewritten to use Google reCaptcha V2.0 - Ken True - Saratoga-weather.org
Version 2.01 - 09-Aug-2018 removed each() for PHP7 compatibility
Version 3.00 - 14-Apr-2020 update to use hCaptcha instead of Google reCaptcha
Version 4.00 - 24-Feb-2021 update to use LoginPad class instead for captcha
Version 4.01 - 01-Mar-2021 conditional define for not_null() function/fix whos-online script fatal error

You are free to use and modify the code
PHP version 5.5 or greater is recommended

*/
############################################################################
# begin settings
############################################################################
# always configure these options before use
# always test your contact form after making changes
#
 $kpChallenge = '26304915'; // pick 8 digits, no repeats for the challenge

# optional file to contain the $kpChallenge
 $kpChallengeFile = './contactlp-key.txt'; 
 
# Optional log file.  use '' as the name if a log file is not desired.
 $logFile = './contact-loglp.txt'; // optional text log of messages.  use '' to disable.

 # email address to receive messages from this form
 $mailto = 'somebody@somesite.com';

 # Site Name / Title
 $sitename = 'My Sitename';
 
 # enable debug display for testing only, should be off (=false;) for production
 $showDebug = false; // =false; suppress debug message, =true; enable debug messages

############################################################################
# end settings
############################################################################
if(!isset($doStandalone)) {$doStandalone = true; }
############################################################################
if(file_exists($kpChallengeFile)) {
	$t = file_get_contents($kpChallengeFile);
	if(preg_match('!^([\d]+)$!Us',trim($t),$m)) {
		$kpChallenge = $m[1];
		print "<!-- m=\n".var_export($m,true)." -->\n";
	}
}
if(!$doStandalone) {
  require_once("Settings.php");
  require_once("common.php");
  ############################################################################
  $TITLE= $SITE['organ'] . " - Contact";
  $showGizmo = false;  // set to false to exclude the gizmo
  include("top.php");
  ############################################################################
?>
<style type="text/css">
.input p { font-family:arial; font-size:1em }
.input a { text-decoration:none }
.input { width:140px; margin-left:50px; padding:10px; background-color:#B0FFB0; border:1px solid grey }
.db { width:33px; height:33px }
.db a:hover { color:red !important; }
.challenge {
 font-size: x-large;
 margin-left: 50px;
 border: 2px blue solid;
 padding: 10px;
}
.input input[type="button"] {
  border-radius: 10px !important;
  font-size: x-large !important;
  width: 33px !important;
  height: 33px !important;
  border: 1px solid black !important;
  color: black !important;
  margin: 1px;
}
.input input[type="button"]:hover {
  color: red !important;
}
.input input[type="submit"] {
  border: 1px solid black !important;
  color: black !important;
}
.input input[type="submit"]:hover {
  color: red !important;
}

.input input[type="reset"] {
  border: 1px solid black !important;
  color: black !important;
}
.input input[type="reset"]:hover {
  color: red !important;
}

.input input[type="password"] {
  border: 1px solid black !important;
  color: black !important;
}
</style>
  </head>
  <body>
<?php
  ############################################################################
  include("header.php");
  ############################################################################
  include("menubar.php");
  ############################################################################
?>

<div id="main-copy">
<?php
} // end !doStandalone
# Shim function if run outside of AJAX/PHP template set
# these must be before the missing function is called in the source
if(!function_exists('langtransstr')) {
	function langtransstr($item) {
		return($item);
	}
}
if(!function_exists('langtrans')) {
	function langtrans($item) {
		print $item;
		return;
	}
}
print "<!-- contactLP-inc.php V4.01 - 01-Mar-2021 -->\n";

$kp = new loginPad($kpChallenge,1,0); // Instansiation with the access code


############################################################################
# Do not alter any code below this point in the script or it may not run properly.
############################################################################
$config_errors = '';
if(strpos($mailto,'somesite') > 0) {
	$config_errors .= "<p>\$mailto address not customized.</p>\n";
}
if(strpos($sitename,'Sitename') > 0) {
	$config_errors .= "<p>\$sitename is not customized.</p>\n";
}

if(strlen($config_errors) > 0) {
	print "<br/>\n";
	print "<div class=\"warningBox\" style=\"text-align: left; padding-left: 5px;\">\n";
	print "<h3>Configuration error(s)</h3>\n";
	print $config_errors;
	print "</div>\n";
}

if ($SITE['lang'] <> 'en' and file_exists("wxcontact-".$SITE['lang'].'.html')) { 
# handle included files for other language wxcontact-XX.html 
	 include_once("wxcontact-".$SITE['lang'].'.html');
 } elseif (file_exists("wxcontact-en.html") ) {
   include_once("wxcontact-en.html"); // default english text for welcome, thankyou
 } else {
	 list($main_top_text,$welcome_intro,$thank_you) = gen_boilerplate();
 }
print $main_top_text;

$error = 0;
$error_print = '';
$error_message = array();
$message_sent = 0;
if(isset($_POST['name'])) { $EMname=$_POST['name']; }
  elseif(!isset($EMname))  {$EMname = '';}
if(isset($_POST['email'])) { $EMaddress=$_POST['email']; }
 elseif (!isset($EMaddress)) {$EMaddress = '';}
if(isset($_POST['email2'])) { $EMaddress2=$_POST['email2']; }
 elseif (!isset($EMaddress2)) {$EMaddress2 = '';}
if(isset($_POST['subject'])) { $EMsubject=$_POST['subject']; }
 elseif (!isset($EMsubject)) {$EMsubject = '';}
if(isset($_POST['text'])) { $EMtext=$_POST['text']; }
 elseif(!isset($EMtext))  {$EMtext = '';}
// print "<p class=\"advisoryBox\" style=\"text-align: left\">POST \n".print_r($_POST,true) . "</p>\n";
//  if (isset($_GET['action']) && ($_GET['action'] == 'send')) {
if(isset($_POST["submit"])) {
	if($kp->isGoodInput()) {
		if($showDebug) {
			print "<p class=\"advisoryBox\" style=\"text-align: left\">POST=".
			  print_r($_POST['h-captcha-response'],true)."</p>\n";
		}

		//get verify response data
		$EMname        = name_case(db_prepare_input(strip_tags($_POST['name'])));
		$EMaddress       = strtolower(db_prepare_input($_POST['email']));
		$EMtext        = db_prepare_input(strip_tags($_POST['text']));

		forbidifnewlines($EMname);  // fights a spammer tactic
		forbidifnewlines($EMaddress); // fights a spammer tactic
		forbidifnewlines($EMaddress2); // fights a spammer tactic
		forbidifnewlines($EMsubject); // fights a spammer tactic

		# check posted input for email injection attempts
		$forbidden = 0;
		$forbidden = spamcheckpost(); // fights a spammer tactic

		if ($forbidden) {
			 echo "<H1>".langtransstr("Input Forbidden")." $forbidden</H1>";
			 exit;
		}


	 if (!preg_match("/[a-z]/", $EMtext)) $EMtext = name_case($EMtext); # CAPS Decapitator

	 if (!validate_email($EMaddress)) {
			 $error = 1;
			 $error_message[0] = langtransstr('A proper email address is required.');
	 }
	 if (!validate_email($EMaddress2)) {
			 $error = 1;
			 $error_message[0] = langtransstr('A proper email address is required.');
	 }
	 if(strcmp($EMaddress,$EMaddress2) !== 0) {
		   $error = 1;
			 $error_message[2] = langtransstr('The email addresses are not the same.');
	 }
	 if(empty($EMname)) {
			 $error = 1;
			 $error_message[3] = langtransstr('Your name is required.');
	 }
	 if(empty($EMsubject)) {
			 $error = 1;
			 $error_message[4] = langtransstr('A subject is required.');
	 }

	 if (!$error) {
			# make the email
			$subj = "$sitename contact: $EMsubject\n";

	$msg =  "Sent from ".$_SERVER['SERVER_NAME']. " by " . $_SERVER['PHP_SELF'] . " form.

Name: $EMname
Email: $EMaddress
Subject: $EMsubject

Message:\n\n" . wordwrap($EMtext) . "

----------------------------------------------------
remove the following before replying to this message

";


	$userdomain = '';
	$userdomain = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$user_info_string  = "Sent from (ip address): ".$_SERVER['REMOTE_ADDR']." ($userdomain)\n";
	$user_info_string .= "Coming from (referer): ".$_SERVER['HTTP_REFERER']."\n";
	$user_info_string .= "Using (user agent): ".$_SERVER['HTTP_USER_AGENT']."\n\n";
	$msg .= $user_info_string;
	$EMname = str_replace(',','',$EMname); // remove comma from name
	$EMname = str_replace(';','',$EMname); // remove semicolon from name

		# send the email
	$subj = trim($subj);
 	mail($mailto,$subj,$msg,"From: $EMname <$EMaddress>\r\nReply-to: $EMname <$EMaddress>");

	$message_sent =1;
	if ($logFile <> '') { // make a log also if needed
		$log = fopen($logFile,'a');
		if ($log) {
			 $todayis = date("l, F j, Y, g:i a T") ;

		 $t = "-------------------------------------------------------------------------------------\n\n" .
			"Date: $todayis \n" . $msg . "\n";
		 
		 fwrite($log,$t);
		 fclose($log);
		} else {
		 print "<!-- unable to open/write log -->\n";
		}
	} // end if $logFile
	} // end if !error
}  // end if $_POST['submit']
   else {
	 $kp->countErr();
	 $error =1;
	 $error_message[] = langtransstr('Please complete the captcha before Send.');
	 }
}

if ($error) {
	foreach($error_message as $key => $value) {
	  $error_print .= "<p style=\"color: maroon\">$value</p>";
	}
	$error_print .= '<p style="color: maroon">'. langtransstr('Please make any necessary corrections and try again.'). '</p>';

}

if($message_sent) {

   # thank you mesage is printed here
    echo $thank_you;

} else {
   if (!$error) {
    # welcome intro is printed here
    echo $welcome_intro;

   }
?>

<?php echo $error_print ?>

<form name='keypad' method='post' 
  action='<?php echo $_SERVER["PHP_SELF"]; ?>' onreset='javascript:code=""'>

<table border="0" width="98%" cellspacing="1" cellpadding="2">
  <tr>
    <td>
    <table border="0" width="620px" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php langtrans('Full Name:'); ?>
        </td>

      </tr>
      <tr>
        <td class="small"><input type="text" name="name" value="<?php echo $EMname ?>" size="93" />
        <br />
        <?php 
				/* langtrans('Please enter your name and correct e-mail address here.'); ?><br/>
<?php langtrans('A few people mistype their e-mail addresses, making it impossible for us to respond.'); ?><br/>
<?php langtrans('Please double-check carefully.'); 
        */ ?>
        </td>
      </tr>
      <tr>
        <td><?php langtrans('E-Mail Address:'); ?></td>
      </tr>
      <tr>
        <td><input type="text" name="email" value="<?php echo $EMaddress ?>" size="93" /></td>
      </tr>
      <tr>
        <td><?php langtrans('Enter E-Mail Address again:'); ?></td>
      </tr>
      <tr>
        <td><input type="text" name="email2" value="<?php echo $EMaddress2 ?>" size="93" /></td>
      </tr>

      <tr>
        <td><?php langtrans('Subject:'); ?></td>
      </tr>
      <tr>
        <td><input type="text" name="subject" value="<?php echo $EMsubject ?>" size="93" /></td>
      </tr>
      <tr>
        <td><?php langtrans('Message:'); ?></td>
      </tr>
      <tr>
       <td class="small"><textarea name="text" cols="70" rows="15"><?php echo $EMtext ?></textarea><br />
      <br />
      <?php langtrans('Thanks for taking the time to submit your feedback.'); ?>
      </td>
    </tr>
    <tr>
      <td><p><?php langtrans('Please click the buttons on the keypad below to enter'); ?><br/><br/>
         <b><span class="challenge">
				     <?php print $kpChallenge; ?></span></b></p>
        <?php $kp->writeKeypad(); ?>
      </td>
    </tr>
     <tr><td style="text-align:center"><br/><br/><small>Contact script by <a href="https://saratoga-weather.org/scripts-contact.php">Saratoga-Weather.org</a><br/>
    </small>
     </td></tr>
  </table>
 </td>
 </tr>
 </table>
</form>

<?php
}
?>
<?php if(!$doStandalone) { ?>
</div><!-- end main-copy -->

<?php
  ############################################################################
  include("footer.php");
}
############################################################################
# End of Page
############################################################################
# Support functions
#
if(!function_exists('not_null')) {
	function not_null($value) {
		if (is_array($value)) {
			if (sizeof($value) > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
				return true;
			} else {
				return false;
			}
		}
	}
}
function db_input($string) {
	return addslashes($string);
}
function db_output($string) {
	return htmlspecialchars($string);
}
function db_prepare_input($string) {
	if (is_string($string)) {
		return trim(sanitize_string(stripslashes($string)));
	} elseif (is_array($string)) {
		reset($string);
		foreach ($string as $key => $value) {
			$string[$key] = db_prepare_input($value);
		}
		return $string;
	} else {
		return $string;
	}
}
// Parse the data used in the html tags to ensure the tags will not break
function parse_input_field_data($data, $parse) {
	return strtr(trim($data), $parse);
}
function output_string($string, $translate = false, $protected = false) {
	if ($protected == true) {
				return htmlspecialchars($string);
	} else {
		if ($translate == false) {
			return parse_input_field_data($string, array('"' => '&quot;'));
		} else {
			return parse_input_field_data($string, $translate);
		}
	}
}
function output_string_protected($string) {
	return output_string($string, false, true);
}
function sanitize_string($string) {
	$string = preg_replace('| +|', ' ', trim($string));
	return preg_replace("/[<>]/", '_', $string);
}
// Decode string encoded with htmlspecialchars()
function decode_specialchars($string){
	$string=str_replace('&gt;', '>', $string);
	$string=str_replace('&lt;', '<', $string);
	$string=str_replace('&#039;', "'", $string);
	$string=str_replace('&quot;', "\"", $string);
	$string=str_replace('&amp;', '&', $string);
	return $string;
}
//# A function knowing about name case (i.e. caps on McDonald etc)
# $EMname = name_case($EMname);
function name_case($EMname) {
 $break = 0;
 $newname = strtoupper($EMname[0]);
 for ($i=1; $i < strlen($EMname); $i++) {
	 $subed = substr($EMname, $i, 1);
	 if (((ord($subed) > 64) && (ord($subed) < 123)) ||
			 ((ord($subed) > 48) && (ord($subed) < 58))) {
			 $word_check = substr($EMname, $i - 2, 2);
			 if (!strcasecmp($word_check, 'Mc') || !strcasecmp($word_check, "O'")) {
					 $newname .= strtoupper($subed);
			 } elseif ($break){
					 $newname .= strtoupper($subed);
			 } else {
					 $newname .= strtolower($subed);
			 }
				 $break=0;
	 }else{
		 // not a letter - a boundary
		 $newname .= $subed;
		 $break=1;
	 }
 }
 return $newname;
}
function validate_email($EMaddress) {
   // Create the syntactical validation regular expression
   $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
   // Presume that the email is invalid
   $valid = 0;
   //check for all the non-printable codes in the standard ASCII set,
   //including null bytes and newlines, and exit immediately if any are found.
   if (preg_match("/[\\000-\\037]/",$EMaddress)) {
    return 0;
   }
   // Validate the syntax
   if (preg_match('|'.$regexp.'|i', $EMaddress)) {
      list($username,$domaintld) = explode("@",$EMaddress);
      // Validate the domain
      if (getmxrr($domaintld,$mxrecords)) {
         $valid = 1;
      }
   } else {
      $valid = 0;
   }
   return $valid;
}

function forbidifnewlines($input) {
 if (preg_match("|\r|is", $input) ||
		 preg_match("|\n|is", $input) ||
		 preg_match("|\%0a|is", $input) ||
		 preg_match("|\%0d|is", $input)) {
		 echo "<H1>" . langtransstr('Input Forbidden NL')."</H1>";
		 exit;
 }
}

function spamcheckpost() {
 
 if(!isset($_SERVER['HTTP_USER_AGENT'])){
     return 1;
  }

  // Make sure the form was indeed POST'ed:
  //  (requires your html form to use: action="post")
 if(!$_SERVER['REQUEST_METHOD'] == "POST"){
    return 2;
 }
 
	# check posted input for email injection attempts
	$badStrings = array('content-type','mime-version','content-transfer-encoding','to:','bcc:','cc:');
	# Loop through each POST'ed value and test if it contains one of the $badStrings:
	foreach($_POST as $k => $v){
		foreach($badStrings as $v2){
				$v = strtolower($v);
			 if(strpos($v, $v2) !== false){
					return 4;
			 }
		}
	}
	// Made it past spammer test, free up some memory
	unset($k, $v, $v2, $badStrings, $fromArray, $wwwUsed);
	return 0;
}


function gen_boilerplate () {
# generate default text when no wxcontact-LL.html files are available
# local language customization for wxcontact.php 
# note that specific fields are translated in language-LL-local.txt
# and this file just contains the 'bulk' text items for the page
#

 # The $main_top_text is what gets printed when the form is first presented.

$main_top_text = <<<EOT

  <h1>Contact Us!</h1>
  <p>Please use the form below to contact us.  We do appreciate all feedback.<br />
  </p>
  <p>Thanks in advance!</p>

EOT;
// do not remove the above EOT line

 # The $welcome_intro is what gets printed when the form is first presented.
 # It is not printed when there is an input error and not printed after the form is completed
 $welcome_intro = <<<EOT

<p>
This is just a hobby site, we are not weather experts.<br/>
If you want to make a site like this, look at our links page or do a web search to find the answers.<br/>
If your question was answered and was helpful, it is always nice to know.
</p>

EOT;
// do not remove the above EOT line

 # The $thank_you is what gets printed after the form is sent.
 $thank_you = <<<EOT

 <h1>Message Sent</h1>

 <p align="left">
    Your message has been sent, thank you.
 </p>

EOT;
// do not remove the above EOT line
return array($main_top_text,$welcome_intro,$thank_you);
}

#--------------------------------------------------
#  Build the random arranged keypad
class loginPad {

  // class loginPad
  // --------------
  // Pierre FAUQUE, pierre@fauque.net
  // Role : Display a block of numbers (0..9) randomly arranged and check the input made
  // Version 0.4 - January 30, 2021
  // User guide and history at the end of this file.
  // Encoding : ANSI
	// Modifications by Ken True - webmaster@saratoga-weather.org

  // <WARNING>
  // This Class is ONLY available from :
  // - my website (http://www.fauque.fr/downloads.php)
  // - PHP Classes (http://www.phpclasses.org) (prefered site)
  // If you have downloaded it from another website, probably
  // you do not have the lastest version. To avoid using any
  // obsolete version, please refer to the original repositories.
  // You aren't authorized to delete this warning from this script.
  // </WARNING> 

  private $rightcode = "";

  private $keys = array(-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1); // array for 16 bttons
  private $kpw  = 145;       // width of the Keypad DIV in pixels
  private $left =  50;       // width of the left margin of the keypad DIV in pixels
  private $bgc  = "#B0FFB0"; // background color of the keypad DIV
  private $lg   =  33;       // width of digit button in pixels
  private $ht   =  33;       // height of digit button in pixels
  private $cmd  =  68;       // width of command button in pixels (submit, reset)
  private $kin  = 133;       // width of the input text-password ('keyin') in pixels
  private $ro   =   1;       // Set the input 'keyin' to 0 (read and write) or to 1 (read only: default)
  private $hide =   1;       // Hide (1:default) or not (0) the input 'keyin'   
  private $merr =   3;       // Number max of authorized errors
  private $err  =   0;       // 0 error by default. Stop on $merr (max errors)

  // You can translate these below two texts
  private $texts = array(
	"send" => "Send", 
	"reset" => "Reset", 
	"maxerr" => "Too many errors - reload page to retry."
	);

  // ~~~~~~~~~~ Constructor ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  public function __construct($code="",$ro=0,$hidden=0) {
    $this->rightcode=$code; // Save the given code,
    $this->ro=$ro;          // the input read only or read and write and
    $this->hide=$hidden;    // Hide (1) or not (0) the input 'keyin'
    $this->buildKeypad();   // build 10 value on 16 randomly buttons
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // v0.1 Write the digits (0..9) on the 16 randomly digit buttons
  private function buildKeypad() {
    for($n=0;$n<10; $n++) {
      $pos=rand(0,15);
      $set=0;
      while(!$set) {
        if($this->keys[$pos]==-1) {
          $this->keys[$pos]=$n;
          $set=1;
        }
        $pos = rand(0,15);
      }
    }
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // v0.1 Write the necessary CSS and Javascript
  private function writeCSSandJS() {
    #echo "<style type='text/css'>\n";
    #echo ".input { width:$this->kpw"."px; margin-left:$this->left"."px; padding:10px; background-color:$this->bgc; border:1px solid grey }\n";
    #echo ".db { width:$this->lg"."px; height:$this->ht"."px }\n"; // digit button style
    #echo "</style>\n";
    echo "<script type='text/javascript'>\n";
		echo "// <![CDATA[\n";
    echo "var code='';\n";
    echo "var keys='0123456789';\n";
    echo "function addkey(key) {\n";
    echo "  var key;\n";
    echo "  if(keys.indexOf(key) >= 0) {\n";
    echo "    code=code+key;\n";
    echo "    document.keypad.keyin.value=code;\n";  
		echo "  }\n";
    echo "}\n";
		echo "// ]]>\n";
    echo "</script>\n";
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // v0.1 Write the digits on the screen keypad, v0.2 No loginpad when too much errors
  public function writeKeypad() {
		foreach ($this->texts as $key => $val) {
			$this->texts[$key] = langtransstr($val);
		}
    if($this->err == $this->merr) {
      #echo "<style type='text/css'>\n";
      #echo ".err { width:$this->kpw"."px; margin-left:$this->left"."px; background-color:$this->bgc; border:1px solid grey }\n";
      #echo "</style>\n";
      echo "<div class='err' style='width:$this->kpw"."px; margin-left:$this->left"."px; background-color:$this->bgc; border:1px solid grey;'>";
      echo "<p style='text-align:center'>".$this->texts["maxerr"]."</p>";
      echo "</div>";
      return;
    }
    $this->writeCSSandJS();
    if($this->hide) { $type="hidden"; } else { $type="password"; }
    if($this->ro) { $ro=" readonly=\"readonly\""; } else { $ro=""; }
    echo "<div class='input'>";
    #echo "<form name='keypad' method='post' action='".$_SERVER["PHP_SELF"]."' onreset='javascript:code=\"\"'>";
    for($n=0; $n<16; $n++) {
      if($this->keys[$n]==-1) { $value=""; } else { $value=$this->keys[$n]; }
      echo "<input type='button' class='db' name='b$n' value='$value' onclick='javascript:addkey(this.value)'/>";
    }
    echo "<input type='$type' name='keyin' style='width:$this->kin"."px'$ro/>";
    echo "<input type='submit' name='submit' value='".$this->texts["send"]."' style='width:$this->cmd"."px'/>";
    echo "<input type='reset' name='reset' value='".$this->texts["reset"]."' style='width:$this->cmd"."px'/>";
    echo "<input type='hidden' name='err' value='".$this->err."'/>";
    #echo "</form></div>\n";
    echo "</div>\n";
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // v0.1 Return TRUE if the input is correct code
  public function isGoodInput() {
    if($_POST["keyin"] == $this->rightcode) { return true; } else { return false; }
  }

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // v0.2 Count the number of bad input code
  public function countErr() { $this->err = $_POST["err"]+1; }

  // <USER_GUIDE>
  //
  // These lines in USER_GUIDE container (tag included) can be deleted after reading (for a lighter file).
  // Take a look at the test "index.php" page to understand how this class runs.
  // You have to instanciate a new variable with the class, giving it the access code you want to use.
  // Example :
  // $access = new loginPad("25122020"); // 25122020 is the good code to type in.
  // $access->isGoodInput() return true or false according to the input code made
  // If the code is true, you can redirect to a new page or do anything else (start a download, etc.)
  // You can also use this "loginpad" instead of a captcha with a random code displayed.
  //
  // NB: At the end of each input control (button or input) there is no new line nor <br/> tag.
  // The disposition (four lines of four buttons) is due to the width of the buttons and the
  // width of the "input" DIV. If you change the width of the buttons, you will almost certainly
  // have to change the width of the DIVision
  //
  // to force people to click on each button digit rather to type in on the keybord to enter the code,
  // set the input 'keyin' readonly when you instaciate the class.
  // Example: $ok = new loginPad("25122020",1);
  // You can also prefer to set the private member $ro to 1
  //     ...
  //     private $ro = 1; // keyin = Read only (clics only available)
  //     ...
  //     public function __construct($code="",$ro=0,$hide=0) {
  //     ...
  //
  // You can indicate in the private member $merr the max number of authorized errors.
  // You can hide or not the control 'keyin' input with two ways :
  // - by default : private $hide = 1;
  // - at the instanciation : $ok = new loginPad("25122020",1,1); // code, click only, 'keyin' hidden
  // You have to respect the order : $ok = new loginPad("code-to-use",read-only-or-not,hide-or-not)
  //
  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // History :
  // 2020-12-20  v0.0  : Original version
  // 2021-01-03  v0.1  : First distribued version.
  // 2021-01-26  v0.2  : A maximum of authorized errors is implemented
  // 2021-01-29  v0.3  : Fixed error after a click on the reset button (reported by Peter, .ca -thanks-. See Support forum)
  // 2021-01-30  v0.4  : Hide or not the input 'keyin' control
  //
  // </USER_GUIDE>
}
# end contact-inc.php
