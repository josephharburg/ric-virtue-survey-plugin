jQuery(document).ready(function($){
  $(document).scroll(function(){
    var text = $("#stop-scroll-effect").text();
    if($(this).scrollTop() >= 300 && text !== 'yes'){
        $('.header-answer-key-v2').css({"position":"fixed","top":0});
        $('.header-answer-key-v2').addClass("scroll-effects");
        $('#stop-scroll-effect').text('yes');}
    else if($(this).scrollTop() < 300 && text !== 'no'){
        $('#stop-scroll-effect').text('no');
        $('.header-answer-key-v2').css({"position":"relative","top":"unset"});
       $('.header-answer-key-v2').removeClass("scroll-effects");
        }
});});
