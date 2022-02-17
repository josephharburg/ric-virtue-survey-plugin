(function($){
  $('#uploadSurveyForm').submit(uploadFileToDirectory);
  $('#uploadEntryForm').submit(uploadFileToDirectory);
  function uploadFileToDirectory(e){
    e.preventDefault();
    var surveyAPIURL = uploadDataObject.apiURL;
    var wpNonce = uploadDataObject.nonce;
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
        $('#uploadSuccess').text(response);
      },
      error: (response) => {
        $('#uploadError').show();
        $('#uploadError').text(response);
      }
    });
  }
})(jQuery);
