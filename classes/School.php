<?php

class School
{

    private $_db;

    function __construct()
    {
        $this->_db = DB::get_instance();
    }


    public static function getSchools($type = 1)
    {
        switch ($type) {
            case 1:
                return [
                    'Hikmah College Katako' => 'HCK',
                    'Hikmah College Bauchi Road' => 'HCB',
                    'Hikmah International School' => 'HIS',
                    'Hikmah Academy' => 'HA',
                    'Hikmah Madrasah' => 'HM',
                    'Hikmah Creche Islamiyah' => 'HCI',
                    'Hikmah E-Madrasah' => 'H E-M',
                    'Hikmah College Madrasah' => 'HCM'
                ];

            case 2:
                return ['HCK', 'HCB', 'HIS', 'HA', 'HM', 'HCI', 'H E-M', 'HCM'];

            case 3:
                return ['Hikmah College Katako', 'Hikmah College Bauchi Road', 'Hikmah International School', 'Hikmah Academy', 'Hikmah Madrasah', 'Hikmah Creche Islamiyah', 'Hikmah E-Madrasah', 'Hikmah College Madrasah'];
        }
    }

    public static function getIslamiyahSchools($type = 1)
    {
        switch ($type) {
            case 1:
                return [
                    'Hikmah Madrasah' => 'HM',
                    'Hikmah Creche Islamiyah' => 'HCI',
                    'Hikmah E-Madrasah' => 'H E-M',
                    'Hikmah College Madrasah' => 'HCM'
                ];

            case 2:
                return ['HM', 'HCI', 'H E-M', 'HCM'];

            case 3:
                return ['Hikmah Madrasah', 'Hikmah Creche Islamiyah', 'Hikmah E-Madrasah', 'Hikmah College Madrasah'];
        }
    }

    public static function getConvectionalSchools($type = 1)
    {
        switch ($type) {
            case 1:
                return [
                    'Hikmah College Katako' => 'HCK',
                    'Hikmah College Bauchi Road' => 'HCB',
                    'Hikmah International School' => 'HIS',
                    'Hikmah Academy' => 'HA'
                ];

            case 2:
                return ['HCK', 'HCB', 'HIS', 'HA'];

            case 3:
                return ['Hikmah College Katako', 'Hikmah College Bauchi Road', 'Hikmah International School', 'Hikmah Academy'];
        }
    }

    //this returns all the level name in a school
    public static function getLevels($sch_abbr)
    {
        $abbr = strtoupper($sch_abbr);
        switch ($abbr) {
            case 'HIS':
            case 'HA':
                return [
                    'Preparatory' => 1,
                    'Nursery 1' => 2,
                    'Nursery 2' => 3,
                    'Pre-Basic' => 4,
                    'Basic 1' => 5,
                    'Basic 2' => 6,
                    'Basic 3' => 7,
                    'Basic 4' => 8,
                    'Basic 5' => 9,
                ];
            case 'HCK':
            case 'HCB':
                return [
                    'J.S.S 1' => 1,
                    'J.S.S 2' => 2,
                    'J.S.S 3' => 3,
                    'S.S 1' => 4,
                    'S.S 2' => 5,
                    'S.S 3' => 6
                ];
            case 'HCI':
            case 'HM':
                return [
                    'KG' => 1,
                    'Creche 1' => 2,
                    'Creche 2' => 3,
                    'Primary 1' => 4,
                    'Primary 2' => 5,
                    'Primary 3' => 6,
                    'Primary 4' => 7,
                    'Primary 5' => 8
                ];
            case 'H E-M':
            case 'HCM':
                return [
                    'J.S.S 1' => 1,
                    'J.S.S 2' => 2,
                    'J.S.S 3' => 3
                ];
        }
    }


    //this function returns some basic details for all class in a school
    public static function getClassDetail($sch_abbr)
    {
        $db = DB::get_instance();
        $db->query('select class.*, staff.title, staff.fname, staff.lname, staff.oname from class left join staff on class.teacher_id = staff.staff_id where class.sch_abbr=?', [$sch_abbr]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        } else {
            return [];
        }
    }


