<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './apm.inc.php';
    //this custom function helps to detemine which option in the select tag for sch_abbr is selected
    function getSelectedSchool($val){
        global $sch_abbr;
        return ($val === $sch_abbr)?'selected':'';
    }
    
    function getSelectedLevel($lev){
        global $level;
       return ($lev == $level)?'selected':'';
    }
    
    function outputScore($score){
        return (!empty($score))?$score:'pending';
    }
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="HandheldFriendly" content="True">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Admission Decisions</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('ld_loader/ld_loader.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="styles/admission_decision.css" />
    </head>
    <body>
        <main>
             <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
             if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$apm->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$apm->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        <?php
           
          
            if(Input::submitted('get')){
                if(!empty(Input::get('sch_abbr')) && !empty(Input::get('level'))){
                    $sch_abbr = Utility::escape(Input::get('sch_abbr'));
                    $level = (int)Input::get('level');
                     $applicants = $apm->selectAdmissionApplicants($sch_abbr,$level);
                }
            }else{
                $applicants = $apm->selectAdmissionApplicants();
                
            }
            echo '<label>Select School</label> <select name="sch_abbr" id="sch_abbr" onchange="populateLevel(this)"><option value="ALL" selected>ALL</option>';
            $sch_abbrs = School::getConvectionalSchools(2); //this returns the abbreviation for each of the convectional schools
            foreach ($sch_abbrs as $sch_ab){
                echo '<option value="'.$sch_ab.'" '.getSelectedSchool($sch_ab).'>'.$sch_ab.'</option>';
            }
            echo '</select>'; 
            echo '<label>Select Level</label>
                <select name="level" id="level"><option value="ALL" '.getSelectedLevel("ALL").'>ALL</option>';
                $levels = School::getLevels($sch_abbr);
                if(!empty($levels)){
                    foreach ($levels as $levName=>$lev){
                        echo '<option value="'.$lev.'" '.getSelectedLevel($lev).'>'.$levName.'</option>';
                   }
                }
            
             echo '</select> <button onclick="reSubmit()">Filter</button>';?>
    
            <form method="POST" onsubmit="return false" id="form">
                <?php
            if(!empty($applicants)){?>
                <div>
                    <label for="selectAll" id="selectAll">Select all</label>
                    <input type="checkbox" name="selectAll"  checked="checked" onclick="checkAll(this)"/>
                </div>          
                <table>
                    <thead>
                        <tr><th>Adm ID</th><th>Name</th><th>Score</th><th>Accept</th><th>Application Details</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res=$applicants;
                        $count = 1;
                        foreach($res as $val){
                            $idVal = Utility::escape(strtoupper($val->adm_id));
                            echo '<tr id="row'.$val->id.'"><td>'.$idVal.'</td><td>'.Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)).
                                 '</td><td>'.outputScore($val->score).'</td><td><input type="checkbox" id="chk'.$count.'" checked /><input type="hidden" id="val'.$count.'" value="'.$idVal.'"/></td><td><a href="application.php?adm_id='.$idVal.'">view</a></td></tr>';
                            $count++;
                        }
                        ?>
                    </tbody>
                </table> 
                <input type = "hidden" value = "<?php echo($count-1); ?>" id="counter" /> <!--hidden counter -->
                <?php
                }else{
                    echo '<div class="message">No Admission Request</div>';
                }
        ?>
                <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
                 <div id="msg" class="failure"></div> <!-- expected to always be a failure message -->
                <div>
                    <button id="acceptAdm">Accept</button><span id="ld_loader"></span><button id="declineAdm">Decline</button>
                </div>
            </form>
            
        </main>
        <script>
            window.onload = function(){
                appendScript('<?php echo Utility::escape($url->to('ld_loader/ld_loader.js',0))?>');  
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/ajaxrequest.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/portalscript.js',0))?>');  
                appendScript('scripts/admission_decision.js');  
            };

            function appendScript(source){
                let script = document.createElement('script');
                script.src=source;
                document.body.appendChild(script);
            }
        </script>
    </body>

</html>
