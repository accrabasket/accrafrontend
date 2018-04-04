//var app = angular.module('app', []);
app.controller('product', function ($scope, $http, $sce, $timeout, searchBy,page, $rootScope) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.no_record = '';
    $scope.searchProductParams = {};
    $scope.quantity = {};
    $scope.ajaxLoadingData = false;
    $scope.shortByData = 'relevence';
    
    if(searchBy != undefined && searchBy != ''){
        if(searchBy.category_id != undefined && searchBy.category_id != ''){
            $scope.searchProductParams.category_id = searchBy.category_id;
            $scope.categoryName = searchBy.category_name;
            if(searchBy.parent_category_name) {
                $scope.parent_category_name = searchBy.parent_category_name;
                $scope.parent_category_id = searchBy.parent_category_id;
            }
        }
        
        if(searchBy.merchant_id != undefined && searchBy.merchant_id != ''){
            $scope.searchProductParams.merchant_id = searchBy.merchant_id;
        } 
        
        if(searchBy.product_name != undefined && searchBy.product_name != ''){
            $scope.searchProductParams.product_name = searchBy.product_name;
        } 
        if(searchBy.product_type != undefined && searchBy.product_type != ''){
            $scope.searchProductParams.product_type = [searchBy.product_type];
        }        
    }else if(page != 'product'){
        $scope.searchProductParams.product_type = ['offers', 'hotdeals'];
    }
    $scope.searchProductParams.page = 1;
    $scope.allProductListResponse = {};
    var productData = {};
    if(productData == ''){
        $scope.no_record = 'No Data Available.';
    }
    $scope.currentPage = 1;
    $scope.productlist = function () { 
//        $("#selectBox option[text='']").remove();
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/productlist',
            data: ObjecttoParams($scope.searchProductParams),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
            $scope.productDataList = {};
            if (response.status == 'success') {
                angular.forEach(response.data, function(product, key){
                    angular.forEach(product.attribute, function(attributedata, key){
                        $scope.quantity[product.product_id] = key;
                    });
                });
                $scope.numberOfRecord = response.totalNumberOFRecord;
                $scope.allProductListResponse = response;
                $scope.productDataList = response.data;
//                $("#selectBox option[text='']").remove();
            }else{
                $scope.numberOfRecord = 0;
            }
        });
    }
    $scope.selectPage = function(page_number) {
        $scope.currentPage = $scope.searchProductParams.page = page_number;
        $scope.productlist();
    };    
    $rootScope.getCategoryWiseProduct = function(category_id,category_name, parent_category_id, parent_category_name) {
        var currentLocation = window.location.href;
        var locationarr = currentLocation.split("/");
        var lastIndex = locationarr.length-1;
        if(locationarr[lastIndex] == 'hotdeals'){
            locationarr.splice(lastIndex, 1);
            currentLocation = window.location.href=locationarr.join("/")+"/product?id="+category_id;
            window.location.href = currentLocation;
        }else{
            $scope.currentPage = $scope.searchProductParams.page = 1;
            $scope.searchProductParams.category_id = category_id;
            $scope.searchProductParams.merchant_id = 0; 
            $scope.searchProductParams.product_name = '';
            $scope.searchProductParams.product_type = '';
            $scope.categoryName = category_name;
            $scope.parent_category_id = parent_category_id;
            $scope.parent_category_name = parent_category_name;
            $scope.productlist();
        }
    };    
    $scope.productlist();
    
    $scope.shortBy = function() {
        if($scope.shortByData != 'relevence'){
            $scope.searchProductParams.short_by = 'price';
            $scope.searchProductParams.order_by = $scope.shortByData;
        }else{
            delete $scope.searchProductParams.short_by;
            delete $scope.searchProductParams.order_by;
        }
        $scope.productlist();
    };
});