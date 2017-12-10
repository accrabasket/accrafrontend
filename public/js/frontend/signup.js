var app = angular.module('app', []);
app.controller('signup', function ($scope, $http, $sce, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.signupData = {};

    $scope.createaccount = function (signupData) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        var error = ' ';
        if (signupData.name == undefined || signupData.name == '') {
            error = 'Name should not empty';
        }
        if (signupData.email == undefined || signupData.email == '') {
            error = 'Email should not empty';
        }else{
            if (!filter.test(signupData.email)) {
                error = 'Enter correct email id .';
            }
        }
        if (signupData.password == undefined || signupData.password == '') {
            error = 'Password should not empty';
        }
        if (signupData.mobile_number == undefined || signupData.mobile_number == '') {
            error = 'Mobile number should not empty';
        }
        
        if (signupData.city_id == undefined || signupData.city_id == '') {
            error = 'Please select city';
        }
        
        if (error == ' ') {
            $http({
                method: 'POST',
                url: serverAppUrl + '/createuser',
                data: ObjecttoParams(signupData),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                if (response.status == 'success') {
                    $scope.successShow = true;
                    $scope.successMsg = response.msg ;
                    
                    $timeout(function(){
                        $scope.successShow = false;
                    },2000);
                }else{
                    $scope.errorShow = true;
                    $scope.errorMsg = response.msg == undefined ? 'somthing went wrong ':response.msg;
                    $timeout(function(){
                            $scope.errorShow = false;
                    },2000);
                }
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