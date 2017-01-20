
angular.module('ZenApplication')
.factory('RoomtypesFactory', ['$resource', function($resource) {
    return $resource(
            '/api/roomtypes', {},
            {
                'query': { method: 'GET', isArray: true, cancellable: true },
            }
    );
}])
.service('Roomtypes', ['RoomtypesFactory', '$q', function(RoomtypesFactory, $q){

    var defer = $q.defer();

    var roomtypes = {}

    RoomtypesFactory.query({}, function(response){
        response.map(function(room){
            roomtypes[room.id] = room;
        })
        defer.resolve(roomtypes);
    });

    return {
        promise: defer.promise,
        get: function(id){ return id?roomtypes[id]:roomtypes; }
    }

}]);
