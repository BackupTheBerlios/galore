<?php
/****************************/
/*  Galore                  */
/*  reviews.php             */
/*  Dummy reviews php page  */
/****************************/

require "config.php";

require $init->pathObjects . "/reviews.php";
$object=new reviews;
echo $object->init();

?>
