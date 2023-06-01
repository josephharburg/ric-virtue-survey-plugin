// jQuery(document).ready(function($){
//   const wpNonce = randomCode.nonce;
//   var ajaxURL = randomCode.ajaxURL;
//   $.ajax({
//     beforeSend: (xhr) => {
//       xhr.setRequestHeader('X-WP-Nonce', wpNonce);
//     },
//     url: ajaxURL,
//     type: 'POST',
//     data: {
//       action: 'wp_rest',
//       nonce: wpNonce,
//     },
//     success: (response) => {
//       $('.put-return-code-here').text(response.data);
//       $('#input_1_19').val(response.data);
//     },
//     error: (response) => {
//       $('.put-return-code-here').text(response.data);
//     }
//   });
// });
console.log('testing');
