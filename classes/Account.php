<?php

class Account
{

    private $_db;
    private $_mgtFirstLetter = 'M';

    public function __construct()
    {
        $this->_db = DB::get_instance();
    }

    public function getAccountDetails($receiver)
    {
        $firstLetter = strtoupper(substr($receiver, 0, 1));
        if ($firstLetter === $this->_mgtFirstLetter) {
            //this shows that $reciever is a management member
            $table = Config::get('users/table_name0');
            $column = Config::get('users/username_column0');
        } else {
            //this show $receiver is a staff
            $table = Config::get('users/table_name1');
            $column = Config::get('users/username_column1');
        }

        $this->_db->query('select account.*,' . $table . '.fname,' . $table . '.lname,' . $table . '.oname from account inner join ' . $table . ' on account.receiver = ' . $table . '.' . $column . ' where account.receiver=? and account.approved=1', [$receiver]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->one_result();
        }
    }
    public function getRecipientsAccounts($recipient)
    {

        if ($recipient === 'management') {
            //this shows that $recipient is a management member
            $table = Config::get('users/table_name0');
            $column = Config::get('users/username_column0');
        } else {
            //this show $recipient is a staff
            $table = Config::get('users/table_name1');
            $column = Config::get('users/username_column1');
        }

        $this->_db->query('select account.*,' . $table . '.fname,' . $table . '.lname,' . $table . '.oname,' . $table . '.sch_abbr,' . $table . '.title from account inner join ' . $table . ' on account.receiver = ' . $table . '.' . $column . ' where account.approved=1');
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
    }
    /**
     * Summary of getPaymentDetails
     * @param int $payment_month
     * @param string $recipient
     * @param string $school
     * @param array $receivers refers to array of staff members or management members , this would be used to filter the output 
     * @return array
     */
    public function getPaymentDetails(int $payment_month, string $recipient, string $school = '', array $receivers = []): array
    {

        if ($recipient === 'management') {
            //this shows that $recipient is a management member
            $table = Config::get('users/table_name0');
            $column = Config::get('users/username_column0');
        } else {
            //this show $recipient is a staff
            $table = Config::get('users/table_name1');
            $column = Config::get('users/username_column1');
        }
        $sql = "select account.salary,account.no,account.bank,banks.code,salary.*,$table.title,$table.fname,$table.lname,$table.oname from salary inner join $table on salary.receiver=$table.$column inner join account on salary.receiver = account.receiver inner join banks on account.bank = banks.name where salary.payment_month = ? and account.approved=1 and salary.cancelled=0";

        if (count($receivers)) {
            $receivers_string = implode("','", $receivers);
            $sql .= " and account.receiver in ('$receivers_string')";
        }
        $vals = [$payment_month];
        if (!empty($school)) {
            $sql .= " and salary.sch_abbr = ?";
            $vals[] = $school;
        }
        $sql .= " order by account.receiver asc";
        $this->_db->query($sql, $vals);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }
    //this function uses prepared statement when the $receiver is an array of receivers
    public function updateSalary($receiver, $salary = null)
    {
        if (!is_array($receiver)) {
            $this->_db->query('update account set salary = ?, approved = ? where receiver = ?', [$salary, false, $receiver]);
        } else {
            $x = 1;
            foreach ($receiver as $rec => $sal) {
                if ($x === 1) {
                    $this->_db->query('update account set salary = ?, approved = ? where receiver = ?', [$sal, false, $rec]);
                    $x++;
                } else {
                    //using requery
                    $this->_db->requery([$sal, false, $rec]);
                }
            }
        }
    }

    public function getSalariesDetails($category)
    {
        switch ($category) {
            case 1:
                $table_name = Config::get('users/table_name0');
                $col_name = Config::get('users/username_column0');
                break;
            case 2:
                $table_name = Config::get('users/table_name1');
                $col_name = Config::get('users/username_column1');
        }
        $this->_db->query('select ' . $table_name . '.fname, ' . $table_name . '.lname, ' . $table_name . '.oname, account.* from ' . $table_name . ' inner join account on ' . $table_name . '.' . $col_name . ' = account.receiver order by account.receiver asc');
        return $this->_db->get_result();
    }

