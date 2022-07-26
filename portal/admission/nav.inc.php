<?php
    //display alert
    if($alert->hasAlerts($username) && basename(Utility::myself()) != 'notifications.php'){
        $count = $alert->getUnseenCount($username);
        echo $count.' <a href="notifications.php">notifications</a>';
    }
    
    //display request
    if($req2->hasRequests($username) && basename(Utility::myself()) != 'requests2.php'){
        $count = $req2->getCount($username);
        echo $count.' <a href="requests.php">requests</a>';
    }
?>
<link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/nav.inc.css',5))?>" />
<div id="headerContainer">
    <div id="logoContainer">
        <img src="<?php echo Utility::escape($url->to('images/hkm_logo.jpg')) ?>" id="school_logo"/>
    </div>
    <div id="navlinks">
        <div id="linkContainer" class="hideNav">
            <a href="<?php echo $url->to('index.php',5) ?>">Home</a>
            <a href="<?php echo $url->to('profile.php?id='.$id,5) ?>">View Profile</a>
            <a href="<?php echo $url->to('update.php',5) ?>">Update Details</a>
            <a href="<?php echo $url->to('changepassword.php',5) ?>">Change Password</a>
            <a href="<?php echo $url->to('logout.php',5) ?>">Logout</a>
            <a href="<?php echo $url->to('index.php', 4) ?>" id="examNav">Exam Portal</a>
        </div>
        <button id="homeBar" onclick="location.assign('<?php echo Utility::escape($url->to('index.php',5)) ?>')">H</button>
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