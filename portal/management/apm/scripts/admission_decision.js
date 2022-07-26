let acceptAdm = _('acceptAdm');
let declineAdm = _('declineAdm');
let counter = _('counter');
let token = _('token');
let form = _('form');
let msg = _('msg');

acceptAdm.addEventListener('click', acceptAdmission);
declineAdm.addEventListener('click', declineAdmission);

function acceptAdmission(){
    if(confirm('This will Admit the marked applicants to the school')){
        let idObj = {"id":popId()};
        let postData = 'idarr='+JSON.stringify(idObj)+'&token='+token.value+'&type=accept';
        ld_startLoading('acceptAdm');
        ajaxRequest('responses/admission_decision.rsp.php', acceptAdmRsp,postData);
    }   
}

function declineAdmission(){
    if(confirm('This will decline the admission request of the marked students')){
        let idObj = {"id":popId()};
        let postData = 'idarr='+JSON.stringify(idObj)+'&token='+token.value+'&type=decline';
        ld_startLoading('declineAdm');
        ajaxRequest('responses/admission_decision.rsp.php', declineAdmRsp,postData);
    }   
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


function reSubmit(){
    location.assign('admission_decision.php?sch_abbr='+_('sch_abbr').value+'&level='+_('level').value);
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

function checkAll(event){
    let count = counter.value;
    if(event.checked){
        for(let i=1;i<=count;i++){
           _('chk'+i).checked = true;
        }
    }else{
        for(let i=1;i<=count;i++){
           _('chk'+i).checked = false;
        }
    }
}

function acceptAdmRsp(){
    ld_stopLoading('acceptAdm');
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(rsp.success){
        alert('Operation was successful\nAn email has been sent to the selected students to respond to the offer');
        form.submit();
    }else{
       msg.innerHTML = '<div class="failure">Operation was not successful</div>';
    }
}

function declineAdmRsp(){
  ld_stopLoading('declineAdm');
  let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if(rsp.success){
       alert('Declination was successful\nThe selected student have been notified of the declination via email');
       form.submit();
       
    }else{
       msg.innerHTML = '<div class="failure">Operation was not successful</div>';
    }
}

