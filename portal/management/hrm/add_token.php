<?php
require_once './includes/hrm.inc.php';

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Generate Staff Tokens</h4>
            <form class="forms-sample" id="tokenForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="staffname">Name</label>
                    <input type="text" class="form-control" id="staffname" onfocus="clearHTML('messageContainer')" title="Name" required pattern="^[a-zA-Z` ]+$">
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                    <select class="js-example-basic-single w-100 p-2" id="position" title="Position" name="position" required>
                        <option value="7">Teacher</option>
                        <option value="8">Non Teaching Staff</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required>
                        <?php
                        $schools = School::getConvectionalSchools();
                        $genHtml = '';
                        foreach ($schools as $sch => $sch_abbr) {
                            $genHtml .= '<option value="' . $sch_abbr . '">' . $sch . '</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Salary(&#8358;)</label>
                    <input type="text" class="form-control" id="salary" onfocus="clearHTML('messageContainer')" title="Salary" required pattern="^[0-9]+\.?[0-9]+$">
                </div>
                <div id="messageContainer"></div>
                <button type="button" class="btn btn-primary mr-2" id="generatePin" onclick="addToken()">Generate Pin</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hrm/add_token.js"></script>
<script>
    validate('tokenForm');;
</script>