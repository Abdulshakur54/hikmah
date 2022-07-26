let addPassage = _('addPassage');
let passageDiv = _('passage');
let showPassage = _('showPassage');
let qtnType = _('qtnType');
let optionBox = _('optionBox');
let qtnText = _('qtnText');
let qtnIndicator = _('qtnIndicator');
let token = _('token');
let passage = _('psgText');
let msgQtn = _('msgQtn');
let msgGen = _('msgGen');
let mark = _('mark');
let optMsg = _('optMsg');
let hiddenDetails = _('hiddenDetails');
let addBtn = _('addBtn');
let editExamBtn = _('editExamBtn');
let publishExamBtn = _('publishExamBtn');
let questionContainer = _('questionContainer');
let pubEditContainer = _('pubEditContainer');
let msgBox = _('msgBox');

let typ = 1;
let germanCounter = 1;
let mrk = 1;
let qtnNo = parseInt(_('qtnNo').value) + 1; //adds 1 to the real qtn no just for user experience
let examData;

let qtnBool = false;

let psgBox = _('psgBox');
let passageIsOn = false;
let passageShow = true;

runFirst();

addBtn.addEventListener('click',addQuestion);
addPassage.addEventListener('click',togglePassage);
showPassage.addEventListener('click',showPsg);
qtnType.addEventListener('change',toggleType);
mark.addEventListener('change',changeMark);
qtnText.addEventListener('blur',germanQtn);
qtnText.addEventListener('blur',function(){
    if(validate(qtnText,{"name":"Question","required":true},msgQtn)){
        qtnBool = true;
    }else{
        qtnBool = false;
    }
});
editExamBtn.addEventListener('click',function(){
    location.assign("edit_exam.php?examid="+examData.exam_id+'&token='+token.value);
});
publishExamBtn.addEventListener('click',function(){
    location.assign("publish_exam.php?examid="+examData.exam_id+'&token='+token.value);
});


function togglePassage(){
    if(passageIsOn){
        passageDiv.style.display = 'none';
        passageIsOn = false;
    }else{
        passageDiv.style.display = 'block';
        passageIsOn = true;
    } 
}


function showPsg(){
    if(passageShow){
        psgBox.style.display = 'none';
        passageShow = false;
    }else{
        psgBox.style.display = 'block';
        passageShow = true;
    } 
}


function toggleType(){
    optionBox.innerHTML='';
    msgGen.innerHTML=''; //empty the message box
    changeType(); //store the type of the question for reference
    switch(typ){
        case 1:
            optMsg.innerHTML = 'Options with Answer';
            optionBox.appendChild(mulChoBox('A','optA','optTextA'));
            optionBox.appendChild(mulChoBox('B','optB','optTextB'));
            optionBox.appendChild(mulChoBox('C','optC','optTextC'));
            optionBox.appendChild(mulChoBox('D','optD','optTextD'));
        break;
        case 2:
            optMsg.innerHTML = 'Answer';
            optionBox.appendChild(trueFalseBox('True','trueRad'));
            optionBox.innerHTML += ' ';
            optionBox.appendChild(trueFalseBox('False','falseRad'));
        break;
        case 3:
            optMsg.innerHTML = 'Answer(s)';
            germanQuestion();
        break;
        case 4:
            optMsg.innerHTML = 'Options with Answer(s)';
            for(let i=1;i<=4;i++){
                optionBox.appendChild(multiChoiceBox(i));
            }
        break
        case 5:
           optMsg.innerHTML = 'Answer';
           optionBox.innerHTML = '<textarea id="theoryAns"></textarea>';
        break;
        
    }
}



