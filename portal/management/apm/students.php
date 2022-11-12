<?php
require_once './includes/apm.inc.php';
$sch_abbr = Input::get('school');
function selected($sch)
{
    global $sch_abbr;
    return ($sch_abbr === $sch) ? 'selected' : '';
}
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Students</h4>
            <div class="form-group">
                <label for="school">School</label>
                <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required onchange="changeSchool()">
                    <?php
                    $schools = School::getSchools();
                    $count = 0;
                    $genHtml = '';
                    foreach ($schools as $sch => $abbr) {

                        if ($count === 0 && empty($sch_abbr)) {
                            $genHtml .= '<option value="' . $abbr . '" selected >' . $sch . '</option>';
                            $sch_abbr = $abbr; //use the first school as the selected school
                        } else {
                            $genHtml .= '<option value="' . $abbr . '" ' . selected($abbr) . ' >' . $sch . '</option>';
                        }
                        $count++;
                    }
                    echo $genHtml;

                    ?>
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-hover display" id="staffTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>School</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subordinates = Permission::get_subordinates('apm', $sch_abbr);
                        if (!empty($subordinates)) {
                            foreach ($subordinates as $sb) {
                                echo '<tr id="row_' . $sb->std_id . '">
                            <td></td>
                            <td>' . $sb->std_id . '</td>
                            <td>' . Utility::formatName($sb->fname, $sb->oname, $sb->lname) . '</td>
                           <td>' . School::getLevelName($sb->sch_abbr,(int)$sb->level).' '.$sb->class . '</td>
                           <td>'.$sb->sch_abbr.'</td>
                            <td><button class="btn btn-sm btn-warning" id="btn_' . $sb->std_id . '" onclick="remove(\'' . $sb->std_id . '\',\'expel\')">Expel</button><span id="ld_loader_expel_' . $sb->std_id . '"></span></td>
                            <td><button class="btn btn-sm btn-danger" id="btn_' . $sb->std_id . '" onclick="remove(\'' . $sb->std_id . '\',\'delete\')">Delete</button><span id="ld_loader_delete_' . $sb->std_id . '"></span></td>
                         </tr>';
                            }
                        }

                        ?>
                    </tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
    </div>
    <script>
        var table = $("#staffTable").DataTable(dataTableOptions);
        $(".js-example-basic-single").select2();
        async function remove(id, type) {
            let confirmMessage, op;
            if (type == 'expel') {
                confirmMessage = '<p>This will expel the selected student</p><p>His/Her data would remain but not accessible by the student</p><p>Director\'s approval is required</p>';
                op = 'expel_student';
            } else {
                confirmMessage = '<p>This will delete the selected stydebt</p><p>His/Her data would also be deleted</p><p>Director\'s approval is required</p>';
                op = 'delete_student';
            }
            if (await swalConfirm(confirmMessage, 'question')) {
                let username = _('username');
                let token = _('token');
                ld_startLoading('btn_' + id, `ld_loader_${type}_${id}`);
                let payload = 'id=' + id + '&op=' + op + '&username=' + username.value + '&token=' + token.value;
                ajaxRequest('management/apm/responses/responses.php', handleSack, payload);
            }

            async function handleSack() {
                ld_stopLoading('btn_' + id, `ld_loader_${type}_${id}`);
                const rsp = JSON.parse(xmlhttp.responseText);
                _("token").value = rsp.token;
                if (rsp.status === 204) {
                    if (type === 'delete') {
                        table
                            .row("#row_" + id)
                            .remove()
                            .draw();
                    }
                    swalNotify(rsp.message, "success");
                } else {
                    swalNotify(rsp.message, "error");
                }
            }
        }

        function changeSchool() {
            let school = _('school').value;
            getPage('management/apm/students.php?school=' + school + '&token=' + _('token').value);
        }
    </script>