<?php

namespace app\home\controller;

use gophp\request;
use gophp\response;
use app\home\model\user;

class project extends auth {


    public function index()
    {

        response::redirect('project/select');

    }

    /**
     * 选择项目
     */
    public function select()
    {

        $user_id = $this->user_id;

        //创建的项目
        $create_projects = db('project')->show(false)->where('user_id', '=', $user_id)->findAll();

        //参与的项目
        $join_projects  = db('member')->show(false)->where('user_id', '=', $user_id)->findAll();

        $this->assign('create_projects', $create_projects);
        $this->assign('join_projects', $join_projects);

        $this->display('project/select');

    }

    /**
     * 添加/编辑项目
     */
    public function add(){

        if(request::isAjax()){

            $id    = request::post('id', 0);
            $title = request::post('title', '');
            $version = request::post('version', '');
            $intro = request::post('intro', '');
            $allow_search = request::post('allow_search', 1);
            $user_ids = request::post('user_ids', '');

            if($title){

                $data['title'] = $title;

            }else{

                response::ajax(['code' => 301, 'msg' => '项目标题不能为空']);

            }

            $project = db('project')->show(false)->where('title', '=', $title)->where('id', 'not in', [$id])->find();

            if($project){

                response::ajax(['code' => 304, 'msg' => '该项目名称已存在']);

            }

            if($intro){

                $data['intro'] = $intro;

            }else{

                response::ajax(['code' => 302, 'msg' => '项目简介不能为空']);

            }

            $data['allow_search'] = $allow_search;
            $data['user_id']      = $this->user_id;
            $data['add_time']     = date('Y-m-d H:i:s');

            $project  = db('project')->find($id);

            $user_ids = array_filter(explode(',',$user_ids));

            if($project){
                // 更新操作
                $result = db('project')->where('id', '=', $id)->update($data);

                if($result !== false){

                    $member  = db('member')->where('project_id', '=', $id)->delete();

                    if($member !== false){

                        foreach ($user_ids as $user_id){

                            db('member')->add(['user_id' => $user_id, 'project_id' => $id, 'add_time' => date('Y-m-d H:i:s')]);

                        }

                    }

                    response::ajax(['code' => 200, 'msg' => '项目更新成功']);

                }

            }else{
                // 添加操作
                $result = db('project')->add($data);

                if($result){

                    $project_id = $result;

                    foreach ($user_ids as $user_id){

                        db('member')->add(['user_id' => $user_id, 'project_id' => $project_id, 'add_time' => date('Y-m-d H:i:s')]);

                    }

                    db('version')->add(['project_id' => $project_id, 'name' => $version, 'user_id' => $user_id]);

                    response::ajax(['code' => 200, 'msg' => '项目添加成功']);

                }
            }

        }else{

            $id = get('id', 0);

            $project = db('project')->find($id);

            // 获取项目成员
            $members = db('member')->show(false)->where('project_id', '=', $id)->findAll();

            // 获取初始版本号
            $version = db('version')->show(false)->where('project_id', '=', $id)->orderBy('id asc')->find();

            $this->assign('project', $project);
            $this->assign('version', $version);
            $this->assign('members', $members);

            $this->display('project/add');

        }


    }

    /**
     * 转让项目
     */
    public function transfer(){

        if(request::isAjax()){

            $project_id = request::post('id', 0);
            $password   = request::post('password', '');
            $password   = md5(encrypt($password));
            $user_id    = request::post('user_id', 0);

            $project = db('project')->show(false)->where('user_id', '=', $this->user_id)->where('id', '=', $project_id)->find();

            if(!$project){

                response::ajax(['code' => 301, 'msg' => '你无权转让该项目']);

            }

            if(!_uri('user', $user_id)){

                response::ajax(['code' => 302, 'msg' => '暂无可转让的项目成员，请先添加项目成员']);

            }

            $user = db('user')->where('id', '=', $this->user_id)->where('password', '=', $password)->find();

            if(!$user){

                response::ajax(['code' => 302, 'msg' => '抱歉，登录密码验证错误!']);

            }

            $result = db('project')->where('id', '=', $project_id)->update(['user_id' => $user_id]);

            if($result){

                response::ajax(['code' => 200, 'msg' => '转让成功!']);

            }else{

                response::ajax(['code' => 303, 'msg' => '转让失败!']);

            }

        }else{

            $id = get('id', 0);

            $project = db('project')->find($id);

            // 获取项目成员
            $members = db('member')->show(false)->where('project_id', '=', $id)->findAll();

            $this->assign('project', $project);
            $this->assign('members', $members);

            $this->display('project/transfer');

        }


    }

