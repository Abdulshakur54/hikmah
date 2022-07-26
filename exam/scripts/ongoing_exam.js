let data = _('data');
data = JSON.parse(data.innerHTML);
let qtns = data['qtns'];
let examData = data['examData'];
let username = data['username'];
let indexes = data['indexes'];
let timesAllowed = data['count'];
let min = parseInt(examData['duration']) - 1;
let sec = 59;
let qtnNo = 0;
let maxQtnNo = parseInt(examData['no_qtn_req']);
let timeExpired = false;
let typ;
let timeHandler;
let curQtn;
let germanCounter;
let answers = {};

let startBtn = _('startBtn');
let startCon = _('startContainer');
let qtnCon = _('qtnContainer');
let qtnInd = _('qtnIndicator');
let passageDiv =_('passageDiv');
let questionDiv =_('questionDiv');
let optionDiv = _('optionDiv');
let linkDiv = _('linkDiv');
let prevBtn = _('prevBtn');
let nextBtn = _('nextBtn');
let subBtn = _('subBtn');
let token = _('token');
let msgDiv = _('msgDiv');
let timer = _('timer');


runFirst();

function runFirst(){
    let navs = '';
    for(let i=1;i<=maxQtnNo;i++){
        navs+='<span onclick="navigateToQtn('+i+')" class="qtnNavs" id="qtnNav'+i+'">'+i+'</span>';
    }
    linkDiv.innerHTML = navs;
}

function showTimer(){
    //display the time
    if(sec<10){
        timer.innerHTML = min + ':0'+sec;
    }else{
        timer.innerHTML = min + ':'+sec;
    }
    //end of display time

    //dynamically change the values of time
    if(sec === 0){
        sec = 59;
        if(min > 0){
            min--;
        }else if(min === 0){
            clearTimer();
        }
    }else{
        sec--;
    }
    //end of dynamically change the values of time
   
}


function clearTimer(){
    timeExpired = true;
    clearInterval(timeHandler);
    stopSubmission();
}

function stopSubmission(){
    subBtn.style.display = 'none';
    displayExpiredMessage();
}

function displayExpiredMessage(){
    timer.innerHTML = 'Time up!';
    timer.style.backgroundColor = 'rgb(200,0,0)';
    timer.style.borderRadius = '0px';
}

startBtn.addEventListener('click',startExam);

function startExam(){
    startCon.style.display = 'none';
    qtnCon.style.display = 'block';
    showQuestion();
    timeHandler = setInterval(showTimer,1000);
}

nextBtn.addEventListener('click', function(){
    let num = qtnNo+1;
    if(num > maxQtnNo){
        navigateToQtn(1);
    }else{
        navigateToQtn(num);
    }
    
});

prevBtn.addEventListener('click', function(){
    let num = qtnNo-1;
    if(num < 1){
        navigateToQtn(maxQtnNo);
    }else{
        navigateToQtn(num);
    }
    
});

subBtn.addEventListener('click', function(){
    let cont = confirm("Are you sure you want to submit");
    if(cont){
        storeAnswer();
        let postData = 'examid='+examData['exam_id']+'&examinee_id='+username+'&answers='+JSON.stringify(answers)+'&token='+token.value+'&timesallowed='+timesAllowed;
        ld_startLoading('subBtn');
        ajaxRequest('responses/add_completed_exam.rsp.php', addCompletedExamRsp,postData);
    }
});


//this handles the ajax response from the server
function addCompletedExamRsp(){
    ld_stopLoading('subBtn');
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    switch(rsp.statuscode){
        case 1: //indicate that it was successful and result is ready
            location.assign('success.php?examid='+examData['exam_id']+'&examineeid='+username+'&canViewResult=true');
        break;
        case 2:
            token.value = rsp.token;
            //output an error message using a div
            msgDiv.innerHTML = '<div class="failure">Something went wrong, Exam not submitted</div>';  
        break;
        case 3: //indicate that it was successful and result is not ready
            location.assign('success.php?examid='+examData['exam_id']+'&examineeid='+username+'&canViewResult=false');
        break;
    }
}


