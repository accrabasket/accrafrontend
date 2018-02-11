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
    
var app = angular.module('app', []);

app.controller('cartcontroller', function ($scope, $http) {
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
            }
        });        
    }    
});