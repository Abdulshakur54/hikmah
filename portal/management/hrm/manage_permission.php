<?php
require_once './includes/hrm.inc.php';
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

            <h4 class="card-title text-primary">Manage Permissions</h4>
            <div class="form-group">
                <label for="school">School</label>
                <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required onchange="changeSchool()">
                    <?php
                    $schools = School::getSchools();
                    $genHtml = '<option value="">:::Select School:::</option>';
                    foreach ($schools as $sch => $abbr) {
                        $genHtml .= '<option value="' . $abbr . '" ' . selected($abbr) . '>' . $sch . '</option>';
                    }
                    echo $genHtml;
                    ?>
                </select>
            </div>
            <?php
            if (!empty($sch_abbr)) {
            ?>
                <div class="table-responsive">
                    <table class="table table-hover display" id="permissionTable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subordinates = Permission::get_subordinates('hrm', $sch_abbr);
                            if (!empty($subordinates)) {
                                foreach ($subordinates as $sb) {
                                    echo '<tr>
                            <td></td>
                            <td>' . $sb->staff_id . '</td>
                            <td>' . Utility::formatName($sb->fname, $sb->oname, $sb->lname) . '</td>
                           <td><a href="#" onclick="getPage(\'management/hrm/set_permissions.php?staff_id=' . $sb->staff_id . '\')">set permissions</a></td>
                         </tr>';
                                }
                            } else {
                                echo '<tr><td colspan ="4" class="text-center">No management member found</td></tr>';
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <script>
                    $(document).ready(function() {
                        $("#permissionTable").DataTable(dataTableOptions);
                    });
                </script>
            <?php
            } else {
                echo '<div class="message  text-center">Select a school first</div>';
            }
            ?>

        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        <input type="hidden" value="<?php echo $sch_abbr; ?>" name="school" id="school" />
    </div>
    <script>
        $(".js-example-basic-single").select2();

        function changeSchool() {
            const school = _('school').value;
            getPage('management/hrm/manage_permission.php?school=' + school);
        }
    </script>