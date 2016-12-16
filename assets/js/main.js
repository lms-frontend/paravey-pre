console.log('main.js');

$(function(){


  // var pHei;
  // $('.quiz-form').each(function(i){
  //   alert(0);
  //   if($(this).is(':visible')){
  //     pHei = $(this).eq(i).height();
  //   }
  //   $('.quizSection').css('height', pHei);
  //   console.log(pHei);
  // });


  // $('.coupen').each(function(){
  //   var bg = $(this).attr('data-bg');
  //   console.log(bg);
  //   alert(0);
  // });
});

function changeBG() {
  // $('.quizBG').addClass('noBG');
  // setTimeout(function () {
  //   $('.quizBG').removeClass('noBG');
  // }, 300);
}
function clickeffect(){
  $('.btn-ans').on('mousedown', function() {
    $(this).addClass('mover');
  }).on('mouseup mouseleave', function() {
    $(this).removeClass('mover');
  });
}
