let pageContainer = document.getElementById("page");
let pageToken = document.getElementById("page_token");
let customJSContainer = document.getElementById("customJS");
function getPage(url, scripts = "", useLastScripts = false) {
  if (url.indexOf("?") === -1) {
    url += "?page_token=" + pageToken.value;
  } else {
    url += "&page_token=" + pageToken.value;
  }
  if (useLastScripts == false) {
    if (sessionStorage.getItem("scripts") !== scripts) {
      sessionStorage.setItem("alt_scripts", sessionStorage.getItem("scripts"));
      sessionStorage.setItem("scripts", scripts);
    }
  }
  ajaxRequest(url, loadPage);
}

function loadPage() {
  const rsp = xmlhttp.responseText;
  if (rsp.substr(rsp.length - 15) === "status_code:200") {
    //15 is the lenght of the string 'status_code:200'
    pageContainer.innerHTML = rsp.substr(0, rsp.length - 79); //79 is gotten from the addition of 'page token':64 and status code lenght
  } else {
    pageContainer.innerHTML = rsp;
  }
  pageToken.value = rsp.substr(rsp.length - 79, 64);
  let customScripts = sessionStorage.getItem("scripts").trim()
  if(customScripts != 'null'){
    customScripts = customScripts.split("|");
    if (customScripts.length > 0) {
      customJSContainer.innerHTML = "";
      for (let script of customScripts) {
        const scriptNode = document.createElement("script");
        scriptNode.src = script;
        customJSContainer.appendChild(scriptNode);
      }
    }
  }
}

function getAltPage(altPage) {
  getPage(altPage, sessionStorage.getItem("alt_scripts"));
}

function swalNotify(message,icon) {
  Swal.fire({
    text: message,
    icon: icon,
    allowOutsideClick: false,
  });
}

function swalNotifyDismiss(message,icon) {
  Swal.fire({
    text: message,
    icon: icon,
    showConfirmButton: false,
    allowOutsideClick: false,
    timer: 1700,
  });
}

async function swalConfirm(message, icon) {
  const resp = await Swal.fire({
    text: message,
    icon: icon,
    showCancelButton: true,
    allowOutsideClick: false,
  });
  return resp.isConfirmed;
}

function emptyInputs(input){
  if(Array.isArray(input)){
    for(let x of input){
      _(x).value = "";
    }
  }else{
    _(input).value = "";
  }
}

function clearHTML(elementId){
  _(elementId).innerHTML='';
}



