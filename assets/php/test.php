<?php
//     $lordicon = '<lord-icon
//     src="https://cdn.lordicon.com/zsyzaums.json"
//     trigger="in"
//     delay="1000"
//     colors="primary:#121331,secondary:#3a3347,tertiary:#f24c00,quaternary:#5451a4"
//     style="width:30px;height:30px">
// </lord-icon>';

//     $lordicon_parts = explode('</', $lordicon);
//     if ($lordicon_parts[1] == "lord-icon>") {
//         echo "Good, this is a valid lord icon";
//     }
//     else {
//         echo "Invalid tag, an attempt to XSS!!!<br><br>";
//     }

// $connection = new SQLite3("../mind-planner.sqlite");
// if ($connection) {
//     $id = 1;
//     // $stmt = $connection->query("SELECT * FROM campaigns WHERE id = $id");
//     // $results = $stmt->fetchArray(SQLITE3_ASSOC);
//     # Try another way to insert
//     try {
//         $stmt = $connection->prepare("INSERT INTO objectives VALUES (:id, :title, :content, :image, :priority, :date_time, :missions_id)");
//         $stmt->bindValue(':id', NULL, SQLITE3_INTEGER);
//         $stmt->bindValue(':title', "Nice Objective", SQLITE3_TEXT);
//         $stmt->bindValue(':content', "Nice Content", SQLITE3_TEXT);
//         $stmt->bindValue(':image', NULL, SQLITE3_BLOB);
//         $stmt->bindValue(':priority', "primary", SQLITE3_TEXT);
//         $stmt->bindValue(':date_time', date("d/m/Y h:i:s A"), SQLITE3_TEXT);
//         $stmt->bindValue(':missions_id', 100, SQLITE3_INTEGER);
//         $stmt->execute();
//         echo "Row has been inserted successfully!";
//     }
//     catch (Exception $bug) {
//         echo $bug->getMessage();
//     }
//     $connection->close();
// }
// else {
//     echo $connection->lastErrorMsg();
// }