    public function getPaymentMonths(): array
    {
        return $this->_db->select('payment_months', '*', '', 'id', 'desc');
    }

    public function getReceiversData(array $receivers, string $category): array
    {
        if ($category == 'staff') {
            $table = 'staff';
            $column = 'staff_id';
        } else {
            $table = 'management';
            $column = 'mgt_id';
        }
        $receivers_string = implode("','", $receivers);
        return $this->_db->select($table, 'title,fname,oname,lname', "$column in('$receivers_string')", $column);
    }

    public static function getSalaryMonth(int $salaryMonth): string
    {
        $db = DB::get_instance();
        $salary_obj = $db->get('payment_months', 'month,year', "id=$salaryMonth");
        return $salary_obj->month . ', ' . $salary_obj->year;
    }

    public static function getSalaryMonths(): array
    {
        $db = DB::get_instance();
        return $db->select('payment_months', '*', '', 'id', 'desc');
    }

    public function getSchoolAccounts(): array
    {
        return $this->_db->select('accounts', '*');
    }
    public function getSchoolAccount(string $account): object
    {
        return $this->_db->get('accounts', '*', "account_name='$account'");
    }

    public function getMonthlySalaryDetails(int $payment_month, string $recipients): array
    {
        if ($recipients === 'management') {
            //this shows that $recipient is a management member
            $table = Config::get('users/table_name0');
            $column = Config::get('users/username_column0');
        } else {
            //this show $recipient is a staff
            $table = Config::get('users/table_name1');
            $column = Config::get('users/username_column1');
        }
        $sql = "select salary.id, salary.receiver, salary.paid, salary.status, salary.sch_abbr, salary.category, account.salary,$table.title,$table.fname,$table.oname,$table.lname, $table.sch_abbr from salary inner join account on salary.receiver = account.receiver inner join $table on salary.receiver = $table.$column where salary.payment_month=$payment_month and salary.cancelled=0 order by $table.sch_abbr";


        $this->_db->query($sql);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }
    public function getMonthlySalaryTransactionDetails(int $payment_month, string $recipients): array
    {
        if ($recipients === 'management') {
            //this shows that $recipient is a management member
            $table = Config::get('users/table_name0');
            $column = Config::get('users/username_column0');
        } else {
            //this show $recipient is a staff
            $table = Config::get('users/table_name1');
            $column = Config::get('users/username_column1');
        }
        $this->_db->query("select transaction.*,$table.title, $table.fname, $table.oname, $table.lname, from transaction inner join $table on transaction.receiver = $table.$column where transaction.payment_month_id=$payment_month");
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }

    public function canTransfer(string $account, float $amount): bool
    {
        return ($this->getSchoolAccount($account)->balance > $amount) ? true : false;
    }

