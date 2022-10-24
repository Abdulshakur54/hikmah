function runFirst(){
  const user_type = document.getElementById('userType').value;
  $(".js-example-basic-single").select2();
  const children = document.getElementsByClassName("tab")[0].children;
  for (let child of children) {
    child.classList.remove("active");
  }
  localStorage.setItem("user_type", user_type);
  document.getElementById(user_type).className = "active";
}
runFirst();

function showImage(event) {
   const image = _("image");
   const hiddenPic = _("hiddenPic");
   const picMsg = _("picMsg");
  if (objLength(event.files) > 0) {
    if (event.files[0].size > 100 * 1024) {
      image.style.display = "none";
      picMsg.innerHTML =
        '<div class="failure">Picture should not be more than 100kb</div>';
      return;
    }
    picMsg.innerHTML = "";
    hiddenPic.value = "hasvalue";
    image.src = URL.createObjectURL(event.files[0]);
    image.style.display = "block";
  } else {
    hiddenPic.value = "";
    picMsg.innerHTML = '<div class="failure">Select a Picture</div>';
    image.style.display = "none";
  }
}


function changeContent(userType){
  location.assign('register.php?user_type='+userType);
}

function register(event) {
  event.preventDefault();
  if (validate("regForm", { validateOnSubmit: true })) {
    event.target.submit();
  }
 
}

function populateLGA(obj){
  const stateId = obj.value;
  if(stateId.trim().length > 0){
     ajaxRequest(
       "responses/responses.php",
       handleLGAListResponse,
       `op=get_lga_list&state_id=${stateId}&token=${token.value}`
     );
  }

   function handleLGAListResponse(){
     const rsp = JSON.parse(xmlhttp.responseText);
      _("token").value = rsp.token;
     if (rsp.status === 200) {
       const lgaContainer = _('lga');
       const lgas = rsp.data;
       let output = ``;
       for(let lga of lgas){
        output += `<option value="${lga.id}">${lga.name}</option>`;
       }
       lgaContainer.innerHTML = output;
     }
     
   }
}

validate("regForm");