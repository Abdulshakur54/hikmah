
var acceptAdm = _("acceptAdm");
var declineAdm = _("declineAdm");
var token = _("token");
var form = _("form");
var table = $("#admissionTable").DataTable(dataTableOptions);

acceptAdm.addEventListener("click", acceptAdmission);
declineAdm.addEventListener("click", declineAdmission);
async function acceptAdmission() {
  let idObj = { id: popId() };
  if (await swalConfirm("This will Admit the selected applicants to the school","warning")) {
    let postData =
      "idarr=" +
      JSON.stringify(idObj) +
      "&token=" +
      token.value +
      "&type=accept";
    ld_startLoading(['acceptAdm','declineAdm']);
    ajaxRequest("management/apm/responses/admission_decision.rsp.php", acceptAdmRsp, postData);
  }

  async function acceptAdmRsp() {
    ld_stopLoading(["acceptAdm", "declineAdm"]);
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if (rsp.success) {
      swalNotify(
        "Operation was successful\nAn email has been sent to the selected students to respond to the offer","success"
      );
      form.submit();
    } else {
      swalNotify("Operation was not successful", "danger");
    }

    let ids = idObj.id;
    for(let id of ids){
      _('row'+id).style.display = 'none';
    }

  }

}

async function declineAdmission() {
  if (
    await swalConfirm("This will decline the admission request of the selected students","warning")
  ) {
    let idObj = { id: popId() };
    let postData =
      "idarr=" +
      JSON.stringify(idObj) +
      "&token=" +
      token.value +
      "&type=decline";
    ld_startLoading("declineAdm");
    ajaxRequest(
      "management/apm/responses/admission_decision.rsp.php",
      declineAdmRsp,
      postData
    );
  }

  async function declineAdmRsp() {
    ld_stopLoading("declineAdm");
    let rsp = JSON.parse(xmlhttp.responseText);
    token.value = rsp.token;
    if (rsp.success) {
      swalNotify(
        "Declination was successful\nThe selected student have been notified of the declination via email","success"
      );
      form.submit();
    } else {
      swalNotify(
        "Operation was not successful",
        "danger"
      );
    }
  }
}

//this function is available for the apm
function populateLevel(e) {
  let genHtml = '<option value ="ALL">ALL</option>';
  let levObj = getLevels(e.value);
  if(levObj != null){
     if (objLength(levObj) > 0) {
       for (let objName in levObj) {
         genHtml +=
           '<option value="' + levObj[objName] + '">' + objName + "</option>";
       }
     }
  }
 

  //insert the generated html into level select element
  _("level").innerHTML = genHtml;
}

function reSubmit() {
    getPage(
      "management/apm/admission_decision.php?sch_abbr=" +
        _("sch_abbr").value +
        "&level=" +
        _("level").value
    );
}

function popId() {
  let checkElements = document.querySelectorAll("tbody input[type=checkbox]");
  let count = checkElements.length;
  let valObj = [];
  for (let i = 1; i <= count; i++) {
    if (_("chk" + i).checked) {
      valObj.push(_("val" + i).value);
    }
  }
  return valObj;
}

function checkAll(event) {
  let checkElements = document.querySelectorAll('tbody input[type=checkbox]');
  if (event.checked) {
    for (let chk of checkElements) {
      chk.checked = true;
    }
  } else {
   for (let chk of checkElements) {
     chk.checked = false;
   }
  }
}


$(".js-example-basic-single").select2();
