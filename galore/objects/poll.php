<?php
/********************************/
/*  Galore                      */
/*  objects/poll.php            */
/*  Galore Poll Engine          */
/*  This script is the engine   */
/*  that runs the Galore poll   */
/*                              */
/*  TODO:                       */
/*   *Change _REQUEST to _POST  */
/*   *Add Admin functions       */
/********************************/

class poll {
  function init() {
    // Start up the object
    global $init;
    if ($_GET['clrck'] == 1)
      $output .= $this->_clearCookie();
    // Is this a valid vote request?
    if (strlen($_REQUEST['vote']) > 0 && strlen($_REQUEST['pid']) > 0)
      $output .= $this->_vote($_REQUEST['pid'], $_REQUEST['vote']);
    $output .= $init->printMsgs();
    return $output;
  }

  function initAdmin() {
    // Start up the object
    global $init;
    if ($_GET['clrck'] == 1)
      $output .= $this->_clearCookie();
    // Is this a valid vote request?
    $init->newMsg("PID: " . $_REQUEST['pid']);
    $init->newMsg("strlen(pid): " . strlen($_REQUEST['pid'])); 
    if (strlen($_REQUEST['pid']) > 0 && strlen($_REQUEST['answer']) > 0) {
      $output .= $this->_editAnswer($_REQUEST['pid'], $_REQUEST['answer']);
    } elseif (strlen($_REQUEST['pid']) > 0) {
      $output .= $this->_editPoll($_REQUEST['pid']);
    } else {
      $this->_listPolls();
    }
    $output .= $init->printMsgs();
    return $output;
  }

  function _vote($poll,$vote) {
    // vote on a given poll
    global $init;
    if ($_COOKIE['voted'] == "true$poll") {
      $vote="null";
      $init->newMsg("vote: $vote");
    } elseif ($vote=="null") {
      $vote="null";
      $init->newMsg("vote: $vote");
    } else {
      if ($init->debug != 1)
        setcookie ("voted", "true$poll");
      $init->newMsg("vote: \"$vote\" recorded");
      $init->newMsg("Cookie: ". $_COOKIE['voted']);
    }
    // Grab the original vote value - to add to
    $result=mysql_query("SELECT * FROM " . $init->myPollTable . " WHERE question='$poll' and answer='$vote'") or $init->error("sql");
    $init->newMsg("<b>Called mysql1</b>");
    $row=mysql_fetch_array($result);
    $voteCount=$row['votes'];
    $voteCount++;
    $init->newMsg("Old votes: " . $row['votes']);
    $init->newMsg("votecount: $voteCount");
    // If we are voting, lets update the value in the db
    if ($vote != "null")
      $result=mysql_query("UPDATE " . $init->myPollTable . " SET votes='$voteCount' WHERE question='$poll' and answer='$vote'") or $init->error("sql");
      $init->newMsg("<b>Called mysql2</b>");
    // Return some results for the poor little users
    $output .= $this->_getResults($poll);
    return $output;
  }
  
  function _clearCookie() {
    // Clear the cookie - development purposes only!
    global $init;
    if ($init->debug != 1)
      setcookie ("voted");
    $output .= "<li>Cookie cleared";
    exit();
    return $output;
  }
  
  function _getResults($poll) {
    // Get results of a given poll
    global $init;
    $init->newMsg("Poll Table: " . $init->myPollTable);
    $result=mysql_query("SELECT * FROM " . $init->myPollTable . " WHERE question='$poll'") or $init->error("sql");
    echo $result;
    $init->newMsg("<b>Called mysql3</b>");
    // Grab the answer and number of votes for the question
    while ($row=mysql_fetch_array($result)) {
      $answer=$row['answer'];
      $results[$answer] = $row['votes'];
    }
    if (count($results) < 1)
      $init->error("Poll not found!");

    // Get a total to get percentages out of
    $total = array_sum($results);
    
    // Get each value's percentage
    foreach ($results as $key => $values) {
      $init->newMsg("$key: $values");
      $answerPercent[$key] = $values * 100 / $total;
    }
    
    // Print out the data into the pretty poll results page (only real output)
    $output .= "<table>\n<tr><td colspan=\"3\">$poll</td></tr>\n";
    foreach ($answerPercent as $key => $values) {
      $percent=number_format($values, 1);
      $imageWidth=$percent*2;
      $output .= "<tr><td>$key</td><td><img src=\"" . $init->pathImages . "/res.png\" height=\"10\" width=\"$imageWidth\"></td><td>$percent %</td></tr>\n";
    }
    $output .= "</table>";
    $init->newMsg($init->pathImages);
    return $output;
  }
  
