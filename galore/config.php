<?php
/****************************/
/*  Galore v .01            */
/*  config.php              */
/*  Basic configuration of  */
/*  the Galore scripts      */
/****************************/

$init = new galoreInit;
$init->debug    = 1;
$init->myServer = "localhost";
$init->myDB     = "phlyingpenguin_net";
$init->myUser   = "csexton";
$init->myPass   = "wiw01d";
$init->myPollTable = "poll";
$init->myReviewTable = "reviews";
$init->myReviewcatsTable = "reviewCategories";

$init->pathWebRoot  = "http://www.phlyingpenguin.net/galore";
$init->path     = "$DOCUMENT_ROOT/galore";
$init->pathImages = $init->pathWebRoot . "/images";
$init->pathObjects = $init->path . "/objects";
$init->pathAdmin = $init->path . "/admin";
$init->pathf2mail = "$DOCUMENT_ROOT/form2mail.php";
$init->pathf2admin = "$DOCUMENT_ROOT/f2admin.php";

$init->pdbInc = $init->path . "/include/php-pdb.inc";
$init->pdbDoc = $init->path . "/include/modules/doc.inc";

echo $init->mainLoop();

class galoreInit {
  // galoreInit keeps all of the
  // config variables and methods.
  //var $debug, $myServer, $myUser, $myPass,$myPort,$myDB, $path, $pathObjects;
  //var $pathImages, $pathAdmin, $form2mail, $pathf2mail, $pathf2admin, $msgQueue;
  //var $myReviewTable, $myPollTable, $myReviewcatsTable;
   
  function mainLoop() {
    $this->myConnect();
    $this->newMsg("galore Path: " . $this->path);
    $this->newMsg("Image Path: " . $this->pathImages);
    $this->newMsg("Object Path: " . $this->pathObjects);
    $this->newMsg("Admin Path: " . $this->pathAdmin);
    if (strlen($this->pathf2mail) > 0) {
      $this->newMsg("Form2Mail: " . $this->pathf2mail);
      $this->newMsg("F2Admin: " . $this->pathf2admin);
    } else {
      $this->newMsg("Form2Mail: Disabled");
    }
    $this->newMsg("" . $this . "'s mainLoop is finished<br>");
    $output .= $this->printMsgs();
    return $output;
  }
  
  function myConnect () {
    $db=mysql_connect($this->myServer, $this->myUser, $this->myPass);
    mysql_select_db($this->myDB,$db) or die("Error opening " . $this->myDB);
    $this->newMsg("Server: " . $this->myServer);
    $this->newMsg("User: " . $this->myUser); 
    $pass = ("Password: ");
    $passlen=strlen($this->myPass);
    for ($i=0; $i < $passlen; $i++) { $pass = $pass . "*"; }
    $this->newMsg($pass);
    $this->newMsg("Database: " . $this->myDB);
    $this->newMsg("Connected"); 
  }
  
  function error($error) {
    // Call an error
    if ($error == "sql") {
      die("<li>SQL Error: " . mysql_errno() . ": " . mysql_error());
    } else {
      die("<li>Critical Error: " . $error);
    }
  }
  
  function newMsg($msg) {
    $prevMsgs = $this->msgQueue;
    if ($this->msgCount == 0)
      $this->msgCount++;
    $messages = $prevMsgs . "\n<li>Message " . $this->msgCount . ": " . $msg;
    $this->msgCount++;
    $this->msgQueue = $messages;
  }
  
  function printMsgs() {
    if ($this->debug == 1)
      $output .= $this->msgQueue;
      $this->msgQueue = "";
      return $output;
  }
  
  function checkAdmin() {
    global $PHP_AUTH_USER, $PHP_AUTH_PW;
    $auth = false; // Assume user is not authenticated 
    if (isset( $PHP_AUTH_USER ) && isset($PHP_AUTH_PW)) { 
        // Formulate the query 
        $sql = "SELECT * FROM users WHERE usrName = '$PHP_AUTH_USER' AND usrPass = '$PHP_AUTH_PW'"; 
        // Execute the query and put results in $result 
        $result = mysql_query( $sql ) or die ( 'Unable to execute query.' ); 
        // Get number of rows in $result. 
        $num = mysql_numrows( $result ); 
        if ( $num != 0 ) { 
            // A matching row was found - the user is authenticated. 
            $auth = true; 
        } 
    }
    if ( ! $auth ) { 
        header( 'WWW-Authenticate: Basic realm="Galore Authentication System"' ); 
        header( 'HTTP/1.0 401 Unauthorized' ); 
        echo 'Authorization Required.'; 
        exit; 
    } else { 
        //echo '<P>You are authorized!</P>'; 
    }
  }
}
?>