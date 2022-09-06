<?php
require_once './includes/staff.inc.php';


?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body" id="requestsContainer">
            <h4 class="card-title text-primary">Requests</h4>
            <?php
            $requests = $req2->getMyRequests($username);
            if (!empty($requests)) {
                foreach ($requests as $rqst) {
            ?>

                    <div class="card border border-1 rounded mb-3" id="row<?php echo $rqst->id ?>">
                        <div class="card-header text-primary d-flex justify-content-between">
                            <div class="font-weight-bold"><?php echo $rqst->title ?></div>
                            <div class="text-muted font-italic "><?php echo Utility::get_past_time($rqst->created_at) ?></div>
                        </div>
                        <div class="card-body"><?php echo $rqst->request; ?></div>
                        <div class="card-footer d-flex justify-content-end border-0" style="background-color:#fff;">
                            <?php
                            echo '<button class="btn btn-success btn-sm mr-3" onclick="accept(' . $rqst->id . ',\'' . $rqst->requester_id . '\',' . $rqst->category . ')">Accept</button><span id="ld_loader_' . $id . '"></span><button class="btn btn-danger btn-sm" onclick="decline(' . $rqst->id . ',\'' . $rqst->requester_id . '\',' . $rqst->category . ')">Decline</button>';
                            ?>
                        </div>
                    </div>
                    <div class="invisible" id="other<?php echo $rqst->id?>"><?php echo $rqst->other ?></div>
            <?php
                }
            } else {
                echo '<div class="message">No Requests available</div>';
            }

            ?>

        </div>
    </div>
    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
</div>
<script src="scripts/staff/requests2.js"></script>