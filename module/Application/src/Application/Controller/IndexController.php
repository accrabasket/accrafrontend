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
class IndexController extends AbstractActionController
{
    public $commonObj;
    public $view;
    public $session;
    public function __construct() {
        $this->view =  new ViewModel();
        $this->session = new Container('User');
        
    }
    public function indexAction()
    { 
      return $this->view;
    }
    
    public function contactAction() {
        return $this->view;
    }
    
    public function aboutAction() {
        return $this->view;
    }
   
    
    public function productAction() {
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
    
    public function signup1Action() {
        $return = array('status' => false, 'msg' => 'error');
        $request = (array) $this->getRequest()->getPost();
        $registrationResponse = $this->commonObj->registration($request);
        if (!empty($registrationResponse)) {
            $return = array('status' => true, 'msg' => 'Succesfully created');
        }
        echo json_encode($return);
        exit;
    }
    
    public function addcompanyAction() {
        $request = $this->getRequest()->getPost();
        $params = array();
        $params['company_name'] = $request['name'];
        $params['company_url'] = $request['company_url'];
        $params['country_id'] = $request['country_id'];
        $params['state_id'] = $request['state_id'];
        $params['address'] = $request['address'];
        $params['zip_code'] = $request['zip_code'];
        $params['email'] = $request['email_id'];
        $params['phone_number'] = $request['phone_number'];
        $params['alt_phone_number'] = $request['alt_phone_number'];
        $params['type'] = $request['type'];
        $params['contact_via'] = $request['contact_via'];
        $inputParams['parameters'] = json_encode($params);
        $response = $this->commonObj->curlhit($inputParams, 'addcompany');
        $response = json_decode($response, true);
        if($response['status'] == true){
            $this->flashMessenger()->addMessage('Thank you for your registration, We will contact you soon!');
            return $this->redirect()->toRoute('application');
        }
        echo json_encode($response);die;
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
