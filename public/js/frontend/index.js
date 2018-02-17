app.controller('index', function ($scope, $http,$timeout) {
    $scope.searchProductParams = {};
    $scope.quantity = {};
    $scope.searchProductParams.page = 1;    
    $scope.productlist = function (produtType) {        
        $scope.searchProductParams.product_type = $scope.productType;
        $http({
            method: 'POST',
            url: serverAppUrl + '/productlist',
            data: ObjecttoParams($scope.searchProductParams),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if (response.status == 'success') {
                angular.forEach(response.data, function(product, key){
                    angular.forEach(product.attribute, function(attributedata, key){
                        $scope.quantity[product.product_id] = key;
                    });
                });
                $scope.numberOfRecord = response.totalNumberOFRecord;
                $scope.allProductListResponse = response;
                $scope.productDataList = response.data;
            }else{
                $scope.numberOfRecord = 0;
            }
        });
    }
    $scope.productType = ['offers', 'hotdeals'];
    $scope.productlist($scope.productType);
});

var slidingWidth = 0;
var liWidth = $("#myTab li").width()+10;
var licount = $("#myTab li:last").index();
var totalWidth = liWidth*(licount+1);
$(".next-circle").click(function(){
    slidingWidth = slidingWidth+$(".category_main_div").width();
    if(slidingWidth<totalWidth){
        var resSpaceForSlide = totalWidth-slidingWidth;
        if(resSpaceForSlide<slidingWidth){
            slidingWidth = resSpaceForSlide;
        }
        $("#myTab").animate({left:-slidingWidth+'px'});
    }    
})
$(".prev-circle").click(function(){
    slidingWidth = 0;
    $("#myTab").animate({left:-slidingWidth+'px'});
})