    /** 
     * 删除项目
     */
    public function delete(){

        $id = request::post('id', 0);
        $password = request::post('password', '');
        $password = md5(encrypt($password));

        $project  = db('project')->find($id);

        if(!$project){

            response::ajax(['code' => 301, 'msg' => '请选择要删除的项目!']);

        }

        $user = db('user')->where('id', '=', $this->user_id)->where('password', '=', $password)->find();

        if(!$user){

            response::ajax(['code' => 303, 'msg' => '抱歉，密码验证错误!']);

        }

        $result = db('project')->show(false)->delete($id);

        if($result){

            response::ajax(['code' => 200, 'msg' => '删除成功!']);

        }else{

            response::ajax(['code' => 403, 'msg' => '删除失败!']);

        }

    }

    /**
     * 退出项目
     */
    public function quit(){

        $project_id = request::post('project_id', 0);
        $user_id    = request::post('user_id', 0);

        if(!$user_id){

            $tip = '退出';

            $user_id = $this->user_id;

        }else{

            $tip = '移除';

        }

        $project = db('project')->find($project_id);

        if(!$project){

            response::ajax(['code' => 301, 'msg' => '请选择要退出的项目!']);

        }

        $member = db('member')->where('project_id', '=', $project_id)->where('user_id', '=', $user_id)->find();

        if(!$member){

            response::ajax(['code' => 302, 'msg' => '该会员不是该项目的成员!']);

        }

        $result = db('member')->show(false)->where('project_id', '=', $project_id)->where('user_id', '=', $user_id)->delete();

        if($result){

            response::ajax(['code' => 200, 'msg' => $tip . '成功!']);

        }else{

            response::ajax(['code' => 303, 'msg' => $tip . '失败!']);

        }
    }

    /**
     * 搜索项目
     */
    public function search()
    {

        $projects = db('project')->where('allow_search', '=', 1)->findAll();

        $this->assign('projects', $projects);
        $this->display('project/list');

    }

    /**
     * 项目详情
     * @param $id
     * @param $arguments
     */
    public function __call($id, $arguments)
    {

        $ids = explode('-', $id);

        $project_id = $ids[0];
        $version_id = $ids[1];

        $project = db('project')->find($project_id);

        // 判断项目是否存在
        if(!$project){

            $this->error('抱歉，该项目不存在');

        }

        // 判断版本是否存在
        $version = db('version')->find($version_id);

        if(!$version){

            $this->error('抱歉，该版本不存在');

        }else{

            $project['version'] = $version;

        }

        $auth = user::get_auth($project_id);

        if($auth == 1 || $auth == 5){

            $this->error('抱歉，您无权查看该项目');
        }

        // 获取项目成员
        $members = db('member')->where('project_id', '=', $project_id)->findAll();

        // 获取项目版本
        $versions = db('version')->where('project_id', '=', $project_id)->orderBy('name desc,id desc')->findAll();

        // 获取项目模块
        $modules = db('module')->where('project_id', '=', $project_id)->findAll();

        // 获取项目环境域名
        $envs    = json_decode($project['env'], true);

        $this->assign('project', $project);
        $this->assign('members', $members);
        $this->assign('modules', $modules);
        $this->assign('envs', $envs);
        $this->assign('versions', $versions);

        $this->display('project/detail');

    }

}