//var app = angular.module('app', []);
app.controller('chekout', function ($scope, $http, $sce, $timeout, $rootScope) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.no_record = '';
    $scope.ajaxLoadingData = false;
    $scope.createAddress = false;
    $scope.address = {};
    
    $rootScope.getcartdata = function () {
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
    function getUserAddress(){
       $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/getUserAddress',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            console.log(response);
            $scope.AddressLeng = 0;
            $scope.ajaxLoadingData = false;
            if (response.status == 'success') {
                $scope.userAddress = response.data;
                var keys = Object.keys($scope.userAddress);
                $scope.AddressLeng = keys.length;
            }
        }); 
    }
    $scope.getDeliveryTime = function(){
        $http({
            method: 'POST',
            url: serverAppUrl + '/getdeliverytime',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            console.log(response);
            if (response.status == 'success') {
                $scope.deliverTimeSlotList = response.datewisetimeslot;
            }
        });
    }    
    $scope.getUserAddress = function(){
        $scope.getDeliveryTime();
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
    $scope.getUserAddress();   
    $scope.placeOrderData = {};
    $scope.selectDeliveryDate = function(deliveryDate){
        $scope.placeOrderData.delivery_date =  deliveryDate;
        $scope.placeOrderData.time_slot_id = '';
        $scope.payment = 0;
        $(".timeslotradioclass").prop('checked', false);
    }
    
    $scope.selectTimeSlot = function(deliveryTimeSlot) {
        $scope.placeOrderData.time_slot_id =  deliveryTimeSlot;
        $scope.payment = 0;
    }
    $scope.selectShipingAddress = function(shipping_address_id) {
        $scope.placeOrderData.shipping_address_id = shipping_address_id;
        $scope.show_shipping_address = 0;
        //$scope.createAddress = false;
    }
    $scope.showAddress = function() {
        $scope.show_shipping_address = 1;
        $scope.payment = 0;
    }
    
    $scope.openNewAddress = function(id){
        $scope.createAddress = true;
        $scope.placeOrderData.shipping_address_id = id;
        if(id == undefined){
            $scope.placeOrderData.shipping_address_id = 0;
        }
        
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
        $scope.ajaxLoadingData = true;
        if (error == ' ') {
            $http({
                method: 'POST',
                url: serverAppUrl + '/saveaddress',
                data: ObjecttoParams(address),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                $scope.ajaxLoadingData = false;
                $('#modal-default').modal('hide');
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    $scope.createAddress = false;
                    $scope.placeOrderData.shipping_address_id = response.data.id;
                    $scope.getUserAddress();
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

    $scope.SelectPaymentMethod = function () {
        $("#cartbox").fadeOut();
        $("#shippingbox").fadeOut();
        $("#paybox").fadeIn();

        $("#edit-cart").fadeIn();
        $("#edit-shipping").fadeIn();
    };
    
    $scope.PlaceOrder = function() {
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/placeorder',
            data: ObjecttoParams($scope.placeOrderData),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if (response.status == 'success') {
                $scope.ajaxLoadingData = false;
                console.log(response.data.tokenResponse);
                if(response.data.tokenResponse !=undefined && response.data.tokenResponse.TokenId != undefined) {
                    window.location.href = "http://52.35.53.106/gateway/checkout?token="+response.data.tokenResponse.TokenId+"&returnurl=http://54.233.182.212/basketapi/application/cron/updatepaymentstatus";
                }else{
                    window.location.href=serverAppUrl+'/currentorder';
                }
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
    

    $scope.editAddress = function(data,id){
        $scope.address = data;
        $scope.openNewAddress(id)
    }
    
    $scope.deleteAddress = function(id){
        var data = {};
        $scope.ajaxLoadingData = true;
        data.id = id;
        $http({
            method: 'POST',
            data:ObjecttoParams(data),
            url: serverAppUrl + '/deleteShippingAddress',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
            $scope.getUserAddress();
        });
    }
    $scope.makepayment = function() {
        $scope.payment = 1;
    }
    $scope.editDeliveryTime = function() {
        $scope.payment = 0;
    }


});