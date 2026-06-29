app.controller('forgetpassword', function ($scope, $http, $sce, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.ajaxLoadingData = false;
    $scope.forgetpasswordData = {};

    $scope.forgetpassword = function (forgetpasswordData) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        var error = ' ';
        
        if (forgetpasswordData.email == undefined || forgetpasswordData.email == '') {
            error = 'Email should not empty';
        }else{
            if (!filter.test(forgetpasswordData.email)) {
                error = 'Enter correct email id .';
            }
        }
        
        if (error == ' ') {
            $scope.ajaxLoadingData = true;
            $http({
                method: 'POST',
                url: serverAppUrl + '/forgetpassworduser',
                data: ObjecttoParams(forgetpasswordData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    $timeout(function(){
                        var path = serverAppUrl + '/index';
                        window.location.href = path;
                    },2000);
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