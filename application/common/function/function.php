<?php
/**
 * 公共函数库
 */

if (!function_exists('get_ip_address')) {
    function get_ip_address($ip = '', $type = null)
    {
        if(!$ip){

            $ip = \gophp\request::getClientIp();
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        //调用淘宝接口获取信息
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data['code']) {

            return $data['data'];

        } else {

            $country = $data['data']['country'];
            $province = $data['data']['region'];
            $city = $data['data']['city'];
            $area = $data['data']['area'];

            if($type == 'country'){

                return $country;

            }elseif($type == 'province'){

                return $province;

            }elseif($type == 'area'){

                return $area;

            }elseif($type == 'city'){

                return $city;

            }else{

                return $country.' '.' '.$province.' '.$city;

            }

        }

    }
}

if(!function_exists('get_visit_source')) {
    function get_visit_source(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if(stripos($user_agent, 'MicroMessenger') !== false) {
                return 'weixin';
            }
        $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        if($is_mobile){
            return 'mobile';
        }

        return 'pc';
    }
}
