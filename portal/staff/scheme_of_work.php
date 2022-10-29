<?php
require_once './includes/staff.inc.php';
require_once './includes/sub_teacher.inc.php';
$term = (!empty(Input::get('term'))) ? Utility::escape(Input::get('term')) : $currTerm;
function selectedTerm($tm)
{
    global $term;
    return ($term === $tm) ? 'selected' : '';
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2 mb-2" onclick="getPage('staff/scheme_edit.php?op=add<?php echo '&term=' . $term . '&subid=' . $subId ?>')">Add Scheme</button></div>
            <h4 class="card-title text-primary"><?php echo ucwords(strtolower($subName)) ?> Scheme of Work</h4>
            <div class="row d-flex justify-content-center">
                <div class="form-group d-flex align-items-center">
                    <label for="term" class="mr-2">Term</label>
                    <select class="js-example-basic-single w-100 p-2 flex-grow-1" id="term" title="term" name="term" required onchange="changeTerm()">
                        <option value="ft" <?php echo selectedTerm('ft') ?>>First Term</option>
                        <option value="st" <?php echo selectedTerm('st') ?>>Second Term</option>
                        <option value="tt" <?php echo selectedTerm('tt') ?>>Third Term</option>
                    </select>
                </div>
            </div>
            <?php
            $scheme = Subject::get_schemes($subId, $term);
            if (!empty($scheme)) {
                $numRows = count($scheme);
                echo '<div class="message mb-2 text-right">' . $numRows . ' records found</div>';
                echo '<table class="table table-striped table-bordered nowrap responsive" id="schemesTable"><thead><th>S/N</th><th>Title</th><th>Scheme</th><th>Order</th><th></th><th></th></thead><tbody>';
                foreach ($scheme as $sch) {
                    echo '<tr id="row' . $sch->id . '"><td></td><td>' . $sch->title . '</td><td>' . $sch->scheme . '</td><td>' . $sch->scheme_order . '</td>
                    <td><button class = "btn btn-success btn-sm" onclick="getPage(\'staff/scheme_edit.php?op=edit&scheme_id=' . $sch->id . '&subid=' . $subId . '\')">Edit</button></td>
                                        <td><button class="btn btn-danger btn-sm" onclick="deleteScheme(' . $sch->id . ')" id="delete_' . $sch->id . '">Delete</button><span id="ld_loader_delete_' . $sch->id . '"></span></td>
                    </tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="message">No record found</div>';
            }
            ?>
            <input type="hidden" name="subid" id="subid" value="<?php echo $subId ?>">
            <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2();
        }
    });

    function changeTerm() {
        let term = document.getElementById('term').value;
        let subId = document.getElementById('subid').value;
        getPage('staff/scheme_of_work.php?term=' + term + '&subid=' + subId);
    }
</script>
<script src="scripts/staff/scheme.js"></script>