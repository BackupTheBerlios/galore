<?php
class reviews {
  var $reviewHits, $dataMode, $pageNum, $category;
  function init() {
    // Start up the object
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
    $init->newMsg("Object reviews has started");
    // If we have a category and review to look at, go for it!
    if (strlen($HTTP_GET_VARS['category']) > 0 && strlen($HTTP_GET_VARS['review']) > 0) {
      $output .= $this->displayReview($HTTP_GET_VARS['category'], $HTTP_GET_VARS['review']);
    } elseif (strlen($HTTP_GET_VARS['category']) > 0) {
      $output .= $this->_indexReviews($HTTP_GET_VARS['category']);
      $init->newMsg("Category: " . $HTTP_GET_VARS['category']);
    } else {
      $output .= $this->_indexReviews(0);
      $init->newMsg("Category: " . $HTTP_GET_VARS['category']);
    }
    $output .= $init->printMsgs();
    return $output;
  }

  
  function initAdmin() {
    // Start up the object
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
    switch ($HTTP_GET_VARS['action']) {
      case "edit":
        $output .= $this->_editReview();
      case "new":
        $output .= $this->_postReview();
      default:
        $output .= $this->_editList($HTTP_GET_VARS['category']);
    }
    $output .= $init->printMsgs();
    return $output;
  }
  
  function _indexReviews($category) {
    // Display an index of reviews in a given category
    // Default to showing a list of indexes to browse
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
    if (strlen($this->category) > 0) {
      $category = 0; 
    } elseif (strlen($category) == 0) {
      $category = 0;
    }
    
    $result=mysql_query("SELECT * FROM " . $init->myReviewTable . " WHERE category=$category and page=1") or $init->error("sql");
    while ($row=mysql_fetch_array($result)) {
      $output .= "Title: <a href=\"$PHP_SELF?object=reviews&category=$category&review=" . $row['title'] . "\">" . $row['title'] . "<br><img border=\"0\" src=\"". $row['image'] . "\"></a><br>";
    }
    
    $result=mysql_query("SELECT * FROM " . $init->myReviewcatsTable . " WHERE parent=$category") or $init->error("sql");
    while ($row=mysql_fetch_array($result)) {
      $output .= "Category: <a href=\"$PHP_SELF?object=reviews&category=" . $row['id'] . "\">" . $row['title'] . "<br><img border=\"0\" src=\"" . $row['image'] . "\"></a><br>";
    }
    return $output;
  }

  function _editList($category) {
    // Display an index of reviews in a given category
    // Default to showing a list of indexes to browse
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
    if (strlen($this->category) > 0) {
      $category = 0; 
    } elseif (strlen($category) == 0) {
      $category = 0;
    }
    
    $result=mysql_query("SELECT * FROM " . $init->myReviewTable . " WHERE category=$category and page=1") or $init->error("sql");
    while ($row=mysql_fetch_array($result)) {
      $output .= "Title: <a href=\"$PHP_SELF?object=reviews&category=$category&review=" . $row['title'] . "\">" . $row['title'] . "<br><img border=\"0\" src=\"". $row['image'] . "\"></a><br>";
    }
    
    $result=mysql_query("SELECT * FROM " . $init->myReviewcatsTable . " WHERE parent=$category") or $init->error("sql");
    while ($row=mysql_fetch_array($result)) {
      $output .= "Category: <a href=\"$PHP_SELF?object=reviews&category=" . $row['id'] . "\">" . $row['title'] . "<br><img border=\"0\" src=\"" . $row['image'] . "\"></a><br>";
    }
    return $output;
  }


  function _postReview() {
    // Post a new review
  }
  
  function displayReview($category,$review) {
    // Display a given review of a given category
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
    $init->newMsg("Method displayReview started.");
    if ($HTTP_GET_VARS['page'] >> 1) {
      $pageNum = $HTTP_GET_VARS['page'];
    } elseif (strlen($this->pageNum) > 0) {
      $pageNum = $this->pageNum;
    } else {
      $pageNum = 1;
    }
    $init->newMsg("Page: " . $HTTP_GET_VARS['page']);
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
    } elseif (strlen($HTTP_GET_VARS['datamode']) > 0) {
      $dataMode=$HTTP_GET_VARS['datamode'];
    } else {
      $dataMode="html";
    }
    return $this->_dataParser($rawData, $dataMode);
  }
  
  function _dataParser($rawData, $dataMode) {
    // Parse the data into whatever format you want
    global $init,$HTTP_GET_VARS,$HTTP_POST_VARS;
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
