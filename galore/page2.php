<?php
/****************************/
/*  Galore                  */
/*  page2.php               */
/*  Dummy review php page   */
/****************************/

require "config.php";

require $init->pathObjects . "/reviews.php";
$object=new reviews;
$object->dataMode = "html";
$object->pageNum = 2;
$object->displayReview(2, "stuff");

?>