<?php
enum TransactionCategory :int implements Enums{
    case SCHOOL_FEES = 1;
    case STAFF_SALARY = 2;
    case MANAGEMENT_SALARY = 3;
    case REGISTRATION_FEES = 4;
    case FORM_FEES = 5;
    case WITHDRAWAL = 6;
    case DEPOSIT = 7;
    case TRANSFER = 8;
    case REVOKE = 9;

    public function getName(): string
    {
        return str_replace('_', ' ', $this->name);
    }

    public static function getAllCases(bool $returnWithValue = false) :array{
        $cases = [];
        if($returnWithValue){
            foreach (self::cases() as $case) {
                $cases[$case->getName()] = $case->value;
            }
        }else{
            foreach (self::cases() as $case) {
                $cases[] = $case->getName();
            }
        }
       
        return $cases;
    }
}