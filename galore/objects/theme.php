<?php
// Sample theme file.

class theme {
function init() {
$output .= $this->top();
$output .= $this->main();
$output .= $this->bottom();
return $output;
}

function top() {
$output.='<table border=1 width="100%"><tr><td colspan="2"><h1>Logo</h1></td></tr><tr><td valign="top" width="10%">Navigation<p>link<br>link<br>link</p></d><td>';
    return $output;
  }

  
  function main() {
    // Includes the object file and then runs the init() function
    global $init;
    // We found an object variable
    $init->newMsg("Object: " . $HTTP_GET_VARS['object']);
    // Include the object file
    require $init->pathObjects . "/" . $this->object . ".php";
    // Initialize the object - create an instance then run the
    // object's init() function
    $object=new $this->object;
    $output .= $object->init();
    $output .= $init->newMsg("Finished executing objects");
    $output .= $init->printMsgs();
    return $output;
  }

  function bottom() {
    $output.='</td></tr><tr><td colspan="2"><p align="center">Ads</p></td></tr></table>';
    return $output;
  }
  
}
?>
