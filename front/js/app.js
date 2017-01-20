
var $ZenApplication = angular.module('ZenApplication',["ngResource"])
.controller("MainController", ["$scope", "Roomtypes", "$q", function($scope, Roomtypes, $q){
    var ready = false;
    $q.all( [ Roomtypes.promise ] )
      .then(function(){
        ready = true;
    })

    $scope.AppReady = function(){
        return ready;
    }

}]);
