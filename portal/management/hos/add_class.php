<?php
require_once './includes/hos.inc.php';

function selectLevel($lev)
{
    global $level;
    if ($level !== null && $lev == $level) {
        return 'selected';
    }
}
$operation = Utility::escape(Input::get('operation'));
$hos = new HOS();

if (!empty($operation)) {
    if ($operation == 'edit') {
        $classId = Utility::escape(Input::get('classid'));
        $classData = $hos->getClassDetail($classId);
        $class = $classData->class;
        $nos = $classData->nos;
        $petname = $classData->petname;
    } else {
        $class = '';
        $nos = '';
        $petname = '';
        $classId = '';
    }
} else {
    exit();
}


?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo ucfirst($operation) ?> Class</h4>
            <form class="forms-sample" id="classForm" onsubmit="return false" novalidate>
                <?php
                $level = null; //this will only change when form is submitted via post
                if (Input::submitted() && Token::check(Input::get('token'))) {
                    $msg = '';
                    $operation = Utility::escape(Input::get('operation'));
                    if (!empty($operation)) {
                    } else {
                        exit();
                    }
                    if ($operation == 'edit') {

                        $val = new Validation();
                        $values = [
                            'classid' => [
                                'name' => 'Class',
                                'required' => true
                            ],
                            'class' => [
                                'name' => 'Class',
                                'required' => false,
                                'pattern' => '^[a-zA-Z]$'
                            ],
                            'petname' => [
                                'name' => 'Petname',
                                'required' => false,
                                'pattern' => '^[a-zA-Z]+$',
                            ]
                        ];
                        if ($val->check($values)) {
                            $classId = Utility::escape(Input::get('classid'));
                            $level = Utility::escape(Input::get('level'));
                            $class = Utility::escape(Input::get('class'));
                            $class = strtoupper($class);
                            $petname = Utility::escape(Input::get('petname'));
                            if ($hos->classExists($sch_abbr, $level, $class)) {

                                $msg = '<div class="failure">' . School::getLevelName($sch_abbr, $level) . ' ' . strtoupper($class) . ' already exist</div>';
                            } else {
                                //insert into class table
                                $hos->editClass($classId, $class, $petname);
                                $msg = '<div class="success">Update was successful</div>';
                            }
                        } else {
                            $errors = $val->errors();
                            foreach ($errors as $error) {
                                $msg .= $error . '<br>';
                            }
                            $msg = '<div class="failure">' . $msg . '</div>';
                        }
                    } else {
                        $class = Utility::escape(Input::get('class'));
                        $class = strtoupper($class);
                        $petname = Utility::escape(Input::get('petname'));
                        $level = (int)Input::get('levelid');
                        $nos = (int)Input::get('nos');

                        if (preg_match('/^[a-zA-Z]$/', $class)) {
                            if (empty($petname) || preg_match('/^[a-zA-Z]+$/', $petname)) { //validate petname
                                $utils = new Utils();
                                //$currSession = $utils->getSession($sch_abbr);
                                if ($hos->classExists($sch_abbr, $level, $class)) {
                                    $msg = '<div class="failure">' . School::getLevelName($sch_abbr, $level) . ' ' . strtoupper($class) . ' already exist</div>';
                                } else {
                                    //insert into class table
                                    $hos->addClass($sch_abbr, $level, $class, $nos, $petname);
                                    //output success message
                                    $msg = '<div class="success">Successfully Added ' . School::getLevelName($sch_abbr, $level) . ' ' . strtoupper($class) . '</div>';
                                    $class = '';
                                    $nos = '';
                                    $petname = '';
                                }
                            } else {
                                $msg = '<div class="failure">Invalid Petname</div>';
                            }
                        } else {
                            $msg = '<div class="failure">Invalid Class Name</div>';
                        }
                    }

                    Session::set_flash('message', $msg);
                }
                ?>

                <?php
                if ($operation != 'edit') {
                ?>
                    <div class="form-group">
                        <label for="level">Level</label>
                        <select class="js-example-basic-single w-100 p-2" id="level" title="Level" name="levelid" required>
                            <?php
                            $schLevels = School::getLevels($sch_abbr);
                            foreach ($schLevels as $levName => $lev) {
                                echo '<option value="' . $lev . '"' . selectLevel($lev) . '>' . $levName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                <?php
                }
                ?>
                <div class="form-group">
                    <label for="class">Class</label>
                    <input type="text" class="form-control" id="class" onfocus="clearHTML('messageContainer')" title="Class" required pattern="^[a-zA-Z]$" value="<?php echo $class; ?>" name="class">
                </div>

                <div class="form-group">
                    <label for="petname">Petname</label>
                    <input type="text" class="form-control" id="petname" onfocus="clearHTML('messageContainer')" title="Petname" required pattern="^[a-zA-Z]+$" value="<?php echo $petname; ?>" name="petname">
                </div>

                <div class="form-group">
                    <label for="petname">Min No of Subjects</label>
                    <input type="number" class="form-control" id="nos" onfocus="clearHTML('messageContainer')" title="Min No of Subjects" required min="1" value="<?php echo $nos; ?>" name="nos" <?php echo ($operation == 'edit') ? 'disabled' : '' ?>>
                </div>
                <div id="messageContainer">
                    <?php
                    $msg = Session::get_flash('message');
                    if (!empty($msg)) {
                    ?>
                        <script>
                            swalNotifyDismiss('<?php echo $msg ?>', 'info', 2000);
                        </script>
                    <?php
                    }
                    ?>

                </div>
                <button type="button" class="btn btn-primary mr-2" id="addBtn" onclick="saveClass()">Save</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo $operation ?>" name="operation" id="operation" />
                <input type="hidden" value="<?php echo $classId ?>" name="classid" id="classid" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hos/classes.js"></script>
<script>
    validate('classForm');
</script>