//This is the api function that can be used to return spotify song data from the database.

<?php
function getSongsInRange(){
    if(isset($_GET['min'])){
        $min = $_GET['min'];
    }else{
        echo("No minimum value sent through. Returned nothing.");
        return;
    }

    if(isset($_GET['max'])){
        $max = $_GET['max'];
    }else{
        echo("No maximum value sent through. Returned nothing.");
        return;
    }

    global $databaseConnection;

        $query = "SELECT *,
                  CASE
                      WHEN rdate_google IS NULL THEN rdate_spotify
                      WHEN rdate_spotify IS NULL THEN rdate_google
                      WHEN rdate_spotify < rdate_google THEN rdate_spotify
                      ELSE rdate_google
                  END AS lowest_date
              FROM songs
              WHERE (
                  (rdate_google IS NOT NULL OR rdate_spotify IS NOT NULL) 
                  AND (
                      (rdate_google >= $min OR (rdate_spotify >= $min AND rdate_google IS NULL)) 
                      AND (rdate_google <= $max OR rdate_spotify <= $max)
                  )
              )
              ORDER BY RAND() 
              LIMIT 50;";

    $statement = mysqli_prepare($databaseConnection, $query);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    $songs = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
        $song = array(
            "uri" => $row['uri'],
            "title" => $row['title'],
            "artist" => utf8_decode($row['artist']),
            "album" => $row['album'],
            "rdate_spotify" => $row['rdate_spotify'],
            "rdate_google" => $row['rdate_google']
        );

        $songs[] = $song;
    }

    header('Content-Type: application/json');
    echo json_encode($songs, JSON_UNESCAPED_UNICODE);
}
