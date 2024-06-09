$(document).ready(function(){
    $(".thumbnail").click(function(){
        var imageSrc = $(this).attr("src");
        $("#mainImage").attr("src", imageSrc);
    });
});
