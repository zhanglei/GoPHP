<?php

namespace gophp\upload\driver;

use gophp\upload\contract;

class local extends contract {

    public function __construct($config)
    {

        $this->config = $config;

    }

    public function file($inputName)
    {

        $uploadInfo = $_FILES[$inputName];


        if($this->exist($inputName)){

            $uploadDir = trim($this->config['local']['save_dir'], '/');

            $this->info['name']   = $uploadInfo['name'];
            $this->info['size']   = $uploadInfo['size'];
            $this->info['suffix'] = pathinfo($uploadInfo['name'], PATHINFO_EXTENSION);

            if(!in_array($this->info['suffix'], explode('|', $this->config['allow_suffix']))){

                $this->error = '文件类型不允许上传';
                return false;

            }

            $uploadMaxFileSize = ini_get('upload_max_filesize');
            $postMaxFileSize   = ini_get('post_max_size');

            if($this->info['size'] >= $uploadMaxFileSize * 1024 * 1024){

                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                return false;

            }

            if($this->info['size'] >= $postMaxFileSize * 1024 * 1024){

                $this->error = '上传的文件超过了 php.ini 中 post_max_size 选项限制的值！';
                return false;

            }

            if($this->info['size'] >= $this->config['max_size'] * 1024 * 1024){

                $this->error = '上传的文件超过了配置文件里限制的最大值！';
                return false;

            }

            if(!trim($uploadDir)){

                $this->error = '上传目录不存在';
                return false;

            }

            if(!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true)){

                $this->error = '上传目录创建失败';
                return false;

            }

            if($this->config['local']['save_name']){

                $saveName = $this->config['local']['save_name'];

            }else{

                $saveName = date('YmdHis').uniqid();

            }

            $filePath = $uploadDir .'/'. $saveName . '.' . $this->info['suffix'];

            if ( ! move_uploaded_file($uploadInfo['tmp_name'], $filePath ) ) {

                $this->error = '移动临时文件失败';
                return false;

            }

            return '/'.$filePath;

        }

    }

    private function error($errorNo) {

        switch ($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！';
                break;
            case 3:
                $this->error = '文件只有部分被上传！';
                break;
            case 4:
                $this->error = '没有文件被上传！';
                break;
            case 6:
                $this->error = '找不到临时文件夹！';
                break;
            case 7:
                $this->error = '文件写入失败！';
                break;
            default:
                $this->error = '未知上传错误！';
        }

    }


}