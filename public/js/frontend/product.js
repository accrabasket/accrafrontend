var app = angular.module('app', []);
app.controller('product', function ($scope, $http, $sce, $timeout, categoryId) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.no_record = '';
    $scope.searchProductParams = {};
    $scope.quantity = [];
    $scope.searchProductParams.category_id = categoryId;
    $scope.searchProductParams.page = 1;
    $scope.allProductListResponse = {};
    var productData = {};
    if(productData == ''){
        $scope.no_record = 'No Data Available.';
    }
    
    $scope.changeAttribute = function (attribute_id) {
        angular.forEach(productData, function (value, key) {
            angular.forEach(value['attribute'], function (values, keys) {
                if (values.id == attribute_id) {
                    $('#att_' + key).val(values.id);
                    $('#' + key).text('GHS '+values.price);
                }
            });
        });
    }

    $scope.changeAllAttribute = function () {
        angular.forEach(productData, function (value, key) {
            angular.forEach(value['attribute'], function (values, keys) {
                $('#' + key).text('GHS '+values.price);
                $('#att_' + key).val(values.id);
            });
        });
    }

    $scope.addtocart = function (product_id, product_name) {
        var cartData = {};
        cartData.number_of_item = 1;
        cartData.action = 'add';
        cartData.item_name = product_name;
        cartData.merchant_inventry_id = $('#att_' + product_id).val();
        var error = ' ';
        if (cartData.item_name == undefined || cartData.item_name == '') {
            error = 'Item name should not empty';
        }
        if (cartData.merchant_inventry_id == undefined || cartData.merchant_inventry_id == '') {
            error = 'Please select attribute';
        }
        if (error == ' ') {
            $http({
                method: 'POST',
                url: serverAppUrl + '/addtocart',
                data: ObjecttoParams(cartData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
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
    
    $scope.productlist = function () {
        $http({
            method: 'POST',
            url: serverAppUrl + '/productlist',
            data: ObjecttoParams($scope.searchProductParams),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.totalNumberOfProduct = response.totalNumberOFRecord;
            if (response.status == 'success') {
                $scope.allProductListResponse = response;
                $scope.productDataList = response.data;
            }
        });
    }
    
    $scope.addToCart = function(inventory_id) {
        alert('sfdf'+ inventory_id);    
    }
    $scope.productlist();


});