<?php
    
    if((Input::submitted() || Input::submitted('get')) && !empty(Input::get('subid'))){ //submitted via post or get request
        $subId = Utility::escape(Input::get('subid'));
        $util = new Utils();
        $subDetails = $util->getSubDetails($subId);
        $subName = Utility::escape($subDetails->subject);
        $subClass = Utility::escape($subDetails->class);
        $subLevel = (int)$subDetails->level;
        if(!$staff->isSubjectTeacher($username, $subId)){
            Redirect::to(404);
        }
    }else{
        Redirect::to(404);
    }
    
?>