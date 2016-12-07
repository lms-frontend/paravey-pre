myApp.controller('RegisterCtrl', function($http,$location,$rootScope){
  console.log('RegisterCtrl');
  var vm = this;
  vm.name = "mudit Jain";
  vm.errMessage = "";//showing top of form when error occurs
  var regData = {};

  vm.registerme = function(){
     regData.role_id= 4;
     regData.first_name= vm.userdata.firstname;
     regData.last_name= vm.userdata.lastname;
     //For Publisher 2, For Advertiser 3, For End User 4
    //  regData.user_role= "4";
     regData.email= vm.userdata.email;
     regData.mobile = vm.userdata.mobile;
     regData.password= vm.userdata.password;
     regData.address =  vm.userdata.address;
     regData.city =  vm.userdata.city;
     regData.state =  vm.userdata.state;
     regData.zip =  vm.userdata.zip;
     regData.gender = vm.userdata.gender;
     regData.dob =  vm.userdata.dobd +"-"+vm.userdata.dobm+"-"+vm.userdata.doby;

     $http({
          url: "http://localhost/paravey-pre/webservices/v1/api/register",
          method: 'POST',
          data: regData
      }).success(function(response){
        console.log(response);
        $rootScope.isRegistered = true;
        $location.path('/quiz');

      }).error(function(response){
        console.log(response);
      })
   };

   vm.bDay = [];
   for(i = 0; i<31; i++){
     vm.bDay[i] = i;
   }
  //  console.log(vm.bDay);
});