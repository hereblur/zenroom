angular.module('ZenApplication')
.directive('validPrice', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
        ctrl.$validators.validPrice = function(modelValue, viewValue) {
        if (ctrl.$isEmpty(modelValue)) {
          return false;
        }
        var value = +modelValue;
        return !isNaN(value) && value>=0;
      };
    }
  };
})
.directive('after', function() {
  return {
    require: 'ngModel',
    scope: { after: '=after' },
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$validators.after = function(modelValue, viewValue) {
        if(!scope.after) return true;
        return !moment(modelValue).isBefore(scope.after);
      };
    }
  };
})
.directive('indeterminate', function() {
  return {
    scope: { indeterminate: '=indeterminate' },
    link: function(scope, elm, attrs, ctrl) {
      scope.$watch("indeterminate", function(value, prev){
          elm[0].indeterminate = value;
      });
    }
  };
})

.filter('errorMessage', function() {
    return function(msg){
        switch(msg){
            case 'required' : return "Required information"; break;
            case 'after' : return "End date must be after start date"; break;
            case 'validPrice' : return "Amount is not valid"; break;
        }

        return (msg[0].toUpperCase() + msg.substr(1)).replace('_', ' ');
    }
})
