<?php

namespace app;

class user {

    /**
     * 获取当前登录用户id
     * @return mixed
     */
    public static function get_user_id(){

        $user_id = session('user_id');

        return $user_id ? $user_id : 0;

    }

    /**
     * 根据用户id获取指定用户信息
     * @param $user_id
     * @return mixed
     */
    public static function get_user_info($user_id)
    {
        if(!$user_id){

            $user_id = self::get_user_id();

        }

        return db('user')->show(false)->find($user_id);

    }

    /**
     * 根据用户id获取用户名
     * @param $user_id
     */
    public static function get_user_name($user_id)
    {

        return self::get_user_info($user_id)['name'];

    }

    /**
     * 根据用户id获取用户类型
     * @param $user_id
     */
    public static function get_user_type($user_id)
    {

        return self::get_user_info($user_id)['type'];

    }

    /**
     * 根据用户id获取用户昵称
     * @param $user_id
     */
    public static function get_user_email($user_id)
    {

        return self::get_user_info($user_id)['email'];

    }

    /**
     * 根据用户id获取创建的项目数量
     * @param $user_id
     */
    public static function get_create_project_num($user_id)
    {

        if(!$user_id){

            $user_id = self::get_user_id();

        }

        return db('project')->where('user_id', '=', $user_id)->count();

    }

    /**
     * 根据用户id获取参与的项目数量
     * @param $user_id
     */
    public static function get_join_project_num($user_id)
    {

        if(!$user_id){

            $user_id = self::get_user_id();

        }

        return db('member')->where('user_id', '=', $user_id)->count();

    }

    /**
     * 获取用户列表
     * @param array $filter
     * @return mixed
     */
    public static function get_user_list($filter=[])
    {

        $users = db('user')->findAll();

        return $users;
    }

    /**
     * 判断当前登录用户是否是管理员
     * @return bool
     */
    public static function is_admin()
    {

        $type = self::get_user_type();

        if($type == 2){

            return true;

        }else{

            return false;

        }

    }

    /**判断当前登录用户是否是项目创建者
     * @param $project_id
     * @return bool
     */
    public static function is_creater($project_id)
    {

        $user_id = self::get_user_id();

        $project = db('project')->show(false)->where('id', '=', $project_id)->where('user_id', '=', $user_id)->find();

        if($project){

            return true;

        }else{

            return false;

        }

    }

    /**判断当前登录用户是否是项目成员
     * @param $project_id
     * @return bool
     */
    public static function is_joiner($project_id)
    {

        $user_id = self::get_user_id();

        $member = db('member')->where('project_id', '=', $project_id)->where('user_id', '=', $user_id)->find();

        if($member){

            return true;

        }else{

            return false;

        }

    }

    /**判断当前登录用户是否有可以查看项目的权限
     * @param $project_id
     * @return bool
     */
    public static function has_view_auth($project_id)
    {

        if(self::is_admin() || self::is_creater($project_id) || self::is_joiner($project_id)){

            return true;

        }else{

            return false;

        }

    }

    /**
     * 验证密码是否正确
     * @param $user_id
     * @return mixed
     */
    public static function check_password($password)
    {

        if(!$password){

            return false;

        }

        $password = md5(encrypt($password));

        $user_id  = self::get_user_id();

        $result   = db('user')->where('id', '=', $user_id)->where('password', '=', $password)->find();

        if($result){

            return true;

        }else{

            return false;

        }

    }

    /**
     * 获取当前登录用户最近登录信息
     * @return int
     */
    public static function get_last_login()
    {

        $user_id = user::get_user_id();

        return db('login_log')->show(false)->where('user_id', '=', $user_id)->find();

    }

    /**
     * 获取登录设备列表
     * @param $type
     * @return mixed
     */
    public static function get_device_list($type, $device)
    {

        if($type == 'title'){
            $data = [
                'pc'     => '电脑',
                'weixin' => '微信',
                'mobile' => '手机',
            ];
        }elseif($type == 'icon'){
            $data = [
                'pc'     => '<i class="fa fa-desktop fa-fw"></i>',
                'weixin' => '<i class="fa fa-weixin fa-fw"></i>',
                'mobile' => '<i class="fa fa-mobile fa-fw"></i>',
            ];
        }

        return $device ? $data[$device] : $data;

    }

}