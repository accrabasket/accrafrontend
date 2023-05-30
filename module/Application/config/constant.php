<?php
$GLOBALS['HTTP_SITE_ADMIN_URL'] = 'http://' .$_SERVER['HTTP_HOST'].'/admin/';
$GLOBALS['SITE_APP_URL'] = 'https://'.$_SERVER['SERVER_NAME'].'/index.php/application/index';
$GLOBALS['SITE_COMPANY_URL'] = 'http://172.104.239.54/accrabasket/merchant/';
$GLOBALS['PAGE_BEFORE_LOGIN'] = array('Admin\Controller\Index\login','Admin\Controller\Index\index');
$GLOBALS['SITE_PATH'] = $_SERVER['DOCUMENT_ROOT'];
define('NODE_API', 'http://172.104.239.54:3000/');
define('BASKET_API', 'https://afrobaskets.com/basketapi/index.php/');
$GLOBALS['PRODUCTIMAGEPATH'] = $_SERVER['DOCUMENT_ROOT'].'accrabasket/product_img';
$GLOBALS['ATTRIBUTEIMAGEPATH'] = $_SERVER['DOCUMENT_ROOT'].'/accrabasket/attribute_img';
$GLOBALS['LEFT_MENU_ALLOW'] = array('product','hotdeals');
define('APIKEY', 'secure#api$__');
define('EZEEPAY_SALTKEY', 'hsyd2KDJ29');
define('EZEEPAY_MERCHANT', 'dsfdSSa22AAAj');
define('EZEEPAY_URL', 'http://54.218.162.6:9095/api');
