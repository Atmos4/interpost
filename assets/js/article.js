$(document).ready(function(){

    $('#article-content a').attr("target","_blank");

    $('#article-content img').on('click',function(){
        $('#imgModal').css("display", "block");
        $('#imgModalContent').attr('src', $(this).attr('src'));
        $('#imgCaption').html($(this).attr('alt'));
    });
})