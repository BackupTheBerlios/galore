<?php
/****************************/
/*  Galore                  */
/*  reviews.php             */
/*  Dummy reviews php page  */
/****************************/

require "config.php";

require $init->pathObjects . "/theme.php";
$object=new theme;
$object->object = "reviews";
echo $object->init();

?>
