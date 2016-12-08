myApp.controller('MainCtrl', function($rootScope,$location){
  var vm = this;
  vm.isActive = function (viewLocation) {
      return viewLocation === $location.path();
  };
  $rootScope.isRegistered = true;
  $rootScope.userId = '';
  $rootScope.currPage = '';
});
