
angular.module('ZenApplication')
.controller('CalendarController', ['$scope', 'BookingsFactory', 'Roomtypes', function($scope, BookingsFactory, Roomtypes) {
    $scope.viewMonth = moment().startOf('month');
    $scope.calendar = []

    $scope.roomtypes = Roomtypes.get();


    $scope.buildCalendar = function(){
        $scope.calendar = []

        var today = $scope.viewMonth.clone()

        $scope.calendar = [];
        today.startOf('month');

        for(var block = 0; block < today.daysInMonth(); block++) {
            $scope.calendar.push({
                date : today.format("YYYY-MM-DD"),
                label: today.format("DD"),
                day : today.format("ddd"),
                enabled : today.month() == $scope.viewMonth.month(),
            })

            today.add(1, "day")
        }

    }

    $scope.getBooking = function(date, type) {
        var key = [date, key].join("#")
        if( key in $scope.bookings ){
            return $scope.bookings[key];
        }

        console.log(date, type)


        return {
            date: date,
            roomtypeId: type,
            roomtypeName: $scope.roomtypes[type].type,
            occupied: 0,
            price: $scope.roomtypes[type].baseprice || 9999,
        }
    }

    $scope.loadCalendar = function(){
        BookingsFactory.query({yearMonth: $scope.viewMonth.format("Y-MM")}, function(response){
            $scope.bookings = {}
            response.map(function(booking){
                $scope.bookings[ [booking.date, booking.roomtypeId].join('#') ] = booking
            })
            $scope.buildCalendar()
        })
    }

    $scope.goNextMonth = function(){
        $scope.viewMonth.add(1, 'month');
        $scope.loadCalendar()
    }

    $scope.goPrevMonth = function(){
        $scope.viewMonth.subtract(1, 'month');
        $scope.loadCalendar()
    }

}]);
