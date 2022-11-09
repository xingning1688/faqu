<?php


    function reponse($status,$msg='',$code="200",$data=[]){
        $res['status'] = $status;
        $res['msg'] = $msg;
        $res['code'] = $code;
        $res['data'] = $data;
        return json_encode($res);
    }

    function getOrderNumber(){
        $osn = date('Ymd').'-' . str_pad(mt_rand(1, 99999), 9, '0', STR_PAD_LEFT);
        return $osn;
    }

    function getRandNumber(){
        $rand_number = str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
        return $rand_number;
    }


// 小程序支付日志记录
function _minipay_log($msg='') {
    if(is_array($msg)) {
        $msg = json_encode($msg);
    }

    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/../runtime/data/pay/minipay_'.date('Y-m-d').'.txt',$msg."\r\n",FILE_APPEND);
}