    public function transfer(string $source, string $destination, float $amount, bool $returnBalance = false)
    {
        $accounts = $this->_db->select('accounts', '*', "account_name in('$source','$destination')");
        foreach ($accounts as $acct) {
            if ($acct->account_name == $source) {
                $newSourceBalance = (float)$acct->balance - $amount;
            } else {
                $newDestinationBalance = (float)$acct->balance + $amount;
            }
        }
        try {
            $this->_db->beginTransaction();
            $this->_db->update('accounts', ['balance' => $newSourceBalance], "account_name='$source'");
            $this->_db->update('accounts', ['balance' => $newDestinationBalance], "account_name='$destination'");
            $this->_db->insert('transaction', ['trans_id' => Token::create(Config::get('transaction/token_length')), 'payer' => $source, 'receiver' => $destination, 'amount' => $amount, 'type' => 0, 'category' => 8, 'description' => "<p><span class='font-weight-bold'>$source bal: </span>&#8358;" . number_format($newSourceBalance, 2) . "</p><p><span class='font-weight-bold'>$destination bal: </span>&#8358;" . number_format($newDestinationBalance, 2) . "</p>"]);
            $this->_db->commit();
            if ($returnBalance) {
                return ['source' => $newSourceBalance, 'destination' => $newDestinationBalance];
            }
        } catch (PDOException $e) {
            $this->_db->rollBack();
            echo response(500, 'An Error has prevented us from executing transfer operation');
            exit();
        }
    }
    public function deposit(string $account, float $amount, string $description, string $depositor): bool
    {
        $balance = (float)$this->getSchoolAccount($account)->balance;
        $newBalance = $balance + $amount;
        try {
            $this->_db->beginTransaction();
            $this->_db->update('accounts', ['balance' => $newBalance], "account_name='$account'");
            $this->_db->insert('transaction', ['trans_id' => Token::create(Config::get('transaction/token_length')), 'payer' => $depositor, 'receiver' => $account, 'amount' => $amount, 'school_balance' => $newBalance, 'type' => 0, 'category' => 7, 'description' => $description]);
            $this->_db->commit();
            return true;
        } catch (PDOException $e) {
            $this->_db->rollBack();
            return false;
        }
    }
    public function withdraw(string $account, float $amount, string $description, string $recipient): bool
    {
        $balance = (float)$this->getSchoolAccount($account)->balance;
        $newBalance = $balance - $amount;
        try {
            $this->_db->beginTransaction();
            $this->_db->update('accounts', ['balance' => $newBalance], "account_name='$account'");
            $this->_db->insert('transaction', ['trans_id' => Token::create(Config::get('transaction/token_length')), 'payer' => $account, 'receiver' => $recipient, 'amount' => $amount, 'school_balance' => $newBalance, 'type' => 0, 'category' => 6, 'description' => $description]);
            $this->_db->commit();
            return true;
        } catch (PDOException $e) {
            $this->_db->rollBack();
            return false;
        }
    }

    public function getTransactions(string $from, string $to, int $category): array
    {
        if (!empty($category)) {
            $this->_db->query("select transaction.*,transaction_type.type as trans_type,transaction_category.category as trans_cat from transaction inner join transaction_category on transaction.category = transaction_category.id inner join transaction_type on transaction.type = transaction_type.id where transaction.category=$category and transaction.created >= '$from' and transaction.created <= '$to'");
        } else {
            $this->_db->query("select transaction.*,transaction_type.type as trans_type,transaction_category.category as trans_cat from transaction inner join transaction_category on transaction.category = transaction_category.id inner join transaction_type on transaction.type = transaction_type.id where transaction.created >= '$from' and transaction.created <= '$to'");
        }
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }

    public function getSchoolFeeDetail(string $std_id, string $term, string $session): object
    {
        $this->_db->query('select school_fee.id, school_fee.amount,school_fee.paid, school_fee.status,student.fname,student.oname,student.lname,student.sch_abbr from school_fee inner join student on school_fee.std_id = student.std_id where school_fee.term = ? and school_fee.session=? and school_fee.std_id = ?', [$term, $session, $std_id]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->one_result();
        } else {
            return json_decode('');
        }
    }
    public function getRegFeeDetail(string $std_id): object
    {
        $this->_db->query('select reg_fee.id, fee.reg_fee,reg_fee.status,student.fname,student.oname,student.lname,student.sch_abbr from reg_fee inner join student on reg_fee.std_id = student.std_id  inner join fee on fee.level = student.level and fee.sch_abbr = student.sch_abbr where student.std_id = ?', [$std_id]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->one_result();
        } else {
            return json_decode('');
        }
    }
    public static function getAccountBalance(string $sch_abbr): float
    {
        $db = DB::get_instance();
        return (float) $db->get('accounts', 'balance', "account_name='$sch_abbr'")->balance;
    }

