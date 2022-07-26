<?php
    //check that student has completed subject registration and message him if he haven't
    if(!$data->sub_reg_comp && !empty($classId) && (basename(Utility::myself()) !== 'sub_reg.php') && $std->classHasSubject($classId)){
        echo '<div class="message">You need to complete your subject registration. click <a href="sub_reg.php">here</a> to complete.</div>';
    }
    //display alert
    if($alert->hasAlerts($username) && basename(Utility::myself()) != 'notifications.php'){
        $count = $alert->getUnseenCount($username);
        echo $count.' <a href="notifications.php">notifications</a>';
    }
?>
<link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/nav.inc.css',3))?>" />
<div id="headerContainer">
    <div id="logoContainer">
        <img src="<?php echo Utility::escape($url->to('images/hkm_logo.jpg')) ?>" id="school_logo"/>
    </div>
    <div id="navlinks">
        <div id="linkContainer" class="hideNav">
            <a href="<?php echo $url->to('index.php',3) ?>">Home</a>
            <a href="<?php echo $url->to('profile.php?id='.$id,3) ?>">View Profile</a>
            <a href="<?php echo $url->to('update.php',3) ?>">Update Details</a>&nbsp
            <a href="<?php echo $url->to('changepassword.php',3) ?>">Change Password</a>
            <a href="<?php echo $url->to('logout.php',3) ?>">Logout</a>
            <a href="<?php echo $url->to('index.php', 4) ?>" id="examNav">Exam Portal</a>
        </div>
        <button id="homeBar" onclick="location.assign('<?php echo Utility::escape($url->to('index.php',3)) ?>')">H</button>
        <button id="toggleBar">+</button>
    </div>
</div>
<br/>

<script>
    let show = false;
    let = toggleBar = document.getElementById('toggleBar');
    let = linkContainer = document.getElementById('linkContainer');
    toggleBar.addEventListener('click',function(){
        if(!show){
            linkContainer.className = 'showNav';
            show = true;
        }else{
            linkContainer.className = 'hideNav';
            show = false;
        }
    });
    </script>