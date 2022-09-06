<?php
    $classWithId = $staff->getClassWithId($username);
    if(!empty($classWithId)){
        $class = $classWithId->class;
        $classId = $classWithId->id;
        $level = $classWithId->level;
       
        //check if all students have complete subject registration
        if(!$staff->isStdsSubRegComplete($classId)&& basename(Utility::myself()) !== 'need_to_reg_sub.php'){
            echo '<p style="font-weight: bold" class="text-right">Some students are yet to complete their Subject Registeration. <a onclick="getPage(\'staff/need_to_reg_sub.php\')" href="#">View Here</a></p>';
        }
    }else{
        Redirect::to(404);
    }
