app.controller('login', function ($scope, $http, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.ajaxLoadingData = false;
    $scope.loginData = {};

    $scope.login = function (loginData) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        var error = ' ';
        
        if (loginData.email == undefined || loginData.email == '') {
            error = 'Email should not empty';
        }else{
            if (!filter.test(loginData.email)) {
                error = 'Enter correct email id .';
            }
        }
        if (loginData.password == undefined || loginData.password == '') {
            error = 'Password should not empty';
        }
        
        if (error == ' ') {
            $scope.ajaxLoadingData = true;
            $http({
                method: 'POST',
                url: serverAppUrl + '/loginuser',
                data: ObjecttoParams(loginData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                if (response.status == 'success') {
//                    $scope.successShow = true;
                    //$scope.successMsg = response.msg ;
                    $timeout(function(){
                        var path = serverAppUrl + '/index';
                        window.location.href = path;
                    },200);
                }else{
                    $scope.errorShow = true;
                    $scope.errorMsg = response.msg == undefined ? 'somthing went wrong ':response.msg;
                    $timeout(function(){
                            $scope.errorShow = false;
                    },2000);
                }
                $scope.ajaxLoadingData = false;
            });

        }else{
            $scope.errorShow = true;
            $scope.errorMsg = error;
            $timeout(function () {
                $scope.errorShow = false;
            }, 2000);
        }
    }




});