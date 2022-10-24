function viewResult(term) {
  let classId = _("class").value;
  let url;
  if (term === "ses") {
     url = _("ses_url").value;
  } else {
     url = _("url").value+'&term='+term;
  }

  ld_startLoading(term+"_resultBtn","ld_loader_"+term);
  let postData =
    "op=view_results&class_id=" +
    classId +
    "&school=" +
    _("school").value +
    "&token=" +
    _("token").value;
  ajaxRequest(
    "management/hos/responses/responses.php",
    viewResultRsp,
    postData
  );

  function viewResultRsp() {
    const rsp = JSON.parse(xmlhttp.responseText);
    let successCodes = [200, 201, 204];
    _("token").value = rsp.token;
    if (successCodes.includes(rsp.status)) {
      let results = rsp.data;
 ld_stopLoading(term + "_resultBtn", "ld_loader_" + term);
      if (results.length > 0) {
        let students = [];
        results.forEach((element) => {
          students.push(element.std_id);
        });
        location.assign(
          url +
            "&token=" +
            rsp.token +
            "&student_ids=" +
            JSON.stringify(students)
        );
      } else {
        swalNotify("Result is not ready for the class", "info");
      }
    }
  }
}

$(".js-example-basic-single").select2();
