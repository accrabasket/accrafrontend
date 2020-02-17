<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Model\common;
class IndexController extends AbstractActionController
{
    public $commonObj;
    public $view;
    public $session;
    public function __construct() {
        $this->view =  new ViewModel();
        $this->session = new Container('User');
        $this->commonObj = new common();
        if(empty($this->session['city_list'])){
            $this->session['city_list'] = $this->getCityList();
            $GLOBALS['city_list'] = $this->session['city_list'];
        }
        //if(empty($this->session['category_list'])){
        $GLOBALS['category_list'] = $this->session['category_list'] = $this->categoryList();
	$GLOBALS['fcityaddress'] = $this->session['fcityaddress'];
	$GLOBALS['fcityaddresstmp'] = $this->session['fcityaddresstmp'];
        //}
    }
    public function indexAction()
    { 
        $this->view->marchantList = $this->getMarchantList();
        $this->view->banner = $this->banner();
        $this->view->cityList = $this->session['city_list'];
        $postParams['product_type'] = array('offers','hotdeals'); 
        $productData = $this->productlistAction($postParams);
        $GLOBALS['hidemenu'] = 1;
        $this->view->productDataList = json_decode($productData, true);
        return $this->view;
    }
    
    public function contactAction() {
        return $this->view;
    }
    
    public function aboutAction() {
        return $this->view;
    }
    public function termandconditionsAction() {
        return $this->view;
    }   
   public function verifyOptAction() {
        return $this->view;
    }
    
    public function productAction() {
        $searchParams = array();
        $request = (array) $this->getRequest()->getQuery();
        $postParams = (array) $this->getRequest()->getPost();
        $this->view->session = !empty($this->session['user']['data'][0]['id'])?$this->session['user']['data'][0]['id']:0;
        if(!empty($request['id'])){
            $searchParams['category_id'] = $request['id'];
            if(!empty($this->session['category_list']['data'][$request['id']])) {
                $searchParams['category_name'] = $this->session['category_list']['data'][$request['id']]['category_name'];
            }else{
                foreach($this->session['category_list']['data'] as $categoryDetails) {   
                    //$childCategoryArr = array_keys($categoryDetails['child']);
                    $childCategoryArr = array();
                    if(!empty($categoryDetails['child'])) {
                        $childCategoryArr = array_keys($categoryDetails['child']);
                    }                    
                    if(in_array($request['id'], $childCategoryArr)){
                        $searchParams['parent_category_name'] = $categoryDetails['category_name'];
                        $searchParams['parent_category_id'] = $categoryDetails['id'];
                        $searchParams['category_name'] = $categoryDetails['child'][$request['id']]['category_name'];
                        break;
                    }
                }
            }
        }
        if(!empty($searchParams['parent_category_id'])) {
            $GLOBALS['category_id'] = $searchParams['parent_category_id'];
        }else if(!empty($searchParams['category_id'])){
            $GLOBALS['category_id'] = $searchParams['category_id'];
        }        
        if(!empty($request['merchant'])){
            $searchParams['merchant_id'] = $request['merchant'];
        }
        if(!empty($postParams['search'])){
            $searchParams['product_name'] = $postParams['search'];
        }
        if(!empty($request['product_type'])){
            $searchParams['product_type'] = $request['product_type'];
        }        
        		//print_r($searchParams);die;
        $this->view->searchBy = $searchParams;
        return $this->view;
    }
    
    public function productlistAction($postParam = array()){
        $postParams = (array) $this->getRequest()->getPost();
        if(!empty($postParams['product_type'])) {
            $postParams['product_type'] = explode(',', $postParams['product_type']);
        }else if(!empty($postParam['product_type'])) {
            $postParams['product_type'] = $postParam['product_type'];
        }
        $postParams['method'] = 'productlist';
        
        if(!empty($postParams['merchant'])){
            $postParams['merchant_id'] = $postParams['merchant'];
        }
        if(!empty($this->session->city)){
            $postParams['city_id'] = $this->session->city;
        }   
        $postParams['pagination'] = 1;
        $postParams['page'] = !empty($postParams['page'])?$postParams['page']:1;
        $getProduct = $this->commonObj->curlhitApi($postParams,'application/product');
        if(!empty($postParams['product_type'])) {
            return $getProduct;
        }
        echo $getProduct;
        exit;
    }
  
