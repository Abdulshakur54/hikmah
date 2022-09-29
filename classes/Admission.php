<?php
class Admission extends User
{

    public function __construct()
    {
        parent::__construct($cat = 4);
    }


    //this method gets an id for admission student after increement by 1
    public static function genId()
    {
        $agg = new Aggregate();
        $id = $agg->lookUp('adm_count', 'sing_val', 'id,=,1') + 1; //gets the current no and increement by 1
        $agg->edit($id, 'adm_count', 'sing_val', 'id,=,1'); //updates the admission count
        $preZeros = '';
        $preZerosCount = 3 - strlen((string)$id);
        for ($i = 1; $i <= $preZerosCount; $i++) {
            $preZeros .= '0';
        }
        return $preZeros . $id;
    }

    public function apply($fatherName, $motherName, $username)
    {
        return $this->_db->query('update admission set fathername=?, mothername=?, applied = ? where adm_id=?', [$fatherName, $motherName, true, $username]);
    }

    public function getData($adm_id)
    {
        $this->find($adm_id);
        return $this->data();
    }


    public function hasApplied($adm_id): bool
    {
        $this->_db->query('select applied from admission where id=?', [$adm_id]);
        return ($this->_db->one_result()->applied) ? true : false;
    }

    public function getLogo($sch)
    {
        $this->_db->query('select logo from school where sch_abbr = ?', [$sch]);
        return $this->_db->one_result()->logo;
    }

    public function get_admission_data(string $adm_id, string $sch_abbr)
    {
        $this->_db->query('select admission.fname,admission.lname,admission.oname,admission.rank,admission.level,admission.sch_abbr,admission.fathername, admission.picture,admission.date_of_admission, school.address,school.phone_contacts,school.email,school.logo from admission inner join school on school.sch_abbr = admission.sch_abbr where admission.adm_id=? and admission.sch_abbr=?', [$adm_id, $sch_abbr]);
        if ($this->_db->row_count() > 0) {
            return $this->_db->one_result();
        } else {
            $this->_db->query('select student.fname,student.lname,student.oname,student.rank,student.level,student.sch_abbr,student2.fathername,student.picture, student.date_of_admission,school.address,school.phone_contacts,school.email,school.logo from student inner join student2 on student.std_id = student2.std_id inner join school on school.sch_abbr = student.sch_abbr where student.adm_id=? and student.sch_abbr=?', [$adm_id, $sch_abbr]);
            if ($this->_db->row_count() > 0) {
                return $this->_db->one_result();
            }
            return [];
        }
    }
}
