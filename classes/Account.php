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
        $sql = "select account.salary,account.no,account.bank,banks.code,salary.*,$table.title,$table.fname,$table.lname,$table.oname from salary inner join $table on salary.receiver=$table.$column inner join account on salary.receiver = account.receiver inner join banks on account.bank = banks.name where salary.payment_month = ? and account.approved=1";
        if (count($receivers)) {
            $receivers_string = implode("','", $receivers);
            $sql .= " and account.receiver in ('$receivers_string')";
        }
        $sql .= " order by account.receiver asc";

        $vals = [$payment_month];
        if (!empty($school)) {
            $sql .= " and salary.sch_abbr = ?";
            $vals[] = $school;
        }
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
        return $this->_db->select('payment_months', '*');
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
        return $db->select('payment_months', '*');
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
        $this->_db->query("select salary.id, salary.receiver, salary.paid, salary.status, salary.sch_abbr, salary.category, account.salary,$table.title,$table.fname,$table.oname,$table.lname from salary inner join account on salary.receiver = account.receiver inner join $table on salary.receiver = $table.$column where salary.payment_month=$payment_month");
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
        $this->_db->query("select transaction.*,$table.title, $table.fname, $table.oname, $table.lname from transaction inner join $table on transaction.receiver = $table.$column where transaction.payment_month_id=$payment_month");
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
        if($this->_db->row_count() > 0){
            return $this->_db->get_result();
        }
        return [];
    }
}
