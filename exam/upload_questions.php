<?php[2,4,6,7,15,17]

    //initializations

	spl_autoload_register(

		function($class){

			require_once'../classes/'.$class.'.php';

		}

	);

	session_start(Config::get('session/options'));

	//end of initializatons



	$url = new Url();

    $allowed = [2,4,5,7,15,17]; //stores the rank of people allowed

    $mgt = new Management();

    $staff = new Staff();

    $user = null;

    if($mgt->isRemembered()){

        $user = $mgt;

    }

    if($staff->isRemembered()){

        $user = $staff;

    }

	if(!isset($user)){ //ensure user is legally logged in

		Redirect::to('index.php'); //redirect to exam index

	} 

    $rank = $user->getRank();

    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page

		Redirect::to(404);

	}  

    $exam = new Exam();

    $qtn = new Question();

    if(Input::submitted('get') && !empty(Input::get('examid'))){

        $examId = Utility::escape(Input::get('examid'));

        if(!$exam->idExists($examId)){ //ensure that the exam id exists

            Redirect::to(404);

        }

        if(!empty(Input::get('download'))){ //this is true if the download button is clicked

           File::download('question template.xlsx','question template.xlsx');

           exit();

        }

        $details = $exam->getDetails($examId);

        $qtnNo = $qtn->getCount($examId);

    }else{

        Redirect::to('index.php'); //redirect to exam index

    }

    

?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta name="HandheldFriendly" content="True">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Upload Questions</title>

        <link rel="stylesheet" type="text/css" href="ld_loader/ld_loader.css" />

        <link rel="stylesheet" type="text/css" href="styles/style.css" />

        <link rel="stylesheet" type="text/css" href="styles/upload_questions.css" />

    </head>

    <body>

        <?php require_once 'nav.inc.php'?>

        <main>

        <?php  echo Session::get_flash('exam created'); //output the flash message ?>

        <form method="post" action="<?php echo Utility::myself();?>" onsubmit="return false;">

          

            <h2 class="formhead">Upload Questions to <?php echo strtoupper($details->exam_id);?></h2>

            <div id ="questionContainer">

                <input type="file" name="uploadedFile" id="uploadedFile" />

                <input type = "hidden" value = "<?php echo Token::generate(); ?>" name = "token" id="token"/> <!--hidden token -->

                 <input type = "hidden" value = "<?php echo $examId; ?>" name = "examid" id="examId"/> <!--hidden exam Id -->

                 <button id="uploadBtn">Upload</button><span id="ld_loader"></span>

                 <button id="downloadBtn">Download Sample</button>

                <div id="msgDiv"></div>

               

            </div>

            <div id="pubEditContainer">

                <div class="success" id="msgBox">Questions have been successfully uploaded</div>

                <button id="editExamBtn">Edit Exam</button>

                <button id="publishExamBtn">Publish Exam</button>

            </div>

        </form>

        </main>

        <script>

            window.onload = function(){

                appendScript('ld_loader/ld_loader.js');

                appendScript('scripts/script.js');

                appendScript('scripts/validation.js');

                appendScript('scripts/ajaxrequest.js');

                appendScript('scripts/upload_questions.js');

            }



            function appendScript(source){

                let script = document.createElement('script');

                script.src=source;

                document.body.appendChild(script);

            }

        </script>

    </body>

</html>