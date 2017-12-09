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
    }
    public function indexAction()
    { 
        $this->view->cityList = $this->session['city_list'];
        $this->view->marchantList = $this->session['marchant_list'];
        $this->view->categoryList = $this->session['category_list'];
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
        if (!empty($request)) {
            $postParams['method'] = 'productlist';
            $postParams['city_id'] = 0;
            $postParams['category_id'] = $request['id'];
            $postParams['pagination'] = 1;
            $postParams['page'] = 1;
            $getProduct = $this->commonObj->curlhitApi($postParams,'application/product');
            $getProduct = json_decode($getProduct,true);
            if(!empty($getProduct)){
                $this->view->product = $getProduct;
                $this->view->categoryList = $this->session['category_list']['data'];
                $this->view->categoryName = $this->session['category_list']['data'][$request['id']]['category_name'];
            }
        }
        return $this->view;
    }
    
    public function faqAction(){
        return $this->view;
    }
    
    public function loginAction(){
        return $this->view;
    }
    
    public function signupAction(){
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
    public function updatecompanyAction() {
        $request = $this->getRequest()->getQuery();
        $params = array();
        $params['user']['first_name'] = $request['name'];
        $params['user']['password'] = md5($request['password']);
        $params['company']['activation_code'] = $request['activation_code'];
        $inputParams['parameters'] = json_encode($params);
        $response = $this->commonObj->curlhit($inputParams, 'updatecompany');
        $response = json_decode($response, true);
        if($response['status'] == true){
            $this->flashMessenger()->addMessage('Thank you for your registration, We will contact you soon!');
            return $this->redirect()->toRoute('admin');
        }
        echo json_encode($response);die;
    }    
    public function activateAction()
    {
        $request = $this->getRequest()->getQuery();
        $params = array();
        if(isset($request['code']) && !empty($request['code'])){
            $params['activation_code'] = $request['code'];
            $params['status'] = 1;
            $companyDetailResponse = $this->commonObj->curlhit($params, 'getcompanylist', 'companycontroller');        
            $companyDetail = json_decode($companyDetailResponse, true);
            if($companyDetail['status']){
                $this->view->companyDetail = $companyDetail['data'][0];
            }
        }
        return $this->view;
    }    
    public function aboutusAction()
    {
        return new ViewModel();
    }
     public function servicesAction()
    {
        return new ViewModel();
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
