let maxScore = _('maxscore');
let form = _('form');
let scores = JSON.parse(_('scores').innerHTML);
let dtn = _('dtn');

function submitForm(){
    maxScore.value = scores[dtn.value];
    form.submit();
}