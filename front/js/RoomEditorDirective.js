
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
        controller: ['$scope', 'BookingsFactory', 'Roomtypes', function($scope, BookingsFactory, Roomtypes){
            $scope.booking = $scope.bookingGetter($scope.day, $scope.roomtype);
            $scope.roomtypeData = Roomtypes.get($scope.booking.roomtypeId);
            $scope.available = $scope.booking.inventory;

            $scope.saveBooking = function(field){
                return function(value, saveResultCallback){
                  var updater = {};
                  switch(field){
                    case 'inventory' :
                                      var v = +value;
                                      if(isNaN(v) || v<0 || v%1>0)
                                        return saveResultCallback("Invalid number");

                                      updater['inventory'] = v
                                      break;
                    case 'price'    :
                                      var v = +value;
                                      if(isNaN(v) || v<0)
                                        return saveResultCallback("Invalid amount");

                                      updater['price'] = v
                                      break;
                  }

                  BookingsFactory.save({date: $scope.booking.date, roomtype: $scope.booking.roomtypeId}, updater, function(data){
                      if(data.error){
                          return saveResultCallback(data.error)
                      }

                      $scope.booking.inventory = data[0].inventory;
                      $scope.booking.price = data[0].price;
                      return saveResultCallback( true )
                  }, function(response) {
                      return saveResultCallback( response.data.error || Object.values(response.data)[0].join('<br>') || response.statusText )
                  })
                }
            }
        }]
    }
}])