    public function faqAction(){
        return $this->view;
    }
    
    public function loginAction(){
        return $this->view;
    }
    
    public function signupAction(){
        $this->view->cityList = $this->session['city_list'];
        return $this->view;
    }
    
    public function privacyAction(){
        return $this->view;
    }
    
    public function productdetailsAction(){
        $postParams = (array) $this->getRequest()->getQuery();
        if (!empty($postParams)) {
            $params['method'] = 'productlist';
            $params['city_id'] = $this->session->city;
            $productIdAndAtt = explode('-', $postParams['id']);
            $this->view->selectedAttr = $productIdAndAtt['1'];
            $params['product_id']= $postParams['id'] = $productIdAndAtt['0'];
            $response = $this->commonObj->curlhitApi($params, 'application/product');
            $product_details = json_decode($response,true);
            $breadcrum = array();
            foreach($this->session['category_list']['data'] as $categoryDetails) {   
                $childCategoryArr = array();
                if(!empty($categoryDetails['child'])) {
                    $childCategoryArr = array_keys($categoryDetails['child']);
                }
                if(in_array($product_details['data'][$postParams['id']]['category_id'], $childCategoryArr)){
                    $breadcrum['parent_category_name'] = $categoryDetails['category_name'];
                    $breadcrum['parent_category_id'] = $categoryDetails['id'];
                    $breadcrum['category_name'] = $categoryDetails['child'][$product_details['data'][$postParams['id']]['category_id']]['category_name'];
                    $breadcrum['category_id'] =  $categoryDetails['child'][$product_details['data'][$postParams['id']]['category_id']]['id'];
                    break;
                }else if($product_details['data'][$postParams['id']]['category_id'] == $categoryDetails['id']){
                    $breadcrum['category_name'] = $categoryDetails['category_name'];
                    $breadcrum['category_id'] =  $categoryDetails['id'];
                    break;
                }
            }
            if(!empty($product_details['data'])){
                $this->view->productDetails = $product_details['data'][$postParams['id']];
                $this->view->productImageList = $product_details['productImageData'][$postParams['id']];
                $this->view->imageRootPath = $product_details['imageRootPath'];
                
                if(!empty($product_details['productImageData']) && !empty($product_details['imageRootPath'].'/product/'.$postParams['id'].'/'.$product_details['productImageData'][$postParams['id']][0]['image_name'])) {
                $this->view->productImage = $product_details['imageRootPath'].'/product/'.$postParams['id'].'/'.$product_details['productImageData'][$postParams['id']][0]['image_name'];
                }else{
                    $this->view->productImage = '';
                }
            }
        }
//        print_r($this->view->productDetails);die;
        $this->view->breadcrum = $breadcrum;
        
        return $this->view;
    }
    
    function getCityList(){
        $postParams = (array) $this->getRequest()->getPost();
        $cityList  = array();
        $postParams['method'] = 'cityList';
        $getCity = $this->commonObj->curlhitApi($postParams);
        $getCity = json_decode($getCity,true);
        if(!empty($getCity)){
            $cityList = $getCity['data'];
        }
        return $cityList;
    }
    
    function categoryList(){
        $postParams = (array) $this->getRequest()->getPost();
        $categoryList  = array();
        $postParams['method'] = 'categoryList';
        $categoryList = $this->commonObj->curlhitApi($postParams);
        $categoryList = json_decode($categoryList,true);
        if(!empty($categoryList)){
            $categoryList['data'] = $this->prepairCategory($categoryList['data']);
        }
        return $categoryList;
    }
    
