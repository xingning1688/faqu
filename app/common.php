<?php


    function reponse($status,$msg='',$code="200",$data=[]){
        $res['status'] = $status;
        $res['msg'] = $msg;
        $res['code'] = $code;
        $res['data'] = $data;
        return json_encode($res);
    }

    function getOrderNumber(){
        $osn = date('Ymd').'-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return $osn;
    }

    function getRandNumber(){
        $rand_number = str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
        return $rand_number;
    }
