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
        }
        if(empty($this->session['category_list'])){
            $this->session['category_list'] = $this->categoryList();
        }
        if(empty($this->session['marchant_list'])){
            $this->session['marchant_list'] = $this->getMarchantList();
        }
        if(empty($this->session['banner'])){
            $this->session['banner'] = $this->banner();
        }
    }
    public function indexAction()
    { 
        $this->view->cityList = $this->session['city_list'];
        $this->view->marchantList = $this->session['marchant_list'];
        $this->view->categoryList = $this->session['category_list'];
        $this->view->session = !empty($this->session['user']['data'][0]['id'])?$this->session['user']['data'][0]:0;
        $this->view->banner = $this->session['banner'];
        return $this->view;
    }
    
    public function contactAction() {
        return $this->view;
    }
    
    public function aboutAction() {
        return $this->view;
    }
   
    
    public function productAction() {
        $request = (array) $this->getRequest()->getQuery();
        $this->view->session = !empty($this->session['user']['data'][0]['id'])?$this->session['user']['data'][0]['id']:0;
        $this->view->categoryId = $request['id'];
        
        return $this->view;
    }
    
    public function productlist(){
        $request = (array) $this->getRequest()->getPost();
        $postParams['method'] = 'productlist';
        if(!empty($request['category_id'])){
            $postParams['category_id'] = $request['category_id'];
        }
        if(!empty($request['merchant'])){
            $postParams['merchant_id'] = $request['merchant'];
        }
        $postParams['pagination'] = 1;
        $postParams['page'] = !empty($request['page'])?$request['page']:1;
        $getProduct = $this->commonObj->curlhitApi($postParams,'application/product');
        
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
        $postParams = (array) $this->getRequest()->getPost();
        $cartList  = array();
        $postParams['method'] = 'getitemintocart';
        if(!empty($this->session['user']['data'][0]['id'])){
            $postParams['user_id'] = $this->session['user']['data'][0]['id'];
        }else{
            $postParams['guest_user_id'] = session_id();
        }
        $cartList = $this->commonObj->curlhitApi($postParams,'application/customer');
        $cartList = json_decode($cartList,true);
        print_r($cartList);die;
        if(!empty($cartList)){
            $cartList = $cartList['data'];
        }
        return $this->view;
    }    
    public function createuserAction() {
        $postParams = (array) $this->getRequest()->getPost();
        $postParams['method'] = 'addedituser';
        $response = $this->commonObj->curlhitApi($postParams, 'application/customer');
        echo $response;
        exit;
    }
    
    public function loginuserAction() {
        $postParams = (array) $this->getRequest()->getPost();
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
        }
        echo $response;
        exit;
    }

    public function logoutAction() {
        $this->session->offsetUnset('user');
        unset($this->session['user']);
        $this->redirect()->toUrl($GLOBALS['SITE_APP_URL'] . '/login');
    }

    public function pricingAction()
    {
        return new ViewModel();
    }
    
    public function signinAction()
    {
        return new ViewModel();
    }
    public function forgetpasswordAction()
    {
        return new ViewModel();
    }
    public function contactusAction()
    {
        return new ViewModel();
    }
    public function pagenotfoundAction()
    {
        return new ViewModel();
    } 
    public function pakagelistAction(){
		$package = array();
        $packageList = $this->commonObj->getPackageList();
        if(!empty($packageList)){
            foreach ($packageList as $key => $value) {
                $package[] = $value;
            }
        }    
		
		$this->view->packageList = $package;
        return $this->view;
    }
	
	public function bookingAction(){
		$packageList = array();
		$id = $this->params()->fromQuery('data');
        if (!empty($id)) {
            $getPackageList = $this->commonObj->getPackageList($id);
            if (!empty($getPackageList)) {
                foreach ($getPackageList as $key => $value) {
                    $packageList[] = $value;
                }
                
            }
        }
		$this->view->packageList = $packageList;
        return $this->view;
    }
	
	public function createbookingAction(){
		$request = (array) $this->getRequest()->getPost();
		// create user
		$user_id ;
		$checkUserExist =  $this->commonObj->checkUserExist(array('email'=>$request['email']));
		if(!empty($checkUserExist)){
			foreach($checkUserExist as $value){
				$user_id = $value['id'];
			}
		}
		if(empty($user_id)){
			$userArr = array();
			$userArr['name'] = $request['first_name'];
			if(!empty($request['last_name'])){
			    $userArr['name'] = $request['first_name'].' '.$request['last_name'];
			}
			$userArr['email'] = $request['email'];
			$userArr['number'] = $request['mobile'];
			$userArr['password'] = 123;
			$user_id = $this->commonObj->registration($userArr);
		}
		
		
        if (!empty($user_id)) {
            $bookingArr = array();
			$bookingArr['package_id'] = $request['package_id'];
			$bookingArr['location_id'] = $request['location_id'];
			$bookingArr['number_of_person'] = $request['number_of_person'];
			$bookingArr['checkin_date'] = $request['checkin_date'];
			$bookingArr['checkout_date'] = $request['checkout_date'];
			$bookingArr['user_id'] = $user_id;
			$bookingresponce = $this->commonObj->createBooking($bookingArr);
			if($bookingresponce > 0){
				$path = $GLOBALS['SITE_APP_URL'].'index/bookingconfirm?data=success';
				header('Location:'.$path);exit;
			}else{
				$path = $GLOBALS['SITE_APP_URL'].'index/bookingconfirm?id=error';
			     header('Location:'.$path);exit;
			}
        }else{
				$path = $GLOBALS['SITE_APP_URL'].'index/bookingconfirm?id=error';
			  header('Location:'.$path);exit;
			}
       
    }
	
	public function bookingconfirmAction(){
		$id = $this->params()->fromQuery('data');
        $this->view->msg = $id;
        return $this->view;
    }    
	
    public function galleryAction(){
		
        return $this->view;
    }

	public function detailAction(){
		$packageList = array();
		$id = $this->params()->fromQuery('data');
		$this->view->id = $id;
		$packageList = array();
        if (!empty($id)) {
            $getPackageList = $this->commonObj->getPackageList($id);
            if (!empty($getPackageList)) {
                foreach ($getPackageList as $key => $value) {
                    $packageList[] = $value;
                }
                
            }
        }
		
		$this->view->packageList = $packageList;
        return $this->view;
    }

	public function userinfoAction(){
		$request = (array) $this->getRequest()->getPost();
		$start_date = $request['start_date'];
		$date = str_replace('/', '-', $start_date);
		$request['start_date'] = date('Y-m-d', strtotime($date));
		$end_date = $request['end_date'];
		$date = str_replace('/', '-', $end_date);
		$request['end_date'] = date('Y-m-d', strtotime($date));
		
		$this->view->data = $request;

        return $this->view;
    } 
}
