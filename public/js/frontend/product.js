//var app = angular.module('app', []);
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
    
    $scope.productlist();
});