function showQuestion(){
    qtnNo+=1;
    qtnInd.innerHTML = 'Question '+qtnNo+' of '+maxQtnNo;
    curQtn = qtns[indexes[qtnNo-1]];
    let psg = curQtn['passage'].trim();
    if(psg.length > 0){
        passageDiv.className = 'isPassage';
        passageDiv.innerHTML = '<div style="font-weight: bold; text-align:center">Passage</div>'+psg;
        
    }else{
        passageDiv.className = 'noPassage';
    }
    questionDiv.innerHTML = curQtn['qtn'];
    typ = parseInt(curQtn['type']);
    //fill options
    fillOptions();
    //style the current nav
    currentSelectStyle();
    if(qtnNo === maxQtnNo && !timeExpired){
        subBtn.style.display = 'block';
    }

}


function navigateToQtn(num){
    afterSelectStyle();
    storeAnswer();
    qtnNo = num-1; //the removed back will be added back in showQuestion
    showQuestion();
    displayAnswer();
}



function displayAnswer(){
    let answer = answers[curQtn['id']];
    let objAns;
    let counter;
    switch(typ){
        case 1:
            switch(answer){
                case 'A':
                    _('optA').checked = true;
                break;
                case 'B':
                    _('optB').checked = true;
                break;
                case 'C':
                    _('optC').checked = true;
                break;
                case 'D':
                    _('optD').checked = true;
                break;
            }
        break;
        case 2:
            switch(answer){
                case 'true':
                    _('trueRad').checked = true;
                break;
                case 'false':
                    _('falseRad').checked = true;
                break;
            }
        break;
        case 3:
            if((answer !== null) && (answer !== undefined)){
                answer = JSON.parse(answer);
                let answered = false;
                for(let i=1;i<=germanCounter;i++){
                    if(answer['ans'+i].length > 0){
                            answered = true;
                            break;
                    }
                }

                if(answered){
                    counter = 1;
                    for (let key in answer) {
                        if(answer[key] !== undefined){
                            _('ans'+counter).value = answer[key]; 
                        }
                        counter++;
                    }
                }
            }
            
            
        break;
        case 4:
             if((answer !== null) && (answer !== undefined)){
                objAns = JSON.parse(answer);
                for(let key in objAns){
                    switch(key.substr(-1,1)){
                        case '1':
                            _('chk1').checked = true;
                        break;
                        case '2':
                            _('chk2').checked = true;
                        break;
                        case '3':
                            _('chk3').checked = true;
                        break;
                        case '4':
                            _('chk4').checked = true;
                        break;
                    }
                }
            }    
        break;
        case 5:
            if((answer !== null) && (answer !== undefined)){
                _('theoryAns').value = answer;
            }
    }

     //hide the necessary butttons
     nextBtn.style.display = 'block';
     prevBtn.style.display = 'block';
     if(qtnNo === maxQtnNo){
         nextBtn.style.display = 'none';
     }
     if(qtnNo === 1){
         prevBtn.style.display = 'none';
     }
     
}

function afterSelectStyle(){
    _('qtnNav'+qtnNo).className = 'afterselection';
}

function beforeSelectStyle(){
    _('qtnNav'+qtnNo).className = 'qtnNavs';
}

function storeAnswer(){
    let ans = getAnswers();
    if(ans === null){
        beforeSelectStyle();
    }
    if(typ === 3){ //German question is handle separately because it will never return undefined
        let answered = false;
        ans = JSON.parse(ans);
        for(let i=1;i<=germanCounter;i++){
           if(ans['ans'+i].length > 0){
                answered = true;
                break;
           }
        }
        if(!answered){
            beforeSelectStyle();
        }
        ans = JSON.stringify(ans);
    }
    answers[curQtn['id']] = ans;
}

