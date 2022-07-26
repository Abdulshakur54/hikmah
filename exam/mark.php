<?php 
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
        Redirect::to('index.php'); //redirect to exam home page
	} 
    $rank = $user->getRank();
    if(!in_array($rank, $allowed)){ //ensure that only the allowed people can access page
        Redirect::to(404);
    }  
    
    require_once '../libraries/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory as IOObj;
    if(Input::submitted('get') && !empty(Input::get('examid'))){
        $qtn = new Question();
        $exam = new Exam();
        $examId = Utility::escape(Input::get('examid'));
        $examId = strtoupper($examId);
        if(!empty(Input::get('download'))){   
            //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$examId.' Theory Question.xlsx"');
            //create a spreadsheet
            $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Trebuchet MS');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(13);
            $activeSheet = $spreadsheet->getActiveSheet();
            $activeSheet->getRowDimension('1')->setRowHeight(30); //set the height for the first row, the header
            //setting the default column with for column A - D
            $activeSheet->getColumnDimension('A')->setWidth(20);
            $activeSheet->getColumnDimension('B')->setWidth(60);
            $activeSheet->getColumnDimension('C')->setWidth(40);
            $activeSheet->getColumnDimension('D')->setWidth(8);
            //end of setting default column
            
            
            $activeSheet->setCellValue('A1','MARK '.$examId.' THEORY QUESTIONS'); //write the header of the excel file
            $activeSheet->mergeCells('A1:D1');
            $activeSheet->getStyle('A1:D1')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS); //setting horizontal alignment for the header
            $activeSheet->getStyle('A1:D1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); //setting vertical alignment for the header
            $activeSheet->getProtection()->setSheet(true); //enable protection
            $spreadsheet->getDefaultStyle()->getProtection()->setLocked(false); //overide default to unlock all cells
            $activeSheet->getStyle('A1:D1')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock cell
            $activeSheet->getStyle('A1:D1')->getFont()->setBold(true);
            $activeSheet->getStyle('A1:D1')->getFont()->setSize(13);
            $activeSheet->getStyle('A1:D1')->getFont()->setName('verdana');
            $distinctQtn = $exam->getDistinctTheoryQtns($examId); // no two questions will have same qtn_id
            $allQtnValues = []; //this store the comment and mark for all the questions, it is an associative array
            /* loop through the distinct exam questions*/
            $row = 2; //start at row 2
           
           
           
            if(!empty($distinctQtn)){
                $styleArray = [
                                'borders' => [
                                    'outline' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        'color' => ['argb' => 'dddddd'],
                                    ],
                                ],
                            ]; //this is to be used to style cell border
                
              
                foreach ($distinctQtn as $qstn){
                    $maxValue = Utility::escape($qstn->mark);
                    
                    //validating a cell which will be cloned by other cells that requires same validation
                   
                    $validation = $spreadsheet->getActiveSheet()->getCell('H2')
                    ->getDataValidation();
                    $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE );
                    $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP );
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Only numbers between 0 and '.$maxValue.' are allowed.');
                    $validation->setPromptTitle('Allowed input');
                    $validation->setPrompt('Only numbers between 0 and '.$maxValue.' are allowed.');
                    $validation->setFormula1(0);
                    $validation->setFormula2($maxValue);
                   
                    //end of validating cell
                    
                    $id = Utility::escape($qstn->id);  //the id of the each distinct question
                    $theoryQtns = $exam->getTheoryQtns($examId, $id, false); //get all the question for each distinct exam
                    if(!empty($theoryQtns)){ 
                        $start = $row; //setting start point for styling
                        $activeSheet->setCellValue('A'.$row,'Question: '.$qstn->qtn); //write the exam question for each distict question
                        $activeSheet->mergeCells('A'.$row.':D'.$row);
                       
                        $activeSheet->getRowDimension($row)->setRowHeight(25); //set the height for the question row
                        $activeSheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);
                        $row++; //move to next line
                        
                        $activeSheet->setCellValue('A'.$row,'Answer: '.$qstn->answers); //write the exam answers for each distict question
                        $activeSheet->mergeCells('A'.$row.':D'.$row);
                        $activeSheet->getStyle('A'.($row-1).':D'.$row)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock the question and answer cell
                        $activeSheet->getRowDimension($row)->setRowHeight(25); //set the height for the answer row
                        $activeSheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);
                        $row+=2; //move to next line
                        $distQtnCount = count($theoryQtns); //count for distinct question
                        
                        $activeSheet->setCellValue('A'.$row,'Examinee Id');
                        $activeSheet->setCellValue('B'.$row,'Examinee Answer');
                        $activeSheet->setCellValue('C'.$row,'Your Comment');
                        $activeSheet->setCellValue('D'.$row,'Mark');
                        $activeSheet->getStyle('A'.($row-3).':D'.$row)
                        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //setting horizontal alignment from the question row to the examinee row
                        $activeSheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);
                        
                        
                        //this are hidden values that will help read the files when uploaded
                        $activeSheet->setCellValue('E'.$row,'readable');
                        $activeSheet->setCellValue('F'.$row,$distQtnCount);
                        $activeSheet->setCellValue('G'.$row,$id);
                        
                        
                        
                       $activeSheet->getStyle('A'.$row.':G'.$row)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock column A - F of the examinee row
                        $examineeRow = $row; //will help to set the fill color down the code
                        //Style the examinee row border
                        $activeSheet->getStyle('A'.$row)->applyFromArray($styleArray); 
                        $activeSheet->getStyle('B'.$row)->applyFromArray($styleArray); 
                        $activeSheet->getStyle('C'.$row)->applyFromArray($styleArray); 
                        $activeSheet->getStyle('D'.$row)->applyFromArray($styleArray); 
                        //end of styling the examinee row border
                        
                        foreach($theoryQtns as $tQtn){ 
                            $row++; //move to next line
                            $activeSheet->setCellValue('A'.$row,$tQtn->examinee_id);
                            $activeSheet->setCellValue('B'.$row,$tQtn->answer); 
                            $activeSheet->getStyle('A'.$row.':B'.$row)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock 2 cell, sideways
                            $activeSheet->getCell('D'.$row)->setDataValidation(clone $validation);
                            //set the border for each of the table row
                            $activeSheet->getStyle('A'.$row)->applyFromArray($styleArray);
                            $activeSheet->getStyle('B'.$row)->applyFromArray($styleArray);
                            $activeSheet->getStyle('C'.$row)->applyFromArray($styleArray);
                            $activeSheet->getStyle('D'.$row)->applyFromArray($styleArray);
                             //end of set the border for each of the table row
                        }
                        $end = $row;
                      
                       
                       $activeSheet->getStyle('A'.$examineeRow.':D'.$examineeRow)->getFont()->getColor()->setRGB('141478'); //change the font color of the table header
                       $activeSheet->getStyle('E'.$examineeRow.':G'.$examineeRow)->getFont()->getColor()->setRGB('FFFFFF'); //change the font color to white so it will be hidden
                    }
                    $row+=4;
                }
                $activeSheet->setCellValue('E1',$row-4);  //write the max number of row value, this will be of help when reading the file
                $activeSheet->getStyle('E1')->getFont()->getColor()->setRGB('FFFFFF'); //change the font color to white so it will be hidden
                $activeSheet->getStyle('E1')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock cell
                $activeSheet->getStyle('A1:D'.$row)->getAlignment()->setWrapText(true); //wrap text
                
            }
           
            $writer = IOObj::createWriter($spreadsheet, 'Xlsx');
            $filename = $examId.' Theory Question.xlsx';
            $filepath = 'uploads/exam/comment/'.$filename;
            $writer->save($filepath);
            File::download($filepath, $filename); //download file
            unlink($filepath); //delete the file
            exit();
        }
    
    }else{
        Redirect::to('index.php'); //exam home page
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="HandheldFriendly" content="True">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Mark Scripts</title>
        <link rel="stylesheet" type="text/css" href="ld_loader/ld_loader.css" />
        <link rel="stylesheet" type="text/css" href="styles/style.css" />
        <link rel="stylesheet" type="text/css" href="styles/mark.css" />
    </head>
    <body>
        <?php require_once 'nav.inc.php'?>
        <main>
            <?php require_once 'header.inc.php'?>
        <form onsubmit="return false;" method="post" action="<?php echo Utility::myself();?>">
        <?php
            $distinctQtn = $exam->getDistinctTheoryQtns($examId); // no two questions will have same qtn_id
            if(!empty(Input::get('upload'))){
                echo'<div class="success">Upload was successful</div>';
            }
            
            if(!empty(Input::get('submissionsuccessful'))){
                echo'<div class="success">Submission was successful</div>';
            }
            if(!empty($distinctQtn)){
                $qtnIdArr = []; //to store the ids for each distinct question
                $boxNoArr = []; //to store the box no for easy reference when passed in to ajax
                ?>
                   
                <div id ="buttonContainer">
                    <input type="file" name="uploadedFile" id="uploadedFile" />
                    <input type = "hidden" value = "<?php echo Token::generate(); ?>" name = "token" id="token"/> <!--hidden token -->
                    <input type = "hidden" value = "<?php echo $examId; ?>" name = "examid" id="examId"/> <!--hidden exam Id -->
                    <button id="uploadBtn">Upload</button><span id="ld_loader_upload"></span>
                    <button onclick="downloadTemplate()" id="downloadBtn">Download Template</button>
                   <div id="msgDiv">
                      
                   </div>

               </div>
                    
               <?php
                $boxNo = 1;
                foreach ($distinctQtn as $qstn){
                    $maxValue = Utility::escape($qstn->mark);
                    $id = Utility::escape($qstn->id);
                    $theoryQtns = $exam->getTheoryQtns($examId, $id, false); //get all the question that have same qtn_id
                   
                    if(!empty($theoryQtns)){
                        $qtnIdArr[] = $id; //populate with distinct qtn ids
                        $boxNoArr[$id] = $boxNo;
                ?>      <div class="questionWrapper" id="<?php echo 'wrapper_'.$id ?>">
                        <div id="qtnAns">            
                            <div class="questionDiv">
                                <span><?php echo Utility::escape($qstn->qtn) ?></span><span id="boxCount"><?php echo 'Box '.$boxNo; ?></span>
                            </div>
                            <div class="rightAns">
                                 <?php echo Utility::escape($qstn->answers) ?>
                            </div>
                        </div>



    <?php
                        echo '<table>
                        <tr id="headerRow"><th>Examinee Id</th><th>Examinee Answer</th><th>Your comment</th><th>Mark</th></tr><tbody>';

                        $row = '';
                        $qtnValues = []; //this store the comment and mark for a particular question for each of the student
                        foreach($theoryQtns as $tQtn){
                            $examineeId = Utility::escape($tQtn->examinee_id);
                            $mark = (!empty($tQtn->mark)) ? Utility::escape($tQtn->mark) : '';
                            $comment = (!empty($tQtn->comment)) ? Utility::escape($tQtn->comment) : '';
                            $row .=  '<tr><td>'.$examineeId.'</td><td>'.Utility::escape($tQtn->answer).'</td><td><textarea id="comm_'.$examineeId.$id.'">'.$comment.'</textarea></td><td><input type="number" min="0" id="score_'.$examineeId.$id.'" max="'.$maxValue.'" value="'.$mark.'" /></td></tr>';
                            $qtnValues[] = $examineeId; //store the examinee id;                       
                        }
                        echo $row;
                        echo '</tbody></table>';
                        ?>
                        <div class="hidden" id="<?php echo'qtns_'.$id ?>"><?php echo json_encode($qtnValues)?></div>
                        <input type="hidden" value="<?php echo $maxValue?>" id="<?php echo 'maxMark_'.$id?>" />
                        <div id="<?php echo 'msg_'.$id?>"></div>
                        <footer><button onclick="saveData(<?php echo $id; ?>)" id="<?php echo 'saveBtn_'.$id ?>">Save</button><span id="<?php echo 'ld_loader_'.$id;?>"></span><button onclick="submitData(<?php echo $id; ?>)" id="<?php echo'submitBtn_'.$id ?>">Submit</button><button onclick="removeQtn(<?php echo $id; ?>)" class="closeBtn" id="<?php echo 'closeBtn_'.$id ?>">X</button></footer>;
                        <?              
                    
                ?>

            </div>
           
        </form>
        
               <?php
                    }
                
                    $boxNo++;
                }
                ?>
                 <div class="hidden" id="distQtnIds"><?php echo json_encode($qtnIdArr)?></div>
                 <div class="hidden" id="boxNumsBox"><?php echo json_encode($boxNoArr)?></div>
                <div id="msgAll"></div>
                <footer><button onclick="saveAll()" id="saveAllBtn">Save All</button><span id="ld_loader_all"></span><button onclick="submitAll()" id="submitAllBtn">Submit All</button></footer>;
              
                <?php
            }else{
                echo '<div class="message">No questions available</div>';
                exit();
            }

        ?>
        
        <input type="hidden" value="<?php echo $exam->getPassMark($examId)?>" id="examPassMark" />
        </main>
        <script>
            window.onload = function(){
                appendScript('ld_loader/ld_loader.js');
                appendScript('scripts/script.js');
                appendScript('scripts/validation.js');
                appendScript('scripts/ajaxrequest.js');
                appendScript('scripts/mark.js');
            }

            function appendScript(source){
                let script = document.createElement("script");
                script.src=source;
                document.body.appendChild(script);
            }
        </script>
    </body>
</html>