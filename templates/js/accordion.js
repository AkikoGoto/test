$(document).ready(function(){
    $('.accordion_head').click(function() {
        $(this).next().toggle('fast');
        return false;
    }).next().hide();
});
