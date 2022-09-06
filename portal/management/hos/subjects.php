<?php
require_once './includes/hos.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('management/hos/add_subject.php?operation=add')">Add Subject</button></div>
            <h4 class="card-title text-primary">Subjects</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered nowrap responsive" id="subjectsTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Teacher</th>
                            <th>School</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $hos->getSubjects($sch_abbr);
                        if (!empty($res)) {
                            foreach ($res as $val) {
                                echo '<tr id="row' . $val->id . '">
                            <td></td>
                            <td>' . Utility::escape($val->subject). '</td>
                            <td>' . School::getLevelName(Utility::escape($val->sch_abbr), (int)$val->level) . ' ' . Utility::escape(ucwords($val->class)) . '</td>
                            <td>' . Utility::escape($val->title . '. ' . Utility::formatName($val->fname, $val->oname, $val->lname)) . '</td>
                            <td>' . Utility::escape($val->sch_abbr) . '</td>
                             <td><button class="btn btn-success btn-sm" onclick="getPage(\'management/hos/add_subject.php?operation=edit&subject_id='.$val->id.'&subject_name='.$val->subject.'\')">Edit</button></td>                          
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
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script src="scripts/management/hos/subjects.js"></script>