var app = angular.module('app', []);
app.controller('changepassword', function ($scope, $http, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.ajaxLoadingData = false;
    $scope.changepasswordData = {};

    $scope.changepassword = function (changepasswordData) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        var error = ' ';
        if (changepasswordData.password != changepasswordData.confirm_password) {
            error = 'New password and confirm password should be same';
        }
        
        if (changepasswordData.password == undefined || changepasswordData.password == '') {
            error = 'Password should not empty';
        }
        
        if (changepasswordData.confirm_password == undefined || changepasswordData.confirm_password == '') {
            error = 'Enter new password';
        }
        
        if (error == ' ') {
            $scope.ajaxLoadingData = true;
            $http({
                method: 'POST',
                url: serverAppUrl + '/changepasswordsave',
                data: ObjecttoParams(changepasswordData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    $timeout(function(){
                        var path = serverAppUrl + '/logout';
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