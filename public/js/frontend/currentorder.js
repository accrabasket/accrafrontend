
app.controller('orderController', function ($scope, $http,$timeout) {
    $scope.errorShow = false;
    $scope.showAttr = false;
    $scope.productData = {};
    $scope.filter = {};
    $scope.index = 0;
    $scope.filter.order_status = 'current_order';
    
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
    $scope.payNow = function(orderId) {
    	console.log('ss');
        $scope.ajaxLoadingData = true;
	$scope.params = {};
	$scope.params.order_id = orderId;
        $http({
            method: 'POST',
            url: serverAppUrl + '/payNow',
            data : ObjecttoParams($scope.params),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
	    if(response !=undefined ) {
		window.location.href = response.paymentUrl;
            }
        });  
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
    
    $scope.cancelOrder = function(store_id,order_id) { 
        $scope.ajaxLoadingData = true;
        var data = {};
        data.order_id = order_id;
        data.store_id = store_id;

        $http({
            method: 'POST',
            url: serverAppUrl + '/cancelorder',
            data : ObjecttoParams(data),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            $scope.ajaxLoadingData = false;
            $scope.getOrderList();
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