  function _editPoll($poll) {
    // Edit poll questions
    global $init;
    $init->newMsg("Currently in _editPoll()");
    
    if (strlen($_POST['new']) > 0) {
      mysql_query("INSERT INTO " . $init->myPollTable . " (question,answer) VALUES ('$poll','" . $_POST['new'] . "');") or $init->error("sql");
      $init->newMsg("<b>Called mysql4</b>");
      $init->newMsg("Created new answer: " . $_POST['new']);
    }
    
    $output .= "<p><b>$poll</b></p>";
    $output .= "<table cellpadding=\"3\" cellspacing=\"3\"><tr><td>Answer</td><td>votes</td></tr>";
    $result=mysql_query("SELECT * FROM " . $init->myPollTable . " WHERE question='$poll';") or $init->error("sql");
    $init->newMsg("<b>Called mysql5</b>");
    while ($row=mysql_fetch_array($result)) {
      $output .= "<tr><td><a href=\"$PHP_SELF?object=poll&pid=$poll&answer=" . urlencode($row['answer']) . "\">" . $row['answer'] . "</a></td><td>" . $row['votes'] . "</td></tr>";
    }
    $output .= "</table>";
    $output .= "<form method=\"post\" action=\"" . $init->pathWebRoot . "/admin.php?object=poll&pid=" . urlencode($poll) . "\">\n";
    $output .= "<p>New Answer:<br><input type=\"text\" name=\"new\"> <input type=\"submit\" value=\"New\"></p>";
    $output .= "</form>";
    $output .= "<p><a href=\"" . $init->pathWebRoot . "/admin.php?object=poll\">Back to poll list.</a></p>";
    return $output;
  }
  
  function _editAnswer($poll, $answer) {
    // Edit/Delete an answer on a poll
    global $init;
    $init->newMsg("_editAnswer started for $poll -> $answer");
    $init->newMsg("Action: " . $_REQUEST['action']);
    switch ($_REQUEST['action']) {
    case ("edit"):
      mysql_query("UPDATE " . $init->myPollTable . " SET answer='" . $_POST['answerNew'] . "' WHERE answer='$answer';");
      $init->newMsg("<b>Called mysql6</b>");
      $init->newMsg("Updated");
      $this->_editPoll($poll);
      break;
    case ("delete"):
      mysql_query("DELETE FROM " . $init->myPollTable . " WHERE question='$poll' and answer='$answer'");
      $init->newMsg("<b>Called mysql7</b>");
      $init->newMsg("Deleted: <b>$answer</b> from <b>$poll</b>");
      $this->_editPoll($poll);
      break;
    default:
      // Show form
      $output .= "<p><b>$poll</b></p>";
      $output .= "<form method=\"post\" action=\"" . $init->pathWebRoot . "/admin.php?object=poll\">\n";
      $output .= "<input type=\"hidden\" name=\"pid\" value=\"$poll\">";
      $output .= "<input type=\"hidden\" name=\"answer\" value=\"$answer\">";
      $output .= "<table><tr><td colspan=\"2\">";
      $output .= "<input type=\"text\" name=\"answerNew\" value=\"$answer\">";
      $output .= "</td></tr><tr><td>";
      $output .= "<input type=\"hidden\" name=\"action\" value=\"edit\">";
      $output .= "<input type=\"submit\" value=\"Submit\">";
      $output .= "</form>";
      $output .= "</td><td>";
      $output .= "<form method=\"post\" action=\"" . $init->pathWebRoot . "/admin.php?object=poll\">\n";
      $output .= "<input type=\"hidden\" name=\"pid\" value=\"$poll\">";
      $output .= "<input type=\"hidden\" name=\"answer\" value=\"$answer\">";
      $output .= "<input type=\"hidden\" name=\"action\" value=\"delete\">";
      $output .= "<input type=\"submit\" value=\"Delete\">";
      $output .= "</form>";
      $output .= "</td></tr></table>";
      $output .= "<p><a href=\"" . $init->pathWebRoot . "/admin.php?object=poll&pid=" . urlencode($poll) . "\">Back to answer list.</a>";
    }
    return $output;
  }
  
  function _listPolls() {
    // List polls for admin editing
    global $init;
    $output .= "<p><b>Edit poll:</b></p>";
    $result=mysql_query("SELECT DISTINCT question FROM " . $init->myPollTable . ";");
    $init->newMsg("<b>Called mysql8</b>");
    while ($row=mysql_fetch_array($result)) {
      $output .= "<a href=\"" . $init->pathWebRoot . "/admin.php?object=poll&pid=" . urlencode($row['question']) . "\">" . $row['question'] . "</a><br>";
    }
    $output .= "<p><b>New poll</b></p>";
    $output .= "<form action=\"$PHP_SELF?object=poll\" method=\"post\">";
    $output .= "Question: <input type=\"text\" name=\"pid\"><br>";
    $output .= "Answer: <input type=\"text\" name=\"new\"><br>";
    $output .= "<input type=\"submit\" value=\"New Poll\">";
    $output .= "</form>";
    return $output;
  }
}

?>