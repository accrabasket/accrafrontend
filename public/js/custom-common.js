function ObjecttoParams(obj) {
    var p = [];
    for (var key in obj) {
        p.push(key + '=' + encodeURIComponent(obj[key]));
    }
    return p.join('&');
}
;
var placeSearch, autocomplete;
function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('autocomplete')),
            {types: []});
    autocomplete.addListener('place_changed', fillInAddress);
}
function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
    var scope = angular.element(document.getElementById("managelocation")).scope();
    scope.locationData.googlelocation = place.formatted_address;
    scope.locationData.lat = place.geometry.location.lat();
    scope.locationData.lng = place.geometry.location.lng();
}
    
var app = angular.module('app', ['ui.bootstrap']);

app.controller('cartcontroller', function ($scope, $http, $rootScope) {
    $rootScope.totalItemInCart = 0;
    $rootScope.cartResponse = {};
    $rootScope.cartResponse.productImageData = {};
    $scope.cartItem = {};
    $scope.cartList = function(){
        $http({
            method: 'POST',
            url: serverAppUrl + '/viewcart',
            headers: {'Content-Type': 'application/json'},
        }).success(function (response) {
            $rootScope.totalPrice = 0;
            $rootScope.cartResponse = {};
            $rootScope.totalItemInCart = 0;
            if(response.status=='success') {
                $rootScope.cartResponse = response; 
                $scope.countItemInCart();
            }
        });        
    }
    $scope.cartList();   
    $scope.countItemInCart = function() {
        $rootScope.totalItemInCart = 0; 
        var totalPrice = 0;
        angular.forEach($rootScope.cartResponse.data, function(value, key){
            $rootScope.totalItemInCart = $rootScope.totalItemInCart+1
            totalPrice +=  value.number_of_item*$rootScope.cartResponse.productDetails.data[key].price;
        });   
        $rootScope.totalPrice = totalPrice.toFixed(2);
    }
    
    $rootScope.addToCart = function(inventory_id, product_name, action, number_of_item, checkoutpage) {
        $rootScope.ajaxLoadingData = true;
        $scope.addToCartData = {};
        $scope.addToCartData.merchant_inventry_id = inventory_id;
        $scope.addToCartData.item_name = product_name;
        $scope.addToCartData.number_of_item = number_of_item;
        $scope.addToCartData.action = action;
        $http({
            method: 'POST',
            url: serverAppUrl + '/addtocart',
            data: ObjecttoParams($scope.addToCartData),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if(checkoutpage==1) {
              $rootScope.getcartdata();  
            }else{
                $scope.cartList();
            }
            $rootScope.ajaxLoadingData = false;
            if (response.status == 'success') {
                $scope.successMsg = response.msg;
                $scope.successShow = true;
                $timeout(function(){
                    $scope.successShow = false;
                },2000);                
            }
        });        
    }
    
    $rootScope.setCity = function(){
        var params = {};
        params.city = $scope.city;
        $rootScope.ajaxLoadingData = true;
        $http({
            method: 'POST',
            url: serverAppUrl + '/setcity',
            data: ObjecttoParams(params),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        }).success(function (response) {
            if(response>0){
                $("#selectcity").modal('hide');
                location.reload();
            }
        });        
    }
    
    $rootScope.delete = function(id){
        var select = document.getElementById("selectBox_"+id);
        if(select.options[0].text == ''){
            select.options[0] = null;
        }
	
    }
    $scope.busySemaFor = 0;
    $rootScope.getProductList = function() {
        var params = {};        
        console.log($scope.searchItem);
        if($scope.busySemaFor == 0) {
            params.product_name = angular.copy($scope.searchItem);
            $scope.busySemaFor = 1;
            $http({
                method: 'POST',
                url: serverAppUrl + '/productlist',
                data: ObjecttoParams(params),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            }).success(function (response) {
                $scope.busySemaFor = 0;
                $scope.productArr = [];
                if($scope.searchItem != params.product_name) {
                    $rootScope.getProductList();
                }else{
                    angular.forEach(response.data, function(value, key){
                        $scope.productArr.push(value.product_name);
                    });
                    autocomplete(document.getElementById("myInput"), $scope.productArr);
                }                
            });         
        }
    }
    
    
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  //inp.addEventListener("input", function(e) {
      var a, b, i, val = inp.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      console.log("ra"+val+"test");
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      console.log("id=>"+inp.id)
      a.setAttribute("id", inp.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      inp.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        //if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              console.log("inp.value=>"+inp.value)
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      //}
  //});
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      jQuery("#searchBtn").click();
      //closeAllLists(e.target);
  });
}    
});

