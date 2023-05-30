//var app = angular.module('app', []);
app.controller('edituser', function ($scope, $http, $sce, $timeout,userDetails) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.ajaxLoadingData = false;
    $scope.userData = jQuery.parseJSON(userDetails);
    console.log($scope.userData);
    $scope.updateaccount = function (userData) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        var error = ' ';
        if (userData.name == undefined || userData.name == '') {
            error = 'Name should not empty';
        }
        if (userData.email == undefined || userData.email == '') {
            error = 'Email should not empty';
        }else{
            if (!filter.test(userData.email)) {
                error = 'Enter correct email id .';
            }
        }
        
        if (userData.mobile_number == undefined || userData.mobile_number == '') {
            error = 'Mobile number should not empty';
        }
        
        if (userData.city_id == undefined || userData.city_id == '') {
            error = 'Please select city';
        }
        
        if (error == ' ') {
            $scope.ajaxLoadingData = true;
            $http({
                method: 'POST',
                url: serverAppUrl + '/updateuser',
                data: ObjecttoParams(userData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    
                    $timeout(function(){
                        $scope.successShow = false;
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