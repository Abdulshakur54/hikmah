<?php 
 //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './nav1.inc.php';
    require_once './sub_teacher.inc.php';
    require_once '../../libraries/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory as IOObj;  
    $msg = '';
    $utils = new Utils();
    $table = $utils->getFormatedSession($sch_abbr).'_score';
    $scoreSettings = $staff->getScoreSettings($sch_abbr);
    $subject = new Subject($subId,$table,$scoreSettings);
    $scores = $subject->getScores($table,$currTerm);
    //initialize some settings to false to help determine if it would be displayed on the screen
    $faSet = $saSet = $ftSet = $stSet = $proSet = $examSet = false;
    if($scoreSettings['fa'] > 0){
        $faSet = true;
    }
    if($scoreSettings['sa'] > 0){
        $saSet = true;
    }
    if($scoreSettings['ft'] > 0){
        $ftSet = true;
    }
    if($scoreSettings['st'] > 0){
        $stSet = true;
    }
    if($scoreSettings['pro'] > 0){
        $proSet = true;
    }
    if($scoreSettings['exam'] > 0){
        $examSet = true;
    }
    
    /*getting the required column*/
    $columns = $subject->getNeededColumns($sch_abbr); //this returns an array  of the needed columns
    /*
     * replace exam index with ex and store in another variable, this is done for compatibility for the column names in the different tables
     */
    $pos = array_search('exam', $columns);
    $scoreColumns = $columns;
    if($pos !== false){ //false is used for comparison because 0 is a valid value in this scenario
        $scoreColumns[$pos] = 'ex';
    }
    
   
    $title = $subName.' '. School::getLevName($sch_abbr, $subLevel).$subClass.' Scoresheet'; //this refers to something like 'mathematics 2a scoresheet
    if(Input::get('download')=== 'true'){
       
            //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header('Content-Disposition: attachment; filename="'.$title.'.xlsx"');
            
            
           
             $alphaColumns = ['C','D','E','F','G','H']; //this represents the name of the excel columns
            
            //create a spreadsheet
            $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            $spreadsheet->getDefaultStyle()->getFont()->setName('Trebuchet MS');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(13);
            $activeSheet = $spreadsheet->getActiveSheet();
            $activeSheet->getRowDimension('1')->setRowHeight(30); //set the height for the first row, the header
            
            //setting the default column with for column A to H
            $activeSheet->getColumnDimension('A')->setWidth(17);
            $activeSheet->getColumnDimension('B')->setWidth(35);
            
            $counter = 0; //use for iterating $alphaColumns
            foreach ($columns as $column){
                 $activeSheet->getColumnDimension($alphaColumns[$counter])->setWidth(8);
                 $counter++;
            }
            //end of setting default column
            $endCell = $alphaColumns[$counter-1]; //end cell represents the last cell horizontally expected to be a score column in the excelsheet
             //styling the header(title)
          
            $activeSheet->setCellValue('A1', strtoupper($title)); //write the header of the excel file
            $activeSheet->mergeCells('A1:'.$endCell.'1');
            $activeSheet->getStyle('A1:'.$endCell.'1')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS); //setting horizontal alignment for the header
            $activeSheet->getStyle('A1:'.$endCell.'1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); //setting vertical alignment for the header
            $activeSheet->getProtection()->setSheet(true); //enable protection
            $spreadsheet->getDefaultStyle()->getProtection()->setLocked(false); //overide default to unlock all cells
            $activeSheet->getStyle('A1:'.$endCell.'1')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock cell
            $activeSheet->getStyle('A1:'.$endCell.'1')->getFont()->setBold(true);
            $activeSheet->getStyle('A1:'.$endCell.'1')->getFont()->setSize(13);
            $activeSheet->getStyle('A1:'.$endCell.'1')->getFont()->setName('verdana');
          
            /*generating the score headers e.g FA SA FT*/
            $counter = 0;
            $row = 2; //
            $activeSheet->setCellValue('A'.$row, 'ID');
            $activeSheet->setCellValue('B'.$row, 'NAME');
            foreach ($columns as $column){
               
                $maxValue = $scoreSettings[$column]; //the maximum score input allowed for an excel column
                if($column === 'exam'){ //this code is written so that 'exam' can be outputted as 'ex' in the excelsheet
                   $column = 'ex';
               }
                $activeSheet->setCellValue($alphaColumns[$counter].$row, strtoupper($column).' ('.$maxValue.')');
                $counter++;
            }
            
             $activeSheet->getStyle('A2:'.$endCell.'2')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock cell
            
            //styling the score headers
            $activeSheet->getRowDimension('2')->setRowHeight(25); //set the height for the first row, the header
            $activeSheet->getStyle('A2:'.$endCell.'2') 
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS); //setting horizontal alignment for the header
            $activeSheet->getStyle('A2:'.$endCell.'2')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); //setting vertical alignment for the header
            $activeSheet->getStyle('A2:'.$endCell.'2')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock cell
            $activeSheet->getStyle('A2:'.$endCell.'2')->getFont()->setBold(true);
            $activeSheet->getStyle('A2:'.$endCell.'2')->getFont()->setSize(13);
               // $activeSheet->getStyle('A1:'.$endCell.'1')->getFont()->setName('verdana');
            /*End of generating the score headers*/
            
            
            
          
            $styleArray = [
                                'borders' => [
                                    'outline' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        'color' => ['argb' => 'dddddd'],
                                    ],
                                ],
                            ]; //this is to be used to style cell border
           
          
                
                /*Cell validation to help keep out unwanted inputs*/
                $counter = 2;
                $validationArray= []; //to hold column  validations
                foreach ($columns as $column){
                      //validating a cell which will be cloned by other cells that requires same validation for first assignment
                    $maxValue = $scoreSettings[$column]; //the maximum score input allowed for an excel column
                    $validation = $spreadsheet->getActiveSheet()->getCell('I'.$counter) //I0 is not used because it does not exist, I1 is not used because it is in same line with the header
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
                   $validationArray[] = $validation; //stores validation for present column
                    //end of validating cell
                    $counter++;
                }
               
                  $row = 3; //start at row 3
                foreach($scores as $score){
                    $counter = 0;
                    $activeSheet->setCellValue('A'.$row, $score->std_id);
                    $activeSheet->setCellValue('B'.$row, Utility::formatName($score->fname, $score->oname, $score->lname));   
                    $activeSheet->getStyle('A'.$row.':B'.$row)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED); //lock cell
                    foreach ($scoreColumns as $column){
                        $activeSheet->getRowDimension($row)->setRowHeight(23);
                        $col = $currTerm.'_'.$column;  
                        $activeSheet->setCellValue($alphaColumns[$counter].$row, $score->$col);
                        $activeSheet->getCell($alphaColumns[$counter].$row)->setDataValidation(clone $validationArray[$counter]);
                        $counter++;
                    }
                     $row++;
                }
                $row--; //to remove the effect of the last increement in the foreach loop above
                $activeSheet->getStyle('A1:'.$endCell.$row)->getAlignment()->setWrapText(true); //wrap text
                $activeSheet->getStyle('C3:'.$endCell.$row) 
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //setting horizontal alignment for the score cells
                /* Entering of scores stops here */
                
            $writer = IOObj::createWriter($spreadsheet, 'Xlsx');
            $filename = $title.'.xlsx';
            $filepath = 'uploads/scores/'.$filename;
            $writer->save($filepath);
            File::download($filepath, $filename); //download file
            unlink($filepath); //delete the file
            exit();
    }