    function getMarchantList(){
        $postParams = (array) $this->getRequest()->getPost();
        $marchantList  = array();
        $postParams['method'] = 'getMarchantList';
        $marchantList = $this->commonObj->curlhitApi($postParams);
        $marchantList = json_decode($marchantList,true);
        if(!empty($marchantList)){
            $marchantList = $marchantList['data'];
        }
        return $marchantList;
    }
    
    function banner(){
        $postParams = (array) $this->getRequest()->getPost();
        $postParams['method'] = 'banner';
        $postParams['status'] = 1;
        $banner = $this->commonObj->curlhitApi($postParams);
        $banner = json_decode($banner,true);
        return $banner;
    }
    
     function prepairCategory($categoryList) {
        $childWiseCategory = array();
        $childCategory = array();
        foreach ($categoryList as $key => $value) {
            if($value['parent_category_id'] == 0){
                $childWiseCategory[$value['id']] = $value;
            }else {
                $childCategory[$key] = $value;
            }
        }
        
        if(!empty($childCategory)){
            foreach ($childCategory as $key => $value) {
                $childWiseCategory[$value['parent_category_id']]['child'][$key] = $value;
            }
        }
        return  $childWiseCategory; 
    }    
    public function addtocartAction() {
        $postParams = (array) $this->getRequest()->getPost();
        $postParams['method'] = 'addtocart';
        if(!empty($this->session['user']['data'][0]['id'])){
            $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        }else{
            $postParams['guest_user_id'] = session_id();
        }
        $response = $this->commonObj->curlhitApi($postParams,'application/customer');
        echo $response;
        exit;
    }    
    public function viewcartAction()
    {
	header('Content-type: application/json');
        $postParams = (array) $this->getRequest()->getPost();
        //$cartList  = array();
        $postParams['method'] = 'getitemintocart';
        if(!empty($this->session['user']['data'][0]['id'])){
            $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        }else{
            $postParams['guest_user_id'] = session_id();
        }
       $cartList = $this->commonObj->curlhitApi($postParams,'application/customer');
	echo $cartList;
        exit();
    }    
    public function createuserAction() {
        $postParams = (array) $this->getRequest()->getPost();
        $postParams['phone_number'] = $postParams['mobile_number'];
        $postParams['mobile_number'] = $postParams['phonecode'].$postParams['mobile_number']; 
        $postParams['method'] = 'addedituser';
		$this->session['tmpuser'] = $postParams;
        $response = $this->commonObj->curlhitApi($postParams, 'application/customer');
		//$gotpdata = array('method'=>'generateotp','otp_type'=>'register','user_id'=>0,'mobile_number'=>$postParams['mobile_number']);
	   // $this->commonObj->curlhitApi($gotpdata,'application/customer');

        echo $response;
        exit;
    }
    
    public function loginuserAction() {
        $postParams = (array) $this->getRequest()->getPost();
        if(empty($postParams)) {
            $postParams = (array) $this->getRequest()->getQuery();
        }

	    
	    
	    
	    
	    
        $logpath = $_SERVER['DOCUMENT_ROOT'].'/accrafrontend/public/log/'.date("Y-m-d").'/loginuser/';
        if(!file_exists($logpath.'loginuser'.'.txt')) {
            mkdir($logpath, 0777, true);
        }
        $text = "\n Request - ".date('Y-m-d H:i:s')."\n".json_encode($postParams);
        file_put_contents($logpath.'loginuser'.'.txt', $text, FILE_APPEND);	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
        if(!empty($postParams['agentcode']) && !empty($postParams['tokenID'])) {
            
            $postParams = $this->loginUsingEzeepay($postParams);
            
            if(!empty($postParams['data'])) {
                $postParams['data'] = array_values($postParams['data']);
                $postParams['email'] = $postParams['data'][0]['email'];
                $postParams['password'] = $postParams['data'][0]['email'].$postParams['data'][0]['name'];
            }
        }
        $postParams['method'] = 'login';
        
        $response = $this->commonObj->curlhitApi($postParams, 'application/customer');
        $user = json_decode($response,true);
        if($user['status'] == 'success'){
            $data['data'] = array_values($user['data']);
            $params['method'] = 'updatecart';
            $params['user_id'] = $data['data'][0]['id'];
            $params['guest_user_id'] = session_id();
            $response = $this->commonObj->curlhitApi($params, 'application/customer');
            $update = json_decode($response,true);
            $this->session['user'] = $data;
            if(!empty($postParams['agentcode'])) {
                $this->session['agentcode'] = $postParams['agentcode'];
            }
            header('location:'.$GLOBALS['SITE_APP_URL'].'/index');
        }
        echo $response;
        exit;
    }
    
