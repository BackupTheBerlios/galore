<?php
/*************************/
/*  Galore               */
/*  poll.php             */
/*  Dummy poll php page  */
/*************************/

require "config.php";

include $init->pathObjects . "/poll.php";
$object=new poll;
echo $object->init();

?>