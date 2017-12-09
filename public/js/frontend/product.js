var app = angular.module('app', []);
app.controller('product', function ($scope, $http, $sce,$timeout,productData) {
    $scope.errorShow = false;
    var productData = jQuery.parseJSON(productData);
    $scope.changeAttribute = function(attribute_id) {
        angular.forEach(productData, function (value, key) {
            angular.forEach(value['attribute'], function (values, keys) {
                if(values.id == attribute_id){
                    console.log('sad');
                    $('#'+key).text(values.price);
                }
                console.log(values);
            });
        });
    }
    
    $scope.changeAllAttribute = function() {
        angular.forEach(productData, function (value, key) {
            angular.forEach(value['attribute'], function (values, keys) {
                $('#'+key).text(values.price);
            });
        });
    }
    
    
    
    
});