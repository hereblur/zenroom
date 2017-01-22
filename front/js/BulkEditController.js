angular.module('ZenApplication')
.controller('BulkEditController', ['$scope', 'BookingsFactory', 'Roomtypes', '$rootScope', '$timeout', function($scope, BookingsFactory, Roomtypes, $rootScope, $timeout) {

    $scope.roomtypes = Roomtypes.get();
    if($scope.roomtypes[Object.keys($scope.roomtypes)[0]])
      $scope.roomtype = $scope.roomtypes[Object.keys($scope.roomtypes)[0]].id
    else
      $scope.roomtype = 0

    $scope.selectedDays = [false,false,false,false,false,false,false];
    $scope.preset = { all: false, wd: false, we: false }
    $scope.indeterminate = true;

    $scope.presetSelected = function(preset){
      switch(preset){
          case "all" : $scope.selectedDays = $scope.selectedDays.map(function(){ return true;});
                       $scope.preset.wd = $scope.preset.we = false;
                       $scope.indeterminate = false;
                       break;
          case "wd"  : $scope.selectedDays = $scope.selectedDays.map(function(d,i){ return i != 0 && i!=6;});
                       $scope.preset.all = $scope.preset.we = false;
                       break;
          case "we"  : $scope.selectedDays = $scope.selectedDays.map(function(d,i){ return i == 0 || i==6;});
                       $scope.preset.wd = $scope.preset.all = false;
                       break;
      }
    }

    $scope.daySelected = function(){
      var bits = $scope.selectedDays.map(function(v){return v?1:0}).join('')
      $scope.preset = { all: false, wd: false, we: false }
      $scope.indeterminate = false;
      switch( bits ) {
        case "1111111" : $scope.preset.all = true; break;
        case "0111110" : $scope.preset.wd = true; break;
        case "1000001" : $scope.preset.we = true; break;
        case "0000000" : $scope.indeterminate = true;
      }
    }

    $scope.bulkSave = function(){
      $scope.submitted = true;

      if($scope.bulkForm.$invalid) return;

      var weekdays = $scope.selectedDays.map(function(v){return v?1:0});

      if(weekdays.join('') == '0000000') weekdays = [1,1,1,1,1,1,1];

      BookingsFactory.bulkUpdate({}, {
        roomtype: $scope.roomtype,
        start : moment($scope.startDate).format("Y-MM-DD"),
        end   : moment($scope.endDate).format("Y-MM-DD"),
        weekdays : weekdays,
        price : $scope.enableNewPrice ? $scope.newPrice : null,
        inventory : $scope.enableNewAvail ? $scope.newAvailability : null,
      }, function(data){
          $scope.saved = (data.saved + " days has been successfully updated!")
          $scope.reset();
          $rootScope.$broadcast("RequestCalendarRefresh", {})


          $timeout(function(){
              $scope.saved = false;
          }, 3000)

      }, function(response) {
          $scope.extraError = ( response.data.error || response.statusText )
      })

    }

    $scope.reset = function(){
      $scope.submitted = false;
      $scope.newPrice = "";
      $scope.newAvailability = "";
      $scope.enableNewPrice = "";
      $scope.enableNewAvail = "";
      $scope.extraError = false;
    }
}]);
