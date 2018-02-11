app.controller('index', function ($scope, $http, $sce,$timeout,categoryList) {
    $scope.errorShow = false;
    var categoryList = jQuery.parseJSON(categoryList);
//    $scope.categoryData = {};
//    $scope.categoryData.parent_category_id = 0;
//    $scope.id = '';
    function getParentCategory() {
        var filter = {};
//        $http({
//            method: 'POST',
//            url: serverAdminApp + 'dashboard/getCategoryList',
//            data : ObjecttoParams(filter),
//            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//        }).success(function (response) {
//            $scope.categoryList = {};
//            if(response.status == 'success'){
//                $scope.categoryList = response.data;
//                if($scope.id != ''){
//                   delete($scope.categoryList[$scope.id]); 
//                }
//            }
//        });
    }
    
    
    getParentCategory();
    
    
    
    
});