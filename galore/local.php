<?
$init->debug    = 1;
$init->myServer = "localhost";
$init->myDB     = "galore";
$init->myUser   = "";
$init->myPass   = "";
$init->myPollTable = "poll";
$init->myReviewTable = "reviews";
$init->myReviewcatsTable = "reviewCategories";
$init->myUserTable = "users";
$init->myGroupTable = "groups";

$init->pathWebRoot  = "http://www.phlyingpenguin.net/galore";
$init->path     = "$DOCUMENT_ROOT/galore";
$init->pathImages = $init->pathWebRoot . "/images";
$init->pathObjects = $init->path . "/objects";
$init->pathAdmin = $init->path . "/admin";
$init->pathf2mail = "$DOCUMENT_ROOT/form2mail.php";
$init->pathf2admin = "$DOCUMENT_ROOT/f2admin.php";

$init->pdbInc = $init->path . "/include/php-pdb.inc";
$init->pdbDoc = $init->path . "/include/modules/doc.inc";
?>
