<?php
   
    $classWithId = $staff->getClassWithId($username);
    if(!empty($classWithId)){
        $class = $classWithId->class;
        $classId = $classWithId->id;
        $level = $classWithId->level;
        
        //check if all students have complete subject registration
        if(!$staff->isStdsSubRegComplete($classId)){
            echo '<p>Some students are yet to complete their Subject Registeration. <a href="need_to_reg_sub.php">View Here</a></p>';
        }
    }else{
        Redirect::to(404);
    }
