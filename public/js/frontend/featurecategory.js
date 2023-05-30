//var app = angular.module('app', []);
app.controller('product', function ($scope, $http, $sce, $timeout, searchBy,page, $rootScope) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.no_record = '';
    $scope.searchProductParams = {};
    $scope.quantity = {};
    $scope.ajaxLoadingData = false;
    $scope.shortByData = 'relevence';
    console.log(searchBy);
        if(searchBy.feature_category_id != undefined && searchBy.feature_category_id != ''){
            $scope.searchProductParams.feature_category_id = searchBy.feature_category_id;
        }
    $scope.searchProductParams.page = 1;
    $scope.allProductListResponse = {};
    var productData = {};
    if(productData == ''){
        $scope.no_record = 'No Data Available.';
    }
    $scope.currentPage = 1;
    $scope.cateoryOfFeature = function () {
//        $("#selectBox option[text='']").remove();
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/cateoryOfFeature',
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
        $scope.cateoryOfFeature();
    };        
    $scope.cateoryOfFeature();
    
});
