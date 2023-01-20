<?php
require_once './includes/apm.inc.php';

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Generate Admission Tokens</h4>
            <form class="forms-sample" id="tokenForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="staffname">Name</label>
                    <input type="text" class="form-control" id="staffname" onfocus="clearHTML('messageContainer')" title="Name" required pattern="^[a-zA-Z` ]+$">
                </div>

                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required onchange="changeSchool()">
                        <?php
                        $schools = School::getConvectionalSchools();
                        $genHtml = '<option value="">:::Select School:::</option>';
                        foreach ($schools as $sch => $sch_abbr) {
                            $genHtml .= '<option value="' . $sch_abbr . '">' . $sch . '</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="level">Level</label>
                    <select class="js-example-basic-single w-100 p-2" id="level" title="Level" name="level" required onchange="changeLevel()">
                        <option value="">:::Select School First:::</option>
                    </select>
                    <div id="showLevel" class="p-2"></div>
                </div>
                <div class="form-group">
                    <label for="term">Term of entry <i class="mdi mdi-comment-question-outline" style="font-size:1.2rem" onmouseenter="document.getElementById('term_message').style.display='inline'" onmouseleave="document.getElementById('term_message').style.display='none'"></i> <span class="message" style="display:none" id="term_message">This determines the term the student would start paying school fees</span></label>
                    <select class="js-example-basic-single w-100 p-2" id="term" title="Term of entry" name="term" required>
                        <option value="ft">First Term</option>
                        <option value="st">Second Term</option>
                        <option value="tt">Third Term</option>
                    </select>
                </div>

                <div id="messageContainer"></div>
                <button type="button" class="btn btn-primary mr-2" id="generatePin" onclick="addToken()">Generate Pin</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/apm/add_token.js"></script>
<script>
    validate('tokenForm');
</script>