
angular.module('ZenApplication')
.directive('popEdit', [function() {

    return {
        restrict: 'E',
        templateUrl: "html/popEdit.html",
        scope: {
          popValue: '<popValue',
          popSave: '&popSave'
        },
        controller: ['$scope', '$rootScope', '$timeout',  function($scope, $rootScope, $timeout){

          $scope.activate = false;
          $scope.saved = false;
          $scope.error = false;

           $scope.close = function(){
              $scope.activate = false;
              $scope.saved = false;
              $scope.error = false;
           }

           $scope.save = function(){
             if($scope.saved) return;

             var saveResult = function(result){
                                 if (result && angular.isFunction(result.then)) {
                                    result.then(saveResult)
                                    return;
                                 }

                                 if( true === result ){
                                    $scope.error = false;
                                    $scope.saved = true;
                                    $timeout(function(){
                                      $scope.close();
                                    }, 1000)
                                 }else{
                                    $scope.error = result || "Error"
                                    $timeout(function(){
                                        $scope.error = false
                                    }, 5000)
                                 }
                             };

             var result = $scope.popSave()($scope.popValue, saveResult);
           }

           $rootScope.$on("popEditActivated", function(e, data){
             if(data.scopeId != $scope.$id)
                $scope.close()
           })

           $scope.popupEditor = function(){
             $scope.activate = true;
             $rootScope.$broadcast("popEditActivated", {scopeId: $scope.$id});
           }

        }],
        link: function(scope, attr, element){

        }
    }
}])
