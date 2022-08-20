<?php
require_once './includes/apm.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('management/apm/add_token.php')">Add Token</button></div>
            <h4 class="card-title text-primary">Tokens</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="tokensTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Pin</th>
                            <th>School</th>
                            <th>Level</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db->query('select id,owner,token,level,sch_abbr from token where added_by = ? order by owner asc', [$rank]);
                        $res = $db->get_result();
                        if (!empty($res)) {
                            foreach ($res as $val) {
                                echo '<tr id="row' . $val->id . '">
                            <td></td>
                            <td>' . Utility::escape(ucwords($val->owner)) . '</td>
                            <td>' . Utility::escape($val->token) . '</td>
                            <td>' . Utility::escape($val->sch_abbr) . '</td>
                            <td>' . Utility::escape($val->level) . '</td>
                            <td><button class="btn btn-danger btn-md" onclick="deleteToken(' . $val->id . ')">Delete</button></td>
                         </tr>';
                            }
                        } else {
                            echo '<tr><td colspan ="5">No records found</td></tr>';
                        }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script src="scripts/management/apm/tokens.js"></script>