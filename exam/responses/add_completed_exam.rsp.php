<?php

//initializations

spl_autoload_register(

    function ($class) {

        require_once '../../classes/' . $class . '.php';
    }

);

session_start(Config::get('session/options'));

//end of initializatons



$mgt = new Management();

$staff = new Staff();

$std = new Student();

$adm = new Admission();

$user = null;

if ($mgt->isRemembered()) {

    $user = $mgt;
}

if ($staff->isRemembered()) {

    $user = $staff;
}

if ($std->isRemembered()) {

    $user = $std;
}

if ($adm->isRemembered()) {

    $user = $adm;
}

if (!isset($user)) { //ensure user is legally logged in

    Redirect::home('index.php'); //redirect to exam home page

}



header("Content-Type: application/json; charset=UTF-8");

if (Input::submitted() && Token::check(Input::get('token'))) {

    $examId = Utility::escape(Input::get('examid'));

    $examineeId = Utility::escape(strtoupper(Input::get('examinee_id')));

    $answers = Input::get('answers');

    $token = Input::get('token');

    $timesAllowed = Utility::escape(Input::get('timesAllowed'));

    if (Utility::noScript($answers)) {

        $answers = json_decode($answers, true);



        $exam = new Exam();

        $qtn = new Question();

        $examDetails = $exam->getDetails($examId);

        $totalNoOfQtn = 0; //this is same as the total no of answers provided by the examiner, a german and multiple selection may have more than 1 answers

        $totalNoOfCorrAns = 0;  //this is the total no of correct answers provided by the examinee, one taking the exam

        $totalNoOfMarks = 0; //this is the total mark for the whole exam

        $totalMarkOfCorrAns = 0;  //this is the sum of the marks for the correct answer an examinee gives

        $addWrongAns = 0; //to be used by case 4

        $hasTheory = false;  //initialize to false so that the exam would be send to the un_marked table if the value changes to true

        $theoryQtnId = []; //to store theory questions id;

        $marks = []; //to store the examinee mark for each question

        foreach ($answers as $qtnId => $answer) {

            $qtnDetails = $qtn->getQuestionDetails($qtnId, $examId);

            $totalNoOfMarks += $qtnDetails->mark;

            switch ($qtnDetails->type) {

                case 1:

                    $totalNoOfQtn++;

                    if ($qtnDetails->answers === $answer) {

                        $totalNoOfCorrAns++;

                        $totalMarkOfCorrAns += $qtnDetails->mark;

                        $marks[$qtnId] = $qtnDetails->mark;
                    } else {

                        $marks[$qtnId] = 0;
                    }

                    break;

                case 2:

                    $totalNoOfQtn++;

                    if (Utility::equals($qtnDetails->answers, $answer)) {

                        $totalNoOfCorrAns++;

                        $totalMarkOfCorrAns += $qtnDetails->mark;

                        $marks[$qtnId] = $qtnDetails->mark;
                    } else {

                        $marks[$qtnId] = 0;
                    }

                    break;

                case 3:

                    $dbAnswer = json_decode($qtnDetails->answers, true);

                    $counter = count($dbAnswer);

                    $eachMark = ($qtnDetails->mark) / $counter;

                    $usrAnswer = json_decode($answer, true);

                    $totalNoOfQtn += $counter;

                    $mk = 0; //initialize to 0 so it can be increemented by each mark gotten

                    if ($qtnDetails->answer_order) {

                        for ($i = 1; $i <= $counter; $i++) {

                            if (Utility::equals($dbAnswer['ans' . $i], $usrAnswer['ans' . $i])) {

                                $totalNoOfCorrAns++;

                                $totalMarkOfCorrAns += $eachMark;

                                $mk += $eachMark;
                            }
                        }
                    } else {

                        foreach ($usrAnswer as $ans) {

                            foreach ($dbAnswer as $dbAns) {

                                if (Utility::equals($ans, $dbAns)) {

                                    $totalNoOfCorrAns++;

                                    $totalMarkOfCorrAns += $eachMark;

                                    $mk += $eachMark;
                                }
                            }
                        }
                    }

                    $marks[$qtnId] = $mk;

                    break;

                case 4:

                    $dbAnswer = json_decode($qtnDetails->answers, true);

                    $counter = count($dbAnswer);

                    $eachMark = ($qtnDetails->mark) / $counter;

                    $usrAnswer = json_decode($answer, true);

                    $usrAnsCount = count($usrAnswer);

                    $totalNoOfQtn += $counter;

                    $mk = 0; //initialize to 0 so it can be increemented by each mark gotten



                    foreach ($usrAnswer as $usrAnskey => $ans) {

                        $sameKey = false;

                        foreach ($dbAnswer as $dbAnsKey => $dbAns) {

                            if (Utility::equals($usrAnskey, $dbAnsKey)) { //this means they have the same keys

                                $sameKey = true;

                                if (Utility::equals($ans, $dbAns)) {

                                    $totalNoOfCorrAns++;

                                    $mk += $eachMark;
                                } else {

                                    $addWrongAns++;

                                    $mk -= $eachMark;
                                }
                            }
                        }

                        if (!$sameKey) {

                            $addWrongAns++;

                            $mk -= $eachMark;
                        }
                    }

                    if ($mk < 0) {

                        $mk = 0; //this is to ensure examinees mark is not deducted for wrong answers

                    }

                    $totalMarkOfCorrAns += $mk;

                    $marks[$qtnId] = $mk;

                    break;

                case 5:

                    $theoryQtnId[] = $qtnId;

                    $hasTheory = true;

                    $marks[$qtnId] = '';

                    break;
            }
        }

        $jsonConvMarks = json_encode($marks);

        if ($hasTheory) {

            //insert theory questions in to the ex_theory table for marking

            foreach ($theoryQtnId as $qId) {

                $exam->insertTheory($examId, $examDetails->examiner_id, $examineeId, $qId, $answers[$qId], null, null);
            }

            $exam->insertCompleted($examId, $examDetails->examiner_id, $examineeId, 0, 0, 0, 0, json_encode($answers), false, $jsonConvMarks);

            $exam->removeFromAvailableExam($examId, $examineeId);

            echo json_encode(['statuscode' => 3, 'token' => Token::generate()]); //indicate that the submission was successful but user have to wait for result

            exit();
        }



        $totWrongAns = ($totalNoOfQtn - $totalNoOfCorrAns) + $addWrongAns;

        $totalScore = $totalMarkOfCorrAns;

        $ptgScore = round(($totalScore / $totalNoOfMarks) * 100, 2);

        $passed = ($ptgScore >= $examDetails->pass_mark) ? true : false;







        //this is added to calculate the equivalent score of the examinee that would be transfered to a score column in the main portal

        if (!empty($examDetails->transfer)) { //this should only be true if it is an external exam (exam from the main portal)

            $transferDetails = json_decode($examDetails->transfer);

            $maxMark = (int) $transferDetails->maxScore;

            $realScore = round($totalScore * ($maxMark / $totalNoOfMarks), 2);

            $db = new DB();
            if (property_exists($transferDetails, 'subid')) {
                $db->query('update ' . $transferDetails->tableName . ' set ' . $transferDetails->tableColumn . ' = ? where ' . $transferDetails->idColumn . '=? and subject_id=?', [$realScore, $examineeId, $transferDetails->subid]);
            } else { //this would usually run for admission because the exams set are not subject to have subject id
                $db->query('update ' . $transferDetails->tableName . ' set ' . $transferDetails->tableColumn . ' = ? where ' . $transferDetails->idColumn . '=?', [$realScore, $examineeId]);
            }
        }

        //end of added to calculate





        if ($exam->insertCompleted($examId, $examDetails->examiner_id, $examineeId, $totalNoOfQtn, $totalNoOfCorrAns, $totWrongAns, $ptgScore, json_encode($answers), $passed, $jsonConvMarks)) {

            $exam->removeFromAvailableExam($examId, $examineeId);

            echo json_encode(['statuscode' => 1, 'token' => Token::generate()]);  //indicate that the submission was successful and user can check result

        } else {

            echo json_encode(['statuscode' => 2, 'token' => Token::generate()]); //indicate that query was not successful

        }
    } else {

        exit(); // user trying to hack site

    }
}
