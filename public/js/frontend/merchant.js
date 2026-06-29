app.controller('merchant', function ($scope, $http,$timeout) {
    $scope.searchProductParams = {};
    $scope.quantity = {};
    $scope.searchProductParams.page = 1; 
    $scope.ajaxLoadingData = false;
    $scope.merchantlist = function () { 
        $scope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/getmerchantlist',
            data: ObjecttoParams($scope.searchProductParams),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
            if (response.status == 'success') {
                $scope.numberOfRecord = response.totalNumberOFRecord;
                $scope.allProductListResponse = response;
                $scope.merchantDataList = response.data;
            }else{
                $scope.numberOfRecord = 0;
            }
        });
    }
    $scope.merchantlist();
});