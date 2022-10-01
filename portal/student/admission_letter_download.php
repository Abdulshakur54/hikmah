<?php
require_once './includes/std.inc.php';
$download_link = $url->to('students_admission.php?adm_id='.$data->adm_id.'&school='.$sch_abbr.'&token='.Token::generate(),0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Admission Letter Download</h4>
            <form class="forms-sample" id="tokenForm" onsubmit="return false">
                <div class="py-5">
                    <a href="<?php echo $download_link?>" class="d-block text-center"><button type="button" class="btn btn-primary">Download</button></a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/director/add_token.js"></script>
<script>
    validate('tokenForm');;
</script>