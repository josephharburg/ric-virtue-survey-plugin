(function($){
  $('#uploadSurveyForm').submit(uploadFileToDirectory);

  function uploadFileToDirectory($){
    var surveyAPIURL = surveyUploadObject.apiURL;
    var wpNonce = surveyUploadObject.nonce;

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpnonce);
      },
      url: surveyAPIURL,
      type: 'POST',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
      },
      success: (response) => {
        $('#uploadSuccess').show();
      },
      error: (response) => {
        $('#uploadError').show();
        $('#uploadError').text(response);
      }
    });
  }
})(jQuery);
