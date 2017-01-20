
angular.module('ZenApplication')
.directive('roomEditor', [function() {
    return {
        restrict: 'E',
        templateUrl: "html/roomEditor.html",
        scope: {
          day: '=day',
          roomtype: '=roomtype',
          bookingGetter: '=bookingGetter'
        },
        controller: ['$scope', function($scope){
            $scope.booking = $scope.bookingGetter($scope.day, $scope.roomtype)
        }]
    }
}])
