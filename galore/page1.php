<?php
/****************************/
/*  Galore                  */
/*  page1.php               */
/*  Dummy review php page   */
/****************************/

require "config.php";

require $init->pathObjects . "/reviews.php";
$object=new reviews;
$object->dataMode = "html";
$object->pageNum = 1;
$object->displayReview(2, "stuff");

?>