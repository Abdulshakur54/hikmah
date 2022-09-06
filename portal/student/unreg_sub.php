<?php
require_once './includes/std.inc.php';
$sub = new Subject();
$msg = '';
if (Input::submitted() && Token::check(Input::get('token'))) {
    $submitType = Input::get('submittype');
    if ($submitType == 'deregister') {
        $subIds = explode(',', Utility::escape(Input::get('subjectids')));
        $req2 = new Request2();
        $classTeacherId = $std->getClassTeacherId($classId); //get class teacher id
        if (!empty($classTeacherId)) {
            $subDetails = $sub->getSubjectNames($subIds);
            $subjs = [];
            foreach($subDetails as $subDetail){
                $subjs[$subDetail->id] = $subDetail->subject;
            }
            $subjects = implode(', ', $subjs);
            $request = Utility::formatName(Utility::escape($data->fname), Utility::escape($data->oname), Utility::escape($data->lname)) . ' with Registration No: ' . $username .
                ' needs your approval to deregister the following subjects:<div class="font-style-italic p-3 font-weight-bold">' . $subjects.'</div';
            $req2->send($username, $classTeacherId, $request, 1, json_encode($subjs));
            $msg .= '<div class="success">The operation was successful but you would need approval from your Class Teacher to derigester the selected courses<br>A request has been sent to your Class Teacher for approval</div>';
        } else {
            $msg .= '<div class="failure>Sorry, you cannot proceed, you don\'t have have a Class Teacher to approve you request</div>';
        }
    }
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Subjects Deregistration</h4>
            <form class="forms-sample" id="subDeRegForm" onsubmit="return false" novalidate />
            <?php echo $msg; ?>
            <?php
            if (!empty($classId)) {
                $regSubArr = $sub->getRegisteredSubjectsId(Utility::getFormatedSession($currSession) . '_score', $username);

                $minNoSub = $sub->getMinNoSub($classId);
                $regSubsIdArray = Utility::convertToArray($regSubArr, 'id');
            ?>
                <div class="formhead">My Registered Subjects</div>
                <?php
                if (!empty($regSubsIdArray)) {
                ?>

                    <div class="m-3 text-right">
                        <label for="selectAll" id="selectAll">Select all</label>
                        <input type="checkbox" name="selectAll" onclick="checkAll(this)" />
                    </div>
                    <table class="table table-hover display" id="subjectTable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 0;
                            foreach ($regSubArr as $rsl) {
                                $counter++;
                                echo '<tr><td></td><td>' . $rsl->subject . '</td><td><input type="checkbox" id="chk' . $counter . '" value="' . $rsl->id . '"/></td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" value="<?php echo $counter ?>" id="counter" />
                    <input type="hidden" name="subjectids" id="subjectIds" />
                    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                    <input type="hidden" name="submittype" id="submittype" />
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary mr-2" name="register" onclick="confirmSubmission()" id="deRegBtn">Deregister</button>
                    </div>
            <?php
                } else {
                    echo '<div class="message">No record found</div>';
                }
            } else {
                echo '<div class="message">You would be able to deregister subjects after you have been assigned to a class</div>';
            }

            ?>
            </form>
            <script>
                validate('subDeRegForm');;
            </script>
        </div>
    </div>
</div>
<script src="scripts/student/unreg_sub.js"></script>