function mulChoBox(labVal,radioId, textId){
    let box = document.createElement('div');
    let lab = document.createElement('label');
    lab.for=radioId;
    let rad = document.createElement('input');
    rad.type='radio';
    rad.id=radioId;
    rad.name = 'multichoice';
    let txt = document.createElement('input');
    txt.type='text';
    txt.id = textId;
    lab.appendChild(rad);
    lab.innerHTML+= ' '+labVal+' ';
    box.appendChild(lab); 
    box.appendChild(txt);
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


function runFirst(){
    examData = JSON.parse(hiddenDetails.innerHTML);
    optionBox.appendChild(mulChoBox('A','optA','optTextA'));
    optionBox.appendChild(mulChoBox('B','optB','optTextB'));
    optionBox.appendChild(mulChoBox('C','optC','optTextC'));
    optionBox.appendChild(mulChoBox('D','optD','optTextD'));
    controlInterface(); //this controls what the user should interact with
}


function germanBox(num){
    let box = document.createElement('div');
    let lab = document.createElement('label');
    lab.innerHTML = 'Answer '+num+':';
    lab.for = 'ans'+num;
    let txt = document.createElement('input');
    txt.type='text';
    txt.id='ans'+num;
    box.appendChild(lab);
    box.innerHTML+=' ';
    box.appendChild(txt);
    return box;
}


function multiChoiceBox(num){
    let box = document.createElement('div');
    let lab = document.createElement('label');
    lab.innerHTML = 'option '+num+':';
    let txt = document.createElement('input');
    txt.type='text';
    txt.id='opt'+num;
    let chk = document.createElement('input');
    chk.type = 'checkbox';
    chk.id='chk'+num;
    box.appendChild(lab);
    box.innerHTML+=' ';
    box.appendChild(txt);
    box.appendChild(chk);
    return box;
}


function changeMark(){
    msgGen.innerHTML=''; //empty the message box
    mrk = parseInt(mark.value);
}


////this function calculates and returns the total mark for a qtn, it calculates based on the question type
//function calMark(){
//    switch(typ){
//        case 3:
//            return mrk * germanCounter;
//        case 4:
//            return mrk * countTicked();
//        default:
//            return mrk;
//    }
//}


//this function returns the no of answer ticked for multiple answers type question
function countTicked(){
    let x=0;
    for(let count=1;count<=4;count++){
        if(_('chk'+count).checked){
            x++;
        }
    }
    return x;
}


function germanQtn(){
    msgGen.innerHTML=''; //empty the message box
    let txt = qtnText.value;
    if(txt.length > 0 && txt.search(/_{2,}/) > -1){  //check it the __ is present
        optionBox.innerHTML='';
        germanQuestion();
        qtnType.value = 3; //change the select box to german
        changeType();
    }
}


function germanQuestion(){
    let txt = qtnText.value;
    if(txt.length > 0 && txt.search(/_{2,}/) > -1){  //check it the __ is present
        let matches = txt.match(/_{2,}/g); //returns an array of matches found
        germanCounter = matches.length;
    }else{
        germanCounter = 1;
    }
    for(let i=1;i<=germanCounter;i++){
        optionBox.appendChild(germanBox(i));
    }
    let lab = document.createElement('label');
    lab.for = 'ans_ord';
    lab.innerHTML = 'Answer in order: <input type="checkbox" id="ans_ord"/>';
    optionBox.appendChild(lab);
}


function ansInOrder(){
    if(typ===3){
        if(_('ans_ord').checked){
            return true;
        }
    }
    return false;
}


function changeType(){
    typ = parseInt(qtnType.value);
}


//this function add questions to a particular exam
function addQuestion(){
    //codes to add question here
    let options = getOptions();
    let answers = getAnswers();
    if(qtnBool && options !== undefined && answers !== undefined){
        msgGen.innerHTML = '';
        let qtn = qtnText.value;
        
        let postData = 'examid='+examData.exam_id+'&qtn='+encodeURIComponent(qtn)+'&type='+typ+'&options='+encodeURIComponent(options)+'&answers='+encodeURIComponent(answers)+'&answerorder='+ansInOrder()+'&mark='+mrk+'&passage='+encodeURIComponent(passage.value)+'&token='+token.value;
        ld_startLoading('addBtn');
        ajaxRequest('responses/add_questions.rsp.php', addQuestionRsp,postData);
    }else{
        msgGen.innerHTML = '<span class="failure">Ensure the options are complete and the answer is selected</span>';
    }
}


//this function handles ajax response after query to add question to the ex_question table
function addQuestionRsp(){
    ld_stopLoading('addBtn');
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    switch(rsp.statuscode){
        case 1:
            qtnNo++;
            qtnIndicator.innerHTML = 'Question '+qtnNo+'/'+ examData.no_qtn_added;
            clearDivs();
            msgGen.innerHTML = '<span class="success">Question ' + (qtnNo-1) +' added successfully</span>';
        break;
        case 2:
        msgGen.innerHTML = '<span class="failure">You have exceeded the no of questions to be added</span>';
        break;
        default:
            msgGen.innerHTML = '<span class="failure">Something went wrong</span>';
    }
    controlInterface('<span class="success">You have completey added the required Questions</span>'); //this controls what the user should interact with
}

function clearDivs(){
    qtnText.value = '';
    switch(typ){
        case 1:
            let txtId;
            let radId;
            for(let i=1;i<=4;i++){
                switch(i){
                    case 1:
                        txtId = 'optTextA';
                        radId = 'optA';
                    break;
                    case 2:
                        txtId = 'optTextB';
                        radId = 'optB';
                    break;
                    case 3:
                        txtId = 'optTextC';
                        radId = 'optC';
                    break;
                    case 4:
                        txtId = 'optTextD';
                        radId = 'optD';
                    break;
                }
                _(txtId).value = '';
                _(radId).checked = false;
            }
        break;
        case 2:
            _('trueRad').checked = false;
            _('falseRad').checked = false;
        break;
        case 3:
            optionBox.innerHTML='';
            break;
        case 4:
            for(let i=1;i<=4;i++){
                _('opt'+i).value='';
                _('chk'+i).checked=false;
            }
        case 5:
            _('theoryAns').value = '';
    }
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
            return undefined;  
        case 2:
             if(_('trueRad').checked){
                return true;
             }
             if(_('falseRad').checked){
                return false;
             }
             return undefined; 
        case 3:
            let ans = {};
            let val;
            for(let i=1;i<=germanCounter;i++){
                val = (_('ans'+i).value).trim();
                if(val.length > 0){
                    ans['ans'+i] = val;
                }else{
                    return undefined;
                } 
            }

            if(objLength(ans) > 0){
                return JSON.stringify(ans);
            }
            return undefined;
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
            return undefined; 
        case 5:
           let ansVal =  _('theoryAns').value;
           if(ansVal.trim().length > 0){
               return ansVal;
           }
           return undefined;
    }
}


