myApp.controller('CongoCtrl', function($rootScope, $location ){
  // prevent non registered users to access start
  if(!$rootScope.isRegistered){
    $location.path('/');
  } // prevent non registered users to access ends

  console.log('CongoCtrl');
  var vm = this;


});
