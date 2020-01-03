app.controller('changepassword', function ($scope, $http, $timeout) {
    $scope.errorShow = false;
    $scope.successShow = false;
    $scope.ajaxLoadingData = false;
    $scope.changepasswordData = {};

    $scope.changepassword = function (changepasswordData) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var auth = '';
        auth = jQuery('#auth').val();
        
        var error = ' ';
        if (changepasswordData.new_password != changepasswordData.confirm_password) {
            error = 'New password and confirm password should be same';
        }
        if(auth == ''){
            if (changepasswordData.password == changepasswordData.confirm_password) {
                error = 'New password and old password should not be same';
            }

            if (changepasswordData.password == undefined || changepasswordData.password == '') {
                error = 'Password should not empty';
            }
        }else{
            changepasswordData.auth_key = auth;
        }
        
        if (changepasswordData.confirm_password == undefined || changepasswordData.confirm_password == '') {
            error = 'Enter conform password';
        }
        if (changepasswordData.new_password == undefined || changepasswordData.new_password == '') {
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
