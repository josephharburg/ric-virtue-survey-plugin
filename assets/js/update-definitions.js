(function($){
  $('#updateDefinitionsForm').submit(updateDefinitionsForm);
  $('#virtueSelect').on('change', function(e){
    var virtue = $(this).val()
    $('#selectedVirtue').val(virtue);
    var getDefinitionUrl = definitionsData.getVirtueDefinition;
    var wpNonce = definitionsData.nonce;
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpNonce);
      },
      url: getDefinitionUrl,
      type: 'GET',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
        virtue: virtue
      },
      success: (response) => {
        tinymce.get("definitionContent").setContent(response.data);
      },
      error: (response) => {
        $('#updateError').show();
        $('#updateError').text(response.responseJSON.data);
      }
    });
  });

  function updateDefinitionsForm(e){
    var virtue = $('#selectedVirtue').val();
    var definition = tinymce.get("definitionContent").getContent();
    var updateApiUrl = definitionsData.apiURL;
    var wpNonce = definitionsData.nonce;
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
        definition:definition
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
})(jQuery);
