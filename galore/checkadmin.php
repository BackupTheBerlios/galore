<?php 
$auth = false; // Assume user is not authenticated 
if (isset( $PHP_AUTH_USER ) && isset($PHP_AUTH_PW)) { 
    // Formulate the query 
    $sql = "SELECT * FROM users WHERE usrName = '$PHP_AUTH_USER' AND usrPass = '$PHP_AUTH_PW'"; 
    // Execute the query and put results in $result 
    $result = mysql_query( $sql ) or die ( 'Unable to execute query.' ); 
    // Get number of rows in $result. 
    $num = mysql_numrows( $result ); 
    if ( $num != 0 ) { 
        // A matching row was found - the user is authenticated. 
        $auth = true; 
    } 
}
if ( ! $auth ) { 
    header( 'WWW-Authenticate: Basic realm="Galore Authentication System"' ); 
    header( 'HTTP/1.0 401 Unauthorized' ); 
    echo 'Authorization Required.'; 
    exit; 
} else { 
    //echo '<P>You are authorized!</P>'; 
} 
?>