    function deductAmountFromEzeepayWallet($amount) {
        $response = array();
        $response['status'] = 'error';
        $response['msg'] = 'User Vefication Failed';
        $params = array();
        $params['timeStamp'] = time();
        $params['userId'] = $this->session['user']['data'][0]['email'];
        $params['amount'] = $amount;
        $parameters = json_encode($params);
        //{\"timeStamp\": \"10022020155132\",\n\"userId\": \"ashish@yopmail.com\",\n\"amount\": 1\n};
        
        $waletVerificationRespone = $this->commonObj->paymentWalletVerificationFromEzeepay($parameters);
        //$waletVerificationRespone = rtrim($waletVerificationRespone, "\0");
        //$waletVerificationRespone = trim($waletVerificationRespone, " ");
       // $waletVerificationRespone = stripslashes(html_entity_decode($waletVerificationRespone));
        $walletVerificatonData = json_decode($waletVerificationRespone, true);
        if(!empty($walletVerificatonData['isSuccess'])) {
            $deductAmountParams = array();
            $deductAmountParams['securityCode'] = $walletVerificatonData['result']['SecurityCode'];
            $deductAmountParams['timeStamp'] = time();
            $deductAmountParams['otp'] = null;
            $deductAmountParams['userId'] = $this->session['user']['data'][0]['email'];
            //{\n  \"securityCode\": \"F++G/VLQHUlb6lUK3XKC+w==\",\n  \"timeStamp\": \"10022020155132\",\n  \"otp\": null,\n  \"userId\":\"ashish@yopmail.com\"\n}",

            $walletDecutionParams = json_encode($deductAmountParams);
            $paymentDeductinResponse = $this->commonObj->deductAmountFromWallet($walletVerificatonData['result']['SessionId'], $walletDecutionParams);            
            $paymentDeductionResponseData = json_decode($paymentDeductinResponse, true);
            if(!empty($paymentDeductionResponseData['isSuccess'])) {
                $response['status'] = 'success';
                $response['msg'] = 'payment Successfull';
            }else {
                $response['msg'] = $paymentDeductionResponseData['message'];
            }
        }else {
            $response['msg'] = $walletVerificatonData['message'];
        }
        
        return $response;
    }
    
    function loginUsingEzeepay($data) {
        $params = array();
        $params['agentcode'] = $data['agentcode'];
        $params['tokenID'] = $data['tokenID'];
        $params['tgt'] = $data['tgt'];
        $checksum = $data['Checksum'];
        $generatedCheckSum = hash('sha512',"$data[agentcode]|$data[tokenID]|$data[tgt]|".EZEEPAY_SALTKEY);
        
        //echo $generatedCheckSum;die;
        if($generatedCheckSum != $checksum || $generatedCheckSum == $checksum) {
            $parameters = array();
            $parameters['merchantcode'] = EZEEPAY_MERCHANT;
            $parameters['agentcode'] = $params['agentcode'];
            $parameters['tokenid'] = $params['tokenID'];
            $parameters['checksum'] = hash('sha512',"$parameters[merchantcode]|$parameters[agentcode]|$parameters[tokenid]|".EZEEPAY_SALTKEY);
            $url = EZEEPAY_URL."/AfroBasket/AfroDataVerification";
            $ezzepayParams = json_encode($parameters);
            $ezeepayResponse = $this->commonObj->curlHitUsingBody($url, $ezzepayParams);
            $ezeepayData = json_decode($ezeepayResponse, true);
        }
        $response =array();
        $response['status'] = 'fail';
        if(!empty($ezeepayData['isSuccess']) || empty($ezeepayData['isSuccess'])) {
            $signupData = array();
            $signupData['name'] = !empty($ezeepayData['result']['firstname'])?$ezeepayData['result']['firstname']:'test2';
            $signupData['mobile_number'] = !empty($ezeepayData['result']['mobile'])?$ezeepayData['result']['mobile']:8298989898;
            $signupData['email'] = !empty($ezeepayData['result']['emailid'])?$ezeepayData['result']['emailid']:'test2@yopmail.com';
            $signupData['password'] = $signupData['email'].$signupData['name'];
            $signupData['method'] = 'addedituser';
            $signupData['city_id'] = 1;
            $signupData['ezeepay_signup'] = 1;
            $result = $this->commonObj->curlhitApi($signupData, 'application/customer'); 
            $response = json_decode($result, true);
        }
        
        return $response;
    }

