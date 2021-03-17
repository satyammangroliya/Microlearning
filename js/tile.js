
$(document).ready(function () {
    var heights=[];
    $(".card_title").each(function(index){
        heights.push($(this).heights);
    });
    var largestTitle=Math.max(...heights);
    $(".card_title").css("height", largestTitle);

});
