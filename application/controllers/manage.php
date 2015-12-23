<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        $uid = $this->session->userdata('uid');
        $nick = $this->session->userdata('nick');
        $token = $this->session->userdata('token');

        if (!$nick || !$token) {//MOD 2015-08-25 判断是否有nick以及token 该两个数据来自南天门
            redirect('/admin/account/', 'location', 301);
        }

        //装载各种组件
        $this->load->helper('tools');
        $this->load->model('wechat_gamelist_model', "gamelist");
        $this->load->model('wechat_setting_model', "setting");
        $this->load->model('wechat_menu_model', "menu");
        $this->load->model('wechat_reply_model', "reply");
        $this->load->model('wechat_event_model', "event");
        $this->load->model('wechat_code_model', "code");
        $this->load->model('wechat_tool_model', "tool");
        $this->load->model('wechat_template_model');
    }

    //获取所有游戏列表
    public function index()
    {
        $this->session->unset_userdata('game_id');
        $data['list'] = $this->gamelist->gamelist_get_all();
        //传入信息给header使用
        $data['current_user'] = $this->session->userdata('nick');
        $data['game_id'] = $gid = $this->session->userdata('game_id');
        if ($gid) {
            $current_game_info = $this->gamelist->gamelist_get(array('game_id' => $gid));
            $data['game_name'] = $current_game_info['name'];
        }else{
            $data['game_name'] = null;
        }
        $data['active'] = "game";
        $this->load->view('manage/header_view', $data);
        $this->load->view('manage/game_list_body_view');
        $this->load->view('manage/footer_view');
    }

    /**
     * 微信管理主体函数
     * @param  string  $type    类型 判断各种
     * @param  integer $gameid  游戏ID
     * @param  id  $wxid    微信ID
     * @param  id  $eventid 发礼包码活动的id
     * @param  mixed  $codeo   礼包码的东东
     * @return nonx           none
     */
    public function wechat($type="view", $gameid = 0, $wxid=null, $eventid=null, $codeo=null){
        $data['flag'] = ($gameid == 0) ? false : true;
        $data['current_user'] = $this->session->userdata('nick');
        //传入header需要的相关信息
        $data['game_id'] = $gid = $this->session->userdata('game_id');
        $game_name = null;
        if ($gid) {
            $current_game_info = $this->gamelist->gamelist_get(array('game_id' => $gid));
            $game_name = $data['game_name'] = $current_game_info['name'];
        }else{
            $data['game_name'] = null;
        }
        $data['wxid'] = $wxid;
        $data['eventid'] = $eventid;
        $data['codeo'] = $codeo;
        //确定type塞入$data数组传给header
        switch ($type) {
            case 'view':
            case 'wxedit':
                $data['active'] = "wechat";
                break;
            case 'menu':
                $data['active'] = "menu";
                break;
            case 'reply':
                $data['active'] = "reply";
                break;
            case 'event':
                $data['active'] = "event";
                break;
            case 'template':
                $data['active'] = "template";
                break;
            default:
                $data['active'] = "";
                break;
        }

        $this->load->view('manage/header_view', $data);

        //查看某游戏下的微信
        if ($type == "view") {
            if ($gameid != 0) {
                if ($this->session->userdata('game_id') == false) {
                    $this->session->set_userdata(array('game_id' => $gameid));
                    redirect(current_url(), 'location',303);
                }
                $data['list'] = $this->setting->wechat_list_get_all($gameid);
            }
            $data['gameid'] = $gameid;
            $this->load->view('manage/wechat_list_body_view', $data);
        }

        //编辑添加的微信号的相关信息
        if ($type == "wxedit") {
            $data['error'] = 0;
            $data['gameid'] = $gameid;//直接从参数获得gameid赋值过去
            $data['wechat_info'] = $this->setting->setting_get(array('wid' => $wxid, 'game_id' => $gameid));
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
                                    'game_id'=>$this->input->post('game_id'),
                                    'EncodingAESKey'=>$this->input->post('EncodingAESKey'),
                                    'addtime'=>time(),
                                    'flag' => generate_random_str(8,true,true,true),
                                    'first_checkin_value' => $this->input->post('first_checkin_value'),
                                    'checkinvalue'=>intval($this->input->post('checkinvalue')),
                                    'packageAppid'=>$this->input->post('packageAppid'),
                                    'lucky_rule' => intval($this->input->post('lucky_rule')),
                                    'lucky_num' => intval($this->input->post('lucky_num')),
                                    // 'lucky_current_times' => intval($this->input->post('lucky_current_times')),
                );
                $db_data['type'] = $this->input->post('type');
                if($data['error'] == 0){    //入库
                    if($this->input->post("action")=='insert'){
                        $re = $this->setting->setting_add($db_data);
                        if ($re) {
                            redirect("manage/wechat/view/$gameid/", 'location', 303);
                        }
                    }else{
                        unset($db_data['game_id']);
                        unset($db_data['flag']);
                        $re = $this->setting->setting_mod(array('wid' => $wxid, 'game_id' => $gameid), $db_data);
                        if ($re) {
                            redirect("manage/wechat/view/$gameid/", 'location', 303);
                        }
                    }

                    // redirect("admin/wechat/wlist/{$this->game_id}");
                }
            }
            $this->load->view('manage/wechat_edit_body_view', $data, FALSE);
        }

        if ($type == "gmedit") {
            $data['gameid'] = $gameid;//直接从参数获得gameid赋值过去
            $data['game_info'] = $this->gamelist->gamelist_get(array('game_id' => $gameid));
            if(empty($data['game_info'])){
                $data['action'] = "insert";
            }else{
                $data['action'] = "update";
            }
            if($this->input->post("action")=='insert' || $this->input->post("action")=='update'){
                $db_data = array(   'game_id'=>$this->input->post('game_id'),
                                    'name'=>$this->input->post('name'),
                                    'icon_120'=>$this->input->post('icon'),
                );
                if($this->input->post("action")=='insert'){
                    $re = $this->gamelist->gamelist_add($db_data);
                    if ($re) {
                        redirect("manage/wechat/view/$gameid/", 'location', 303);
                    }
                }else{
                    $re = $this->gamelist->gamelist_mod(array('game_id' => $gameid), $db_data);
                    if ($re) {
                        redirect("manage/wechat/view/$gameid/", 'location', 303);
                    }
                }
            }
            $this->load->view('manage/game_edit_body_view', $data, FALSE);
        }

        //显示当前微信号下的自定义菜单
        if ($type == "menu") {
            $menus = $this->menu->menu_get(array('wid' => $wxid));
            if ($menus) {
                $data['bar_arr'] = $menus;
            }else{
                $data['bar_arr'] = array();
            }

            $data['wid'] = $wxid;
            $data['active'] = 'menu';
            $this->load->view('manage/menu_body_view',$data);
        }

        //自定义回复页面
        if ($type == "reply") {
            $data['wid'] = $wxid;
            $reply_type = $this->input->post("reply_type");
            //文本型回复设置
            if ($reply_type == "text") {
                if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                    // if (trim($this->input->post("reply")) != "" && trim($this->input->post("alias1")) != "" && trim($this->input->post("alias2")) != "") {
                    // TODO...for phone number match, alias1 and alias2 can be empty. You can modify the code to
                    // check more specifically.
                    if (trim($this->input->post("reply")) != "") {
                        $db_data = array(
                            'wid' => $wxid,
                            'cat_name' => $this->input->post('cat_name'),
                            'alias1' => $this->input->post("alias1"),
                            'alias2' => $this->input->post("alias2"),
                            'reply' => htmlspecialchars(trim($this->input->post("reply"))),
                            'addtime' => time(),
                            'disabled' => 0,
                            'reply_type' => 'text',
                            );
                        if($this->input->post("action")=="insert"){
                            $this->reply->reply_add($db_data);
                        }elseif($this->input->post("action")=="update"){
                            $rid = $this->input->post("rid");
                            $this->reply->reply_mod(array('rid' => $rid),$db_data);
                        }
                    }
                }
            }
            //图文消息型回复
            if ($reply_type == "news") {
                if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                    if (trim($this->input->post("alias1")) != "" && trim($this->input->post("alias2")) != "" && trim($this->input->post("extra"))) {
                        $db_data = array(
                            'wid' => $wxid,
                            'alias1' => $this->input->post("alias1"),
                            'alias2' => $this->input->post("alias2"),
                            'extra' => $this->input->post("extra"),
                            'reply' => '',
                            'addtime' => time(),
                            'disabled' => 0,
                            'reply_type' => 'news',
                            );
                        if($this->input->post("action")=="insert"){
                            $this->reply->reply_add($db_data);
                        }elseif($this->input->post("action")=="update"){
                            $rid = $this->input->post("rid");
                            $this->reply->reply_mod(array('rid' => $rid),$db_data);
                        }
                    }
                }
            }
            //事件型回复 主要是签到用途
            if ($reply_type == "event") {
                if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                    // if (trim($this->input->post("alias1")) != "" && trim($this->input->post("alias2")) != "" && trim($this->input->post("extra"))) {
                    // TODO...for phone number match, alias1 and alias2 can be empty. You can modify the code to
                    // check more specifically.
                    if (trim($this->input->post("extra"))) { 
                        $db_data = array(
                            'wid' => $wxid,
                            'cat_name' => $this->input->post('cat_name'),
                            'alias1' => $this->input->post("alias1"),
                            'alias2' => $this->input->post("alias2"),
                            'extra' => $this->input->post("extra"),
                            'reply' => '',
                            'addtime' => time(),
                            'disabled' => 0,
                            'reply_type' => 'event',
                            );
                        if($this->input->post("action")=="insert"){
                            $this->reply->reply_add($db_data);
                        }elseif($this->input->post("action")=="update"){
                            $rid = $this->input->post("rid");
                            $this->reply->reply_mod(array('rid' => $rid),$db_data);
                        }
                    }
                }
            }
            //发码型回复
            if ($reply_type == "code") {
                if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                    if (trim($this->input->post("alias1")) != "" && trim($this->input->post("alias2")) != "" && trim($this->input->post("extra"))) {
                        $db_data = array(
                            'wid' => $wxid,
                            'alias1' => $this->input->post("alias1"),
                            'alias2' => $this->input->post("alias2"),
                            'extra' => $this->input->post("extra"),
                            'reply' => '',
                            'addtime' => time(),
                            'disabled' => 0,
                            'reply_type' => 'code',
                            );
                        if($this->input->post("action")=="insert"){
                            $this->reply->reply_add($db_data);
                        }elseif($this->input->post("action")=="update"){
                            $rid = $this->input->post("rid");
                            $this->reply->reply_mod(array('rid' => $rid),$db_data);
                        }
                    }
                }
            }
            //抽奖的
            if ($reply_type == "lottery") {
                if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                    if (trim($this->input->post("alias1")) != "" && trim($this->input->post("alias2")) != "" && trim($this->input->post("extra"))) {
                        $db_data = array(
                            'wid' => $wxid,
                            'alias1' => $this->input->post("alias1"),
                            'alias2' => $this->input->post("alias2"),
                            'extra' => $this->input->post("extra"),
                            'reply' => '',
                            'addtime' => time(),
                            'disabled' => 0,
                            'reply_type' => 'lottery',
                            );
                        if($this->input->post("action")=="insert"){
                            $this->reply->reply_add($db_data);
                        }elseif($this->input->post("action")=="update"){
                            $rid = $this->input->post("rid");
                            $this->reply->reply_mod(array('rid' => $rid),$db_data);
                        }
                    }
                }
            }
            //停用为通用 不区分类型
            if (trim($this->input->post("disabled")) != "" && trim($this->input->post("reply")) == "" && trim($this->input->post("rid")) != "") {
                    $rid = $this->input->post("rid");
                    $disabled = $this->input->post("disabled");
                    $db_data = array(
                        'disabled' => $disabled,
                        );
                    $this->reply->reply_mod(array('rid' => $rid), $db_data);
            }

            //删除
            if($this->input->post("action")=="delete"){
                $rid = $this->input->post("rid");
                echo $this->reply->reply_del(array('rid' => $rid, 'disabled' => 1));
            }
            //获取当前微信下的活动列表
            $data['event_list'] = $this->event->event_get_all(array('game_id' => $gameid, 'wid' => $wxid));
            $data['register_list'] = array();
            $data['list'] = $this->reply->reply_get_all(array('wid' => $wxid));
            $data['active'] = 'reply';
            $data['wid'] = $wxid;
            /* 获取当日签到人数 */
            $startTime = strtotime(date("Y-m-d"));//获取零点时间戳
            $endTime = $startTime+86399;//0点时间戳+23小时59分59秒时间戳
            $signCount = $this->tool->count_checkin($startTime, $endTime, $wxid);//统计该微信号下的签到人数
            $data['signCount'] = $signCount;
            $this->load->view('manage/reply_body_view',$data);
        }

        //本地发礼包码的活动
        if ($type == "event") {
            //从数据库中拿到所有信息，如果没有则跳转到setting，如果有则index
            if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                if (trim($this->input->post("event_name")) != "" && trim($this->input->post("event_info")) != "") {
                    $db_data = array(
                        'game_id' => $this->input->post("game_id"),
                        'wid' => $this->input->post("wid"),
                        'event_name' => $this->input->post("event_name"),
                        'event_info' => $this->input->post("event_info"),
                        );
                    if($this->input->post("action")=="insert"){
                        $result = $this->event->event_add($db_data);
                        if ($result) {
                            redirect("manage/wechat/event/$gameid/$wxid/", 'location', 303);
                        }
                    }elseif($this->input->post("action")=="update"){
                        $gid = $this->input->post("gid");
                        $result = $this->event->event_mod(array('gid' => $gid),$db_data);
                        if ($result) {
                            redirect("manage/wechat/event/$gameid/$wxid/", 'location', 303);
                        }
                    }
                }
            }
            if($this->input->post("action")=="delete"){
                $gid = $this->input->post("gid");
                //顺带把giftcode也一起删除了
                echo $this->event->event_del(array('gid' => $gid, 'game_id' => $gameid));
            }
            if ($eventid != null) {
                if ($eventid == "add") {
                    // 新增
                    $data['event_info'] = array();
                    $data['tag'] = 1;
                    $data['gameid'] = $gameid;
                    $data['wid'] = $wxid;
                    $this->load->view('manage/event_edit_body_view',$data);

                }else if(is_numeric($eventid)){
                    //开始判断codeo了 这个是唯一比较坑的 参数做了具体的特殊判断 特殊判断为code或者addcode 如果不为两者则接收为事件型id进行判断
                    if ($codeo == "code") {
                        //此微信活动下的码
                        $data['list'] = $this->code->code_get_all(array('wid' => $wxid, 'gid' => $eventid));
                        $this->load->view('manage/code_body_view',$data);
                        //删除某条数据
                        if($this->input->post("action")=="delete"){
                            $cid = $this->input->post('cid');
                            $wid = $this->input->post('wid');
                            $this->code->code_del(array('cid' => $cid, 'wid' => $wid));
                        }
                    }else if($codeo == "addcode"){
                        $data['wid'] = $wxid;
                        $data['gid'] = $eventid;
                        $data['message'] = "";
                        $data['type'] = "add";
                        if($this->input->post("action")=="insert" || $this->input->post("action")=="update"){
                            $giftcode = $this->input->post('giftcode');
                            $db_insert = array();
                            if ($giftcode) {
                                $codeArray = explode("\n", $giftcode);
                                for ($index=0; $index < count($codeArray); $index++) {
                                    if ($codeArray[$index] != "") {
                                        $db_data = array(
                                            'gid' => $eventid,
                                            'wid' => $wxid,
                                            'code' => $codeArray[$index],
                                            'isUsed' => 0,
                                            'usedTime' => "",
                                        );
                                        array_push($db_insert, $db_data);
                                    }
                                }
                                $insertId = $this->code->code_add_batch($db_insert);
                                if ($insertId) {
                                    $data['message'] = "导入成功";
                                }
                            }
                        }

                        $this->load->view('manage/code_edit_body_view', $data);
                    }else{
                        $data['event_info'] = $this->event->event_get(array('game_id' => $gameid, 'gid' => $eventid));
                        $data['gameid'] = $gameid;
                        $data['wid'] = $wxid;
                        $data['gid'] = $eventid;
                        $data['action'] = 'update';
                        $data['tag'] = 0;
                        $this->load->view('manage/event_edit_body_view',$data);
                    }

                }
            }else{
                $data['list'] = $this->event->event_get_all(array('game_id' => $gameid, 'wid' => $wxid));
                $data['game_name'] = $game_name;
                $data['gameid'] = $gameid;
                $data['wid'] = $wxid;
                $this->load->view('manage/event_body_view',$data);
            }
        }
        
        if($type == 'template'){//模板消息配置
            if($this->input->post('act') == 'edit'){
                $template_id = $this->input->post('template_id');
                $template_type = $this->input->post('type');
                $template_desc = $this->input->post('desc');
                for($i=0;$i<count($template_id);$i++){
                    $template_info[$i]['template_id'] = $template_id[$i];
                    $template_info[$i]['type'] = $template_type[$i];
                    $template_info[$i]['desc'] = $template_desc[$i];
                }
                $update_data = array(
                    'wid'=>$wxid,
                    'game_id'=>$gameid,
                    'template_info'=>json_encode($template_info)
                );
                $this->wechat_template_model->update_template_info($gameid,$wxid,$update_data);
                redirect(base_url("manage/wechat/template/{$gameid}/{$wxid}"),'refresh');
            }else{
                $template_list = $this->wechat_template_model->get_template_type();
            $template = $this->wechat_template_model->get_template_arr($wxid,$gameid);
            $template_arr = !empty($template)? json_decode($template['template_info'],true) : array();
            $data['template_arr'] = $template_arr;
            $data['template_list'] = !empty($template_list) ? $template_list : array();
            $this->load->view('manage/template_body_view',$data);
            }
            
            
        }

        //载入Footer
        $this->load->view('manage/footer_view');
    }

    /**
     * 读取微信服务器端的自定义菜单
     * @param  integer $wid 传入的微信ID参数
     * @return [type]       [description]
     */
    public function requestMenu($wid = 0){
        if ($wid == 0) {
            die("");
        }
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
            $bar_arr = $this->wechat->getMenu();

            if ($bar_arr) {
                $index = 0;
                $this->menu->menu_del(array('wid' => $wid));
                $num = 0;
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
                    $this->menu->menu_add($datao);
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
                            $this->menu->menu_add($datat);
                        }
                    }
                }
                echo json_encode(array('status' => 200, 'message' => '读取远程自定义菜单成功'));
            }else{
                echo json_encode(array('status' => 500, 'message' => '您填写的微信资料有误或自定义菜单不存在'));
            }
        }else{
            echo json_encode(array('status' => 500, 'message' => $this->wechat->errMsg));
        }
    }

}

/* End of file manage.php */
/* Location: ./application/controllers/manage.php */
 ?>