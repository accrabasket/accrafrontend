<?php
namespace Application\Model;

class curl{
    public function callCurl($url){
        $ch = curl_init(); 
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;        
    }
    public function callPostCurl($url, $params) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec ($ch);
        curl_close($ch);
        
        return $result;
    }
}