function getOptions(){
    let opt = {};
    let val;
    switch(typ){
        case 1:
            let txtId;
            for(let i=1;i<=4;i++){
                switch(i){
                    case 1:
                        txtId = 'optTextA';
                    break;
                    case 2:
                        txtId = 'optTextB';
                    break;
                    case 3:
                        txtId = 'optTextC';
                    break;
                    case 4:
                        txtId = 'optTextD';
                    break;
                }
                val = (_(txtId).value).trim();
                if(val.length>0 && noScripts(val)){
                    opt['opt'+i] = val;
                }else{
                    return undefined;
                } 
            }
            if(objLength(opt) > 0){
                return JSON.stringify(opt);
            }
            return undefined; 
        case 4:
            for(let i=1;i<=4;i++){
                val = (_('opt'+i).value).trim();
                if(val.length>0 && noScripts(val)){
                    opt['opt'+i] = val;
                }else{
                    return undefined;
                } 
            }
            if(objLength(opt) > 0){
                return JSON.stringify(opt);
            }
            return undefined;
        default:
            return ''; 
    }
}


//this function returns through if the max no of qtn to be added to an exam has been reached
function maxQtnReached(){
    if(qtnNo > examData.no_qtn_added){
        return true;
    }else{
        return false;
    }
}

//this function hides add question interface and shows the publish or edit interface
//it is to be called when max no of qtn required has been added
function hideQtnInterface(msg){
    questionContainer.style.display = 'none';
    pubEditContainer.style.display = 'block';
    msgBox.innerHTML = msg;
} 

//this controls what the user should interact with
function controlInterface(msg='<span class="message"> Maximum no of questions have been added</span>'){
    if(maxQtnReached()){
        hideQtnInterface(msg); //hides the add question interface
    }
}