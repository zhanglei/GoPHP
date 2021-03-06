<?php

namespace app\home\controller;

use gophp\request;
use gophp\response;

class notify extends auth {

    /**
     * 消息通知
     */
    public function load()
    {

        $this->display('notify/load');
    }

    /**
     * 删除通知
     */
    public function delete(){

        $id = request::post('id', 0);

        $notify = _uri('notify', $id);

        if(!$notify){

            response::ajax(['code' => 301, 'msg' => '该消息不存在!']);

        }

        $result = db('notify')->show(false)->where('id', '=', $id)->update(['is_readed' => 1]);

        if($result !== false){

            response::ajax(['code' => 200, 'msg' => '消息删除成功!']);

        }else{

            response::ajax(['code' => 403, 'msg' => '消息删除失败!']);

        }

    }

}