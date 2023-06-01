jQuery(document).ready(function($){
    var labels = $('.gfield_radio:visible .gchoice:first-child').find('label');
    var radios = $('.gfield_radio:visible .gchoice:first-child').find('input');
    var parentRadioContainers = $('.gfield_radio:visible .gchoice:first-child');
    parentRadioContainers.css('margin-right', '1em');
    radios.each(function(x, el){
        var t = $(el).siblings();
        el.before(t[0]);
        t.css('margin-right', '1em');
    });
});