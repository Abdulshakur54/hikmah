
let token = _('token');
let examId = _('examId');
let examPassMark = _('examPassMark');
let distQtnIds = _('distQtnIds');
let boxNumsBox = _('boxNumsBox');
let submitAllBtn = _('submitAllBtn');
let saveAllBtn = _('saveAllBtn');

const uploadBtn = _('uploadBtn');
const buttonContainer = _('buttonContainer');
const browseBtn = _('uploadedFile');
const msgDiv = _('msgDiv');
const msgAll = _('msgAll');

function saveData(id){
    _('msg_'+id).innerHTML = ''; //dynamically empty message div for a distict question
    msgDiv.innerHTML = ''; //empty the message div for upload
    let valData = getValidData(id)
    let maxMark = _('maxMark_'+id).value;
    let postData = 'data='+JSON.stringify(valData)+'&action=save&token='+token.value+'&examid='+examId.value+'&qtnid='+id+'&maxmark='+maxMark;
    ld_startLoading(['saveBtn_'+id,'submitBtn_'+id,'closeBtn_'+id],'ld_loader_'+id);
    ajaxRequest('responses/mark.rsp.php', saveMarkingsRsp,postData);
}


function saveAll(){
    msgAll.innerHTML = ''; //dynamically empty message div for a distict question
    msgDiv.innerHTML = ''; //empty the message div for upload
    let qtnIds = JSON.parse(distQtnIds.innerHTML);
    let allData = {};
    let allMark = {};
    for(let qtnId of qtnIds){
       allData[qtnId] = getValidData(qtnId);
       allMark[qtnId] = _('maxMark_'+qtnId).value;
    }
    let postData = 'allData='+JSON.stringify(allData)+'&maximumMarks='+JSON.stringify(allMark)+'&allBoxNum='+boxNumsBox.innerHTML+'&action=save&token='+token.value+'&examid='+examId.value;
    ld_startLoading(['saveAllBtn','submitAllBtn'],'ld_loader_all');
    ajaxRequest('responses/mark_all.rsp.php', saveAllMarkingsRsp,postData);
}


function saveAllMarkingsRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    ld_stopLoading(['saveAllBtn','submitAllBtn'],'ld_loader_all');
    if(rsp.success){
        msgAll.className = 'successDiv';
        msgAll.innerHTML = 'changes have been saved';
         
    }else{
        msgAll.className = 'failureDiv';
        msgAll.innerHTML = rsp.message;
    }
}


function submitAll(){
    
    let confirmed = confirm('Do you want to submit');
    if(confirmed){
        msgAll.innerHTML = ''; //dynamically empty message div for a distict question
        msgDiv.innerHTML = ''; //empty the message div for upload
        let qtnIds = JSON.parse(distQtnIds.innerHTML);
        let allData = {};
        let allMark = {};
        for(let qtnId of qtnIds){
           allData[qtnId] = getValidData(qtnId);
           allMark[qtnId] = _('maxMark_'+qtnId).value;
        }
        let postData = 'allData='+JSON.stringify(allData)+'&maximumMarks='+JSON.stringify(allMark)+'&allBoxNum='+boxNumsBox.innerHTML+'&action=submit&token='+token.value+'&examid='+examId.value;
        ld_startLoading(['saveAllBtn','submitAllBtn'],'ld_loader_all');
        ajaxRequest('responses/mark_all.rsp.php', submitAllMarkingsRsp,postData);
    }
   
}


function submitAllMarkingsRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    ld_stopLoading(['saveAllBtn','submitAllBtn'],'ld_loader_all');
    if(rsp.success){
        //redirect the page and attach submission success message
        location.assign('mark.php?token='+token.value+'&examid='+examId.value+'&submissionsuccessful=successful');
    }else{
        msgAll.className = 'failureDiv';
        msgAll.innerHTML = rsp.message;
    }
}

function submitData(id){
    let confirmed = confirm('Do you want to submit');
    if(confirmed){
        _('msg_'+id).innerHTML = ''; //dynamically empty message div for a distict question
        msgDiv.innerHTML = ''; //empty the message div for upload
        let valData = getValidData(id);
        let maxMark = _('maxMark_'+id).value;
        let postData = 'data='+JSON.stringify(valData)+'&action=submit&token='+token.value+'&examid='+examId.value+'&qtnid='+id+'&maxmark='+maxMark+'&passmark='+examPassMark.value;
        ld_startLoading(['saveBtn_'+id,'submitBtn_'+id,'closeBtn_'+id],'ld_loader_'+id);
        ajaxRequest('responses/mark.rsp.php', submitMarkingsRsp,postData);
    }
    
}

function saveMarkingsRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    let id = rsp.qtnid;
    token.value = rsp.token;
    let msgDiv = _('msg_'+id);
    ld_stopLoading(['saveBtn_'+id,'submitBtn_'+id,'closeBtn_'+id],'ld_loader_'+id);
    if(rsp.success){
        msgDiv.className = 'successDiv';
        msgDiv.innerHTML = 'changes have been saved';
         
    }else{
        msgDiv.className = 'failureDiv';
        msgDiv.innerHTML = rsp.message;
    }
}

function submitMarkingsRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    let id = rsp.qtnid;
    token.value = rsp.token;
    ld_stopLoading(['saveBtn_'+id,'submitBtn_'+id,'closeBtn_'+id],'ld_loader_'+id);
    if(rsp.success){
       _('wrapper_'+id).style.display = 'none';
       alert('Submission was successful');
         
    }else{
        let msgDiv = _('msg_'+id);
        msgDiv.className = 'failureDiv';
        msgDiv.innerHTML = rsp.message;
    }
}

function removeQtn(id){
    _('wrapper_'+id).style.display = 'none';
}

function getValidData(id){
    let valData = {};
    let data = JSON.parse(_('qtns_'+id).innerHTML); //this will return an array of examinee id;
    let len = data.length;
    for(let i=0;i<len;i++){
        let examineeId = data[i];
        valData[examineeId] = [_('comm_'+examineeId+id).value, _('score_'+examineeId+id).value];
    }
    return valData;
}



function downloadTemplate(){
    location.assign('mark.php?download=true&token='+token.value+'&examid='+examId.value);
}


//upload functionalities

uploadBtn.addEventListener('click',function(){
    msgDiv.innerHTML = '';//empty the message div for upload
    if(!window.FormData){
        buttonContainer.innerHTML = '<div class="message">Upgrade your browser to be able to upload files</div>';
    }else{
        const formData = new FormData();
        const filesArray = browseBtn.files;
        if(filesArray.length > 0){
            formData.append('uploadedFile',filesArray[0],filesArray[0].name);
            formData.append('token',token.value);
            formData.append('examid',examId.value);
            formData.append('passmark',examPassMark.value);
            ld_startLoading('uploadBtn','ld_loader_upload');
            ajaxRequestWithUpload('responses/mark_theory.rsp.php', markTheoryRsp,formData);
        }else{
            msgDiv.innerHTML = '<div class="failure">Choose a file first</div>';
        }
    }
    
});


function markTheoryRsp(){
    ld_stopLoading('uploadBtn','ld_loader_upload');
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(!rsp.success){
        msgDiv.innerHTML = '<div class="failure">'+rsp.message+'</div>';
    }else{
        //redirect the page and attach upload success message
        location.assign('mark.php?token='+token.value+'&examid='+examId.value+'&upload=successful');
    }
}
