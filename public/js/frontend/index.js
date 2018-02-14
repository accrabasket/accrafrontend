app.controller('index', function ($scope, $http,$timeout) {
    $scope.searchProductParams = {};
    $scope.quantity = {};
    $scope.searchProductParams.page = 1;    
    $scope.productlist = function (produtType) {        
        $scope.searchProductParams.product_type = produtType;
        $http({
            method: 'POST',
            url: serverAppUrl + '/productlist',
            data: ObjecttoParams($scope.searchProductParams),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
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
    $scope.productlist('offers');
    //$scope.productlist('hotdeals');
});