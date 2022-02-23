(function($){
  $('#vs-select-type').on('change', function(e){
    var item = `#${$(this).val()}`;
    $('.active').hide();
    $('.active').removeClass('active');
    $(item).addClass('active');
    $('.active').show();
    }
  );
  
  $('#uploadSurveyForm').submit(uploadFileToDirectory);
  $('#uploadEntryForm').submit(uploadFileToDirectory);
  function uploadFileToDirectory(e){
    e.preventDefault();
    var uploadApiUrl = uploadDataObject.apiURL;
    var wpNonce = uploadDataObject.nonce;
    var file = $(this).find('input[name=fileToUpload]')[0].files[0];
    var uploadType = $(this).find('input[name=upload-type]').val();
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
      },
      error: (response) => {
        $('#uploadError').show();
        $('#uploadError').text(response.responseJSON.data);
        console.log(response.responseJSON.data);
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
