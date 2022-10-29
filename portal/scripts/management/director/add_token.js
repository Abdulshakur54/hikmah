function changeSchool(event) {
  let pos = event.querySelector(
    'option[value="' + event.value + '"]'
  ).innerHTML;
  let genHtml = "";
  let schools = "";
  switch (event.value) {
    case "5":
      schools = getConvectionalSchools();
      for (let school in schools) {
        genHtml +=
          '<option value="' + schools[school] + '">' + school + "</option>";
      }
      break;
    case "17":
      schools = getIslamiyahSchools();
      for (let school in schools) {
        genHtml +=
          '<option value="' + schools[school] + '">' + school + "</option>";
      }
      break;
    default:
      genHtml += "<option value='All'>All</option>";
  }
  changePosition(pos);
  $("#school").html(genHtml);
}

function addToken() {
  if (validate("tokenForm", { validateOnSubmit: true })) {
    ld_startLoading("generatePin");
    const staffname = _("staffname");
    const position = _("position");
    const salary = _("salary");
    const token = _("token");
    const school = _("school");
    const assts = document.querySelectorAll('input[name="asst"]');
    let asst;
    for (let a of assts) {
      if (a.checked) {
        asst = a.value;
        break;
      }
    }
    ajaxRequest(
      "management/director/responses/add_token.rsp.php",
      addTokenRsp,
      "name=" +
        staffname.value +
        "&rank=" +
        position.value +
        "&sch_abbr=" +
        school.value +
        "&token=" +
        token.value +
        "&salary=" +
        salary.value+
        "&asst="+asst
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
    emptyInputs(["staffname", "position", "school", "salary"]);
    resetInputStyling("tokenForm", "inputsuccess", "inputfailure");
    ld_stopLoading("generatePin");
  }
}

function changeAsst(obj) {
  doAfterPositionChanges(parseInt(obj.value));
}

function changePosition(pos) {
  _("pos").value = pos;
  _("mainPos").checked = true;
  doAfterPositionChanges(0);
}

function doAfterPositionChanges(asst) {
  let asstDiv = _("asstDiv");
  let pos = _("pos");
  let description = "";
  if (pos.value.length > 0) {
    switch (asst) {
      case 0:
        description += "";
        break;
      case 1:
        description += "Deputy";
        break;
      case 2:
        description += "Secretary to ";
        break;
      default:
    }
    asstDiv.innerHTML =
      '<div class="font-weight-bold my-3"> Position: <span class="message">' +
      description +
      " " +
      pos.value +
      "</span></div>";
  } else {
    asstDiv.innerHTML =
      '<div class="text-danger">Select a position first</div>';
  }
}

$(".js-example-basic-single").select2();
