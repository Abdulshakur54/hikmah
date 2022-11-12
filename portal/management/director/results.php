<?php
require_once './includes/director.inc.php';

if (!Input::submitted('get')) {
    Redirect::to(404);
}
$classes = School::get_classes($sch_abbr);
$classId = Input::get('class_id');
function selectedClass($id)
{
    global $classId;
    if ($id == $classId) {
        return 'selected';
    }
}
$res_url = $url->to('students_result.php?session=' . $currSession . '&term=' . $currTerm . '&school=' . $sch_abbr, 0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-center g-2">
                <div class="form-group">
                    <select class="js-example-basic-single w-100 p-2" id="class" name="class">
                        <option value="">:::Select Class:::</option>
                        <?php
                        foreach ($classes as $class) {
                        ?>
                            <option value="<?php echo $class->id ?>"><?php echo School::getLevelName($sch_abbr, $class->level) . ' ' . $class->class ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary btn-md" onclick="viewResult()" id="resultBtn">View Result</button><span id="ld_loader"></span>
                </div>
            </div>

            <?php
            if (empty($classId)) {
                echo '<div class="message text-center">Select a class first</div>';
            } ?>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>
            <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            <input type="hidden" value="<?php echo $res_url ?>" name="url" id="url" />
            <input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
            <script src="scripts/management/hos/results.js"></script>