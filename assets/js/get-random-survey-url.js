jQuery(document).ready(function($){
  const wpNonce = randomSurvey.nonce;
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  var retakeBool = (window.location.href.includes("survey-results")) ? "YES" : "NO";
  var adultBool = (window.location.href.includes("adult")) ? "YES" : "NO";
  var formID = (retakeBool == "YES")? urlParams.get('formID') : null;
  var ajaxURL = randomSurvey.ajaxURL;
  $.ajax({
    beforeSend: (xhr) => {
      xhr.setRequestHeader('X-WP-Nonce', wpNonce);
    },
    url: ajaxURL,
    type: 'POST',
    data: {
      action: 'wp_rest',
      nonce: wpNonce,
      retake: retakeBool,
      formID: formID,
      adult: adultBool
    },
    success: (response) => {
      $('.new-random-survey-button a').attr("href", response.data);
      // console.log(response.data);
      // $('.survey-rdm-button').attr("href", response.data);
      // console.log(response.data);
    },
    error: (response) => {
      console.log(response.responseJSON.data);
    }
  });
});
