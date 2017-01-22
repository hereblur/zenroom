
angular.module('ZenApplication')
.factory('BookingsFactory', ['$resource', function($resource) {
    return $resource(
            '/api/bookings/:yearMonth', {},
            {
                'query': { method: 'GET', isArray: true, cancellable: true },
                'save': { method: 'POST', isArray: false, url:  '/api/bookings/:date/:roomtype'},
                'bulkUpdate': { method: 'POST', isArray: false, url:  '/api/bookings/bulk'}
            }
    );
}]);