    public function logoutAction() {
        $this->session->offsetUnset('user');
        unset($this->session['user']);
        $this->redirect()->toUrl($GLOBALS['SITE_APP_URL'] . '/login');
    }

    public function changepasswordAction(){
        $postParams = (array) $this->getRequest()->getQuery();
        $this->view = new ViewModel();
        if(!empty($postParams['key'])){
            $this->view->authkey = $postParams['key'];
        }
        return $this->view;
    }
    
    public function changepasswordsaveAction(){
        $postParams = (array) $this->getRequest()->getPost();
        $data = array();
        if(!empty($postParams['auth_key'])){
            $data['method'] = 'changepasswordbyauthkey';
            $data['auth_key'] = $postParams['auth_key'];
        }else{
            $data['method'] = 'changepassword';;
            $data['password'] = $postParams['password'];
            $data['user_id'] =  $this->session['user']['data'][0]['id'];
        }
        $data['new_password'] = $postParams['new_password'];
        $response = $this->commonObj->curlhitApi($data, 'application/customer');
        echo $response;
        exit;
    }
    public function forgetpasswordAction(){
        return new ViewModel();
    }
    
    
    public function forgetpassworduserAction(){
        $postParams = (array) $this->getRequest()->getPost();
        $data = array();
        $data['method'] = 'forgetpassword';
        $data['email'] = $postParams['email'];
        $response = $this->commonObj->curlhitApi($data, 'application/customer');
        echo $response;
        exit;
    }
    
    public function checkoutAction(){
        if(empty($this->session['user']['data'][0]['id'])){
            $path = $GLOBALS['SITE_APP_URL'].'/login';
            header('Location: '.$path);
            exit;
        }
        $this->view = new ViewModel();
        $this->view->user_details = $this->session['user']['data'][0];
        $this->view->agentCode = !empty($this->session['agentcode'])?$this->session['agentcode']:'';
        return $this->view;
    } 
    
    public function getcheckoutdetailAction() {
        $postParams = (array) $this->getRequest()->getPost();
        $cartList  = array();
        $postParams['method'] = 'checkout';
        if(!empty($this->session['user']['data'][0]['id'])) {
            $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        }
        $cartList = $this->commonObj->curlhitApi($postParams,'application/customer');
        echo $cartList;
        exit;
    }
    
    public function getUserAddressAction(){
	$postParams = (array) $this->getRequest()->getPost();
        $postParams['method'] = 'getaddresslist';
        $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        $addressList = $this->commonObj->curlhitApi($postParams,'application/customer');
        echo $addressList;
        exit;
    }
     
