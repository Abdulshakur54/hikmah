let counter = _('counter');
let token = _('token');
let nameIndicator = _('nameIndicator');
let errMsg = _('errMsg');
let hiddenFileName = _('hiddenFileName');
let form = document.querySelector('form');

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


function filterDisplay(){
     _('filter').value = 'true';
     form.submit();
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

function deleteAttachment(id){
    alert(id);
}

function displayName(event,size){
   if(objLength(event.files) > 0){
       if(event.files[0].size > size*1024*1024){
           nameIndicator.innerHTML = '';
           errMsg.innerHTML = '<div class="failure">File should not be more than '+size+'MB</div>';
           return;
       }
       errMsg.innerHTML = '';
       hiddenFileName.value = 'hasvalue';
       nameIndicator.innerHTML = event.files[0].name;
   }else{
          hiddenFileName.value = '';
          errMsg.innerHTML = '<div class="failure">First select a file</div>';
   }

}


//helps to ensure a file is selected
function fileSelected(){
    if(!emp(hiddenFileName.value)){
         errMsg.innerHTML = '';
         return true;
    }else{
         errMsg.innerHTML = '<div class="failure">Select a File</div>';
         return false;
    }
}

//function that uploads attachment
function uploadFile(){
    if(fileSelected()){
     _('attachment').value = 'true';
     form.submit();
    }
}


function deleteAttachment(id){
    if(confirm('This will delete the target file')){
        location.assign('admission_attachment.php?idToDelete='+id+'&delete=true');
    }
   
}

