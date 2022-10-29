<?php
require_once './includes/hos.inc.php';
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
            <div class="table-responsive">
                <table class="table table-hover display" id="permissionTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subordinates = Permission::get_subordinates('hos', $sch_abbr);
                        if (!empty($subordinates)) {
                            foreach ($subordinates as $sb) {
                                echo '<tr>
                            <td></td>
                            <td>' . $sb->std_id . '</td>
                            <td>' . Utility::formatName($sb->fname, $sb->oname, $sb->lname) . '</td>
                           <td><a href="#" onclick="getPage(\'management/hos/set_permissions.php?std_id=' . $sb->std_id . '\')">set permissions</a></td>
                         </tr>';
                            }
                        } else {
                            echo '<tr><td colspan ="4" class="text-center">No management member found</td></tr>';
                        }

                        ?>
                    </tbody>
                </table>
            </div>

        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script>
        $(document).ready(function() {
            $("#permissionTable").DataTable(dataTableOptions);
        });
    </script>