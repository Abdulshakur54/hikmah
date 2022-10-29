<?php
require_once './includes/director.inc.php';
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
                            <th>Management ID</th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subordinates = Permission::get_subordinates('director');
                        if (!empty($subordinates)) {
                            foreach ($subordinates as $sb) {
                                echo '<tr>
                            <td></td>
                            <td>' . $sb->mgt_id . '</td>
                            <td>' . Utility::formatName($sb->fname, $sb->oname, $sb->lname) . '</td>
                           <td><a href="#" onclick="getPage(\'management/director/set_permissions.php?mgt_id='.$sb->mgt_id.'\')">set permissions</a></td>
                         </tr>';
                            }
                        } else {
                            echo '<tr><td colspan ="4">No management member found</td></tr>';
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