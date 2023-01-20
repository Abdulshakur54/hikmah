<?php
class Fee
{
    private $_db;
    function __construct()
    {
        $this->_db = DB::get_instance();
    }

    public function initiateFees(string $session, string $sch_abbr)
    {

        try {
            $this->_db->beginTransaction();
            $levels = School::getLevels($sch_abbr);

            foreach ($levels as $level) {
                $start = false;
                $std_ids = $this->_db->select('student', 'std_id', "level=$level");
                $fees = $this->_db->get('fee', 'ft_fee,st_fee,tt_fee', "level=$level and session = '$session'");
                foreach ($std_ids as $std_id) {
                    if ($start) {
                        $this->_db->requery(['std_id' => $std_id->std_id, 'term' => 'ft', 'session' => $session, 'amount' => $fees->ft_fee]);
                        $this->_db->requery(['std_id' => $std_id->std_id, 'term' => 'st', 'session' => $session, 'amount' => $fees->st_fee]);
                        $this->_db->requery(['std_id' => $std_id->std_id, 'term' => 'tt', 'session' => $session, 'amount' => $fees->tt_fee]);
                    } else {
                        $this->_db->insert('school_fee', ['std_id' => $std_id->std_id, 'term' => 'ft', 'session' => $session,'amount'=>$fees->ft_fee]);
                        $this->_db->requery(['std_id' => $std_id->std_id, 'term' => 'st', 'session' => $session, 'amount' => $fees->st_fee]);
                        $this->_db->requery(['std_id' => $std_id->std_id, 'term' => 'tt', 'session' => $session, 'amount' => $fees->tt_fee]);
                        $start = true;
                    }
                }
            }
            $this->_db->commit();
        } catch (PDOException $e) {
            $this->_db->rollBack();
            echo 'error occurred when initializing fees';
        }
    }
}
