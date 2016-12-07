myApp.controller('QuizCtrl', function($http, $rootScope, $location){
  if(!$rootScope.isRegistered){
    $location.path('/');
  }
  clickeffect();
  var vm = this;
  vm.answers = {};
  vm.ques1 = true;
  // vm.ques2 = false;
  // vm.answers.ques1 = 'vm.optAns';
  // console.log(vm.answers);
  vm.quizSubmit = function() {
    console.log(vm.optAns);
    if(vm.optAns !== undefined){
      vm.answers.ques1 = vm.optAns;
      console.log(vm.answers);
      vm.ques1 = false;
      vm.ques2 = true;
      $('.hero-form').addClass('animated slideInRight');
    }
    else {
      $('.quizWrap .hero-form').addClass('animated tada');
      setTimeout(function(){
          $('.quizWrap .hero-form').removeClass('animated tada');
      },1000);
    }
  };

  vm.quizSubmit1 = function() {
    console.log(vm.optAns1);
    if(vm.optAns1 !== undefined){
      vm.answers.ques2 = vm.optAns1;
      console.log(vm.answers);
      vm.ques2 = false;
      vm.ques1 = true;
    }
    else {
      $('.quizWrap .hero-form').addClass('animated tada');
      setTimeout(function(){
          $('.quizWrap .hero-form').removeClass('animated tada');
      },1000);
    }
  };


  console.log('quiz');
  $(function(){
    // $('.quizWrap').each(function(i){
    //   if($(this).hasClass('active')){
    //     $(this).show();
    //   }
    // });


  });
});
