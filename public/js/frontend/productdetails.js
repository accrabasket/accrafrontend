app.controller('productdetails', function ($scope, $http, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.ajaxLoadingData = false;
    $scope.loginData = {};

    $scope.addtocart = function (product_id, product_name,merchant_inventry_id) {
        var cartData = {};
        cartData.number_of_item = 1;
        cartData.action = 'add';
        cartData.item_name = product_name;
        cartData.merchant_inventry_id = merchant_inventry_id;
        var error = ' ';
        if (cartData.item_name == undefined || cartData.item_name == '') {
            error = 'Item name should not empty';
        }
        if (cartData.merchant_inventry_id == undefined || cartData.merchant_inventry_id == '') {
            error = 'Please select attribute';
        }
        if (error == ' ') {
            $scope.ajaxLoadingData = true;
            $http({
                method: 'POST',
                url: serverAppUrl + '/addtocart',
                data: ObjecttoParams(cartData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    $scope.ajaxLoadingData = false;
                    $timeout(function(){
                        $scope.successShow = false;
                    },2000);
                }else{
                    $scope.ajaxLoadingData = false;
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
    
    $scope.proceedtocheckout = function(){
        var path = serverAppUrl + '/checkout';
        window.location.href = path;
    }




});