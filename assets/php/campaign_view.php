<!DOCTYPE html>
<html>
    <head>
        <?php 
            function render_campaign($id, $new_added = null) { 
                $GLOBALS['sqlite_db_dir'] = "../";
                include "dbconfig.php";
                # Query in campaigns
                $campaign_stmt = $connection->prepare("SELECT * FROM campaigns WHERE id = :id");
                $campaign_stmt->bindValue(":id", "");
                $campaign_result = $campaign_stmt->execute();
                $campaign_data = $campaign_result->fetchArray(SQLITE3_ASSOC);
                # Query in missions
                $missions_stmt = $connection->prepare("SELECT * FROM missions WHERE campaign_id = :id");
                $missions_stmt->bindValue(":id", "");
                $missions_result = $missions_stmt->execute();
                # Query in objectives
                $objectives_stmt = $connection->prepare("SELECT * FROM objectives ORDER BY CASE priority WHEN 'danger' THEN 1 WHEN 'warning' THEN 2 WHEN 'primary' THEN 3 WHEN 'dark' THEN 4 END");
                $objectives_result = $objectives_stmt->execute();
                $all_objectives = [];
                while ($objective = $objectives_result->fetchArray(SQLITE3_ASSOC)) {
                    array_push($all_objectives, ["ID" => $objective['id'], "Title" => $objective['title'], "Content" => $objective['content'], "Image" => $objective['image'],"Priority" => $objective['priority'], "Date Time" => $objective['date_time'], "Mission ID" => $objective['mission_id']]);
                }
                # Query in steps
                $steps_stmt = $connection->prepare("SELECT * FROM steps WHERE objective_id = :obj_id");
                $steps_stmt->bindValue(":obj_id", $id);
                $steps_stmt->execute();
        ?>
    </head>
    <body>        
        <div class="container-fluid">
            <div class="row flex-nowrap">
                <?php
                    while ($mission = $missions_result->fetchArray(SQLITE3_ASSOC)) {
                ?>
                    <div class="col">
                        <div class="d-flex" style="gap: 10px;">
                            <?= $mission['icon_code'] ?>
                            <strong><?= $mission['name'] ?></strong>
                        </div>
                        <?php
                            foreach ($all_objectives as $one_obj_data) {
                                if ($one_obj_data['Mission ID'] == $mission['id']) {
                        ?>
                                <div class="card text-bg-<?= $one_obj_data['Priority'] ?> <?= ($one_obj_data['ID'] == $new_added) ? "newest-add" : "" ?> mb-3" id="objective" style="max-width: 18rem;" data-bs-toggle="modal" data-bs-target="#objectiveShow" data-title="<?= $one_obj_data['Title'] ?>" data-content="<?= $one_obj_data['Content'] ?>" data-img="<?= ($one_obj_data['Image'] != null) ? "<img src='data:image/webp;base64,". base64_encode($one_obj_data['Image']) ."'>" : "" ?>" data-date-time="<?= $one_obj_data["Date Time"] ?>" data-id="<?= $one_obj_data['ID'] ?>">
                                    <div class="card-header"><?= $one_obj_data['Title'] ?></div>
                                    <div class="card-body">
                                        <p class="card-text" data-max-char="111"><?= $one_obj_data['Content'] ?></p>
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
                    <div class="mission-creator" id="mission-creator" data-bs-toggle="modal" data-bs-target="#missionCreator" data-content="<?= $campaign_data['name'] ?>" data-campaign-id="<?= $id ?>">
                        <div><i class="fa-solid fa-plus"></i> New Mission</div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </body>
</html>