myApp.controller('QuizCtrl', function($http, $rootScope, $location){
  var vm = this;

  $rootScope.currPage = 'quiz';

  if(!$rootScope.isRegistered){
    $location.path('/');
  }

  clickeffect();

// post quiz ans
  vm.sendQuiz = function(quizData){
    $http({
         url: "http://localhost/paravey-pre/webservices/v1/api/quiz",
         method: 'POST',
         data: quizData
     }).success(function(response){
       console.log(response.message, response.data);
     }).error(function(response){
       console.log(response);
     });
  };

// post quiz ans

  var ansData = [];
  vm.currentQ = 1;
  vm.thankU = false;
  // vm.ques2 = false;
  // vm.answers.ques1 = 'vm.optAns';
  // console.log(vm.answers);
  vm.quizSubmit = function(ans, ques) {
    console.log(vm.optAns);
    if(vm.optAns !== undefined){
        vm.currentQ = 2;
      //post answer to api
      var qdata = {
        user_id : $rootScope.userId,
        question : ques,
        answer : ans
      };
      //  ansData.push(qdata);
      console.log(qdata);
      //pushing to api
      vm.sendQuiz(qdata);

    }
    else {
      $('.quizWrap .quiz-form').addClass('animated tada');
      setTimeout(function(){
          $('.quizWrap .quiz-form').removeClass('animated tada');
      },1000);
    }
  };

  vm.quizSubmit1 = function(ans, ques) {
    if(vm.optAns1 !== undefined){
      // vm.answers.ques2 = vm.optAns1;
      // console.log(vm.answers);
      var qdata = {
        user_id : $rootScope.userId,
        question : ques,
        answer : ans
      };
      // ansData.push(qdata);
      //hide current slide and show another thannkU
      vm.currentQ = 3;

      console.log(qdata);
      //pushing to api
      vm.sendQuiz(qdata);
    }
    else {
      $('.quizWrap .quiz-form').addClass('animated tada');
      setTimeout(function(){
          $('.quizWrap .quiz-form').removeClass('animated tada');
      },1000);
    }
  };

  vm.quizSubmit2 = function(ans, ques) {
    if(vm.optAns2 !== undefined){
      // vm.answers.ques2 = vm.optAns1;
      // console.log(vm.answers);
      var qdata = {
        user_id : $rootScope.userId,
        question : ques,
        answer : ans
      };
      // ansData.push(qdata);
      //hide current slide and show another thannkU
      vm.currentQ = null;
      vm.thankU = true;

      console.log(qdata);
      //pushing to api
      vm.sendQuiz(qdata);
    }
    else {
      $('.quizWrap .quiz-form').addClass('animated tada');
      setTimeout(function(){
          $('.quizWrap .quiz-form').removeClass('animated tada');
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
