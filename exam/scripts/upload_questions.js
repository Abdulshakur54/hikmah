const uploadBtn = _('uploadBtn');
const downloadBtn = _('downloadBtn');
const browseBtn = _('uploadedFile');
const questionContainer = _('questionContainer');
const pubEditContainer = _('pubEditContainer');
const msgDiv = _('msgDiv');
const token = _('token');
const examId = _('examId');
const editExamBtn = _('editExamBtn');
const publishExamBtn = _('publishExamBtn');
uploadBtn.addEventListener('click',function(){
    if(!window.FormData){
        questionContainer.innerHTML = '<div class="message">Upgrade your browser to be able to upload files</div>';
    }else{
        const formData = new FormData();
        const filesArray = browseBtn.files;
        if(filesArray.length > 0){
            formData.append('uploadedFile',filesArray[0],filesArray[0].name);
            formData.append('token',token.value);
            formData.append('examid',examId.value);
            ld_startLoading('uploadBtn');
            ajaxRequestWithUpload('responses/upload_questions.rsp.php', uploadQuestionsRsp,formData);
        }else{
            msgDiv.innerHTML = '<div class="failure">Choose a file first</div>';
        }
    }
    
});

downloadBtn.addEventListener('click', function(){
    location.assign('upload_questions.php?examid='+examId.value+'&download=true');
});

function uploadQuestionsRsp(){
    ld_stopLoading('uploadBtn');
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(!rsp.success){
        msgDiv.innerHTML = '<div class="failure">'+rsp.message+'</div>';
    }else{
        questionContainer.style.display = 'none';
        pubEditContainer.style.display='block';
    }
}

editExamBtn.addEventListener('click',function(){
    location.assign("edit_exam.php?examid="+examId.value+'&token='+token.value);
});
publishExamBtn.addEventListener('click',function(){
    location.assign("publish_exam.php?examid="+examId.value+'&token='+token.value);
});
