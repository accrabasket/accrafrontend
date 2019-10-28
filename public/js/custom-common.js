function ObjecttoParams(obj) {
    var p = [];
    for (var key in obj) {
        p.push(key + '=' + encodeURIComponent(obj[key]));
    }
    return p.join('&');
}
;
var placeSearch, autocomplete;
function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('autocomplete')),
            {types: []});
    autocomplete.addListener('place_changed', fillInAddress);
}
function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
    var scope = angular.element(document.getElementById("managelocation")).scope();
    scope.locationData.googlelocation = place.formatted_address;
    scope.locationData.lat = place.geometry.location.lat();
    scope.locationData.lng = place.geometry.location.lng();
}
    
var app = angular.module('app', ['ui.bootstrap']);

/*app.controller('cartcontroller', function ($scope, $http, $rootScope) {
    $scope.totalItemInCart = 0;
    $rootScope.cartResponse = {};
    $rootScope.cartResponse.productImageData = {};
    $scope.cartItem = {};
    $scope.cartList = function(){
        $http({
            method: 'POST',
            url: serverAppUrl + '/viewcart',
            headers: {'Content-Type': 'application/json'},
        }).success(function (response) {
            $rootScope.cartResponse = {};
            $scope.totalItemInCart = 0;
            if(response.status=='success') {
                $rootScope.cartResponse = response; 
                $scope.countItemInCart();
            }
        });        
    }
    $scope.cartList();
    $scope.countItemInCart = function() {
        $scope.totalItemInCart = 0;
        angular.forEach($rootScope.cartResponse.data, function(value, key){
            $scope.totalItemInCart = $scope.totalItemInCart+1
        });        
    }
    
    $rootScope.addToCart = function(inventory_id, product_name, action, number_of_item, checkoutpage) {
        $rootScope.ajaxLoadingData = true;
        $scope.addToCartData = {};
        $scope.addToCartData.merchant_inventry_id = inventory_id;
        $scope.addToCartData.item_name = product_name;
        $scope.addToCartData.number_of_item = number_of_item;
        $scope.addToCartData.action = action;
        $http({
            method: 'POST',
            url: serverAppUrl + '/addtocart',
            data: ObjecttoParams($scope.addToCartData),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if(checkoutpage==1) {
              $rootScope.getcartdata();  
            }else{
                $scope.cartList();
            }
            $rootScope.ajaxLoadingData = false;
            if (response.status == 'success') {
                $scope.successMsg = response.msg;
                $scope.successShow = true;
                $timeout(function(){
                    $scope.successShow = false;
                },2000);                
            }
        });        
    }
    
    $rootScope.setCity = function(){
        var params = {};
        params.city = $scope.city;
        $rootScope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/setcity',
            data: ObjecttoParams(params),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if(response>0){
                $("#selectcity").modal('hide');
                location.reload();
            }
        });        
    }
    
    $rootScope.delete = function(id){
        var select = document.getElementById("selectBox_"+id);
        if(select.options[0].text == ''){
            select.options[0] = null;
        }
	
    }
});*/
