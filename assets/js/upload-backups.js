(function($){
  $('#uploadSurveyForm').submit(uploadFileToDirectory);
  $('#uploadEntryForm').submit(uploadFileToDirectory);
  function uploadFileToDirectory(e){
    e.preventDefault();
    var uploadApiUrl = uploadDataObject.apiURL;
    var wpNonce = uploadDataObject.nonce;
    var file = $('#surveyToUpload')[0].files[0];
    var uploadType = $('#surveyUploadType').val();
    var formData = new FormData();
    formData.append( 'file', file );
    formData.append( 'upload-type', uploadType );
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpNonce);
      },
      processData: false,
      contentType: false,
      url: uploadApiUrl,
      type: 'POST',
      data: formData,
      success: (response) => {
        $('#uploadSuccess').show();
        $('#uploadSuccess').text(response.data);
      },
      error: (response) => {
        $('#uploadError').show();
        $('#uploadError').text(response);
        console.log(response);
      }
    });
  }
})(jQuery);

// $.ajax({
//   beforeSend: (xhr) => {
//     xhr.setRequestHeader('X-WP-Nonce', wpNonce);
//   },
//   processData: false,
//   contentType: false,
//   url: uploadApiUrl,
//   type: 'POST',
//   data: {
//     action: 'wp_rest',
//     nonce: wpNonce,
//     file:formData
//   },
//   success: (response) => {
//     $('#uploadSuccess').show();
//     $('#uploadSuccess').text(response);
//   },
//   error: (response) => {
//     $('#uploadError').show();
//     $('#uploadError').text(response);
//     console.log(response);
//   }
// });
// }
// })(jQuery);
