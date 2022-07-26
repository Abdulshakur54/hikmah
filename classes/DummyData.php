<?php
class DummyData
{
    public static function insert(string $table_name, array $column_definition, $no_of_times=1)
    {
        $db = DB::get_instance();
        $table_columns = array_keys($column_definition);
        $columns_datatype = array_values($column_definition);
        $data= self::generateData($columns_datatype);
        print_r($data);
    }

    public static function generateData(array $datatypes): array
    {
        $alphabets = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $data = [];
        foreach ($datatypes as $datatype) {
            $len = $datatype[1];
            switch ($datatype[0]) {
                case 'string':
                    $returnVal = '';
                    for ($i = 0; $i < $len; $i++) {
                        $returnVal .= substr($alphabets, rand(0, 51), 1);
                    }
                    $data[] = $returnVal;
                    break;
                case 'int':
                    $returnVal = '';
                    for ($i = 0; $i < $len; $i++) {
                        $returnVal .= rand(0, 9);
                    }
                    $data[] = (int) $returnVal;
                    break;
            }
        }
        return $data;
    }
}