    public function getdeliverytimeAction() {
        $postParams['method'] = 'deliveryTimeSlotList';
        $deliveryTimeList = $this->commonObj->curlhitApi($postParams,'application');
        echo $deliveryTimeList;
        exit;        
    }
    public function placeorderAction() {
        $postParams = (array) $this->getRequest()->getPost();
        $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        $postParams['method'] = 'placeorder';
        if(!empty($this->session['agentcode']) && $postParams['payment_type'] == 'ezeepay_wallet') {
            $paymentResponse = $this->deductAmountFromEzeepayWallet($postParams['payableAmount']);
        }
        if(isset($paymentResponse) && $paymentResponse['status'] != 'success') {
            $response = json_encode($paymentResponse);
            
        }else{
            if(!empty($paymentResponse['status']) && $paymentResponse['status'] == 'success') {
                $postParams['payemnt_status'] = 'paid';
            }
            
            $response = $this->commonObj->curlhitApi($postParams,'application/customer');
        }
        echo $response;
        exit;        
    }    
    public function saveaddressAction(){
        $postParams = (array) $this->getRequest()->getPost();
		unset($postParams['city_id']);
		unset($postParams['city_name']);
		$postParams['city_id']= (int)$this->session['city_tmp'];
		$postParams['city_name']= $this->session['fcityaddresstmp'];
        $postParams['method'] = 'addeditdeliveryaddress';
        $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        $addressList = $this->commonObj->curlhitApi($postParams,'application/customer');
        echo $addressList;
        exit;
    }
	
    public function editprofileAction(){
       $this->view->cityList = $this->session['city_list']; 
       $this->view->user_details = $this->session['user']['data'][0];
       return $this->view;
    }
	
    public function updateuserAction(){
	$postParams = (array) $this->getRequest()->getPost();
        $data = array();
        $data['method'] = 'addedituser';
        $data['id'] = $this->session['user']['data'][0]['id'];
        $data['name'] = $postParams['name'];
        $data['email'] = $postParams['email'];
        $data['mobile_number'] = $postParams['mobile_number'];
        $data['city_id'] = $postParams['city_id'];
        $addressList = $this->commonObj->curlhitApi($data,'application/customer');
        echo $addressList;
        exit;
    }  
	public function verifyotpAction(){
	return $this->view;
	}  
	public function submitotpAction(){
	      	  $postParams = (array) $this->getRequest()->getPost();
              $data = array();
			  $data['otp'] = $postParams['otp'];
			  $data['method'] = 'verifyotp';
			  $data['otp_type'] = 'register';
			  $data['mobile_number'] = $this->session['tmpuser']['phone_number'];
                          $data['country_code'] = $this->session['tmpuser']['phonecode'];
                          
			  $addressList = $this->commonObj->curlhitApi($data,'application/customer');
			  echo $addressList;
			  exit;
	}
function genrateotp($mobileno){
$gotpdata = array('method'=>'generateotp','otp_type'=>'register','user_id'=>0,'mobile_number'=>$mobileno);
echo $addressList = $this->commonObj->curlhitApi($gotpdata,'application/customer');
}
    public function currentorderAction(){
        return $this->view;
    }
    
    function getOrderListAction() {
        $request = (array)$this->getRequest()->getPost();
        $request['method'] = 'orderlist';
        $request['pagination'] = 1;
        if(!empty($request['page'])) {
            $request['page'] = $request['page'];
        }
        $request['user_id'] = $this->session['user']['data'][0]['id'];
		unset($request['order_status']); 
        $productList = $this->commonObj->curlhitApi($request,'application/customer');
        $productList = json_decode($productList, true);
        $productList['data'] = array_values($productList['data']);
        echo json_encode($productList);
        exit;
    }

    function pastorderAction() {
        return $this->view;
    }

    function cancelorderAction() {
        $request = (array)$this->getRequest()->getPost();
        $request['method'] = 'updateOrderstatus';
        $request['order_status'] = 'cancelled';
        $request['user_id'] = $this->session['user']['data'][0]['id'];
        $productList = $this->commonObj->curlhitApi($request,'application/customer');
        $productList = json_decode($productList, true);
        echo $productList;
        exit;
    } 
    
    function orderdetailsAction() {
       $request = (array) $this->getRequest()->getQuery();
       if (!empty($request['order_id'])) {
            $request['method'] = 'orderlist';
            $productList = $this->commonObj->curlhitApi($request,'application/customer');
            $productList = json_decode($productList,true);
            if(!empty($productList['data'])){
                $this->view->productDetails = $productList;
            }
        }
       return $this->view;
    }
    
