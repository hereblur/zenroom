
angular.module('ZenApplication')
.controller('BulkEditController', ['$scope', 'BookingsFactory', 'Roomtypes', function($scope, BookingsFactory, Roomtypes) {

    $scope.roomtypes = Roomtypes.get();
    $scope.bulkFilter = {
        roomtype: $scope.roomtypes[Object.keys($scope.roomtypes)[0]].id
    }

}]);
