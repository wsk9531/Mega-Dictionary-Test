<?php

// used in translation process to create english phrases and spanish translations, based off POST request data
// should be validated by translate.js

// sql info, uses `sqlinfo.inc`
require_once('C://xampp/conf/sqlinfo.inc.php');


// mysqli_connect returns false if connection failed, otherwise a connection value
$conn = mysqli_connect(
    $sql_host,
    $sql_user,
    $sql_pass,
    $sql_db
);

// Checks if connection is successful
if (!$conn) {
    // Displays an error message
    echo "<p>Database connection failure</p>";
} else {
    echo "<p>Database connection sucessful, adding translation...</p>";
}

// Checks the database tables exists, sets up the command to check info schema.
$tableExistQuery = "SHOW TABLES FROM $sql_db";
$tableResult = @mysqli_query($conn, $tableExistQuery);

if ($tableResult == null) {
    echo "<p>Error! No tables detected, setting things up....</p>";
    $sql = "CREATE TABLE IF NOT EXISTS `english` (
                `english_id` int(11) NOT NULL AUTO_INCREMENT,
                `contents` varchar(255) NOT NULL,
                PRIMARY KEY (`english_id`)
                );
                CREATE TABLE IF NOT EXISTS `spanish` (
                `spanish_id` int(11) NOT NULL AUTO_INCREMENT,
                `contents` varchar(255) NOT NULL,
                PRIMARY KEY (`spanish_id`)
                )";
    $result = @mysqli_query($conn, $sql);
}

// Example of POST content from XHR request:
// english="Good Morning"&spanish="Buenos días"

// Uses explode to break down XHR object into variable assignment.
$jsonPOST = file_get_contents('php://input');
if (isset($jsonPOST)) {

    foreach (explode('&', $jsonPOST) as $chunk) {
        $param = explode("=", $chunk);

        if ($param) {
            $arr[] = urldecode($param[1]);
        }
    }

    // escape strings - fallback to client side validation
    $english = mysqli_real_escape_string($conn, $arr[0]);
    $spanish = mysqli_real_escape_string($conn, $arr[1]);

    // Set up the SQL command to add the data into the tables
    $en_sql = "INSERT INTO english(contents) VALUES (\"$english\");";
    $es_sql = "INSERT INTO spanish(contents) VALUES (\"$spanish\");";
    $en_result = @mysqli_query($conn, $en_sql);
    $es_result = @mysqli_query($conn, $es_sql);

    if (!$en_result) {
        echo "<br/><br/>Error! Problem with english query";
    } else if (!$es_result) {
        echo "<br/><br/>Error! Problem with spanish query";
    } else {
        // Get latest submitted entry from DB to confirm
        $sql = "SELECT english.contents AS eng, spanish.contents AS esp\n"
            . "FROM english\n"
            . "INNER JOIN spanish ON english.english_ID=spanish.spanish_ID\n"
            . "ORDER BY `english_id` DESC LIMIT 1;";
        $sqlselect = @mysqli_query($conn, $sql);

        $entry = mysqli_fetch_array($sqlselect);
        
        echo ("<p> Success! Saved " . $english. " as " . $entry['esp'] . "</p>");
    }
    mysqli_close($conn);
}
