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
    echo "<p>Database connection sucessful, checking for similar translations...</p>";

    // Example of POST content from XHR request:
    // english="Good Morning""

    // Uses explode to break down XHR object into variable assignment.
    $jsonPOST = file_get_contents('php://input');
    if (isset($jsonPOST)) {
        foreach (explode('&', $jsonPOST) as $chunk) {
            $param = explode("=", $chunk);
            if ($param) {
                $english_words[] = urldecode($param[1]);
            }
        }
    }

    // Escape strings for use in SQL select queries

    foreach ($english_words as $word) {
        $sql_select = "SELECT english.contents, spanish.contents\n"
            . "FROM english\n"
            . "INNER JOIN spanish ON english.english_ID=spanish.spanish_ID\n"
            . "WHERE english.contents LIKE \"$word%\";";
        $res = mysqli_fetch_array(@mysqli_query($conn, $sql_select), MYSQLI_NUM);
        if ($res) {
            echo($word . " ... " . $res[1]);
        }

    }

    mysqli_close($conn);
}
