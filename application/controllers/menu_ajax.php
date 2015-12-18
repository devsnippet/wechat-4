<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_ajax extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('wechat_menu_model', 'menu');
        $this->load->model('wechat_setting_model', 'setting');
    }

    public function delmenu($itemid=1)
    {
        $result = $this->menu->menu_del(array('id' => $itemid));
        if ($result) {
            echo json_encode(array('status' => 200, 'message' => '删除成功'));
        }else{
            echo json_encode(array('status' => 404, 'message' => '删除失败, 条目不存在'));
        }
    }

    public function initmenu(){
        $wid = $this->input->post("wid")?$this->input->post("wid"):false;
        if ($wid) {
            $menus = $this->menu->menu_del(array('wid' => $wid));
            if ($menus) {
                echo json_encode(array('status' => 200, 'message' => '自定义菜单初始化成功'));
            }else{
                echo json_encode(array('status' => 404, 'message' => '自定义菜单已经初始化'));
            }
        }else{
            echo json_encode(array('status' => 500, 'message' => 'wid有误'));
        }
    }

    public function addmenu(){
        $action_name = $this->input->post("action_name")?$this->input->post("action_name"):false;
        $action_type = $this->input->post("action_type")?$this->input->post("action_type"):false;
        $action_key = $this->input->post("action_key")?$this->input->post("action_key"):false;
        $action_value = $this->input->post("action_value")?$this->input->post("action_value"):false;
        $pid = $this->input->post("pid")?$this->input->post("pid"):false;
        $wid = $this->input->post("wid")?$this->input->post("wid"):false;
        if ($action_name && !$action_type && !$action_key && !$action_value) {

            $db_data = array('name' => $action_name, 'wid' => $wid);
            $result = $this->menu->menu_add($db_data);
            if ($result) {
                echo json_encode(array('status' => 200, 'message' => '菜单添加成功'));
            }
        }
        if ($action_name && $action_type && $action_key && $action_value && $wid) {
            $db_data2 = array();
            if ($pid) {
                $db_data2['pid'] = $pid;
            }else{
                $db_data2['pid'] = null;
            }
            $db_data2['name'] = $action_name;
            $db_data2['type'] = $action_type;
            $db_data2['code'] = $action_value;
            $db_data2['wid'] = $wid;
            $result = $this->menu->menu_add($db_data2);
            if ($result) {
                echo json_encode(array('status' => 200, 'message' => '菜单已成功添加'));
            }
        }
    }

    public function editaction(){
        $action_id = $this->input->post("action_id")?$this->input->post("action_id"):false;
        $action_type = $this->input->post("action_type")?$this->input->post("action_type"):false;
        $action_key = $this->input->post("action_key")?$this->input->post("action_key"):false;
        $action_value = $this->input->post("action_value")?$this->input->post("action_value"):false;
        $wid = $this->input->post("wid")?$this->input->post("wid"):false;
        if (!$action_id || $action_id == 0 || $action_id == "") {
            echo json_encode(array('status' => 500, 'message' => 'id有误'));
            exit();
        }
        if ($action_id && $action_type && $action_key && $action_value) {
            $db_data = array();
            // $db_data['name'] = $action_name;
            $db_data['type'] = $action_type;
            $db_data['code'] = $action_value;
            $db_data['wid'] = $wid;
            $result = $this->menu->menu_mod(array('id' => $action_id), $db_data);
            if ($result) {
                echo json_encode(array('status' => 200, 'message' => '动作修改成功'));
            }
        }
    }

    public function editname(){
        $name = $this->input->post("name")?$this->input->post("name"):false;
        $name_id = $this->input->post("name_id")?$this->input->post("name_id"):false;
        $wid = $this->input->post("wid")?$this->input->post("wid"):false;
        if (!$name_id || $name_id == 0 || $name_id == "") {
            echo json_encode(array('status' => 500, 'message' => 'id有误'));
            exit();
        }
        if ($name_id && $name) {
            $db_data = array();
            $db_data['name'] = $name;
            $db_data['wid'] = $wid;
            $result = $this->menu->menu_mod(array('id' => $name_id), $db_data);
            if ($result) {
                echo json_encode(array('status' => 200, 'message' => '动作修改成功'));
            }
        }
    }

    public function pushmenu()
    {
        $wid = $this->input->post("wid")?$this->input->post("wid"):false;
        if ($wid) {
            $menus = $this->menu->menu_get(array('wid' => $wid));
            if (!$menus) {
                die(json_encode(array('status' => 500, 'message' => '本地没有配置自定义菜单呢，请配置菜单')));
            }
            $menu = $this->setMenu($menus);
            // echo json_encode($menu);

            $target_wechat_info = $this->setting->setting_get(array('wid' => $wid));
            if ($target_wechat_info) {
                $options = array(
                    'token' => $target_wechat_info['token'], //填写你设定的key
                    'encodingaeskey' => $target_wechat_info['EncodingAESKey'], //填写加密用的EncodingAESKey
                    'appid' => $target_wechat_info['appid'], //填写高级调用功能的app id
                    'appsecret' => $target_wechat_info['appsecret'] //填写高级调用功能的密钥
                );
                //载入Wechat并且实例化了
                $this->load->library('Wechat', $options, 'wechat');
                $result = $this->wechat->createMenu($menu);
                if ($result) {
                    echo json_encode(array('status' => 200, 'message' => '推送成功'));
                }else{
                    echo json_encode(array('status' => 500, 'message' => $this->wechat->errMsg));
                }
            }
        }else{
            echo json_encode(array('status' => 500, 'message' => 'wid有误'));
        }
    }

    public function delRemoteMenu(){
        $wid = $this->input->post("wid")?$this->input->post("wid"):false;
        if ($wid) {
            $target_wechat_info = $this->setting->setting_get(array('wid' => $wid));
            if ($target_wechat_info) {
                $options = array(
                    'token' => $target_wechat_info['token'], //填写你设定的key
                    'encodingaeskey' => $target_wechat_info['EncodingAESKey'], //填写加密用的EncodingAESKey
                    'appid' => $target_wechat_info['appid'], //填写高级调用功能的app id
                    'appsecret' => $target_wechat_info['appsecret'] //填写高级调用功能的密钥
                );
                //载入Wechat并且实例化了
                $this->load->library('Wechat', $options, 'wechat');
                $result = $this->wechat->deleteMenu();
                if ($result) {
                    echo json_encode(array('status' => 200, 'message' => '删除远程菜单成功'));
                }else{
                    echo json_encode(array('status' => 500, 'message' => $this->wechat->errMsg));
                }
            }
        }else{
            echo json_encode(array('status' => 500, 'message' => 'wid有误'));
        }
    }

    private function setMenu($menuList){
        //树形排布
        $menuList2 = $menuList;
        foreach($menuList as $key=>$menu){
            foreach($menuList2 as $k=>$menu2){
                if ($menu2['mapnum'] == 0 || $menu['mapnum'] == null) {
                    if($menu['id'] == $menu2['pid']){
                        unset($menu2['mapnum']);
                        unset($menu2['wid']);
                        $menuList[$key]['sub_button'][] = $menu2;
                        unset($menuList[$k]);
                    }
                }else{
                    if($menu['mapnum'] == $menu2['pid']){
                        unset($menu2['mapnum']);
                        unset($menu2['wid']);
                        $menuList[$key]['sub_button'][] = $menu2;
                        unset($menuList[$k]);
                    }
                }
            }
        }
        //处理数据
        foreach($menuList as $key=>$menu){
            //处理type和code
            if(@$menu['type'] == 'view'){
                $menuList[$key]['url'] = $menu['code'];
                //处理URL。因为URL不能在转换JSON时被转为UNICODE
                $menuList[$key]['url'] = $menuList[$key]['url'];
            }else if(@$menu['type'] == 'click'){
                $menuList[$key]['key'] = $menu['code'];
            }else if(@!empty($menu['type'])){
                $menuList[$key]['key'] = $menu['code'];
                if(!isset($menu['sub_button'])) $menuList[$key]['sub_button'] = array();
            }
            unset($menuList[$key]['code']);
            //处理PID和ID
            unset($menuList[$key]['id']);
            unset($menuList[$key]['pid']);
            unset($menuList[$key]['wid']);
            unset($menuList[$key]['mapnum']);
            //处理名字。因为汉字不能在转换JSON时被转为UNICODE
            $menuList[$key]['name'] = $menu['name'];
            //处理子类菜单
            if(isset($menu['sub_button'])){
                unset($menuList[$key]['type']);
                foreach($menu['sub_button'] as $k=>$son){
                    //处理type和code
                    if($son['type'] == 'view'){
                        $menuList[$key]['sub_button'][$k]['url'] = $son['code'];
                        $menuList[$key]['sub_button'][$k]['url'] = $menuList[$key]['sub_button'][$k]['url'];
                    }else if($son['type'] == 'click'){
                        $menuList[$key]['sub_button'][$k]['key'] = $son['code'];
                    }else{
                        $menuList[$key]['sub_button'][$k]['key'] = $son['code'];
                        $menuList[$key]['sub_button'][$k]['sub_button'] = array();
                    }
                    unset($menuList[$key]['sub_button'][$k]['code']);
                    //处理PID和ID
                    unset($menuList[$key]['sub_button'][$k]['id']);
                    unset($menuList[$key]['sub_button'][$k]['pid']);
                    //处理名字。因为汉字不能在转换JSON时被转为UNICODE
                    $menuList[$key]['sub_button'][$k]['name'] = $son['name'];
                }
            }
        }
        //整理格式
        $data = array();
        $menuList = array_values($menuList);
        $data['button'] = $menuList;
        return $data;
    }

}

/* End of file menu_ajax.php */
/* Location: ./application/controllers/menu_ajax.php */ ?>