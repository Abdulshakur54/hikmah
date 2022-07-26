let form = _('form');
let updatedData = _('updateddata');
let scrIds = [];
let hasProject;
let tokenElement = _('token');
let msgDiv = _('genMsg');
let importMsgDiv = _('importMsg');
let subid = _('subid');
let scoreColumns = JSON.parse(_('scorecolumns').innerHTML);

function runFirst(){
    hasProject = _('hasProject').value;   
}

function update(scrId){
    if(!scrIds.includes(scrId)){
        scrIds.push(scrId);
    }   
}

function convertToValidFloat(x){
    if(x!==''){
        return parseFloat(x);
    }
}

        
function saveData(){
    importMsgDiv.innerHTML=''; //clears any message associated with import if there is
    if(confirm('Are you sure you want to save changes')){
        let updData = [];
        for(let x of scrIds){
            let scrArr = []; //to hold all the scores as an array
            for(let y of scoreColumns){
                scrArr.push(convertToValidFloat(_(y+'_'+x).value));
            }
            updData.push([x,scrArr]);
        }
        let updDataObj = {};
        updDataObj["updatedData"] = updData;
        let postData = 'updatedData='+JSON.stringify(updDataObj)+'&subid='+_('subid').value+'&token='+tokenElement.value;
        ld_startLoading('save','save_loader');
        ajaxRequest('responses/update_scores.rsp.php', saveRsp,postData);
    }
    
}

function saveRsp(){
    let rsp = JSON.parse(xmlhttp.responseText);
    tokenElement.value = rsp.token;
    ld_stopLoading('save','save_loader');
    switch(rsp.statuscode){
        case 1:
           msgDiv.innerHTML='<div class="success">Changes saved successfully</div>';
           break;
       case 0:
           let errors = rsp.errors;
           let msg = '';
           for(let x of errors){
               msg+='<div class="failure">'+x+'</div>'
           }
           msgDiv.innerHTML = msg;
           break;
           
    }
    
}


const importFile = (obj)=>{
     
     if(!window.FormData){
        importMsgDiv.innerHTML = '<div class="failure">Upgrade your browser to be able to upload files</div>';
    }else{
        const formData = new FormData();
        const filesArray = obj.files;
        if(filesArray.length > 0){
            formData.append('uploadedFile',filesArray[0]);
            formData.append('token',tokenElement.value);
            formData.append('subid',_('subid').value);
            importMsgDiv.innerHTML = '';
            ld_startLoading('importTrigger','import_loader');
            ajaxRequestWithUpload('responses/import_scores.rsp.php', uploadScoresRsp,formData);
        }else{
            importMsgDiv.innerHTML = '<div class="failure">Choose a file first</div>';
        }
    }
};

const uploadScoresRsp = () =>{
    let rsp = JSON.parse(xmlhttp.responseText);
    tokenElement.value = rsp.token;
    ld_stopLoading('importTrigger','import_loader');
    switch(rsp.statuscode){
        case 1:
           location.assign('scores.php?subid='+subid.value);
           break;
       case 0:
           let errors = rsp.errors;
           let msg = '';
           for(let x of errors){
               msg+='<div class="failure">'+x+'</div>'
           }
           importMsgDiv.innerHTML = msg;
           break;
           
    }
}

const downloadScores = () =>{
    location.assign('scores.php?subid='+subid.value+'&download=true');
};

runFirst();


