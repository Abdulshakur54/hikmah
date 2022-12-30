<?php 
enum TransactionType: int implements Enums{
    case MANUAL = 0;
    case ONLINE = 1;
    public function getName(): string
    {
        return str_replace('_', ' ', $this->name);
    }
    public static function getAllCases(): array
    {
        $cases = [];
        foreach (self::cases() as $case) {
            $cases[] = $case->getName();
        }
        return $cases;
    }
}