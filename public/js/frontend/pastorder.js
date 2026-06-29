
app.controller('pastorderController', function ($scope, $http,$timeout) {
    $scope.errorShow = false;
    $scope.showAttr = false;
    $scope.productData = {};
    $scope.filter = {};
    $scope.index = 0;
    $scope.filter.order_status = 'past_order';
    
    $scope.indexVal = [];
    $scope.errorStatus = false;
    $scope.errorMsg = '';
    $scope.selected_filter_level = 'Action';
    $scope.setFilterType = function(id){
        $scope.filter.filter_type = id;
        $scope.selected_filter_level = id.replace("_", " ");
    }
    $scope.ajaxLoadingData = false;
    $scope.selectPage = function(page_number) {
        $scope.filter.page = page_number;
        $scope.currentPage = page_number;
        $scope.getOrderList();
    };
    
    $scope.currentPage = 1;
    $scope.numberOfRecord = 0;
    
    $scope.querySearch = function(){
       
        if($scope.filterText == '' || $scope.filterText == undefined){
           $scope.errorStatus = true;
           $scope.errorMsg = 'Please enter order id'; 
        }
        if(!$scope.errorStatus){
            $scope.filter.order_id = $scope.filterText;
            delete $scope.filter.page;
            delete $scope.filter.order_status;
            $scope.getOrderList();
        }else{
            $timeout(function () {
                $scope.errorStatus = false;
                $scope.errorMsg = '';
            }, 2000);
        }
        
    }
    
    $scope.refresh = function() {
        $scope.filter = {};
        $scope.filter.page = 1;
        $scope.selected_filter_level = 'Action';
        $scope.filterText = '';
        $scope.filter.order_status = 'current_order';
        $scope.getOrderList();
    }
    
    $scope.getOrderList = function() { 
        $scope.ajaxLoadingData = true;
        //$scope.numberOfRecord = 0;

        $http({
            method: 'POST',
            url: serverAppUrl + '/getOrderList',
            data : ObjecttoParams($scope.filter),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
            $scope.orderList = {};
            if(response.status == 'success'){
                $scope.orderList = response.data;
                $scope.shipping_address_list = response.shipping_address_list;
                $scope.user_details = response.user_details;
                $scope.numberOfRecord = response.totalNumberOfOrder;
                $scope.order_assignment_list = response.order_assignment_list;
                $scope.rider_list = response.rider_list;
            }else{
                $scope.numberOfRecord = 0;
            }
        });
    }    
    
   
});	


app.filter('underscoreless', function () {
  return function (input) {
      return input.replace(/_/g, ' ');
  };
});

app.filter('lengths', function () {
  return function (input) {
      return Object.keys(input).length;
  };
});