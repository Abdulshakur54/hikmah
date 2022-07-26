<?php
    class Question{
        private $_db;

        public function __construct(){
            $this->_db = DB::get_instance();
        }

        //this method add questions to the ex_question table
        public function add($examId,$question,$type,$options,$answers,$answerOrder,$mark,$passage) :bool{
            return $this->_db->query('insert into ex_question(exam_id, qtn, type, options, answers, answer_order, mark, passage) values(?,?,?,?,?,?,?,?)',[$examId,$question,$type,$options,$answers,$answerOrder,$mark,$passage]);
        }
        


        //this method edit questions to the ex_question table
        public function update($examId,$question,$type,$options,$answers,$answerOrder,$mark,$passage,$qtnId) :bool{
            return $this->_db->query('update ex_question set exam_id=?, qtn=?, type=?, options=?, answers=?, answer_order=?, mark=?, passage=? where id=?',[$examId,$question,$type,$options,$answers,$answerOrder,$mark,$passage, $qtnId]);
        }


         //counts the no of questions for an exam
         public function getCount($examId) :int{
            $this->_db->query('select count(id) as examcount from ex_question where exam_id = ?', [$examId]);
            return $this->_db->one_result()->examcount;
        }
        
       


         //returns the sum of all the marks allotted to a question for a particular exam
         public function getMarksTotal($examId){
            $this->_db->query('select sum(mark) as total from ex_question where exam_id = ?', [$examId]);
            return $this->_db->one_result()->total;
        }

        public function complete($qtnCount, $examId) :bool{
            return ($this->getCount($examId) >= $qtnCount)?true:false;
        }

        public function getQuestions($examId){
            $this->_db->query('select * from ex_question where exam_id = ?',[$examId]);
            return $this->_db->get_result();
        }

        public function getOngoingExamQtns($examId){
            $this->_db->query('select id,qtn,type,options,answer_order,mark,passage from ex_question where exam_id = ? order by id asc',[$examId]);
            return $this->_db->get_result();
        }

        public function delete($qtnId,$examId) :bool{
            return $this->_db->query('delete from ex_question where id=? and exam_id = ?',[$qtnId, $examId]);
        }
        
        public function deleteAll($examId) :bool{
            return $this->_db->query('delete from ex_question where exam_id = ?',[$examId]);
        }

        
        public function getQuestionDetails($qtnId,$examId){
            $this->_db->query('select * from ex_question where id=? and exam_id =?',[$qtnId, $examId]);
            return $this->_db->one_result();
        }
        
    }