<?php
require_once './includes/staff.inc.php';
require_once './includes/sub_teacher.inc.php';
$term = (!empty(Input::get('term'))) ? Utility::escape(Input::get('term')) : $currTerm;
function selectedTerm($tm){
    global $term;
    return ($term === $tm) ? 'selected':'';
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2 mb-2" onclick="getPage('staff/scheme_edit.php?op=add<?php echo '&term='.$term.'&sub_id='.$subId?>')">Add Scheme</button></div>
            <h4 class="card-title text-primary"><?php echo ucwords(strtolower($subName)) ?> Scheme of Work</h4>
            <div class="row d-flex">
                <div class="col-md-6 m-auto d-flex justify-content-center">
                    <div class="mr-3"><label>Select Term</label></div>
                    <div>
                        <select>
                            <option value="ft" <?php echo selectedTerm('ft')?>>First Term</option>
                            <option value="st" <?php echo selectedTerm('st')?>>Second Term</option>
                            <option value="tt" <?php echo selectedTerm('tt')?>>Third Term</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            $scheme = Subject::get_schemes($subId, $term);
            if (!empty($scheme)) {
                $numRows = count($scheme);
                echo '<div class="message mb-2 text-right">' . $numRows . ' records found</div>';
                echo '<table class="table table-striped table-bordered nowrap responsive" id="schemeTable"><thead><th>S/N</th><th>Title</th><th>Scheme</th><th>Order</th></thead><tbody>';
                foreach ($scheme as $sch) {
                    echo '<tr><td></td><td>' . Utility::noScript($sch->title) . '</td><td>' . Utility::noScript($sch->scheme) . '</td><td>' . $sch->scheme_order . '</td>
                    <td><button class = "btn btn-success btn-sm" onclick="getPage(\'staff/scheme_edit.php?op=edit&scheme_id=' . $sch->id . '&sub_id='.$subId.'\')">Edit</button></td>
                                        <td><button class="btn btn-danger btn-sm" onclick="deleteScheme(' . $sch->id . ')" id="delete_' . $sch->id . '">Delete</button><span id="ld_loader_delete_' . $sch->id . '"></span></td>
                    </tr>';
                }
                echo '</tbody></table>';
                echo '<div class="text-center p-3"><button onclick="notify()" class="btn btn-md btn-primary">Notify Students</button></div>';
            } else {
                echo '<div class="message">No record found</div>';
            }
            ?>
        </div>
    </div>
</div>
<script>
    function notify() {
        swalNotify('Students have been persistently notified', 'info');
    }

    $(document).ready(function() {
        $("#studentsTable").DataTable(dataTableOptions);
        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2();
        }
    });
</script>