    function hotDeal(){
        $postParams = (array) $this->getRequest()->getPost();
        $hotDealList  = array();
        $postParams['method'] = 'productlist';
        $postParams['city_id'] = $this->session->city;
        $postParams['product_type'] = 'hotdeals';
        $hotDealList = $this->commonObj->curlhitApi($postParams,'application/product');
        $hotDealList = json_decode($hotDealList,true);
        return $hotDealList;
}
    
    function newOffer(){
        $postParams = (array) $this->getRequest()->getPost();
        $newOfferList  = array();
        $postParams['method'] = 'productlist';
        $postParams['city_id'] = $this->session->city;
        $postParams['product_type'] = 'offers';
        $newOfferList = $this->commonObj->curlhitApi($postParams,'application/product');
        $newOfferList = json_decode($newOfferList,true);
        return $newOfferList;
    }
    
    public function hotdealsAction(){
        return $this->view;
    }
    
    public function merchantlistAction(){
        return $this->view;
    }
    
    function getmerchantlistAction(){
        $postParams = (array) $this->getRequest()->getPost();
        $marchantList  = array();
        $postParams['method'] = 'getMarchantList';
        $marchantList = $this->commonObj->curlhitApi($postParams);
        echo $marchantList;
        exit;
    }
    
    function deleteShippingAddressAction(){
        $postParams = (array) $this->getRequest()->getPost();
        $postParams['method'] = 'deleteshippingaddress';
        $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        $response = $this->commonObj->curlhitApi($postParams,'application/customer');
        echo $response;
        exit;
    }
    /*
    function setcityAction() {
        $postParams = (array) $this->getRequest()->getPost();
        $this->session['city'] = $postParams['city'];
        echo $postParams['city'];
        exit;
    }
	*/
	
	
	function setcitytmpAction() {
        $postParams = (array) $this->getRequest()->getPost();
        //$this->session['city'] = $postParams['city'];
		$postParamsr = ['method' => 'getCityIdByAddressOrLatLng', 'address' => $postParams['city'], 'lat' => $postParams['lattitude'], 'lng' => $postParams['langitude']];
		$response = $this->commonObj->curlhitApi($postParamsr,'application/index');
		$response = json_decode($response,true);
		if($response['data']['id']){
		$this->session['city_tmp'] = $response['data']['id'];
		$this->session['fcityaddresstmp'] = $postParams['city'];
		echo "1";
		}else{
		echo "0";
		}
		exit;
		    }
    
	
	function setcityAction() {
        $postParams = (array) $this->getRequest()->getPost();
        //$this->session['city'] = $postParams['city'];
        if(empty($postParams['city'])) {
            $postParams['city'] = '';
        }
		$postParamsr = ['method' => 'getCityIdByAddressOrLatLng', 'address' => $postParams['city'], 'lat' => $postParams['lattitude'], 'lng' => $postParams['langitude']];
		$posParamsr['get_city_name']=1;	
		$response = $this->commonObj->curlhitApi($postParamsr,'application/index');
		$response = json_decode($response,true);  
                
                if(!empty($postParams['city'])) {
                    $this->session['fcityaddress'] = $postParams['city'];
		   
                }else {
                    $this->session['fcityaddress'] = $response['data']['city'];
                }
                $this->session['city'] = 10000;
		if($response['data']['id']){
		$this->session['city'] = $response['data']['id'];		
		echo "1";
		}else{
		echo "0";
		}
		exit;
		    }
    function thankyouAction(){
        $searchParams = array();
        $request = (array) $this->getRequest()->getQuery();       
        //$this->view->orderDetails = $request;
		if (!empty($request['order'])) {
            $request['method'] = 'orderlist';
			$request['order_id'] = $request['order'];
			unset($request['order']);
            $productList = $this->commonObj->curlhitApi($request,'application/customer');
            $productList = json_decode($productList,true);
			if(!empty($productList['data'])){
                $this->view->productDetails = $productList;
            }
        }

        return $this->view;
    }
}
