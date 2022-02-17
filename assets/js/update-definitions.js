(function($){
  $('#updateDefinitionsForm').submit(updateDefinitionsForm);
  $('#virtueSelect').on('change', function(e){
    var virtue = $(this).val()
    $('#selectedVirtue').val(virtue);
    var getDefinitionUrl = definitionsData.getVirtueDefinition;
    var wpNonce = definitionsData.nonce;
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpnonce);
      },
      url: getDefinitionUrl,
      type: 'GET',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
        virtue: virtue
      },
      success: (response) => {
        $('#definitionContent').val(response);
      },
      error: (response) => {
        $('#updateError').show();
        $('#updateError').text(response);
      }
    });
  });

  function updateDefinitionsForm(e){
    e.preventDefault();
    var updateApiUrl = definitionsData.apiURL;
    var wpNonce = definitionsData.nonce;
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', wpnonce);
      },
      url: updateApiUrl,
      type: 'POST',
      data: {
        action: 'wp_rest',
        nonce: wpNonce,
      },
      success: (response) => {
        $('#updateSuccess').show();
        $('#updateSuccess').text(response);
      },
      error: (response) => {
        $('#updateError').show();
        $('#updateError').text(response);
      }
    });
  }
})(jQuery);
