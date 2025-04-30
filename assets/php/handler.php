<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $GLOBALS['sqlite_db_dir'] = "../";
        include 'dbconfig.php';
        $action = $_POST['action'];
        switch ($action) {
            case 'listSteps':
                $objective_id = $_POST['objective_id'];
                $stmt = $connection->prepare("SELECT * FROM steps WHERE objective_id = :id");
                $stmt->bindValue(":id", $objective_id, SQLITE3_INTEGER);
                $results = $stmt->execute();
                $all_data = [];
                $count = 0;
                while ($steps_fetch = $results->fetchArray(SQLITE3_ASSOC)) {
                    $count++;
                }
                # Check for any steps by query results row count
                if ($count > 0) {
                    while ($steps = $results->fetchArray(SQLITE3_ASSOC)) {
                        array_push($all_data, $steps);
                    }
                    print_r(json_encode($all_data));
                }
                else {
                    echo "Nothing";
                }
                break;
            ### Deprecated cases for now...
            case 'create_campaign':
                $name = $_POST['name'];
                $stmt = $connection->prepare("INSERT INTO campaigns (name) VALUES (?)");
                $stmt->execute([$name]);
                echo json_encode(['status' => 'success']);
                break;
            case 'create_mission':
                $name = $_POST['name'];
                $icon_code = $_POST['icon_code'];
                $campaign_id = $_POST['campaign_id'];
                $stmt = $connection->prepare("INSERT INTO missions (name, icon_code, campaign_id) VALUES (?, ?, ?)");
                $stmt->execute([$name, $icon_code, $campaign_id]);
                echo json_encode(['status' => 'success']);
                break;
            case 'create_objective':
                $title = $_POST['name_objective'];
                $content = $_POST['content_objective'];
                $image_name = $_FILES['image_objective']['name'];
                $image_error = $_FILES['image_objective']['error'];
                $priority = $_POST['priority_objective'];
                $mission_id = $_POST['mission_objective'];
                strip_tags($title, $content);
                # Check not empty
                if (!empty($title) && !empty($content) && !empty($priority) && !empty($mission_id)) {
                    $image = "";
                    # Check image selection
                    if ($image_error == 0) {
                        $image = file_get_contents($image_name);
                    }
                    elseif ($image_error == 4) {
                        $image = null;
                    }
                    else {
                        echo json_encode(['status' => "error", 'message' => "Something went wrong on uploading objective's image \n Error Code: $image_error"]);
                        exit;
                    }
                    $stmt = $connection->prepare("INSERT INTO objectives VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute(['', $title, $content, $image, $priority, date("YYYY-m-d h:m:s"), $mission_id]);
                    echo json_encode(['status' => 'success', 'name' => $title]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields']);
                }
                break;
            case 'create_step':
                $title = $_POST['title'];
                $content = $_POST['content'];
                $priority = $_POST['priority'];
                $objective_id = $_POST['objective_id'];
                $stmt = $connection->prepare("INSERT INTO steps (title, content, priority, objective_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $content, $priority, $objective_id]);
                echo json_encode(['status' => 'success']);
                break;
            case 'delete_mission':
                $id = $_POST['id'];
                $stmt = $connection->prepare("DELETE FROM missions WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['status' => 'success']);
                break;
            case 'delete_objective': 
                $id = $_POST['objective_id'];
                $stmt = $connection->prepare("DELETE FROM objectives WHERE id = :id");
                $stmt->bindValue(":id", $id, SQLITE3_INTEGER);
                $stmt->execute();
                echo json_encode(['status' => 'success']);
                break;    
            case 'delete_step':
                $id = $_POST['step_id'];
                $stmt = $connection->prepare("DELETE FROM steps WHERE id = :id");
                $stmt->bindValue(":id", $id, SQLITE3_INTEGER);
                $stmt->execute();
                echo json_encode(['status' => 'success']);
                break;
        }
    }
?>