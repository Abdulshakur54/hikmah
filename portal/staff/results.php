<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
$res_url = $url->to('students_result.php?session=' . $currSession . '&school=' . $sch_abbr, 0);
$ses_res_url = $url->to('students_ses_result.php?session=' . $currSession . '&term=' . $currTerm . '&school=' . $sch_abbr, 0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo Utility::formatTerm($currTerm) . ' ' . School::getLevelName($sch_abbr, $level) . ' ' . $class . ' Result' ?></h4>
            <div class="p-5 text-center d-flex justify-content-center g-1 flex-wrap">
                <button class="btn btn-primary" onclick="viewResult('ft')" id="ft_resultBtn">First Term</button><span id="ld_loader_ft"></span>
                <button class="btn btn-primary" onclick="viewResult('st')" id="st_resultBtn">Second Term</button><span id="ld_loader_st"></span>
                <button class="btn btn-primary" onclick="viewResult('tt')" id="tt_resultBtn">Third Term</button><span id="ld_loader_tt"></span>

                <button class="btn btn-primary" onclick="viewResult('ses')" id="ses_resultBtn">Sessional</button><span id="ld_loader_ses"></span>
            </div>
        </div>
    </div>
</div>
</div> <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
<input type="hidden" value="<?php echo $res_url ?>" name="url" id="url" />
<input type="hidden" value="<?php echo $ses_res_url ?>" name="ses_url" id="ses_url" />
<input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
<input type="hidden" value="<?php echo $classId ?>" name="class" id="class" />

<script src="scripts/staff/results.js"></script>