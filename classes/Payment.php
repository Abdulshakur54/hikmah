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
}
