<?php
    $url = new Url();
    //display alert
    if($alert->hasAlerts($username) && basename(Utility::myself()) != 'notifications.php'){
        $count = $alert->getUnseenCount($username);
        echo $count.' <a href="notifications.php">notifications</a>';
    }
    
    //display request
    if($req2->hasRequests($username) && basename(Utility::myself()) != 'requests2.php'){
        $count = $req2->getCount($username);
        echo $count.' <a href="requests2.php">requests</a>';
    }
?>
<link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/nav.inc.css',2))?>" />
<div id="headerContainer">
    <div id="logoContainer">
        <img src="<?php echo Utility::escape($url->to('images/hkm_logo.jpg')) ?>" id="school_logo"/>
    </div>
    <div id="navlinks">
        <div id="linkContainer" class="hideNav">
            <a href="<?php echo $url->to('index.php',2) ?>">Home</a>
            <a href="<?php echo $url->to('profile.php?id='.$id,2) ?>">View Profile</a>
            <a href="<?php echo $url->to('update.php',2) ?>">Update Details</a>
            <a href="<?php echo $url->to('changepassword.php',2) ?>">Change Password</a>
            <a href="<?php echo $url->to('logout.php',2) ?>">Logout</a>
            <a href="<?php echo $url->to('index.php', 4) ?>" id="examNav">Exam Portal</a>
        </div>
        <button id="homeBar" onclick="location.assign('<?php echo Utility::escape($url->to('index.php',2)) ?>')">H</button>
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

<?php 
    //echo welcome flash message
    if(Session::exists('welcome')){
        echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$data->title.'. '.ucfirst($data->fname).'</div>';
        Session::delete('welcome');
        if(Session::exists('welcome back')){
            Session::delete('welcome back');
        }
    }else{
        if(Session::exists('welcome back')){
            echo '<div class="message">Welcome '.$staff->getPosition($rank).'</div>';
            Session::delete('welcome back');
        }
    }
