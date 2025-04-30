<!DOCTYPE html>
<html data-bs-theme="dark">
    <head>
        <?php
            include "assets/php/head.php";
            # Try to make this variable affectable in the dbconfig script to control the real time path of the SQLite Database file
            $GLOBALS['sqlite_db_dir'] = "assets/";
            include "assets/php/dbconfig.php";
            include "assets/php/functions.php";
            $campaign = [];
            $query_campaign = "";
            # Checking for query string
            if (isset($_GET['campaign'])) {
                $query_campaign = "SELECT * FROM campaigns WHERE id = " . $_GET['campaign'];
            }
            else {
                $query_campaign = "SELECT * FROM campaigns LIMIT 1";
            }
            $stmt = $connection->query($query_campaign);
            $campaign = $stmt->fetchArray(SQLITE3_ASSOC);
            # Checking for query string messages
            if (@$_GET['success'] != "" && @$_GET['success'] != NULL) {
                $success = $_GET['success'];
            }
            elseif (@$_GET['error'] != "" && @$_GET['error'] != NULL) {
                $error = $_GET['error'];
            }
            elseif (@$_GET['warning'] != "" && @$_GET['warning'] != NULL) {
                $warning = $_GET['warning'];
            }
            # Processing POST creations
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                # Save the location campaign and source for every case
                $source = "";
                $campaign_location = $_POST['location_campaign'];
                switch ($_POST['action']) {
                    case "create_objective":
                        $title = $_POST['name_objective'];
                        $content = $_POST['content_objective'];
                        $image_link = $_POST['image_objective'];
                        $priority = $_POST['priority_objective'];
                        $mission_id = $_POST['mission_objective'];
                        if (!isset($_POST['edit_objective'])) {
                            $source = "objective_creation";
                            strip_tags($title, $content);
                            strip_tags($image, $priority);
                            # Check not empty
                            if (!empty($title) && !empty($content) && !empty($priority) && !empty($mission_id)) {
                                # Check no error before running statement
                                if (!isset($error)) {
                                    try {
                                        $stmt = $connection->prepare("INSERT INTO objectives VALUES (:id, :title, :content, :image, :priority, :date_time, :mission_id)");
                                        $stmt->bindValue(':id', NULL, SQLITE3_INTEGER);
                                        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
                                        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
                                        $stmt->bindValue(':image', ($image_link != NULL && $image_link != "") ? $image_link : NULL, SQLITE3_BLOB);
                                        $stmt->bindValue(':priority', $priority, SQLITE3_TEXT);
                                        $stmt->bindValue(':date_time', date("d/m/Y h:i:s A"), SQLITE3_TEXT);
                                        $stmt->bindValue(':mission_id', $mission_id, SQLITE3_INTEGER);
                                        $stmt->execute();
                                        $success = "New objective $title added successfuly";
                                    }
                                    catch (Exception $bug) {
                                        $error = $bug->getMessage();
                                    }
                                }
                            }
                            else {
                                $error = "Please fill all fields!";
                            }
                        }
                        else { # Edit Objective
                            # Check not empty
                            if (!empty($title) && !empty($content) && !empty($priority) && !empty($mission_id)) {
                                # Check for any changes to be saved
                                $stmt = $connection->prepare("SELECT * FROM objectives WHERE id = :id");
                                $stmt->bindValue(":id", $_POST['objective_id']);
                                $origin = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
                                if ($title != $origin['title'] || $content != $origin['content'] || $image_link != $origin['image'] || $priority != $origin['priority'] || $mission_id != $origin['mission_id']) {
                                    try {
                                        $stmt = $connection->prepare("UPDATE objectives SET title = :title, content = :content, image = :image, priority = :priority, mission_id = :mission_id WHERE id = :id");
                                        $stmt->bindValue(":title", $title);
                                        $stmt->bindValue(":content", $content);
                                        $stmt->bindValue(":image", $image_link);
                                        $stmt->bindValue(":priority", $priority);
                                        $stmt->bindValue(":mission_id", $mission_id);
                                        $stmt->bindValue(":id", $_POST['objective_id']);
                                        $stmt->execute();
                                        $success = "Objective $title edited successfuly!";
                                        $source = "objective_edit";
                                    }
                                    catch (Exception $bug) {
                                        $error = $bug->getMessage();
                                    }
                                }
                                else {
                                    $warning = "No changes made to be saved!";
                                }
                            }
                            else {
                                $error = "Can't leave some field empty!";
                            }
                        }
                        break;
                    case "create_mission":
                        $source = "mission_creation";
                        $name = $_POST['name_mission'];
                        $icon_code = $_POST['icon_mission'];
                        $icon_type = $_POST['icon_type'];
                        $target_campaign = $_POST['target_campaign'];
                        strip_tags($name, $icon_code);
                        # Check not empty
                        if (!empty($name)) {
                            # Check if selected icon type
                            if ($icon_type != -1) {
                                # Check if selected source icon
                                switch ($icon_type) {
                                    case 1:
                                        $icon_code_tags = explode('</', $icon_code);
                                        if ($icon_code_tags[1] != "lord-icon>") {
                                            $icon_code = null;
                                        }
                                        break;
                                    case 2: # Icon code is a FontAwesome icon class in that case
                                        $icon_animation = $_POST['icon_animation_type'];
                                        # Check not empty
                                        if ($icon_animation != -1) {
                                            # Check which one
                                            if ($icon_animation == 1) { # Circular Rotation
                                                $icon_code = "<div class='circular-rotation'><i class='$icon_code'></i></div>";
                                            }
                                            else { # Heart Beat animaiton
                                                $icon_code = "<div class='heart-beat'><i class='$icon_code'></i></div>";
                                            }
                                        }
                                        else {
                                            $error = "Please choose an animation type!";
                                        }
                                        break;
                                    case 3: # Source Icon
                                        $icon_animation = $_POST['icon_animation_type'];
                                        $icon_content = $_POST['icon_content_type'];
                                        # Check not empty
                                        if ($icon_animation != -1 && $icon_content != -1) {
                                            $animation = "";
                                            # Setting Animation
                                            if ($icon_animation == 1) {
                                                $animation = "circular-rotation";
                                            }
                                            else {
                                                $animation = "heart-beat";
                                            }
                                            # Setting Content
                                            if ($icon_content == 1) {
                                                $icon_code = "<div class='source-icon $animation'>$icon_code</div>";
                                            }
                                            else {
                                                $icon_code = "<img src='$icon_code' class='source-icon $animation'>";
                                            }
                                        }
                                        break;
                                }
                                # Trying to record new created mission data
                                try {
                                    $stmt = $connection->prepare("INSERT INTO missions VALUES (:id, :name, :icon_code, :date_time, :campaign_id)");
                                    $stmt->bindValue(":id", NULL);
                                    $stmt->bindValue(":name", $name);
                                    $stmt->bindValue(":icon_code", $icon_code);
                                    $stmt->bindValue(":date_time", date("m/d/Y h:i:s A"));
                                    $stmt->bindValue(":campaign_id", $target_campaign);
                                    $stmt->execute();
                                    $success = "New mission $name added successfuly!";
                                }
                                catch (Exception $ex) {
                                    $error = $ex->getMessage() . "\n \n Campaign ID: $target_campaign";
                                }
                            }
                            else {
                                $error = "Please choose the icon's type!";
                            }
                        }
                        else {
                            $error = "Please fill all fields!";
                        }
                        break;
                    case "create_step":
                        $source = "step_creation";
                        $name = $_POST['name_step'];
                        $content = $_POST['content_step'];
                        $link = $_POST['link_step'];
                        $image_link = $_POST['image_link_step'];
                        $objective_id = $_POST['target_objective'];
                        strip_tags($name);
                        strip_tags($content);
                        strip_tags($link);
                        # Check not empty
                        if (!empty($name) && !empty($content)) {
                            # Check for an image link
                            if (empty($image_link)) {
                                $image_link = null;
                            }
                            # Trying to insert new step
                            try {
                                $step_stmt = $connection->prepare("INSERT INTO steps VALUES (:id, :title, :content, :image, :link, :date_time, :objective_id)");
                                $step_stmt->bindValue(":id", NULL);
                                $step_stmt->bindValue(":title", $name);
                                $step_stmt->bindValue(":content", $content);
                                $step_stmt->bindValue(":image", $image_link);
                                $step_stmt->bindValue(":link", $link);
                                $step_stmt->bindValue(":date_time", date("m/d/Y h:i:s A"));
                                $step_stmt->bindValue(":objective_id", $objective_id);
                                if ($step_stmt->execute()) {
                                    $success = "New step $name added successfuly!";
                                    echo "Good!";
                                }
                                else {
                                    echo $connection->lastErrorMsg();
                                    echo "Help!";
                                }
                            }
                            catch (Exception $bug) {
                                $error = "Something went wrong by $bug";
                            }
                        }
                        else {
                            $error = "Please fill all fields!";
                        }
                        break;
                    case "create_campaign":
                        $source = "campaign_creation";
                        $name = $_POST['name_campaign'];
                        strip_tags($name);
                        # Check not empty
                        if (!empty($name)) {
                            try {
                                $campaign_stmt = $connection->prepare("INSERT INTO campaigns VALUES (:id, :name, :date_time)");
                                $campaign_stmt->bindValue(":id", NULL);
                                $campaign_stmt->bindValue(":name", $name);
                                $campaign_stmt->bindValue(":date_time", date("m/d/Y h:i:s A"));
                                $campaign_stmt->execute();
                                $success = "New campaign $name added successfuly!";
                            }
                            catch (Exception $bug) {
                                $error = "Something went wrong on creating $name campaign! \n Error Message: $bug";
                            }
                        }
                        else {
                            $error = "Please type the name of the campaign!";
                        }
                        # Check no error
                        if (!isset($error)) {
                            header("Location: ?campaign=" . $connection->lastInsertRowID());
                        }
                        break;
                }
                header("Location: ?campaign=$campaign_location&utm_source=$source&error=$error&success=$success&warning=$warning&new_added=" . $connection->lastInsertRowID());
            }
        ?>
        <title>Mind Planner</title>
    </head>
    <body>
        <?php 
            include "assets/php/loading_screen.php";
            render_loading_screen($loading_screen_show);
        ?>
        <div class="page" id="page">
            <div class="global-mask"></div>
            <?php recieve_server_messages() ?>
            <!-- Context Menu Intialization -->
            <ul class="context" data-target-id="">
                <li class="context__header"><span class="text-capitalize"></span> Actions</li>
                <li class="context__item context__item--twitter" data-action="edit" data-target=""><i class="fa-solid fa-pen-to-square"></i> Edit</li>
                <li class="context__item context__item--facebook" data-action="delete" data-target="" data-target-name=""><i class="fa-solid fa-trash-can"></i> Delete</li>
            </ul>
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar">
                <div class="sidebar-top">
                    <a href="." class="sidebar-title mb-5 mt-4">
                        <img src="favicon.png?v=<?= time() ?>">
                        <h2>Mind Planner</h2>
                    </a>
                    <ul class="mt-5">
                        <?php
                            # Fetching campaigns
                            $stmt = $connection->prepare("SELECT * FROM campaigns");
                            $result = $stmt->execute();
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        ?>
                        <a href="?campaign=<?= $row['id'] ?>">
                            <li data-target="campaign<?= $row['id'] ?>" class="<?= $row['id'] == $campaign['id'] ? 'active' : '' ?> context-trigger" data-target-type="campaign" data-id="<?= $row['id'] ?>"><?= $row['name'] ?></li>
                        </a>
                        <?php
                            }
                        ?>
                        <li class="creator"><a data-href="creation" data-bs-toggle="modal" data-bs-target="#campaignCreator"><i class="fa-solid fa-circle-plus"></i> Create new campaign</a></li>
                    </ul>
                </div>
                <div class="sidebar-bottom">
                    <!-- For feature use -->
                </div>
            </div>
            <!-- Content -->
            <div class="content">
                <div class="header">
                    <h1 class="display-5"><?= $campaign['name'] ?></h1>
                    <div class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#objectiveCreator">
                        <i class="fa-solid fa-plus"></i>
                        <strong>New Objective</strong>
                    </div>
                </div>
                <!-- Listing Objectives -->
                <div class="objectives">
                    <?php 
                        # Query in campaigns
                        $campaign_stmt = $connection->prepare("SELECT * FROM campaigns WHERE id = :id");
                        $campaign_stmt->bindValue(":id", $campaign['id']);
                        $campaign_result = $campaign_stmt->execute();
                        $campaign_data = $campaign_result->fetchArray(SQLITE3_ASSOC);
                        # Query in missions
                        $missions_stmt = $connection->prepare("SELECT * FROM missions WHERE campaign_id = :id");
                        $missions_stmt->bindValue(":id", $campaign['id']);
                        $missions_result = $missions_stmt->execute();
                        # Query in objectives
                        $objectives_stmt = $connection->prepare("SELECT * FROM objectives ORDER BY CASE priority WHEN 'danger' THEN 1 WHEN 'warning' THEN 2 WHEN 'primary' THEN 3 WHEN 'dark' THEN 4 END");
                        $objectives_result = $objectives_stmt->execute();
                        $all_objectives = [];
                        while ($objective = $objectives_result->fetchArray(SQLITE3_ASSOC)) {
                            array_push($all_objectives, ["ID" => $objective['id'], "Title" => $objective['title'], "Content" => $objective['content'], "Image" => $objective['image'],"Priority" => $objective['priority'], "Date Time" => $objective['date_time'], "Mission ID" => $objective['mission_id']]);
                        }
                    ?>
                    <div class="container-fluid">
                        <div class="row flex-nowrap">
                            <?php
                                while ($mission = $missions_result->fetchArray(SQLITE3_ASSOC)) {
                            ?>
                            <div class="col mission" data-id="<?= $mission['id'] ?>">
                                <div class="d-flex context-trigger" style="gap: 10px;" data-target-type="mission" data-id="<?= $mission['id'] ?>">
                                    <?= $mission['icon_code'] ?>
                                    <strong><?= $mission['name'] ?></strong>
                                </div>
                                <?php
                            foreach ($all_objectives as $one_obj_data) {
                                if ($one_obj_data['Mission ID'] == $mission['id']) {
                            ?>
                                <div class="card objective context-trigger text-bg-<?= $one_obj_data['Priority'] ?> mb-3" data-target-type="objective" style="max-width: 18rem;" data-bs-toggle="modal" data-bs-target="#objectiveShow" data-title="<?= $one_obj_data['Title'] ?>" data-content="<?= $one_obj_data['Content'] ?>" data-img="<?= ($one_obj_data['Image'] != null) ? $one_obj_data['Image'] : "" ?>" data-date-time="<?= $one_obj_data["Date Time"] ?>" data-id="<?= $one_obj_data['ID'] ?>" data-priority="<?= $one_obj_data['Priority'] ?>">
                                    <div class="card-header"><?= $one_obj_data['Title'] ?></div>
                                    <div class="card-body">
                                        <p class="card-text" data-max-char="96"><?= $one_obj_data['Content'] ?></p>
                                    </div>
                                </div>
                            <?php
                                    }
                                    else {
                                        continue;
                                    }
                                }
                            ?>
                            </div>
                            <?php } ?>
                            <div class="col">
                                <div class="mission-creator" id="mission-creator" data-bs-toggle="modal" data-bs-target="#missionCreator" data-content="<?= $campaign_data['name'] ?>" data-campaign-id="<?= $campaign['id'] ?>">
                                    <div><i class="fa-solid fa-plus"></i> New Mission</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Objective Creator Modal -->
            <div class="modal fade" id="objectiveCreator" tabindex="-1" aria-labelledby="objectiveCreatorForm" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create new objective</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="objective-data" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                            <div class="modal-body">
                                <div class="box limited-box mb-2" id="nameObjective">
                                    <input type="text" name="name_objective" id="name-objective" class="form-control" placeholder="Objective name" autocomplete="off" maxlength="21" >
                                    <div>
                                        <strong></strong>
                                        <strong><span>0</span>/21</strong>
                                    </div>
                                </div>
                                <textarea name="content_objective" id="content-objective" class="form-control" placeholder="Objective Content"></textarea>
                                <select name="priority_objective" id="priority-objective" class="form-select">
                                    <option value="-1">Choose the priority of the objective</option>
                                    <option value="dark">⚫ Normal</option>
                                    <option value="primary">🔵 Interesting</option>
                                    <option value="warning">🟡 Important</option>
                                    <option value="danger">🔴 Urgent</option>
                                </select>
                                <select name="mission_objective" id="mission-objective" class="form-select">
                                    <option value="-1">Choose the mission of the objective</option>
                                    <?php
                                        $all_missions_stmt = $connection->prepare("SELECT * FROM missions WHERE campaign_id = :id");
                                        $all_missions_stmt->bindValue("id", $campaign['id'], SQLITE3_INTEGER);
                                        $missions_results_all = $all_missions_stmt->execute();
                                        while ($option = $missions_results_all->fetchArray(SQLITE3_ASSOC)) {
                                    ?>
                                    <option value="<?= $option['id'] ?>"><?= $option['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <input type="text" name="image_objective" id="image-objective" class="form-control" placeholder="Image Link" autocomplete="off">
                                <img src="" class="image-link-preview">
                                <strong></strong>
                                <input type="hidden" name="action" value="create_objective">
                                <input type="hidden" name="objective_id" id="objective-id" value="">
                                <input type="hidden" name="location_campaign" value="<?= $campaign['id'] ?>">
                            </div>
                            <div class="modal-footer">
                                <p class="lead"></p>
                                <div class="btn btn-dark" data-bs-dismiss="modal">Close</div>
                                <input type="submit" value="Create" class="btn btn-primary" id="create-objective" style="transform:translateY(2.5px);">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Mission Creator Modal -->
            <div class="modal fade" id="missionCreator" tabindex="-1" aria-labelledby="missionCreatorForm" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create mission in <strong class="text-lowercase"></strong></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="mission-creation-form">
                                <div class="box limited-box" id="nameCampaign">
                                    <input type="text" name="name_mission" id="name-mission" class="form-control" placeholder="Mission name" autocomplete="off" maxlength="21" >
                                    <div>
                                        <strong></strong>
                                        <strong><span>0</span>/21</strong>
                                    </div>
                                </div>
                                <select class="form-select" name="icon_type" id="icon-type">
                                    <option value="-1">Choose the icon type...</option>
                                    <option value="1">LordIcon</option>
                                    <option value="2">FontAwesome</option>
                                    <option value="3">Source Icon</option>
                                </select>
                                <div class="source-icon-properties">
                                    <select class="form-select" name="icon_animation_type" id="icon-animation-type">
                                        <option value="-1">Choose the animation type...</option>
                                        <option value="1">Circular Rotation</option>
                                        <option value="2">Heart Beat</option>
                                    </select>
                                    <select class="form-select" name="icon_content_type" id="icon-content-type">
                                        <option value="-1">Choose the content type...</option>
                                        <option value="1">Text</option>
                                        <option value="2">Image</option>
                                    </select>
                                </div>
                                <textarea name="icon_mission" id="icon-mission" class="form-control" placeholder="Icon Code"></textarea>
                                <input type="hidden" name="target_campaign" id="target-campaign">
                                <input type="hidden" name="action" value="create_mission">
                                <input type="hidden" name="location_campaign" value="<?= $campaign['id'] ?>">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                            <input type="submit" value="Create" class="btn btn-primary" style="transform: translateY(2.5px);" onclick="$('#mission-creation-form').submit()">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Campaign Creator Modal -->
            <div class="modal fade" id="campaignCreator" tabindex="-1" aria-labelledby="campaignCreatorForm" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create new campaign</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                            <div class="modal-body">
                                <div class="box limited-box" id="nameCampaign">
                                    <input type="text" name="name_campaign" id="name-campaign" class="form-control" placeholder="Campaign name" autocomplete="off" maxlength="20" >
                                    <div>
                                        <strong></strong>
                                        <strong><span>0</span>/20</strong>
                                    </div>
                                </div>
                                <input type="hidden" name="action" value="create_campaign">
                                <input type="hidden" name="campaign_id" id="campaign-id">
                                <input type="hidden" name="location_campaign" value="<?= $campaign['id'] ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                <input type="submit" value="Create" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Objectives' steps Modal -->
            <div class="modal fade" id="objectiveShow" tabindex="-1" aria-labelledby="objectiveShowSteps" aria-hidden="true" data-id="">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Objective 1</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex">
                            <div class="obj-details">
                                <p class="lead content">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                <div class="image-view">
                                    <img src="">
                                </div>
                                <p class="lead fw-bold date-time">
                                    <lord-icon src="https://cdn.lordicon.com/laobovmg.json" trigger="loop" state="hover-flutter" style="width:50px;height:50px"></lord-icon>
                                    <strong></strong>
                                </p>
                            </div>
                            <div class="steps">
                                <h3 class="fw-bold">Steps</h3>
                                <ul></ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary step-creator" data-obj-id="" data-obj-name="" data-modal-target="#stepCreation"><i class="fa-solid fa-circle-plus"></i> New Step</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Step Creation Modal -->
            <div class="modal fade" id="stepCreation" aria-hidden="true">
                <div class="content">
                    <div class="modal-header">
                        <h3 class="modal-title">New step in <span class="text-lowercase"></span> objective</h3>
                    </div>
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                        <div class="modal-body">                        
                            <div class="box limited-box" id="nameStep">
                                <input type="text" name="name_step" id="name-step" class="form-control" placeholder="Step Name" autocomplete="off" maxlength="28">
                                <div>
                                    <strong></strong>
                                    <strong><span>0</span>/28</strong>
                                </div>
                            </div>
                            <textarea name="content_step" id="content-step" class="form-control" placeholder="Step Content"></textarea>
                            <input type="text" name="link_step" id="link-step" class="form-control" placeholder="Step Link">
                            <input type="text" name="image_link_step" id="image-link-step" class="form-control" placeholder="Image Link">
                            <input type="hidden" name="action" value="create_step">
                            <input type="hidden" name="target_objective">
                            <input type="hidden" name="location_campaign" value="<?= $campaign['id'] ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="$(this).parent().parent().submit()">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="assets/js/index.js?v=<?= time() ?>"></script>
        <?php include "assets/php/body.php" ?>
    </body>
</html>