//this function helps get the answers and format the answers based on the question typ
//it returns undefined if an answer has not been selected, it returns the answer(s) if it has been selected
function getAnswers(){
    switch(typ){
        
        case 1:
            if(_('optA').checked){
                return 'A';
            }
            if(_('optB').checked){
                return 'B';
            }  
            if(_('optC').checked){
                return 'C';
            }  
            if(_('optD').checked){
                return 'D';
            }
            return null;  
        case 2:
             if(_('trueRad').checked){
                return 'true';
             }
             if(_('falseRad').checked){
                return 'false';
             }
             return null; 
        case 3:
            let ans = {};
            let val;
            for(let i=1;i<=germanCounter;i++){
                val = (_('ans'+i).value).trim();
                ans['ans'+i] = val;
            }

            if(objLength(ans) > 0){
                return JSON.stringify(ans);
            }
            return null;
        case 4:
           let ans_ = {};
           let val_='';
            for(let i=1;i<=4;i++){
                if(_('chk'+i).checked){
                    val_ = (_('opt'+i).value).trim();
                }
                if(val_.length > 0){
                    ans_['ans'+i] = val_;
                } 
                val_='';
            }
            if(objLength(ans_) > 0){
                return JSON.stringify(ans_);
            }
            return null; 
        case 5:
            let theoryAns = _('theoryAns').value;
            if(theoryAns.trim().length > 0){
                return theoryAns;
            }
            return null;
    }
}

function currentSelectStyle(){
    _('qtnNav'+qtnNo).className = 'selected';
}

function fillOptions(){
    optionDiv.innerHTML='';
    let options;
    switch(typ){
        case 1:
            options = JSON.parse(curQtn['options']);
            optionDiv.appendChild(mulChoBox('A','optA',options['opt1']));
            optionDiv.appendChild(mulChoBox('B','optB',options['opt2']));
            optionDiv.appendChild(mulChoBox('C','optC',options['opt3']));
            optionDiv.appendChild(mulChoBox('D','optD',options['opt4']));
        break;
        case 2:
            optionDiv.appendChild(trueFalseBox('True','trueRad'));
            optionDiv.innerHTML += ' ';
            optionDiv.appendChild(trueFalseBox('False','falseRad'));
        break;
        case 3:
            germanQuestion();
        break;
        case 4:
            options = JSON.parse(curQtn['options']);
            optionDiv.appendChild(multiChoiceBox(1,options['opt1']));
            optionDiv.appendChild(multiChoiceBox(2,options['opt2']));
            optionDiv.appendChild(multiChoiceBox(3,options['opt3']));
            optionDiv.appendChild(multiChoiceBox(4,options['opt4']));
        break;
         case 5:
            optionDiv.innerHTML = '<textarea id="theoryAns"></textarea>';
        break;
        
    }
}


function germanQuestion(){
    let question = curQtn['qtn'];
    germanCounter = (question.match(/_+/g)).length;
    for(let i=1;i<=germanCounter;i++){
        question = question.replace(/_+/,'<input type="text" class="germanAns" id="ans'+i+'"/>');
    }
    questionDiv.innerHTML = question;
}



function mulChoBox(labVal,radioId, ans){
    let box = document.createElement('div');
    let lab = document.createElement('label');
    lab.innerHTML+= labVal+' ';
    let rad = document.createElement('input');
    rad.type='radio';
    rad.id=radioId;
    rad.name = 'multichoice';
    let answerLab = document.createElement('label');
    answerLab.for=radioId;
    answerLab.appendChild(rad);
    answerLab.innerHTML += ' '+ans;
    box.appendChild(lab); 
    box.appendChild(answerLab);
    return box;    
}


function trueFalseBox(labVal,id){
    let spanBox = document.createElement('span');
    let rad = document.createElement('input');
    rad.type='radio';
    rad.id=id;
    rad.name='radBtn';
    let lab = document.createElement('label');
    lab.for = id;
    lab.appendChild(rad);
    lab.innerHTML += labVal;
    spanBox.appendChild(lab);
    return spanBox;
}

function multiChoiceBox(num,opt){
    let box = document.createElement('div');
    let lab = document.createElement('label');
    lab.for = 'chk'+num;
    lab.innerHTML = opt +' ';
    let chk = document.createElement('input');
    chk.type = 'checkbox';
    chk.id='chk'+num;
    //create a hidden input to store the value
    let hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.value = opt;
    hiddenInput.id = 'opt'+num;
    //end of hidden input field
    lab.appendChild(chk);
    box.appendChild(lab);
    box.appendChild(hiddenInput);
    return box;
}