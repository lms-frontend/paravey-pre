myApp.controller('RegisterCtrl', function($http,$location,$rootScope){
  $rootScope.currPage = 'register';
  // alert($rootScope.currPage);
  // alert();
  console.log('RegisterCtrl');
  var vm = this;
  // vm.name = "mudit Jain";
  vm.isloaded = true;
  vm.errMessage = "";//showing top of form when error occurs
  var regData = {};

  vm.registerme = function(){
     vm.isloaded = false;

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
    //  console.log($rootScope.apiBaseUrl + "/webservices/v1/api/register");
     $http({
          url: $rootScope.apiBaseUrl + "/webservices/v1/api/register",
          method: 'POST',
          data: regData
      }).success(function(response){
        console.log(response);
        if(response.status == "success"){
          $rootScope.isRegistered = true;
          $rootScope.userId = response.data.userid;
          $location.path('/quiz');
          vm.userdata = {};
          vm.isloaded = true;
        }else{
          alert('err');
        }

      }).error(function(response){
        console.log(response);
      })
   };


   //form
   vm.bDay = [];
     for(i = 0; i<31; i++){
       vm.bDay[i] = i+1;
     }

  //  console.log(vm.bDay);

  // year dropdown
  var year = new Date().getFullYear();
  var yrange = [];
  yrange.push(year);
  for(var i=year;i>1901;i--) {
    yrange.push(i-1);
  }
  vm.bYears = yrange;
  // console.log(yrange);

  //getting states
  $http.get($rootScope.apiBaseUrl + '/webservices/v1/api/states')
    .success(function(response){
    // console.log(response.data);
    vm.stateData = response.data;
  })
  .error(function(response){
    console.log('error', response);
  });

  //city state dropdown starts
  vm.getCities = function(){
    // alert(vm.userdata.state);
     //getting city data
    $http.get($rootScope.apiBaseUrl + '/webservices/v1/api/cities/'+ vm.userdata.state)
      .success(function(response){
        // console.log(response.data);
        vm.cities =  response.data;
    })
    .error(function(response){
      console.log('error', response);
    });
  };
  //city state dropdown starts
});
