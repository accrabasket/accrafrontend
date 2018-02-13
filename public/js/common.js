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
$(document).ready(function() {
    $("#w3view-cart").click(function() {
        $(".w3view-cart-menu").show('fast'); 
    });
             $(".w3view-cart-close").click(function() {
        $(".w3view-cart-menu").hide('slow');
    });
});
    
var app = angular.module('app', ['ui.bootstrap']);

app.controller('cartcontroller', function ($scope, $http, $rootScope) {
    $scope.cartResponse = {};
    $scope.cartResponse.productImageData = {};
    $scope.cartItem = {};
    $scope.cartList = function(){
        $http({
            method: 'POST',
            url: serverAppUrl + '/viewcart',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if(response.status=='success') {
                $scope.cartResponse = response; 
                console.log($scope.cartResponse);
            }
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
});