//var app = angular.module('app', []);
app.controller('product', function ($scope, $http, $sce, $timeout, searchBy,page) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.no_record = '';
    $scope.searchProductParams = {};
    $scope.quantity = {};
    $scope.ajaxLoadingData = false;
    
    if(searchBy != undefined && searchBy != ''){
        if(searchBy.category_id != undefined && searchBy.category_id != ''){
            $scope.searchProductParams.category_id = searchBy.category_id;
        }
        
        if(searchBy.merchant_id != undefined && searchBy.merchant_id != ''){
            $scope.searchProductParams.merchant_id = searchBy.merchant_id;
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
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/productlist',
            data: ObjecttoParams($scope.searchProductParams),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
            if (response.status == 'success') {
                angular.forEach(response.data, function(product, key){
                    angular.forEach(product.attribute, function(attributedata, key){
                        $scope.quantity[product.product_id] = key;
                    });
                });
                $scope.numberOfRecord = response.totalNumberOFRecord;
                $scope.allProductListResponse = response;
                $scope.productDataList = response.data;
            }else{
                $scope.numberOfRecord = 0;
            }
        });
    }
    $scope.selectPage = function(page_number) {
        $scope.currentPage = $scope.searchProductParams.page = page_number;
        $scope.productlist();
    };    
    $scope.productlist();
});