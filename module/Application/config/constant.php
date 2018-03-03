<?php
$GLOBALS['HTTP_SITE_ADMIN_URL'] = 'http://' .$_SERVER['HTTP_HOST'].'/accrafrontend/admin/';
$GLOBALS['SITE_APP_URL'] = 'http://' .$_SERVER['HTTP_HOST'].'/accrafrontend/application/index';
$GLOBALS['SITE_COMPANY_URL'] = 'http://' .$_SERVER['HTTP_HOST'].'/accrabasket/merchant/';
$GLOBALS['PAGE_BEFORE_LOGIN'] = array('Admin\Controller\Index\login','Admin\Controller\Index\index');
$GLOBALS['SITE_PATH'] = $_SERVER['DOCUMENT_ROOT'];
define('NODE_API', 'http://localhost:3000/');
define('BASKET_API', 'http://54.233.182.212/basketapi/');
$GLOBALS['PRODUCTIMAGEPATH'] = $_SERVER['DOCUMENT_ROOT'].'accrabasket/product_img';
$GLOBALS['ATTRIBUTEIMAGEPATH'] = $_SERVER['DOCUMENT_ROOT'].'/accrabasket/attribute_img';
$GLOBALS['LEFT_MENU_ALLOW'] = array('product','hotdeals');
