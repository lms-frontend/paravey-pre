var myApp = angular.module('paraveyApp', ['ngRoute', 'ngAnimate']);

myApp.config(['$routeProvider','$locationProvider','$httpProvider',function($routeProvider,$locationProvider,$httpProvider) {
    //$httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    $routeProvider
      .when('/', {
          templateUrl: 'assets/views/register.html',
          controller : 'RegisterCtrl as register'
      })
      .when('/quiz', {
          templateUrl: 'assets/views/quiz.html',
          controller : 'QuizCtrl as quizCtrl'
      })
      .otherwise({
            redirectTo: '/'
      });

      // $locationProvider.html5Mode(true);
}]);
