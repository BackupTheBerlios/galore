<?php
/***********************/
/*  Galore             */
/*  galore.php         */
/*  Engine for Galore  */
/***********************/

require "config.php";

class mainLoop {
  // Fires everything off
  function callObject() {
    // Includes the object file and then runs the init() function
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
    if (strlen($HTTP_GET_VARS['object']) <= 0) { 
      // There is no ?object= variable passed
      $init->newMsg("Object: No object"); 
    } else { 
      // We found an object variable
      $init->newMsg("Object: " . $HTTP_GET_VARS['object']);
      // Include the object file
      include $init->pathObjects . "/" . $HTTP_GET_VARS['object'] . ".php";
      // Initialize the object - create an instance then run the 
      // object's init() function
      $object=new $HTTP_GET_VARS['object'];
      $output .= $object->initAdmin();
      $init->newMsg("Finished executing objects");
      $output .= $init->printMsgs();
      return $output;
    }
  }
}

// Rev up our engine
// Create an object for the include module and then run the callObject() which
// includes and runs the object specified. 
$init->checkAdmin();
$site = new mainLoop;
echo $site->callObject();
?>
