<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");
$message = '';
$status = 400;
$data = [];
$db = DB::get_instance();
$acct = new Accountant();
$url = new Url();
$val = new Validation();

if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch ($op) {
        case 'change_password':
            $password = Utility::escape(Input::get('password'));
            $new_password = Utility::escape(Input::get('new_password'));
            $username = Utility::escape(Input::get('username'));
            $rules = [
                'password' => [
                    'name' => 'Password',
                    'required' => true,
                    'pattern' => '^[A-Za-z0-9]+$'
                ],
                'new_password' => [
                    'name' => 'New Password',
                    'required' => true,
                    'pattern' => '^[A-Za-z0-9]+$',
                    'min' => 6,
                    'max' => 32
                ]
            ];
            if ($val->check($rules)) {
                $db_pwd = $db->get('management', 'password', "mgt_id='$username'")->password;
                if (password_verify($password, $db_pwd)) {
                    $db->update('management', ['password' => password_hash($new_password, PASSWORD_DEFAULT)]);
                    echo response(204, 'Successfully changed password');
                } else {
                    echo response(400, 'Present Password is incorrectly entered');
                }
            } else {
                $errors = $val->errors();
                echo response(400, implode('<br />', $errors));
            }
            break;
        case 'update_account':
            $rules = [
                'fname' => [
                    'name' => 'First Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'oname' => [
                    'name' => 'Other Name',
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'lname' => [
                    'name' => 'Last Name',
                    'required' => true,
                    'min' => 3,
                    'max' => 20,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'title' => [
                    'name' => 'Title',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+$'
                ],
                'dob' => [
                    'name' => 'Date of Birth',
                    'required' => true
                ],
                'state' => [
                    'name' => 'State',
                    'required' => true
                ],
                'lga' => [
                    'name' => 'LGA',
                    'required' => true
                ],
                'phone' => [
                    'name' => 'Phone',
                    'required' => true,
                    'size' => 11,
                    'pattern' => '^[0-9]{11}$'
                ],
                'email' => [
                    'name' => 'Email',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'
                ],
                'choosen_email' => [
                    'name' => 'Preffered Email',
                    'required' => true,
                    'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'
                ],
                'account' => [
                    'name' => 'Account No',
                    'required' => true,
                    'pattern' => '^[0-9]{10}$'
                ],
                'bank' => [
                    'name' => 'Bank',
                    'required' => true,
                ]

            ];
            $fileValues = [
                'picture' => [
                    'name' => 'Picture',
                    'required' => false,
                    'maxSize' => 100,
                    'extension' => ['jpg', 'jpeg', 'png']
                ]
            ];
            if ($val->check($rules) && $val->checkFile($fileValues) && Utility::noScript(Input::get('address'))) {
                $fname = Utility::escape(Input::get('fname'));
                $lname = Utility::escape(Input::get('lname'));
                $oname = Utility::escape(Input::get('oname'));
                $title = Utility::escape(Input::get('title'));
                $email = Utility::escape(Input::get('email'));
                $choosen_email = Utility::escape(Input::get('choosen_email'));
                $address = Utility::escape(Input::get('address'));
                $state = Utility::escape(Input::get('state'));
                $lga = Utility::escape(Input::get('lga'));
                $dob = Utility::escape(Input::get('dob'));
                $phone = Utility::escape(Input::get('phone'));
                $account = Utility::escape(Input::get('account'));
                $bank = Utility::escape(Input::get('bank'));
                $username = Utility::escape(Input::get('username'));
                $values = [
                    'fname' => $fname,
                    'lname' => $lname,
                    'oname' => $oname,
                    'title' => $title,
                    'email' => $email,
                    'choosen_email' => $choosen_email,
                    'address' => $address,
                    'state' => $state,
                    'lga' => $lga,
                    'dob' => $dob,
                    'phone' => $phone,
                ];
                if (!empty($_FILES['picture']['name'])) {
                    $file = new File('picture');
                    $pictureName = $username . '.' . $file->extension();
                    $values['picture'] = $pictureName;
                    $db->update('management', $values, "mgt_id='$username'");
                    $file_path = '../../uploads/passports/' . $pictureName;
                    $file->move($file_path);
                } else {
                    $db->update('management', $values, "mgt_id='$username'");
                }
                $db->update('account', ['no' => $account, 'bank' => $bank]);
                echo response(201, 'Update was successful');
            } else {
                $errors = implode('<br />', $val->errors());
                echo response(500, $errors);
            }


            break;
        case 'initiate_payment':
            $year = (int)Utility::escape(Input::get('year'));
            $month =  Utility::escape(Input::get('month'));
            $acct = new Account();
            $db = DB::get_instance();
            if (!empty($db->select('payment_months', 'id', "month='$month' and year=$year"))) {
                echo response(400, 'Salary month already exist');
                exit();
            }
            $staffs = $acct->getRecipientsAccounts('staff');
            $managements = $acct->getRecipientsAccounts('management');
            try {
                $db->beginTransaction();
                $db->insert('payment_months', ['month' => $month, 'year' => $year]);
                $insertId = $db->getLastInsertId();
                $start = false;
                foreach ($staffs as $staff) {
                    if ($start) {
                        $db->requery([$staff->receiver, 0, 0, $staff->sch_abbr, $insertId, 'S']);
                    } else {
                        $db->query('insert into salary(receiver,paid,status,sch_abbr,payment_month,category) values(?,?,?,?,?,?)', [$staff->receiver, 0, 0, $staff->sch_abbr, $insertId, 'S']);
                        $start = true;
                    }
                }
                $start = false;
                foreach ($managements as $mgt) {
                    if ($start) {
                        $db->requery([$mgt->receiver, 0, 0, $staff->sch_abbr, $insertId, 'M']);
                    } else {
                        $db->query('insert into salary(receiver,paid,status,sch_abbr,payment_month,category) values(?,?,?,?,?,?)', [$mgt->receiver, 0, 0, $staff->sch_abbr, $insertId, 'M']);
                        $start = true;
                    }
                }
                $db->commit();
                echo response(201, 'Payment initiated successfully');
            } catch (PDOException $e) {
                $db->rollBack();
                echo response(500, 'An error prevented the initiation of a new payment month');
            }


            break;
        case 'get_recipients':
            $payment_month = Utility::escape(Input::get('payment_month'));
            $school = Utility::escape(Input::get('school'));
            $recipients = Utility::escape(Input::get('recipients'));
            $acct = new Account();
            if ($recipients == 'staff') {
                $payment_details = $acct->getPaymentDetails($payment_month, $recipients, $school);
            } else {
                $payment_details = $acct->getPaymentDetails($payment_month, $recipients);
            }

            echo response(200, '', $payment_details);
            break;
        case 'pay_salary':
            $db = DB::get_instance();
            $payment_month = Utility::escape(Input::get('payment_month'));
            $school = Utility::escape(Input::get('school'));
            $recipients = Utility::escape(Input::get('recipients'));
            $recipients_data = json_decode(Input::get('recipientsData'), true);
            $payment_type = Utility::escape(Input::get('payment_type'));
            $username = Utility::escape(Input::get('username'));
            $percentage = Utility::escape(Input::get('percentage'));
            //ensure request does not exist for the same payment month
            if (!empty($db->get('payment_exist', 'id', "payment_month = $payment_month"))) {
                echo response(400, "<p>Payment request already sent to the Director for this month</p><p>The Director would have to respond to it before you can make another request for the same month</p>");
                exit();
            }
            $acct = new Account();
            //validate payment
            $start = false;
            $total_payable = 0;
            foreach ($recipients_data as $rd) {
                if ($start) {
                    $db->requery([$rd[0], $payment_month]);
                    if ($db->row_count() > 0) {
                        $res = $db->one_result();
                        if (isOverpayment($res->salary, $res->paid, (float)$rd[1])) {
                            echo response(400, "$rd[0] cannot be paid higher than his stipulated salary");
                            exit();
                        }
                    } else {
                        echo response(400, "$rd[0] was not found");
                        exit();
                    }
                } else {
                    $db->query('select salary.paid, account.salary from account inner join salary on salary.receiver = account.receiver where account.receiver =? and salary.payment_month=?', [$rd[0], $payment_month]);
                    if ($db->row_count() > 0) {
                        $res = $db->one_result();
                        if (isOverpayment($res->salary, $res->paid, (float)$rd[1])) {
                            echo response(400, "$rd[0] cannot be paid higher than his stipulated salary");
                            exit();
                        }
                    } else {
                        echo response(400, "$rd[0] was not found");
                        exit();
                    }
                    $start = true;
                }
                $total_payable += (float) $rd[1];
            }
            //get account balance and ensure it is enough to pay salary;
            if ($recipients == 'management') {
                $payer = Config::get('hikmah/management_account');
            } else {
                $payer = $school;
            }
            $payer_balance = (float)$db->get('accounts', 'balance', "account_name='$payer'")->balance;
            if ($total_payable >= $payer_balance) {
                echo response(400, "There is no enough money in $payer account to process this operation");
                exit();
            }
            $qry = $db->get('payment_months', 'month,year', "id=$payment_month");
            $salary_month = $qry->month . ', ' . $qry->year;
            $req = new Request();
            $msg = "<p>Sir, Your Approval is required to process salary payment for the salary month ($salary_month)</p>
                <p><span class='font-weight-bold'>Percentage: </span><span>" . $percentage . "% of remaining salary</span></p>
                <p><span class='font-weight-bold'>Method: </span><span>" . ucfirst($payment_type) . "</span></p>
                <p><span class='font-weight-bold'>Category: </span><span>" . ucfirst($recipients) . "</span></p>
            ";
            if ($recipients == 'staff') {
                $msg .= "<p><span class='font-weight-bold'>school: </span><span>ucfirst($school)</span></p>";
            }
            sort($recipients_data);
            $other = [
                'payment_month' => $payment_month,
                'school' => $school,
                'recipients' => $recipients,
                'recipients_data' => $recipients_data,
                'payment_type' => $payment_type,
                'username' => $username,
                'percentage' => $percentage
            ];
            $req->send($username, 1, $msg, RequestCategory::PAY_SALARY, $other, false, 1);
            //insert into to payment exist table to avoid duplicate request for the same payment month
            $db->insert('payment_exist', ['payment_month' => $payment_month]);
            echo response(201, '<p>A request has been sent to the Director for approval</p><p>Payment would automatically take effect if the Director approves it</p>');
            break;
        case 'transfer_money':
            $source = Utility::escape(Input::get('source'));
            $destination = Utility::escape(Input::get('destination'));
            $amount = (float)Utility::escape(Input::get('amount'));
            //ensure the accounts are not the same
            if (Utility::equals($source, $destination)) {
                echo response(400, "Destination Account should be different from Source Account");
                exit();
            }
            //ensure source has enough money to make this transfer
            $acct = new Account();
            if (!$acct->canTransfer($source, $amount)) {
                echo response(400, "$source doesn't have up to &#8358;" . number_format($amount, 2) . " in account");
                exit();
            }
            $balances = $acct->transfer($source, $destination, $amount, true);
            $not = new Alert(true);
            $msg = "<p>Hi Sir, this is to inform you that the sum of <span style='font-weight:bold'>&#8358;" . number_format($amount, 2) . "</span> have been transferred from <span style='font-weight:bold'>$source</span> account to <span style='font-weight:bold'>$destination</span> account</p><p>This operation was carried out from the  <span style='font-weight:bold'>Accounting Office</span></p>";
            $not->sendToRank(1, "Money Transfer", $msg);
            echo response(200, "<p>Transfer was successful</p><p>The Director have been notified</p>", $balances);
            break;
        case 'get_school_account_balance':
            $account = Utility::escape(Input::get('account'));
            $acct = new Account();
            $acctObj = $acct->getSchoolAccount($account);
            echo response(200, '', ['balance' => $acctObj->balance]);
            break;
        case 'deposit_money':
            $account = Utility::escape(Input::get('account'));
            $amount = (float)Utility::escape(Input::get('amount'));
            $username = Utility::escape(Input::get('username'));
            $depositor = Utility::escape(Input::get('depositor'));
            $description = Utility::escape(Input::get('description'));
            $msg = "<p>Hi Sir, You approval is needed to deposit the sum of <span class='font-weight-bold'>&#8358;" . number_format($amount, 2) . "</span> to <span class='font-weight-bold'>$account</span> account</p>
            <p><span class='font-weight-bold'>Description: </span>$description</p>
            <p>This request is from the Office of the Accountant</p>
            ";
            $req = new Request();
            $req->send($username, 1, $msg, RequestCategory::DEPOSIT_CASH, ['account' => $account, 'amount' => $amount, 'description' => $description, 'depositor' => $depositor]);
            echo response(201, "<p>Operation was successful</p><p>The Director would have to approve it for it to take effect</p><p>A request has already been sent to his office</p>");
            break;
        case 'withdraw_money':
            $account = Utility::escape(Input::get('account'));
            $amount = (float)Utility::escape(Input::get('amount'));
            $username = Utility::escape(Input::get('username'));
            $recipient = Utility::escape(Input::get('recipient'));
            $description = Utility::escape(Input::get('description'));
            //ensure he has this money for withdrawal
            $acct = new Account();
            if (!$acct->canTransfer($account, $amount)) {
                echo response(400, "$account does not have up to <span class='font-weight-bold'>&#8358;" . number_format($amount, 2) . " for withdrawal");
                exit();
            }
            $msg = "<p>Hi Sir, You approval is needed to withdraw the sum of <span class='font-weight-bold'>&#8358;" . number_format($amount, 2) . "</span> from <span class='font-weight-bold'>$account</span> account</p>
            <p><span class='font-weight-bold'>Description: </span>$description</p>
            <p>This request is from the Office of the Accountant</p>
            ";
            $req = new Request();
            $req->send($username, 1, $msg, RequestCategory::WITHDRAW_CASH, ['account' => $account, 'amount' => $amount, 'description' => $description, 'recipient' => $recipient]);
            echo response(201, "<p>Operation was successful</p><p>The Director would have to approve it for it to take effect</p><p>A request has already been sent to his office</p>");
            break;
        case 'transactions':
            $from = Utility::escape(Input::get('from'));
            $to = Utility::escape(Input::get('to'));
            $category = (int) Utility::escape(Input::get('category'));
            if ($from > $to) {
                echo response(400, 'From date should not be later than To date');
                exit();
            }
            $acct = new Account();
            $transactions = $acct->getTransactions($from, $to, $category);
            echo response(200,'',$transactions);
            break;
    }
} else {
    echo response(400, 'Invalid request method');
}


function response(int $status, $message = '', array $data = [])
{
    return Utility::response($status, $message, $data);
}

function isOverpayment($salary, $paid, $amount): bool
{
    return ((($paid + $amount) - $salary) > 0.9) ? true : false;
}
