<?php
class articles {
  function init() {
    // Start up the object
    global $init;
    $init->newMsg("Object articles has started");
    if (strlen($_REQUEST['category']) > 0 && strlen($_REQUEST['article']) > 0)
      $this->displayArticle($_REQUEST['category'], $_REQUEST['article']);
    $init->printMsgs();
  }

  function indexArticles($category) {
    // Display an index of articles in a given category
    // Default to showing a list of indexes to browse
  }
  function postArticle() {
    // Post a new article
  }
  
  function displayArticle($category,$article) {
    // Display a given article of a given category
    global $init;
    $init->newMsg("Method displayArticle started.");
    $result=mysql_query("SELECT * FROM " . $init->myArticleTable . " WHERE category=$category and article=$article;") or $init->error("sql");
  }
}
?>