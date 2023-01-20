var submitType = _("submitType");
var form = _("form");
var token = _("token");
validate("schedulesForm");
async function submitForm() {
  const school = _("school").value;
  const level = _("level").value;
  const currTerm = _("currTerm").value;

  if (school.length > 0 && level.length > 0 && currTerm.length > 0) {
    let formvalues = getFormData("schedulesForm");
    getPage("management/apm/schedules.php", formvalues);
  } else {
    swalNotify("School and Level are required");
  }
}

async function saveChanges() {
  ld_startLoading("saveBtn");
  const rsp = await getPostPageWithUpload(
    "schedulesForm",
    "management/apm/responses/responses.php",
    {
      op: "save_schedules",
    },
    false,
    true
  );
  console.log(rsp);
  const successCodes = [200, 201, 204];
  if (successCodes.includes(rsp.status)) {
    swalNotify(rsp.message, "success");
  } else {
    swalNotify(rsp.message, "warning");
  }
  ld_stopLoading("saveBtn");
}

$(".js-example-basic-single").select2();
