<?php
    $alert = new ExamAlert();
    //display alert
    if($alert->hasAlerts($username)){
        $count = $alert->getUnseenCount($username);
        echo $count.' <a href="notifications.php">notifications</a>';
    }