    public static function getSignature(): string
    {
        $db = DB::get_instance();
        $signature = $db->get('sing_val', 'accountant_signature', "id=1");
        if (!empty($signature->accountant_signature)) {
            return $signature->accountant_signature;
        }
        return '';
    }
    public static function getSchoolFeeStudents(string $sch_abbr, string $session, string $term): array
    {
        $db = DB::get_instance();
        $sql = "select student.std_id, student.fname,student.oname,student.lname from student inner join school_fee on student.std_id = school_fee.std_id where school_fee.session='$session' and school_fee.term = '$term' and student.sch_abbr = '$sch_abbr' and school_fee.cancelled = 0";

        $db->query($sql);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }
    public static function getRegFeeStudents(): array
    {
        $db = DB::get_instance();
        $sql = "select student.std_id, student.fname,student.oname,student.lname from student inner join reg_fee on student.std_id = reg_fee.std_id and reg_fee.cancelled = 0";

        $db->query($sql);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }

    public function getMonthlySalaryPayables(int $payment_month = 0, string $sch_abbr = ''): array
    {
        $sql_staff = "select staff.title,staff.fname,staff.oname,staff.lname,staff.sch_abbr,salary.paid,salary.receiver,salary.status,salary.cancelled,salary.id,account.salary,payment_months.month,payment_months.year from staff inner join salary on staff.staff_id = salary.receiver inner join account on account.receiver = salary.receiver inner join payment_months on payment_months.id = salary.payment_month where salary.status in(0,1) and account.approved=1";
        if ($payment_month != 0) {
            $sql_staff .= " and salary.payment_month = $payment_month";
        }
        if (!empty($sch_abbr)) {
            $sql_staff .= " and staff.sch_abbr = '$sch_abbr'";
        }
        $sql_staff .= " order by salary.payment_month, staff.sch_abbr";
        $sql_mgt = "select management.title,management.fname,management.oname,management.lname,management.sch_abbr,salary.paid,salary.receiver,salary.status,salary.cancelled,salary.id,account.salary,payment_months.month,payment_months.year from management inner join salary on management.mgt_id = salary.receiver inner join account on account.receiver = salary.receiver inner join payment_months on payment_months.id = salary.payment_month where salary.status in(0,1) and account.approved = 1";
        if ($payment_month != 0) {
            $sql_mgt .= " and salary.payment_month = $payment_month";
        }
        if (!empty($sch_abbr)) {
            $sql_mgt .= " and management.sch_abbr = '$sch_abbr'";
        }
        $sql_mgt .= " order by salary.payment_month, management.sch_abbr";
        $this->_db->query($sql_staff);
        $res1 = $this->_db->get_result();
        $res1 = (empty($res1)) ? [] : $res1;
        $count1 = $this->_db->row_count();
        $this->_db->query($sql_mgt);
        $res2 = $this->_db->get_result();
        $res2 = (empty($res2)) ? [] : $res2;
        $count2 = $this->_db->row_count();
        if ($count1 > 0 || $count2 > 0) {
            return array_merge($res1, $res2);
        } else {
            return [];
        }
    }