    //this return the level name of any  level
    public static function getLevelName($sch_abbr, $level)
    {
        $abbr = strtoupper($sch_abbr);
        switch ($abbr) {
            case 'HIS':
            case 'HA':
                switch ($level) {
                    case 1:
                        return 'Preparatory';
                    case 2:
                        return 'Nursery 1';
                    case 3:
                        return 'Nursery 2';
                    case 4:
                        return 'Pre-Basic';
                    case 5:
                        return 'Basic 1';
                    case 6:
                        return 'Basic 2';
                    case 7:
                        return 'Basic 3';
                    case 8:
                        return 'Basic 4';
                    case 9:
                        return 'Basic 5';
                }

            case 'HCK':
            case 'HCB':
                switch ($level) {
                    case 1:
                        return 'J.S.S 1';
                    case 2:
                        return 'J.S.S 2';
                    case 3:
                        return 'J.S.S 3';
                    case 4:
                        return 'S.S 1';
                    case 5:
                        return 'S.S 2';
                    case 6:
                        return 'S.S 3';
                }
            case 'HCI':
            case 'HM':
                switch ($level) {
                    case 1:
                        return 'KG';
                    case 2:
                        return 'Creche 1';
                    case 3:
                        return 'Creche 2';
                    case 4:
                        return 'Primary 1';
                    case 5:
                        return 'Primary 2';
                    case 6:
                        return 'Primary 3';
                    case 7:
                        return 'Primary 4';
                    case 8:
                        return 'Primary 5';
                }
            case 'H E-M':
            case 'HCM':
                switch ($level) {
                    case 1:
                        return 'J.S.S 1';
                    case 2:
                        return 'J.S.S 2';
                    case 3:
                        return 'J.S.S 3';
                }
        }
    }

    //this returns an abbreviation of the name of any level in a school, no dots are removed from returned names
    public static function getLevName($sch_abbr, $level)
    {
        $abbr = strtoupper($sch_abbr);
        switch ($abbr) {
            case 'HIS':
            case 'HA':
                switch ($level) {
                    case 1:
                        return 'Ppt';
                    case 2:
                        return 'Nry 1';
                    case 3:
                        return 'Nry 2';
                    case 4:
                        return 'Pre-Bsc';
                    case 5:
                        return 'Bsc 1';
                    case 6:
                        return 'Bsc 2';
                    case 7:
                        return 'Bsc 3';
                    case 8:
                        return 'Bsc 4';
                    case 9:
                        return 'Bsc 5';
                }

            case 'HCK':
            case 'HCB':
                switch ($level) {
                    case 1:
                        return 'JSS 1';
                    case 2:
                        return 'JSS 2';
                    case 3:
                        return 'JSS 3';
                    case 4:
                        return 'SS 1';
                    case 5:
                        return 'SS 2';
                    case 6:
                        return 'SS 3';
                }
            case 'HCI':
            case 'HM':
                switch ($level) {
                    case 1:
                        return 'KG';
                    case 2:
                        return 'Cre 1';
                    case 3:
                        return 'Cre 2';
                    case 4:
                        return 'Pry 1';
                    case 5:
                        return 'Pry 2';
                    case 6:
                        return 'Pry 3';
                    case 7:
                        return 'Pry 4';
                    case 8:
                        return 'Pry 5';
                }
            case 'H E-M':
            case 'HCM':
                switch ($level) {
                    case 1:
                        return 'JSS 1';
                    case 2:
                        return 'JSS 2';
                    case 3:
                        return 'JSS 3';
                }
        }
    }

    public static function getFullName($sch_abbr)
    {
        $sch_abbr = strtoupper($sch_abbr);
        switch ($sch_abbr) {
            case 'HCK':
                return 'Hikmah College Katatko';
            case 'HCB':
                return 'Hikmah College Bauchi Road';
            case 'HIS':
                return 'Hikmah International School';
            case 'HA':
                return 'Hikmah Academy';
            case 'HM':
                return 'Hikmah Madrasah';
            case 'HCI':
                return 'Hikmah Creche Islamiyah';
            case 'H E-M':
                return 'Hikmah E-Madrasah';
            case 'HCM':
                return 'Hikmah College Madrasah';
        }
    }

    public function getFormFeeDetails()
    {
        if ($this->_db->query('select id,sch_abbr,form_fee from school')) {
            return $this->_db->get_result();
        }
    }

    public static function  get_std_prefix(string $sch_abbr): string
    {
        $sch_abbr = strtoupper($sch_abbr);
        switch ($sch_abbr) {
            case 'HCK':
            case 'HCB':
            case 'HIS':
            case 'HCM':
                return 'Student';
            case 'HA':
            case 'HM':
            case 'HCI':
            case 'H E-M':
                return 'Pupil';
            default:
                return '';
        }
    }

    public static function get_attachments(string $sch_abbr, $level): array
    {
        $db = DB::get_instance();
        return $db->select('attachment', 'name,attachment', "sch_abbr in('$sch_abbr','ALL') AND level in('$level','ALL')");
    }
}
