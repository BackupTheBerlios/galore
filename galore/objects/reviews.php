<?php
class reviews {
  var $reviewHits, $dataMode, $pageNum, $category;
  function init() {
    // Start up the object
    global $init;
    $init->newMsg("Object reviews has started");
    // If we have a category and review to look at, go for it!
    if (strlen($_REQUEST['category']) > 0 && strlen($_REQUEST['review']) > 0) {
      $this->displayReview($_REQUEST['category'], $_REQUEST['review']);
    } elseif (strlen($_REQUEST['category']) > 0) {
      $this->_indexReviews($_REQUEST['category']);
    } else {
      $this->_indexReviews(0);
    }
    $init->printMsgs();
  }

  
  function initAdmin() {
    // Start up the object
    global $init;
    switch ($action) {
      case "edit":
        $this->_editReview();
      case "new":
        $this->_postReview();
      default:
        //$this->editList();
    }
    $init->printMsgs();
  }
  
  function _indexReviews($category) {
    // Display an index of reviews in a given category
    // Default to showing a list of indexes to browse
    global $init;
    if (strlen($this->category) > 0) {
      $category = 0; 
    } elseif (strlen($category) == 0) {
      $category = 0;
    }
    
    $result=mysql_query("SELECT * FROM " . $init->myReviewTable . " WHERE category=$category and page=1") or $init->error("sql");
    while ($row=mysql_fetch_array($result)) {
      echo "Title: <a href=\"$PHP_SELF?category=$category&review=" . $row['title'] . "\">" . $row['title'] . "<br><img border=\"0\" src=\"". $row['image'] . "\"></a><br>";
    }
    
    $result=mysql_query("SELECT * FROM " . $init->myReviewcatsTable . " WHERE parent=$category") or $init->error("sql");
    while ($row=mysql_fetch_array($result)) {
      echo "Category: <a href=\"$PHP_SELF?category=" . $row['id'] . "\">" . $row['title'] . "<br><img border=\"0\" src=\"" . $row['image'] . "\"></a><br>";
    }
  }
  function _postReview() {
    // Post a new review
  }
  
  function displayReview($category,$review) {
    // Display a given review of a given category
    global $init;
    $init->newMsg("Method displayReview started.");
    if ($_REQUEST['page'] >> 1) {
      $pageNum = $_REQUEST['page'];
    } elseif (strlen($this->pageNum) > 0) {
      $pageNum = $this->pageNum;
    } else {
      $pageNum = 1;
    }
    $init->newMsg("Page: " . $_REQUEST['page']);
    $init->newMsg("Official Page: $pageNum");
    $result=mysql_query("SELECT * FROM " . $init->myReviewTable . " WHERE category='$category' and title='$review' and page='$pageNum';") or $init->error("sql");
    // We got the information, now we need to parse it and display it
    while ($row=mysql_fetch_array($result)) {
      $rawData=$row['data'];
      $dataMode=$row['contentType'];
      $reviewHits=$row['hits'];
    }
    $this->reviewHits=$reviewHits + 1;
    $result=mysql_query("UPDATE " . $init->myReviewTable ." SET hits=" . $this->reviewHits . " WHERE category='$category' and title='$review';") or $init->error("sql");
    // Check and see if there is a requested format, if there isn't than parse it as html
    
    if (strlen($this->dataMode) < 0) {
      $dataMode="html";
    } elseif (strlen($_REQUEST['datamode']) > 0) {
      $dataMode=$_REQUEST['datamode'];
    } else {
      $dataMode="html";
    }
    return $this->_dataParser($rawData, $dataMode);
  }
  
  function _dataParser($rawData, $dataMode) {
    // Parse the data into whatever format you want
    global $init;
    switch ($dataMode) {
    case "pdb":
      // Include the pdb-php library and create a PalmOS .doc file
      include $init->pdbInc;
      include $init->pdbDoc;
    case "html":
      // If we get an XML engine - this will parse it into html readable stuff
      // This will be exchanged with else when it's all done
      $data=$rawData;
      //$data=$rawData . "\n" . $this->reviewHits;
    default:
      // We're just going to return anything that's in the db for the fun of it
      // This will be changed to XML if we get an XML engine going
        $data=$rawData;
        //$data=$rawData . "\n<p align=\"right\">Review Hits: " . $this->reviewHits . "</p>\n";
    }
    // Return the parsed review data to the engine
    return $data;
  }
}
?>