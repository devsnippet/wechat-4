<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('admin/wechat_model');
        $this->load->library('session');
        $this->load->library('Wechatdev');
        $this->load->helper('url');
        $this->load->helper('date');
        if (!$this->session->userdata('uid') && $this->session->userdata('logged') != true) {
            redirect('admin/account', 'localtion', 301);
        }
        if ($this->session->userdata('game_id')) {
            $this->game_id = $this->session->userdata('game_id');
        }
        if($this->input->get('gid')){
            $this->game_id = $this->input->get('gid');
            $this->session->set_userdata(array('game_id'=>$this->game_id));
        }
        // $this->game_id = 12000119;
        // $this->game_id = 0;

    }

    public function gsetting($gameid = false){
        $data = array();
        if ($gameid != false) {
            $data['game_info'] = $this->wechat_model->game_info($gameid);
            if (empty($data['game_info'])) {
                $data['action'] = 'insert';
            }else{
                $data['action'] = 'update';
            }
        }

        if($this->input->post("action")=='insert' || $this->input->post("action")=='update'){
            if ($this->input->post('game_id') != "" && $this->input->post('name') != "") {
                $db_data = array(   'game_id'=>$this->input->post('game_id'),
                                'name'=>$this->input->post('name'),
                                'icon_120'=>$this->input->post('icon'),
                );
            
                if($this->input->post("action")=='insert'){
                    $wid = $this->wechat_model->wechat_gsetting_add($db_data);
                }else{
                    unset($db_data['game_id']);
                    $this->wechat_model->wechat_gsetting_edit($db_data,$this->input->post('game_id'));
                }
            }
            redirect("admin/wechat/");
        }

        // $data['action'] = 'update';
        $this->load->view('admin/wechat/header');
        // $this->load->view('admin/wechat/menu',$data);
        $this->load->view('admin/wechat/gsetting',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function index(){
        $data['list'] = $this->wechat_model->game_list();
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/gamelist',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function choose($gameid = false){
        if ($gameid) {
            $this->session->unset_userdata('game_id');
            $this->session->set_userdata(array('game_id' => $gameid));
            redirect('admin/wechat/wlist');
        }else{
            redirect('admin/wechat/');
        }
    }

    public function wlist(){
        if(!$this->session->userdata('game_id')){
            redirect('admin/wechat', 'localtion', 301);//如果没有选择游戏 跳过去
        }
        //从数据库中拿到所有信息，如果没有则跳转到setting，如果有则index
        $data['list'] = $this->wechat_model->wechat_setting_list($this->game_id);
        $data['active'] = "";
        $data['wid'] = "";
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/list',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function elist($wid = false){
        if(!$this->session->userdata('game_id')){
            redirect('admin/wechat', 'localtion', 301);//如果没有选择游戏 跳过去
        }
        if ($wid != false) {
            $this->session->set_userdata(array('wid' => $wid));
        }
        //从数据库中拿到所有信息，如果没有则跳转到setting，如果有则index
        $data['list'] = $this->wechat_model->event_list($this->game_id, $wid);
        
        if ($data['list']) {
            //add name
            $data['list'][0]['name'] = "";
            $gameid = isset($data['list'][0]['game_id'])?$data['list'][0]['game_id']:'';
            $gameinfo = '';
            if ($gameid != '') {
                $gameinfo = $this->wechat_model->game_info($gameid);
                for ($index=0; $index < count($data['list']); $index++) { 
                    $data['list'][$index]['name'] = $gameinfo['name'];
                }
                
            }
        }
        
        $data['active'] = "";
        $data['wid'] = $wid;
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/elist',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function codelist($wid = false, $event_id = false){
        if(!$this->session->userdata('game_id')){
            redirect('admin/wechat', 'localtion', 301);//如果没有选择游戏 跳过去
        }
        $data = array();;
        if ($wid && $event_id) { // 正确输入$wid和$event_id
            $codeList = $this->wechat_model->game_code_list($wid, $event_id);
            if ($codeList) {
                $data['list'] = $codeList;
                $gid = $codeList[0]['gid'];
                $eventInfo = $this->wechat_model->event_info_gid($gid);
                if ($eventInfo) {
                    for ($index=0; $index < count($data['list']); $index++) { 
                        $data['list'][$index]['event_name'] = $eventInfo['event_name'];
                    }
                }
            }
            $data['wid'] = $wid;
            $data['gid'] = $event_id;
        }
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/codelist', $data);
        $this->load->view('admin/wechat/footer');
    }

    public function csetting($type = "add", $wid = false , $gid = false){
        $data['wid'] = $wid;
        $data['gid'] = $gid;
        $data['message'] = "";
        switch ($type) {
            case 'add':
                $giftcode = $this->input->post('giftcode');
                $db_insert = array();
                if ($giftcode) {
                    $codeArray = explode("\n", $giftcode);
                    for ($index=0; $index < count($codeArray); $index++) { 
                        if ($codeArray[$index] != "") {
                            $db_data = array(
                                'gid' => $gid,
                                'wid' => $wid,
                                'code' => $codeArray[$index],
                                'isUsed' => 0,
                                'usedTime' => "",
                            );
                            array_push($db_insert, $db_data);
                        }
                    }
                    $insertId = $this->wechat_model->game_code_insert($db_insert);
                    if ($insertId) {
                        $data['message'] = "导入成功";
                    }
                }
                $this->load->view('admin/wechat/header');
                $this->load->view('admin/wechat/csetting', $data);
                $this->load->view('admin/wechat/footer');
                break;
            case 'edit':

                break;
            case 'delete':
                $cid = $this->input->post('cid');
                //删除之...
                $this->wechat_model->game_code_delete($cid);
                break;
            default:
                # code...
                break;
        }
        

    }

    public function esetting($wid = false, $event_name = false){
        if(!$this->session->userdata('game_id')){
            redirect('admin/wechat', 'localtion', 301);//如果没有选择游戏 跳过去
        }
        // if ($this->session->userdata('game_id')) {
        //     $data['game_info'] = $this->wechat_model->game_info($this->session->userdata('game_id'));
        // }
        $data['tag'] = 1;
        if ($event_name != false) {
            $data['event_info'] = $this->wechat_model->event_info($event_name);
            if (!$data['event_info']) {
                $data['action'] = 'insert';
                $data['tag'] = 1;
            }else{
                $data['action'] = 'update';
                $data['gid'] = $data['event_info']['gid'];
                $data['tag'] = 0;
            }
        }
        $data['game_id'] = $this->game_id;
        
        $data['gameid'] = $this->session->userdata('game_id');
        $data['wid'] = ($wid == true) ? $wid : $this->session->userdata('wid');
        if($this->input->post("action")=='insert' || $this->input->post("action")=='update'){
            if ($this->input->post('event_name') != "" ) {
                $db_data = array(
                    'game_id' => $data['gameid'],
                    'wid' => $wid,
                    'event_name'=>$this->input->post('event_name'),
                    'event_info'=>$this->input->post('event_info'),
                );
            
                if($this->input->post("action")=='insert'){
                    $aid = $this->wechat_model->wechat_esetting_add($db_data);
                }else{
                    $this->wechat_model->wechat_esetting_edit($db_data, $data['gid'], $data['gameid'], $wid);
                }
            }
            redirect("admin/wechat/elist/$wid");
        }

        

        $this->load->view('admin/wechat/header');
        // $this->load->view('admin/wechat/menu',$data);
        $this->load->view('admin/wechat/esetting',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function setting($wid=false){
        $data['error'] = 0;
        $data['game_info'] = $this->game_info();
        $game_id = $data['game_info']['game_id'];
        $data['wechat_info'] = $this->wechat_model->wechat_setting_info($wid);
        if(empty($data['wechat_info'])){
            $data['action'] = "insert";
        }else{
            $data['action'] = "update";
        }
        if($this->input->post("action")=='insert' || $this->input->post("action")=='update'){
            $db_data = array(   'name'=>$this->input->post('name'),
                                'token'=>$this->input->post('token'),
                                'appid'=>$this->input->post('appid'),
                                'appsecret'=>$this->input->post('appsecret'),
                                'game_id'=>$this->game_id,
                                'EncodingAESKey'=>$this->input->post('EncodingAESKey'),
                                'addtime'=>now(),
                                'checkinvalue'=>intval($this->input->post('checkinvalue')),
                                'packageAppid'=>$this->input->post('packageAppid'),
            );
            foreach($db_data as $v){
                if(empty($v) || strlen($v)<4){
                    // $data['error'] = 1;
                    // TODO check form information
                }
            }
            $db_data['type'] = $this->input->post('type');
            if($data['error'] == 0){    //入库
                if($this->input->post("action")=='insert'){
                    $wid = $this->wechat_model->wechat_setting_add($db_data);
                }else{
                    unset($db_data['game_id']);
                    $this->wechat_model->wechat_setting_edit($db_data,$wid);
                }
                redirect("admin/wechat/wlist/{$this->game_id}");
            }
        }
       
        $data['active'] = 'setting';
        $data['wid'] = $wid;
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/menu',$data);
        $this->load->view('admin/wechat/setting',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function test(){
        var_dump($this->wechat_model->wechat_details_update(array('isdrawed' => 1), 4));
    }
    
    public function bar($wid){  //设定和获取菜单
        $data['game_info'] = $this->game_info();
        
        $menus = $this->wechat_model->wechat_custom_menu_get($wid);
        if ($menus) {
            $data['bar_arr'] = $this->wechat_model->wechat_custom_menu_get($wid);
        }else{
            $data['bar_arr'] = array();
        }
        // var_dump($data['bar_arr']);
        $data['active'] = 'bar';
        $data['wid'] = $wid;
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/menu',$data);
        $this->load->view('admin/wechat/bar',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function fetch($wid){
        $data['game_info'] = $this->game_info();
        // $data['game_info']['name'] = "test";
        $wechat_info = $this->wechat_model->wechat_setting_info($wid);
        $bar_json = $this->wechatdev->wechat_get_custom_menu($wid);
        $bar_arr = json_decode($bar_json,true);
        if (!isset($bar_arr['errcode']) || $bar_arr['errcode']!= 46003) {
            $index = 0;
            // $this->wechat_model->wehcat_custom_menu_empty();
            $this->wechat_model->wechat_custom_menu_del_by_wid($wid);
            $num = 0;
            //empty table
            foreach ($bar_arr['menu']['button'] as $key => $value) {
                if (isset($index2)) {
                    $index = $index2;
                }
                $pid = $index++;
                $num++;
                $type = isset($value['type'])?$value['type']:null;
                $name = isset($value['name'])?$value['name']:null;
                $code = isset($value['key'])?$value['key']:null;
                $url = isset($value['url'])?$value['url']:null;
                $coder = "";
                if ($code == "") {
                    $coder = $url;
                }else{
                    $coder = $code;
                }
                $datao = array('name' => $name, 'type' => $type, 'code' => $coder, 'wid' => $wid, 'mapnum' => $num);
                $this->wechat_model->wechat_custom_menu_add_single($datao);
                if (!empty($value['sub_button'])) {
                    $index2 = $index;
                    foreach ($value['sub_button'] as $key2 => $value2) {
                        $index2++;
                        $num++;
                        $type2 = isset($value2['type'])?$value2['type']:null;
                        $name2 = isset($value2['name'])?$value2['name']:null;
                        $code2 = isset($value2['key'])?$value2['key']:null;
                        $url2 = isset($value2['url'])?$value2['url']:null;
                        $coder2 = "";
                        if ($code2 == "") {
                            $coder2 = $url2;
                        }else{
                            $coder2 = $code2;
                        }
                        $datat = array('pid' => $pid+1, 'name' => $name2, 'type' => $type2, 'code' => $coder2, 'wid' => $wid, 'mapnum' => $num);
                        $this->wechat_model->wechat_custom_menu_add_single($datat);
                    }
                }
            }
            echo json_encode(array('status' => 200, 'message' => '读取远程自定义菜单成功'));
        }else{
            echo json_encode(array('status' => 500, 'message' => '自定义菜单不存在'));
        }
        
    }

    public function reply($wid){
        if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
            if (trim($this->input->post("reply")) != "" && trim($this->input->post("target")) != "") {
                $link_register = $this->input->post("link_register");
                $link_language = $this->input->post("link_language");
                $isApibox = $this->input->post('isApibox');
                $language = null;
                if (!empty($link_register)) {
                    $language = empty($link_language)?"simplified":$link_language;
                }
                if ($isApibox == false) {
                    $isApiboxTag = null;
                }
                if ($isApibox != false && $isApibox == "noApibox") {
                    $isApiboxTag = 0;
                }
                $db_data = array(   'wid'=>$wid,
                                    'target'=>trim($this->input->post("target")),
                                    'reply'=>htmlspecialchars(trim($this->input->post("reply"))),
                                    'link_type'=>empty($link_register)?NULL:"register",
                                    'link_value'=>empty($link_register)?NULL:$this->input->post("link_register"),
                                    'link_language'=>$language,
                                    'addtime'=>now(),
                                    'isApibox' => $isApiboxTag,
                                );

                if($this->input->post("action")=="insert"){
                    $this->wechat_model->wechat_reply_add($db_data);
                }elseif($this->input->post("action")=="update"){
                    $rid = $this->input->post("rid");
                    $this->wechat_model->wechat_reply_update($db_data,$rid);
                }
            }
            if (trim($this->input->post("disabled")) != "" && trim($this->input->post("reply")) == "" && trim($this->input->post("target")) == "") {
                $rid = $this->input->post("rid");
                $disabled = $this->input->post("disabled");
                $db_data = array(
                    'disabled' => $disabled,
                    );
                $this->wechat_model->wechat_reply_update($db_data,$rid);
            }
        }
        elseif($this->input->post("action")=="delete"){
            $rid = $this->input->post("rid");
            echo $this->wechat_model->wechat_reply_delete($rid);
        }
        $data['event_list'] = $this->wechat_model->event_list_reply($wid);
        $data['register_list'] = $this->wechat_model->register_list($this->game_id);
        $data['game_info'] = $this->game_info();
        $wechat_info = $this->wechat_model->wechat_setting_info($wid);
        $wechat_reply_list = $this->wechat_model->wechat_reply_list($wid);
        $data['list'] = $wechat_reply_list;
        $data['active'] = 'reply';
        $data['wid'] = $wid;
        $this->load->view('admin/wechat/header');
        $this->load->view('admin/wechat/menu',$data);
        $this->load->view('admin/wechat/reply',$data);
        $this->load->view('admin/wechat/footer');
    }

    public function checkin($wid)
    {//TODO do it

    }

    
    private function game_info(){
        return $this->wechat_model->game_info($this->game_id);
    }
    
    
    
}