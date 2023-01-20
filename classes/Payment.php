<?php

/**
 * Payment class handles operations of payments that concerns transaction operation and the school account(accounts)
 * it handles recordings in the transaction table, accounts table
 */
class Payment
{
    private $db;
    private $schoolBalance;
    private $payer;
    private $payment_month;
    private $type;
    private $category;
    private $paySalRequery;
    public function __construct()
    {
        $this->db = DB::newConnection();
    }

    public function setPaySalariesInitial(float $balance, string $payer, int $payment_month, TransactionType $type, TransactionCategory $category)
    {
        $this->schoolBalance = $balance;
        $this->payer = $payer;
        $this->payment_month = $payment_month;
        $this->type = $type->value;
        $this->category = $category->value;
    }

    //this method requeries i.e use prepared statements
    public function paySalary(string $receiver, float $amount)
    {
        $balance = round(($this->schoolBalance - $amount), 2);
        $this->schoolBalance = $balance;
        if ($this->paySalRequery) {
            $this->db->requery([
                'trans_id' => Token::create(Config::get('transaction/token_length')),
                'payer' => $this->payer,
                'receiver' => $receiver,
                'amount' => $amount,
                'school_balance' => $balance,
                'type' => $this->type,
                'category' => $this->category,
                'payment_month_id' => $this->payment_month
            ]);
        } else {
            $this->db->insert('transaction', [
                'trans_id' => Token::create(Config::get('transaction/token_length')),
                'payer' => $this->payer,
                'receiver' => $receiver,
                'amount' => $amount,
                'school_balance' => $balance,
                'type' => $this->type,
                'category' => $this->category,
                'payment_month_id' => $this->payment_month
            ]);
            $this->paySalRequery = true;
        }
    }

    public function getSchoolBalance(): float
    {
        return $this->schoolBalance;
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function rollBack()
    {
        $this->db->rollBack();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public static function paySchoolFees(string $std_id, string $term, string $session, float $amount, TransactionType $type)
    {
        $db = DB::get_instance();
        $acct = new Account();
        $detail = $acct->getSchoolFeeDetail($std_id, $term, $session);
        $school_balance = Account::getAccountBalance($detail->sch_abbr);
        $paid = ((float) $detail->paid) + $amount;
        if (round($paid, 1) < round((float)$detail->amount, 1)) {
            $status = 1;
        } else {
            $status = 2;
        }
        try {
            $db->beginTransaction();
            $db->update('school_fee', [
                'paid' => $paid,
                'status' => $status
            ], "std_id='$std_id' and term='$term' and session = '$session'");
            $new_balance = $school_balance + $amount;
            $db->insert('transaction', [
                'trans_id' => Token::create(Config::get('transaction/token_length')),
                'payer' => $std_id,
                'receiver' => $detail->sch_abbr,
                'amount' => $amount,
                'school_balance' => $new_balance,
                'type' => $type->value,
                'category' => TransactionCategory::SCHOOL_FEES->value,
                'school_fee_id' => (int)$detail->id
            ]);
            $db->update('accounts', [
                'balance' => $new_balance
            ], "account_name='$detail->sch_abbr'");
            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            echo Utility::response(500, 'Error encountered while trying to pay School Fee');
        }
    }
    public static function payRegFees(string $std_id,TransactionType $type)
    {
        $db = DB::get_instance();
        $acct = new Account();
        $detail = $acct->getRegFeeDetail($std_id);
        $school_balance = Account::getAccountBalance($detail->sch_abbr);
        $amount = (float)$detail->reg_fee;
        try {
            $db->beginTransaction();
            $new_balance = $school_balance + $amount;
            $db->insert('transaction', [
                'trans_id' => Token::create(Config::get('transaction/token_length')),
                'payer' => $std_id,
                'receiver' => $detail->sch_abbr,
                'amount' => $amount,
                'school_balance' => $new_balance,
                'type' => $type->value,
                'category' => TransactionCategory::REGISTRATION_FEES->value,
            ]);
            $insertId = $db->getLastInsertId();
            $db->update('reg_fee', [
                'ref_id'=>$insertId,
                'status' => 2
            ], "std_id='$std_id'");
            $db->update('accounts', [
                'balance' => $new_balance
            ], "account_name='$detail->sch_abbr'");
            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            echo Utility::response(500, 'Error encountered while trying to pay School Fee');
        }
    }

    public static function getPayerOrRecipientCategory(string $payer): string
    {
        $schools = array_merge(School::getSchools(2),['HIKMAH']);
        if (in_array($payer, $schools)) {
            return 'school';
        }
        $first_letter = strtolower(substr($payer, 0, 1));
        switch ($first_letter) {
            case 'm':
                return 'management';
            case 's':
                return 'staff';
            case 'h':
                return 'student';
            default:
                return '';
        }
    }
}