?>
<!doctype html>
<html>
    <head>
        <meta name="HandheldFriendly" content="True">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Scoresheet</title>
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
        <link rel="stylesheet" type="text/css" href="styles/scores.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('ld_loader/ld_loader.css',0))?>" />
    </head>
    <body>
        <?php require_once './nav.inc.php';?>
        <main>
            <div>
                <div class="linkbuttons">
                    <label for = "import" id="importTrigger" style="cursor: pointer; color:blue;">Import</label>
                    <div>
                        <input type="file" name="import" id="import" style="display: none" onchange="importFile(this)" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/><span id="import_loader"></span>
                        <div id="importMsg"><?php if(Session::get_flash('import')==='success'){echo'<div class="success">Import was successful</div>';}?></div>
                    </div>
                </div>
                <div class="linkbuttons">
                    <span style="cursor: pointer; color:blue;" onclick="downloadScores()">Download</span>
                </div>
            </div>
            <form method="post" action="<?php echo Utility::myself()?>" id="form" onsubmit="return false;">
                <div class="formhead"><?php echo $title ?></div>
                <?php echo $msg;?>
                <?php 
                    if(!empty($scores)){
                        $header = '<table><thead><th>SN</th><th>ID</th><th>Name</th>';
                        if($faSet){
                            $header.= '<th>FA</th>';
                        }
                        if($saSet){
                            $header.= '<th>SA</th>';
                        }
                        if($ftSet){
                            $header.= '<th>FT</th>';
                        }
                        if($stSet){
                            $header.= '<th>ST</th>';
                        }
                        if($proSet){
                            $header.= '<th>PRO</th>';
                        }
                        if($examSet){
                            $header.= '<th>EXAM</th>';
                        }
                        echo $header.='</thead><tbody>';
                        $sn = 1;
                        foreach($scores as $score){
                            $tr = '<tr>';
                            $tr.='<td>'.$sn.'</td><td>'.$score->std_id.'</td><td>'.Utility::formatName($score->fname,$score->oname,$score->lname).'</td>';
                            if($faSet){
                                $col = $currTerm.'_fa';
                                $scr = $score->$col;
                                $tr.='<td><input type="number" min="0" max="'.$scoreSettings['fa'].'" value="'.$scr.'" onchange="update('.$score->id.')" id="fa_'.$score->id.'" /></td>';
                            }
                            if($saSet){
                                $col = $currTerm.'_sa';
                                $scr = $score->$col;
                                $tr.='<td><input type="number" min="0" max="'.$scoreSettings['sa'].'" value="'.$scr.'" onchange="update('.$score->id.')" id="sa_'.$score->id.'" /></td>';
                            }
                            if($ftSet){
                                $col = $currTerm.'_ft';
                                $scr = $score->$col;
                                $tr.='<td><input type="number" min="0" max="'.$scoreSettings['ft'].'" value="'.$scr.'" onchange="update('.$score->id.')" id="ft_'.$score->id.'" /></td>';
                            }
                            if($stSet){
                                $col = $currTerm.'_st';
                                $scr = $score->$col;
                                $tr.='<td><input type="number" min="0" max="'.$scoreSettings['st'].'" value="'.$scr.'" onchange="update('.$score->id.')" id="st_'.$score->id.'" /></td>';
                            }
                            if($proSet){
                                $col = $currTerm.'_pro';
                                $scr = $score->$col;
                                $tr.='<td><input type="number" min="0" max="'.$scoreSettings['pro'].'" value="'.$scr.'" onchange="update('.$score->id.')" id="pro_'.$score->id.'" /></td>';
                            }
                            if($examSet){
                                $col = $currTerm.'_ex';
                                $scr = $score->$col;
                                $tr.='<td><input type="number" min="0" max="'.$scoreSettings['exam'].'" value="'.$scr.'" onchange="update('.$score->id.')" id="ex_'.$score->id.'" /></td>';
                            }
                            $tr .= '</tr>';
                            echo $tr;
                            $sn++;
                        }
                        echo '</tbody></table>';
                    }else{
                        echo '<div class="message">No record found</div>';
                    }
                  
                ?>
                <div id="genMsg"></div>
                <input type="hidden" name="updateddata" id="updateddata"/> <!--to hold data that needs to be updated -->
                <input type="hidden" id="hasProject" value="<?php echo ($proSet) ? 'true': 'false'; ?>"/> <!--to determine it there is project or not -->
               
                <div style="visibility: hidden" id="scorecolumns" > <?php echo json_encode($scoreColumns)?></div><!-- score columns -->
                <input type="hidden" name="token" id="token" value="<?php echo Token::generate()?>"/>        
                <input type="hidden" id="subid" name="subid" value="<?php echo $subId?>"/> 
                <button id="save" onclick="saveData()" name="save">Save</button><span id="save_loader"></span>
            </form>
        </main>
        <script>
            window.addEventListener('load',function(){
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('ld_loader/ld_loader.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/ajaxrequest.js',0))?>');
                appendScript('scripts/score.js');
            });
            function appendScript(source){
                let script = document.createElement('script');
                script.src=source;
                document.body.appendChild(script);
            }
            
        </script>
    </body>
</html>
