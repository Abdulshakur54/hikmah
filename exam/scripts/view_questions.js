let token = _('token');

function deleteQuestion(qtnId,examId){
    let cont = confirm('Are you sure you want to delete');
    if(cont){
        location.assign('view_questions.php?examid='+examId+'&token='+token.value+'&qtnid='+qtnId);
    }
    
}