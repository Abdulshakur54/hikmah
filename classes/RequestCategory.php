<?php 
enum RequestCategory: int implements Enums{
    case SALARY_CONFIRMATION=1;
    case SACK_STAFF = 2;
    case DELETE_STAFF = 3;
    case EXPEL_STUDENT = 4;
    case DELETE_STUDENT = 5;
    case UPDATE_SALARY = 6;
    case PAY_SALARY = 7;
    case DEPOSIT_CASH = 8;
    case WITHDRAW_CASH = 9;
    public function getName(): string
    {
        return str_replace('_', ' ', $this->name);
    }
}