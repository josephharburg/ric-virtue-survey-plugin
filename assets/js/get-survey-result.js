jQuery(document).ready(function($){
  $('#getSurveyResultForm').submit(getSurveyResult);
  function getSurveyResult(){
    var getSurveyResultUrl = surveyResults.requestURL;
    var wpNonce = surveyResults.nonce;
    var returnCode = $("#returnCode").val();
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpNonce);
      },
      url: getSurveyResultUrl,
      type: 'POST',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
        returnCode: returnCode
      },
      success: (response) => {
        if(response.data.resultHTML){
          $(".entry-content").html(response.data.resultHTML);
        } else{
          $('#surveyFormError').text('Oops something went wrong please try again.');
        }
      },
      error: (response) => {
        $('#surveyFormError').html('Either that return code has expired or it is entered incorrectly.<br/> Please re-enter your code and try again.');
        $('#surveyFormError').css("background-color", "var(--fortitude)");
      }
    });
  }
});
