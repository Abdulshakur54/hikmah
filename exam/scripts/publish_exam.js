let pubBtn = _('publishBtn');
let msgDiv = _('message');
let pubCon = _('publishContainer');
let counter = _('counter');
let examId = _('examId');
let token = _('token');
let typ = _('type');
let instruction = _('instruction');
let transfer = _('transfer');

pubBtn.addEventListener('click',publishExam);

function publishExam(){
    if(confirm('This will publish '+examId.value+' exam')){
        let trimmedInstruction = (instruction.value.trim());
        if(trimmedInstruction.length > 0){
            let idObj = {"id":popId()};
            let postData = 'idarr='+JSON.stringify(idObj)+'&examid='+_('examId').value+'&type='+typ.value+'&instruction='+trimmedInstruction+'&token='+token.value+'&transfer='+transfer.value;
            ld_startLoading('publishBtn');
            ajaxRequest('responses/publish_exam.rsp.php', pubExamRsp,postData);
        }else{
            alert('Instruction has to be provided');
        }
    }   
}


//this function is used to filter selection, it is to be available for APM
function reSubmit(){
    location.assign('publish_exam.php?examid='+examId.value+'&sch_abbr='+_('sch_abbr').value+'&level='+_('level').value);
}


//this function is available for the apm
function populateLevel(e){
    let genHtml = '<option value ="ALL">ALL</option>';
    let levObj = getLevels(e.value);
    if(objLength(levObj) > 0){
        for(let objName in levObj){
            genHtml += '<option value="'+levObj[objName]+'">'+objName+'</option>';
        }
    }
    
    
    //insert the generated html into level select element
    _('level').innerHTML = genHtml;
}


function popId(){
    let count = counter.value;
    let valObj = [];
    for(let i=1;i<=count;i++){
        if(_('chk'+i).checked){
           valObj.push(_('val'+i).value); 
        }
    }
    return valObj;
}

function pubExamRsp(){
    ld_stopLoading('publishBtn');
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(rsp.statuscode === 1){
        pubCon.style.display = 'none';
        msgDiv.innerHTML = '<span class="success">Successfully published exam</span>';
    }else{
        msgDiv.innerHTML = '<span class="failure">Exam not published, something went wrong</span>';
    }
}
