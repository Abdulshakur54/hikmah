<?php 
enum RequestCategory: int{
    case SALARY_CONFIRMATION=1;
    case SACK_STAFF = 2;
    case DELETE_STAFF = 3;
    case EXPEL_STUDENT = 4;
    case DELETE_STUDENT = 5;
}