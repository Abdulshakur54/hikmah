<?php
require_once './includes/apm.inc.php';


?>
<style>
    .requester {
        color: blue;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body" id="requestsContainer">
            <h4 class="card-title text-primary">Requests</h4>
            <?php
            $requests = $req->getMyRequests($rank);
            if (!empty($requests)) {
                foreach ($requests as $rqst) {
                    if (!empty($rqst->other) && !empty(json_decode($rqst->other, 1)['sender_office'])) {
                        $message =
                            '<p class="text-right m-3 requester">From: ' . json_decode($rqst->other, 1)['sender_office'] . '</p>';;
                    } else {
                        $first_letter = strtoupper(substr($rqst->requester_id, 0, 1));
                        if (is_numeric($first_letter)) {
                            $requester_data = $db->get('admission', 'fname,oname,lname,sch_abbr', "adm_id='$rqst->requester_id'");
                            $message = '<p class="text-right m-3 requester">From: ' . Utility::formatName($requester_data->fname, $requester_data->oname, $requester_data->lname) . ' (admission student of ' . $requester_data->sch_abbr . ')</p>';
                        } else {

                            switch ($first_letter) {
                                case 'H':
                                    $requester_data = $db->get('student', 'fname,oname,lname,sch_abbr,level', "std_id='$rqst->requester_id'");
                                    $message = '<p class="text-right m-3 requester">From: ' . Utility::formatName($requester_data->fname, $requester_data->oname, $requester_data->lname) . ' (student of' . $requester_data->sch_abbr . ', ' . School::getLevelName($requester_data->sch_abbr, (int)$requester_data->level) . ')</p>';
                                    break;
                                case 'S':
                                    $requester_data = $db->get('staff', 'fname,oname,lname,sch_abbr,rank', "staff_id='$rqst->requester_id'");

                                    $message = '<p class="text-right m-3 requester">From: ' . Utility::formatName($requester_data->fname, $requester_data->oname, $requester_data->lname) . ' (' . User::getPosition((int)$requester_data->rank) . ' of ' . $requester_data->sch_abbr . '.)</p>';
                                    break;
                                case 'M':
                                    $requester_data = $db->get('management', 'fname,oname,lname,sch_abbr,rank,asst', "mgt_id='$rqst->requester_id'");
                                    print_r($rqst->requester_id);
                                    if ($requester_data->rank == 5) {
                                        $message = '<p class="text-right m-3 requester">From: ' . Utility::formatName($requester_data->fname, $requester_data->oname, $requester_data->lname) . ' (' . User::getPosition((int)$requester_data->rank, (int)$requester_data->asst) . ' of ' . $requester_data->sch_abbr . '.)</p>';
                                    } else {
                                        $message = '<p class="text-right m-3 requester">From: ' . Utility::formatName($requester_data->fname, $requester_data->oname, $requester_data->lname) . ' (' . User::getPosition((int)$requester_data->rank, (int)$requester_data->asst) . '.)</p>';
                                    }

                                    break;
                                default:
                                    $message = '';
                            }
                        }
                    }

            ?>

                    <div class="card border border-1 rounded mb-3" id="row<?php echo $rqst->id ?>">
                        <div class="card-header text-primary d-flex justify-content-between flex-wrap">
                            <div class="font-weight-bold"><?php echo $rqst->title ?></div>
                            <div class="text-muted font-italic "><?php echo Utility::get_past_time($rqst->created_at) ?></div>
                        </div>
                        <div class="card-body">
                            <?php echo $rqst->request; ?>
                            <?php echo $message ?>
                        </div>
                        <div class="card-footer d-flex justify-content-end border-0" style="background-color:#fff;">
                            <?php
                            echo '<button class="btn btn-success btn-sm mr-3" onclick="accept(' . $rqst->id . ',\'' . $rqst->requester_id . '\',' . $rqst->category . ')" id="accept_' . $rqst->id . '">Accept</button><span id="ld_loader_' . $rqst->id . '"></span><button class="btn btn-danger btn-sm" onclick="decline(' . $rqst->id . ',\'' . $rqst->requester_id . '\',' . $rqst->category . ')" id="decline_' . $rqst->id . '">Decline</button>';
                            ?>
                        </div>
                    </div>
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
<script src="scripts/management/accountant/requests.js"></script>