    public function getSchoolFeeReceivables(string $sch_abbr = '', string $session = '', string $term = ''): array
    {
        $sql = "select student.fname,student.std_id,student.oname,student.lname,student.sch_abbr,school_fee.amount,school_fee.paid,school_fee.term,school_fee.session,school_fee.cancelled,school_fee.id,school_fee.status from student inner join school_fee on student.std_id = school_fee.std_id where school_fee.status in(0,1)";
        if (!empty($sch_abbr)) {
            $sql .= " and student.sch_abbr = '$sch_abbr'";
        }
        if (!empty($session)) {
            $sql .= " and school_fee.session = '$session'";
        }
        if (!empty($term)) {
            $sql .= " and school_fee.term = '$term'";
        }
        $sql .= " order by student.sch_abbr, school_fee.session, school_fee.term";
        $this->_db->query($sql);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }
    public function getRegFeeReceivables(string $sch_abbr = '', string $session = '', $level = ''): array
    {
        $sql = "select student.fname,student.std_id,student.oname,student.lname,student.sch_abbr,fee.reg_fee,fee.level,reg_fee.cancelled, reg_fee.status,reg_fee.id,reg_fee.session, level_name.name from student inner join reg_fee on student.std_id = reg_fee.std_id inner join fee on student.sch_abbr = fee.sch_abbr  AND student.level = fee.level inner join level_name on level_name.level = fee.level and level_name.sch_abbr = fee.sch_abbr where reg_fee.status in(0,1)";
        if (!empty($sch_abbr)) {
            $sql .= " and student.sch_abbr = '$sch_abbr'";
        }
        if (!empty($session)) {
            $sql .= " and fee.session = '$session'";
        }
        if (!empty($level)) {
            $sql .= " and fee.level = '$level'";
        }
        $sql .= " order by student.sch_abbr, fee.session, fee.level";
        $this->_db->query($sql);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        }
        return [];
    }

    public static function changeSalaryDebtStatus(int $sal_id, string $operation): object
    {
        $db = DB::get_instance();
        if ($operation == 'cancel') {
            $newCancelValue = 1;
        } else {
            $newCancelValue = 0;
        }
        $db->update('salary', ['cancelled' => $newCancelValue], "id=$sal_id");
        return $db->get('salary', "status,cancelled", "id=$sal_id");
    }
    public static function changeSchoolFeeDebtStatus(int $fee_id, string $operation): object
    {
        $db = DB::get_instance();
        if ($operation == 'cancel') {
            $newCancelValue = 1;
        } else {
            $newCancelValue = 0;
        }
        $db->update('school_fee', ['cancelled' => $newCancelValue], "id=$fee_id");
        return $db->get('school_fee', "status,cancelled", "id=$fee_id");
    }
    public static function changeRegFeeDebtStatus(int $fee_id, string $operation): object
    {
        $db = DB::get_instance();
        if ($operation == 'cancel') {
            $newCancelValue = 1;
        } else {
            $newCancelValue = 0;
        }
        $db->update('reg_fee', ['cancelled' => $newCancelValue], "id=$fee_id");
        return $db->get('reg_fee', "status,cancelled", "id=$fee_id");
    }
    public static function getAccounts(): array
    {
        return array_merge(School::getSchools(2), [Config::get('hikmah/management_account')]);
    }

    public function getAllAccountsStatus()
    {
        $accounts = Account::getAccounts();
        $management_account = Config::get('hikmah/management_account');
        $result = [];
        foreach ($accounts as $account) {
            $result[$account] = [];
            $result[$account]['balance'] = Account::getAccountBalance($account);
        }
        $start = false;
        foreach ($accounts as $account) { //schools are used because each school has a corresponding account for paying corresponding staffs
            if ($account == $management_account) {
                $result[$management_account]['payables'] = $this->getAccountPayables('M', $account); //management account did not use requery because it is an account
            } else {

                if ($start) {
                    $result[$account]['payables'] = $this->getAccountPayables('S', $account, true);
                } else {

                    $result[$account]['payables'] = $this->getAccountPayables('S', $account);
                    $start = true;
                }
            }
        }

        $start = false;
        $utils = new Utils();
        $terms = $utils->getCurrentTerms();
        foreach ($accounts as $account) { //schools are used because each school has a corresponding account for paying corresponding staffs
            if ($account == $management_account) {
                $result[$management_account]['school_fees'] = 0; //management account did not use requery because it is an account
            } else {
                $term = $terms[$account];

                if ($start) {
                    $result[$account]['school_fees'] = $this->getAccountSchoolFeeReceivables($account, $term, true);
                } else {

                    $result[$account]['school_fees'] = $this->getAccountSchoolFeeReceivables($account, $term);
                    $start = true;
                }
            }
        }
        $start = false;
        foreach ($accounts as $account) { //schools are used because each school has a corresponding account for paying corresponding staffs
            if ($account == $management_account) {
                $result[$management_account]['reg_fees'] = 0; //management account did not use requery because it is an account
            } else {

                if ($start) {
                    $result[$account]['reg_fees'] = $this->getAccountRegFeeReceivables($account, true);
                } else {

                    $result[$account]['reg_fees'] = $this->getAccountRegFeeReceivables($account);
                    $start = true;
                }
            }
        }
        return $result;
    }

    public function getAccountPayables(string $category, string $account, bool $requery = false): float
    {
        if ($category == 'S') {
            if ($requery) {
                $this->_db->requery([$account]);
                if ($this->_db->row_count() > 0) {
                    return (float)$this->_db->one_result()->payable;
                } else {
                    return 0;
                }
            } else {

                $this->_db->query("select SUM(account.salary - salary.paid) AS payable from salary inner join account on salary.receiver = account.receiver inner join staff on account.receiver = staff.staff_id where salary.status in(0,1) and salary.cancelled=0 and account.approved=1 and staff.sch_abbr=?", [$account]);
                if ($this->_db->row_count() > 0) {
                    return (float)$this->_db->one_result()->payable;
                } else {
                    return 0;
                }
            }
        } else {
            $this->_db->query("select SUM(account.salary - salary.paid) AS payable from salary inner join account on salary.receiver = account.receiver inner join management on salary.receiver = management.mgt_id where salary.status in(0,1) and salary.cancelled=0 and account.approved=1");
            if ($this->_db->row_count() > 0) {
                return (float)$this->_db->one_result()->payable;
            } else {
                return 0;
            }
        }
    }
    public function getAccountPayablesDetails(string $account): array
    {
        if (!Utility::equals($account, 'all')) {
            $management_account = Config::get('hikmah/management_account');
            if (Utility::equals($account, $management_account)) {
                $payables = $this->getManagementAccountPayableDetails();
            } else {
                $payables = $this->getStaffAccountPayableDetails($account);
            }
        } else {
            $payables = [];
            $payables = array_merge($payables, $this->getStaffAccountPayableDetails($account));
            $payables = array_merge($payables, $this->getManagementAccountPayableDetails());
        }


        return $payables;
    }

    public function getStaffAccountPayableDetails(string $account): array
    {
        $sql = "select staff.staff_id as user_id, staff.title, staff.fname, staff.oname, staff.lname, staff.sch_abbr as account, payment_months.month, payment_months.year, (account.salary - salary.paid) AS payable from salary inner join account on salary.receiver = account.receiver inner join staff on account.receiver = staff.staff_id inner join payment_months on salary.payment_month = payment_months.id where salary.status in(0,1) and salary.cancelled=0 and account.approved=1";
        if (!Utility::equals($account, 'all')) {
            $sql .= " and staff.sch_abbr='$account'";
        }
        $sql .= " order by payment_months.id";
        $this->_db->query($sql);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        } else {
            return [];
        }
    }
    public function getManagementAccountPayableDetails(): array
    {
        $management_account = Config::get('hikmah/management_account');
        $this->_db->query("select management.mgt_id as user_id, management.title, management.fname, management.oname, management.lname, '$management_account' as account, payment_months.month, payment_months.year, (account.salary - salary.paid) AS payable from salary inner join account on salary.receiver = account.receiver inner join management on salary.receiver = management.mgt_id inner join payment_months on salary.payment_month = payment_months.id where salary.status in(0,1) and salary.cancelled=0 and account.approved=1 order by payment_months.id");
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        } else {
            return [];
        }
    }

    public function getAccountSchoolFeeReceivables(string $sch_abbr, string $term, bool $requery = false): float
    {
        $term_string = '';
        switch ($term) {
            case 'ft':
                $term_string =  "'ft'";
                break;
            case 'st':
                $term_string =  "'ft','st'";
                break;
            case 'tt':
                $term_string =  "'ft','st','tt'";
                break;
        }
        if ($requery) {
            $this->_db->requery([$sch_abbr]);
            if ($this->_db->row_count() > 0) {
                return (float)$this->_db->one_result()->school_fees;
            } else {
                return 0;
            }
        } else {
            $this->_db->query("select sum(school_fee.amount - school_fee.paid) as school_fees from school_fee inner join student on school_fee.std_id = student.std_id where school_fee.cancelled = 0 and school_fee.status in(0,1) and school_fee.term in($term_string) and student.sch_abbr=?", [$sch_abbr]);
            if ($this->_db->row_count() > 0) {
                return (float)$this->_db->one_result()->school_fees;
            } else {
                return 0;
            }
        }
    }
    public function getAccountSchoolFeeReceivablesDetails(string $sch_abbr = 'ALL'): array
    {
        $utils = new Utils();
        if (Utility::equals($sch_abbr, 'all')) {
            $schools = School::getSchools(2);
            $res = [];
            foreach ($schools as $sch) {
                $term = $utils->getCurrentTerm($sch);
                $term_string = $this->getTermString($term);
                $this->_db->query("select student.std_id,student.fname,student.oname,student.lname,student.sch_abbr, (school_fee.amount - school_fee.paid) as school_fee, school_fee.term,school_fee.session from school_fee inner join student on school_fee.std_id = student.std_id where school_fee.cancelled = 0 and school_fee.status in(0,1) and school_fee.term in($term_string) and student.sch_abbr='$sch'");
                if ($this->_db->row_count() > 0) {
                    $res = array_merge($res, $this->_db->get_result());
                }
            }
            return $res;
        } else {

            $term = $utils->getCurrentTerm($sch_abbr);
            $term_string = $this->getTermString($term);
            $this->_db->query("select student.std_id,student.fname,student.oname,student.lname, student.sch_abbr,(school_fee.amount - school_fee.paid) as school_fee, school_fee.term,school_fee.session from school_fee inner join student on school_fee.std_id = student.std_id where school_fee.cancelled = 0 and school_fee.status in(0,1) and school_fee.term in($term_string) and student.sch_abbr='$sch_abbr'");
            if ($this->_db->row_count() > 0) {
                return $this->_db->get_result();
            } else {
                return [];
            }
        }
    }

    private function getTermString(string $term): string
    {
        switch ($term) {
            case 'ft':
                return "'ft'";
            case 'st':
                return "'ft','st'";
            case 'tt':
                return "'ft','st','tt'";
            default:
                return '';
        }
    }

    public function getAccountRegFeeReceivables(string $sch_abbr = '', bool $requery = false): float
    {

        if ($requery) {
            $this->_db->requery([$sch_abbr]);
            if ($this->_db->row_count() > 0) {
                return (float)$this->_db->one_result()->reg_fees;
            } else {
                return 0;
            }
        } else {
            $this->_db->query("select sum(fee.reg_fee) as reg_fees from reg_fee inner join student on reg_fee.std_id = student.std_id inner join fee on student.sch_abbr = fee.sch_abbr and student.level = fee.level where reg_fee.cancelled = 0 and reg_fee.status = 0 and student.sch_abbr=?", [$sch_abbr]);
            if ($this->_db->row_count() > 0) {
                return (float)$this->_db->one_result()->reg_fees;
            } else {
                return 0;
            }
        }
    }
    public function getAccountRegFeeReceivablesDetails(string $sch_abbr = 'ALL'): array
    {
        $sql = "select student.std_id,student.fname,student.oname,student.lname,student.sch_abbr, reg_fee.session,fee.reg_fee from reg_fee inner join student on reg_fee.std_id = student.std_id inner join fee on student.sch_abbr = fee.sch_abbr and student.level = fee.level where reg_fee.cancelled = 0 and reg_fee.status = 0";
        if (!Utility::equals($sch_abbr, 'all')) {
            $sql .= " and student.sch_abbr='$sch_abbr'";
        }
        $this->_db->query($sql);
        if ($this->_db->row_count() > 0) {
            return $this->_db->get_result();
        } else {
            return [];
        }
    }
}
