
$(document).ready(function () {
    var heights=[];
    $(".card_title").each(function(index){
        heights.push($(this).heights);
    });
    var largestTitle=Math.max(...heights);
    console.log(heights);
    $(".card_title").css("height", largestTitle);

});
