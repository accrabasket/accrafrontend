//var app = angular.module('app', []);
app.controller('chekout', function ($scope, $http, $sce, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.no_record = '';
    $scope.ajaxLoadingData = false;
    $scope.createAddress = false;
    $scope.address = {};
    
    $scope.getcartdata = function () {
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/getcheckoutdetail',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            console.log(response);
            if (response.status == 'success') {
                $scope.totalOrderDetails = response.data.totalOrderDetails;
                $scope.cartData = response.cartitems;
                $scope.productDataList = $scope.cartData.productDetails.data;
                $scope.product = $scope.cartData.data;
                $scope.productImage = $scope.cartData.imageRootPath;
                $scope.productImageDetais = $scope.cartData.productDetails.productImageData;
                $scope.ajaxLoadingData = false;
            } else {
                $scope.ajaxLoadingData = false;
                $scope.errorShow = true;
                $scope.errorMsg = response.msg == undefined ? 'somthing went wrong ' : response.msg;
                $timeout(function () {
                    $scope.errorShow = false;
                }, 2000);
            }
        });
        
    }
    getUserAddress();
    function getUserAddress(){
       $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/getUserAddress',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            console.log(response);
            $scope.ajaxLoadingData = false;
            if (response.status == 'success') {
                $scope.userAddress = response.data;
            }
        }); 
    }
    
    $scope.getUserAddress = function(){
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/getUserAddress',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            console.log(response);
            $scope.ajaxLoadingData = false;
            if (response.status == 'success') {
                $scope.userAddress = response.data;
            }
        });
    }
    $scope.openNewAddress = function(){
        $scope.createAddress = true;
    }
    $scope.getcartdata();

    $scope.savenewaddress = function (address) {
        
        var error = ' ';
        if (address.contact_name == undefined || address.contact_name == '') {
            error = 'Full name should not empty';
        }
        if (address.area == undefined || address.area == '') {
            error = 'Area name should not empty';
        }
        if (address.zipcode == undefined || address.zipcode == '') {
            error = 'Zipcode name should not empty';
        }
        address.city_id = $('#cityname').val();
        address.city_name = $("#cityname option:selected").text();
        if (error == ' ') {
            $http({
                method: 'POST',
                url: serverAppUrl + '/saveaddress',
                data: ObjecttoParams(address),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                $("#shipping").click(function () {
                              $("#cartbox").fadeOut();
                              $("#shippingbox").fadeOut();
                              $("#paybox").fadeIn();

                              $("#edit-cart").fadeIn();
                              $("#edit-shipping").fadeIn();
                          });
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    
                    $timeout(function(){
                        $scope.successShow = false;
                    },2000);
                }else{
                    $scope.errorShow = true;
                    $scope.errorMsg = response.msg == undefined ? 'somthing went wrong ':response.msg;
                    $timeout(function(){
                            $scope.errorShow = false;
                    },2000);
                }
            });

        }else{
            alert(error);
        }
    }




});