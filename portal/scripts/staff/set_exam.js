var maxScore = _('maxscore');
var form = _('examForm');
var scores = JSON.parse(_('scores').innerHTML);
var dtn = _('dtn');

function submitForm(){
    maxScore.value = scores[dtn.value];
    form.method = 'post';
    form.submit();
}
  $(".js-example-basic-single").select2();