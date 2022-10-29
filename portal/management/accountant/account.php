<?php
require_once './includes/accountant.inc.php';
$bank_and_acctno = Utility::get_bank_and_acctno($username);
$passport_link = $url->to('uploads/passports/' . $data->picture, 1);
function selectedValue($value, string $type): string
{
    global $data, $bank_and_acctno;
    switch ($type) {
        case 'title':
            if ($value == $data->title) {
                return 'selected';
            }
            return '';
        case 'state':
            if ($value == $data->state) {
                return 'selected';
            }
            return '';
        case 'lga':
            if ($value == $data->lga) {
                return 'selected';
            }
            return '';
        case 'bank':
            if ($value == $bank_and_acctno->bank) {
                return 'selected';
            }
            return '';
        case 'preffered email':
            if ($value == $data->choosen_email) {
                return 'selected';
            }
            return '';
        default:
            return '';
    }
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Account</h4>
            <form method="post" onsubmit="updateAccount(event)" novalidate id="updateAccountForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <select class="js-example-basic-single w-100 p-2" id="title" Title="title" name="title" required>
                        <option <?php echo selectedValue('Mall', 'title') ?>>Mall</option>
                        <option <?php echo selectedValue('Mallama', 'title') ?>>Mallama</option>
                        <option <?php echo selectedValue('Mr', 'title') ?>>Mr</option>
                        <option <?php echo selectedValue('Mrs', 'title') ?>>Mrs</option>
                        <option <?php echo selectedValue('Miss', 'title') ?>>Miss</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fname">Firstname</label>
                    <input type="text" class="form-control form-control-lg" id="fname" title="Firstname" required name="fname" value="<?php echo $data->fname ?>" pattern="^[a-zA-Z`]+$">
                </div>
                <div class="form-group">
                    <label for="lname">Lastname</label>
                    <input type="text" class="form-control form-control-lg" id="lname" title="Lastname" required name="lname" value="<?php echo $data->lname ?>" pattern="^[a-zA-Z`]+$">
                </div>
                <div class="form-group">
                    <label for="oname">Othername</label>
                    <input type="text" class="form-control form-control-lg" id="oname" title="Othername" name="oname" value="<?php echo $data->oname ?>" pattern="^[a-zA-Z`]+$">
                </div>
                <div class="form-group">
                    <label for="dob">Date of birth</label>
                    <input type="date" class="form-control form-control-lg" id="dob" title="Date of birth" required name="dob" value="<?php echo date('Y-m-d', strtotime($data->dob)); ?>">
                </div>

                <div class="form-group">
                    <label for="state">State Of Origin</label>
                    <select class="js-example-basic-single w-100 p-2" id="state" title="State Of Origin" onchange="populateLGA(this)" name="state" required>
                        <?php
                        $states = Utility::getStates();
                        $placeholder = 'state'; //used as the second parameter to selected value
                        foreach ($states as $st) {
                            echo '<option value="' . $st['id'] . '" ' . selectedValue($st['id'], $placeholder) . '>' . $st['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lga">LGA Of Origin</label>
                    <select class="js-example-basic-single w-100 p-2" id="lga" title="LGA Of Origin" name="lga" required>
                        <?php
                        $lgas = Utility::getLgas($data->state);
                        $placeholder = 'lga'; //used as the second parameter to selected value
                        foreach ($lgas as $lg) {
                            echo '<option value="' . $lg['id'] . '" ' . selectedValue($lg['id'], $placeholder) . '>' . $lg['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone">Phone No</label>
                    <input type="text" maxlength="11" class="form-control form-control-lg" id="phone" title="Phone No" required name="phone" pattern="^[0-9]{11}$" value="<?php echo $data->phone ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" title="Email" required name="email" value="<?php echo $data->email ?>">
                </div>
                <div class="form-group">
                    <label for="officialEmail">Official Email</label>
                    <input type="email" class="form-control form-control-lg" value="<?php echo $data->official_email ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="lga">Preffered Email</label> <span class="message" style="font-size: 0.75em;">(This email would be used for sending you emails)</span>
                    <select class="js-example-basic-single w-100 p-2" id="prefferedEmail" title="Preffered Email" name="choosen_email" required>
                       <option <?php echo selectedValue($data->email,'preffered email') ?>><?php echo $data->email?></option>
                       <option <?php echo selectedValue($data->official_email,'preffered email') ?>><?php echo $data->official_email?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="account">Account No</label>
                    <input type="text" class="form-control form-control-lg" id="account" title="Account No" required name="account" pattern="^[0-9]{10}$" value="<?php echo $bank_and_acctno->no ?>">
                </div>
                <div class="form-group">
                    <label for="bank">Bank</label>
                    <select class="js-example-basic-single w-100 p-2" id="bank" title="Bank" name="bank" required>
                        <?php
                        $banks = Utility::getBanks();
                        foreach ($banks as $bank) {
                            echo '<option ' . selectedValue($bank, 'bank') . '>' . $bank . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="address">Residential Address</label>
                    <input  type="text" id="address" name="address" value="<?php echo $data->address ?>" class="form-control" />
                </div>


                <div class="mb-4">
                    <label for="picture" id="uploadTrigger" style="cursor: pointer; color:green;">Change Picture</label>
                    <div>
                        <input type="file" name="picture" id="picture" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png" />
                        <img id="image" width="100" height="100" src="<?php echo $passport_link ?>" />
                        <input type="hidden" name="hiddenPic" value="" id="hiddenPic" />
                        <div id="picMsg" class="errMsg"></div>
                    </div>
                </div>


                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit" id="updateBtn">Update</button><span id="ld_loader"></span>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/accountant/account.js"></script>