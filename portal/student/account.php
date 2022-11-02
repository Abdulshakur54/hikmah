<?php
require_once './includes/std.inc.php';
$passport_link = $url->to('uploads/passports/' . $data->picture, 3);
$db = DB::get_instance();
$data2 = $db->get('student2','phone,email,address',"std_id='$username'");

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Account</h4>
            <form method="post" onsubmit="updateAccount(event)" novalidate id="updateAccountForm" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="phone">Phone No (Parent)</label>
                    <input type="text" maxlength="11" class="form-control form-control-lg" id="phone" title="Phone No" required name="phone" pattern="^[0-9]{11}$" value="<?php echo $data2->phone ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email (Parent)</label>
                    <input type="email" class="form-control form-control-lg" id="email" title="Email" required name="email" value="<?php echo $data2->email ?>">
                </div>


                <div class="form-group">
                    <label for="address">Residential Address</label>
                    <input  type="text" id="address" name="address" value="<?php echo $data2->address ?>" class="form-control" />
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
                <div class="card-body message" style="font-size: 0.75em;">
                    To change other details like: Firstname, LastName, State, LGA Date of birth etc; Contact your Academic Planning Manager
                </div>
            </form>
        </div>
    </div>
</div>
<script src="scripts/staff/account.js"></script>