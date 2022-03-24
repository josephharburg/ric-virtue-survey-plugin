(function($){
  $('#updateVirtueResults').submit(updateVirtueResults);
  $('#virtueSelect').on('change', function(e){
    $('.formSuccess, .formError').hide();
    var virtue = $(this).val();
    $('#currentSelectedVirtue').text(virtue);
    $('#selectedVirtue').val(virtue);
    $('.vs-button-style').val(`Save ${virtue} Result`);
    var getVirtueResultUrl = resultsData.getVirtueResults;
    var wpNonce = resultsData.nonce;
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpNonce);
      },
      url: getVirtueResultUrl,
      type: 'GET',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
        virtue: virtue
      },
      success: (response) => {
        tinymce.get("definitionContent").setContent(response.data.definition);
        if(response.data.imgURL){
          $(".virtue-image-upl").removeClass('vs-button-style');
          $(".virtue-image-upl").html(`<img id="currentVirtueImg" src="${response.data.imgURL}" />`);
        } else{
          $('.virtue-image-rmv').trigger('click');
        }
        console.log(response.data.imgURL);
      },
      error: (response) => {
        $('#updateError').show();
        $('#updateError').text(response.responseJSON.data);
      }
    });
  });

  function updateVirtueResults(e){
    var virtue = $('#selectedVirtue').val();
    var definition = tinymce.get("definitionContent").getContent();
    var updateApiUrl = resultsData.apiURL;
    var wpNonce = resultsData.nonce;
    var imageID = $("#virtue-image-id").val();
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpNonce);
      },
      url: updateApiUrl,
      type: 'POST',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
        virtue: virtue,
        definition:definition,
        imageID: imageID
      },
      success: (response) => {
        $('#updateSuccess').show();
        $('#updateSuccess').html(response.data);
      },
      error: (response) => {
        $('#updateError').show();
        $('#updateError').text(response.responseJSON.data);
      }
    });
  }
  $('.virtue-image-upl').click( function(e){
  e.preventDefault();
  var button = $(this),
  custom_uploader = wp.media({
    title: 'Insert image',
    library : {
      // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
      type : 'image'
    },
    button: {
      text: 'Use this image' // button label text
    },
    multiple: false
  }).on('select', function() { // it also has "open" and "close" events
    var attachment = custom_uploader.state().get('selection').first().toJSON();
    button.html('<img id="currentVirtueImg" src="' + attachment.url + '">').next().show().next().val(attachment.id);
    $(".virtue-image-upl").removeClass('vs-button-style');
  }).open();
});
// On remove button click
$('.virtue-image-rmv').click(function(e){
  e.preventDefault();
  var selectedVirtue = $('#selectedVirtue').val();
  var button = $(this);
  button.next().val(''); // emptying the hidden field
  button.hide().prev().html('Upload icon for ' + selectedVirtue).addClass('vs-button-style');
});
})(jQuery);
