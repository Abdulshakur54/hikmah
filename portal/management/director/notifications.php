<?php
require_once './includes/director.inc.php';


?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Notifications</h4>
            <?php
            $alerts = $alert->getMyAlerts($username);
            if (!empty($alerts)) {
                foreach ($alerts as $alertt) {
            ?>

                    <div class="card border border-1 rounded mb-3">
                       <div class="card-header text-primary d-flex justify-content-between flex-wrap">
                            <div class="font-weight-bold"><?php echo $alertt->title ?></div>
                            <div class="text-muted font-italic "><?php echo Utility::get_past_time($alertt->created_at)?></div>
                        </div>
                        <div class="card-body"><?php echo $alertt->message; ?></div>
                    </div>
            <?php
                }
                $alert->seen($username);
            } else {
                echo '<div class="message">No notifications available</div>';
            }

            ?>

        </div>
    </div>
</div>