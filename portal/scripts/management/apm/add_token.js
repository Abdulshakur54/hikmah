
function changeSchool() {
  let genHtml = "";
  let sch_abbr = _("school").value;
  let levels = getLevels(sch_abbr);
  let showLevel = _('showLevel');
  for (let lev in levels) {
    genHtml += '<option value="' + levels[lev] + '">' + lev + "</option>";
  }
  level.innerHTML = genHtml;
  showLevel.innerHTML = "Level 1";
}

function changeLevel() {
  let showLevel = _("showLevel");
  showLevel.innerHTML = "Level " + level.value;
}

function addToken() {
  if (validate("tokenForm", { validateOnSubmit: true })) {
    ld_startLoading("generatePin");
    const staffname = _("staffname");
    const level = _("level");
    const token = _("token");
    const school = _("school");
    const term = _("term");
    let rank = 11;
    ajaxRequest(
      "management/apm/responses/add_token.rsp.php",
      addTokenRsp,
      "name=" +
        staffname.value +
        "&rank=" +
        rank +
        "&sch_abbr=" +
        school.value +
        "&term=" +
        term.value +
        "&token=" +
        token.value +
        "&level=" +
        level.value
    );
  }

  
  function addTokenRsp() {
    let rsp = JSON.parse(xmlhttp.responseText);
    let msgStyleClass;
    const msgDiv = _("messageContainer");
    if (rsp.success) {
      msgStyleClass = "success m-2";
      msgDiv.innerHTML =
        "<div>Pin successfully generated, Registration details below</div>" +
        '<div class="message">Name: ' +
        staffname.value +
        "<br/>Token: " +
        rsp.createdToken +
        "</div>";
    } else {
      msgStyleClass = "failure m-2";
      msgDiv.innerHTML = "<div>" + rsp.message + "</div>";
    }
    _("token").value = rsp.token;
    msgDiv.className = msgStyleClass;
    emptyInputs(["staffname", "level", "school"]);
    resetInputStyling("tokenForm", "inputsuccess", "inputfailure");
    ld_stopLoading("generatePin");
  }
}

$(".js-example-basic-single").select2();

