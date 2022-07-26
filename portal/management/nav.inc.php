<?php 
    //display alert
    if($alert->hasAlerts($username) && basename(Utility::myself()) != 'notifications.php'){
        $count = $alert->getUnseenCount($username);
        echo $count.' <a href="notifications.php">notifications</a>';
    }

    //display request
    if($req->hasRequests($rank) && basename(Utility::myself()) != 'requests.php'){
        $count = $req->getCount($rank);
        echo $count.' <a href="requests.php">requests</a>';
    }
?>
<link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/nav.inc.css',1))?>" />
<div id="headerContainer">
    <div id="logoContainer">
        <img src="<?php echo Utility::escape($url->to('images/hkm_logo.jpg')) ?>" id="school_logo"/>
    </div>
    <div id="navlinks">
        <div id="linkContainer" class="hideNav">
            <a href="<?php echo $url->to('index.php',1) ?>">Home</a>&nbsp
            <a href="<?php echo $url->to('profile.php?id='.$id,1) ?>">View Profile</a>&nbsp
            <a href="<?php echo $url->to('update.php',1) ?>">Update Details</a>&nbsp
            <a href="<?php echo $url->to('changepassword.php',1) ?>">Change Password</a>&nbsp
            <a href="<?php echo $url->to('logout.php',1) ?>">Logout</a>
            <a href="<?php echo $url->to('index.php', 4) ?>" id="examNav">Exam Portal</a>
        </div>
        <button id="homeBar" onclick="location.assign('<?php echo Utility::escape($url->to('index.php',1)) ?>')">H</button>
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