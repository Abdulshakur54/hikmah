<?php
require_once './includes/hos.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('management/hos/add_class.php?operation=add')">Add Class</button></div>
            <h4 class="card-title text-primary">Classes</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered nowrap responsive" id="classesTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Class</th>
                            <th>Level</th>
                            <th>School</th>
                            <th>Teacher</th>
                            <th>Min No of Subject</th>
                            <th>PetName</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $res = School::getClassDetail($sch_abbr);
                        if (!empty($res)) {
                            foreach ($res as $val) {
                                echo '<tr id="row' . $val->id . '">
                            <td></td>
                            <td>' . School::getLevelName(Utility::escape($val->sch_abbr), (int)$val->level) . ' ' . Utility::escape(ucwords($val->class)) . '</td>
                            <td>' . (int) $val->level . '</td>
                            <td>' . Utility::escape($val->sch_abbr) . '</td>
                            <td>' . Utility::escape($val->title . '. ' . Utility::formatName($val->fname, $val->oname, $val->lname)) . '</td>
                            <td>' . Utility::escape($val->nos) . '</td>
                            <td>' . Utility::escape($val->petname) . '</td> 
                             <td><button class="btn btn-success btn-sm" onclick="getPage(\'management/hos/add_class.php?operation=edit&classid=' . $val->id . '\')">Edit</button></td>                          
                            <td><button class="btn btn-danger btn-md" onclick="deleteClass(' . $val->id . ')">Delete</button></td>
                         </tr>';
                            }
                        } else {
                            echo '<div>No records found</td></div>';
                        }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Utility::escape($sch_abbr) ?>" name="school" id="school" />
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script src="scripts/management/hos/classes.js"></script>