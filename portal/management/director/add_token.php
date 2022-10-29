<?php
require_once './includes/director.inc.php';

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Generate Management Members Tokens</h4>
            <form class="forms-sample" id="tokenForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="staffname">Name</label>
                    <input type="text" class="form-control" id="staffname" onfocus="clearHTML('messageContainer')" title="Name" required pattern="^[a-zA-Z` ]+$">
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                    <select class="js-example-basic-single w-100 p-2" id="position" title="Position" name="position" required onchange="changeSchool(this)">
                        <option value="">:::Select Position:::</option>
                        <?php
                        foreach (Management::getPositions(3) as $pos => $rank) {
                            if ($rank !== 1) {
                                echo '<option value="' . $rank . '">' . $pos . '</option>';
                            }
                        } ?>
                    </select>
                </div>
                <div class="ml-4 d-flex justify-content-start gx-3" style="width: 380px">
                    <div class="form-group mb-0">
                        <label>
                            <input class="form-check-input" type="radio" id="mainPos" name="asst" value="0" onchange="changeAsst(this)"> Main
                        </label>
                    </div>
                    <div class="form-group mb-0">
                        <label>
                            <input class="form-check-input" type="radio" id="deputyPos" name="asst" value="1" onchange="changeAsst(this)"> Deputy
                        </label>
                    </div>
                    <div class="form-group mb-0">
                        <label>
                            <input class="form-check-input" type="radio" id="assistantPos" name="asst" value="2" onchange="changeAsst(this)"> Assistant
                        </label>
                    </div>
                </div>
                <div id="asstDiv">

                </div>
                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required>
                        <option value="">:::Select Position First:::</option>
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
                <input type="hidden" name="pos" id="pos" value="" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/director/add_token.js"></script>
<script>
    validate('tokenForm');;
</script>