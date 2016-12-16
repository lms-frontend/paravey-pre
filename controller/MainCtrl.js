myApp.controller('MainCtrl', function($rootScope,$location){
  var vm = this;
  vm.isActive = function (viewLocation) {
      return viewLocation === $location.path();
  };
  // rootScopes
  $rootScope.apiBaseUrl = 'http://localhost/paravey-pre';//false
  $rootScope.isRegistered = true;//false
  $rootScope.userId = '';// ''
  $rootScope.currPage = '';
});
