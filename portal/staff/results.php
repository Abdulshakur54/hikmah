<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
$token = Token::generate();
$res_url = $url->to('students_result.php?session=' . $currSession . '&term=' . $currTerm . '&school=' . $sch_abbr, 0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo Utility::formatTerm($currTerm) . ' ' . School::getLevelName($sch_abbr, $level) . ' ' . $class . ' Result' ?></h4>
            <div class="p-5 text-center">

                <button class="btn btn-primary" onclick="viewResult()" id="resultBtn">View</button><span id="ld_loader"></span>
            </div>
        </div>
    </div>
</div>
</div> <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
<input type="hidden" value="<?php echo $res_url ?>" name="url" id="url" />
<input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
<input type="hidden" value="<?php echo $classId ?>" name="class" id="class" />

<script src="scripts/staff/results.js"></script>