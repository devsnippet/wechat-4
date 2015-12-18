<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entrance extends CI_Controller {

    private $wid = null;
    private $flag = null;

    public function __construct(){
        parent::__construct();
        $this->load->model('wechat_setting_model', 'setting');
        $this->load->model('wechat_reply_model', 'reply');
        $this->load->model('wechat_user_model', 'user');
        $this->load->model('wechat_user_details_model', 'user_details');
        $this->load->model('wechat_event_model', 'event');
        $this->load->model('wechat_code_model', 'code');
        $this->load->helper('tools');
    }

    public function index($flag = "")
    {
        $this->flag = $flag;
        if ($flag == "") {
            die("Give me wechat flag");
        }
        //根据传入$flag来获得相应的微信信息
        $wechat_info = $this->setting->setting_get(array('flag' => $flag));
        if (!$wechat_info) {
            die("Wechat flag not exists");
        }
        $this->wid = $wechat_info['wid'];
        $options = array(
            'token' => $wechat_info['token'], //填写你设定的key
            'encodingaeskey' => $wechat_info['EncodingAESKey'], //填写加密用的EncodingAESKey
            'appid' => $wechat_info['appid'], //填写高级调用功能的app id
            'appsecret' => $wechat_info['appsecret'] //填写高级调用功能的密钥
            );
        //载入Wechat并且实例化了
        $this->load->library('Wechat', $options, 'wechat');
        //用于微信服务器短点的认证
        $this->wechat->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $this->wechat->getRev()->getRevType();
        //获取用户的信息

        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                    //插入或更新用户数据
                    $this->getUserInfomation($this->wechat->getRev()->getRevFrom());
                    $data = $this->handleText($this->wechat->getRev()->getRevContent());
                    $data = $this->strReplace($data);
                    $data = htmlspecialchars_decode($data);
                    $this->wechat->text($data)->reply();
                    exit;
                    break;
            case Wechat::MSGTYPE_EVENT:
                    //插入或更新用户数据
                    $this->getUserInfomation($this->wechat->getRev()->getRevFrom());
                    $event = $this->wechat->getRev()->getRevEvent();
                    $data = $this->handleEvent($event);
                    $data = htmlspecialchars_decode($data);
                    $this->wechat->text($data)->reply();
                    break;
            case Wechat::MSGTYPE_IMAGE:
                    //插入或更新用户数据
                    $this->getUserInfomation($this->wechat->getRev()->getRevFrom());
                    $picInfo = $this->wechat->getRevPic();
                    $this->wechat->text($picInfo['mediaid'])->reply();
                    $this->wechat->text($picInfo['picurl'])->reply();                    
                    break;
            default:
                    //插入或更新用户数据
                    //$this->getUserInfomation($this->wechat->getRev()->getRevFrom());
                    //$this->wechat->text("help info")->reply();
        }
    }

    /**
     * 接收文字型事件并且响应之
     * @param  string $recivedContent 传入文字
     * @return mixed                字符串或者数组
     */
    private function handleText($recivedContent){
        $content = clean_content($recivedContent);
        $accountBindRegexStr = "/#(.*?)#(\d{11,30})#/";
        $pregResult = preg_match_all($accountBindRegexStr, $content, $matches);
        if ($pregResult) {// if preg successful
            // $result = array(
            //     'server' => $matches[1][0],
            //     'userid' => $matches[2][0]
            // );
            // $result = json_encode($result);
            // $db_data = array(
            //     'bindinfo' => $result
            //     );

            $db_data = array(
                'server' => $matches[1][0],
                'gameuid' => $matches[2][0]
            );

            // $user_info = $this->user->user_get(array('wid' => $this->wid, 'openid' => $this->wechat->getRev()->getRevFrom()));
            if ($this->user_details->user_details_mod(array('raw_openid' => $this->wechat->getRev()->getRevFrom()), $db_data)) {
                 return "更新账号绑定信息成功";
            }
        }
        $re = false;
        $keyword = $content;
        // 尝试匹配手机号
        $phone_reg = '#(\D|^)(\d{11})(\D|$)#';
        if (preg_match($phone_reg, $content, $matches))
        {
            $phone_num = $matches[2];
            $result_arr = $this->reply->get_by_wid_cat_name($this->wid, 'phone_num');
            if (!empty($result_arr))
            {
                $re = $result_arr[0];
            }
        }
        // 如果没有手机号匹配或者没有设定手机号匹配的回复内容，尝试关键词严格匹配
        if (empty($re))
        {
            $result_arr = $this->reply->get_exact_matches($this->wid, $keyword);
            if (empty($result_arr))
            {
                // 如果没有关键词严格匹配，尝试关键词模糊匹配
                $record_arr = $this->reply->get_by_wid_cat_name($this->wid, 'vague_match');
                foreach ($record_arr as $record)
                {
                    if (strpos($keyword, $record['alias1']) !== false || strpos($keyword, $record['alias2']) !== false)
                    {
                        $re = $record; 
                        break;
                    }
                }
            }
        }

        //查询到关键词的信息
        if ($re) {
            $reply_type = $re['reply_type'];
            //文本型回复 返回处理过的文字内容
            if ($reply_type == "text") {
                return $re['reply'];
            }
            if ($reply_type == "news") {
                //不return 在这里直接reply
                if ($re['extra'] != "" || $re['extra'] != null) {
                    $img_msg = json_decode($re['extra'], true);
                    //回复内容
                    // var_dump($img_msg);
                    $this->wechat->news($img_msg)->reply();
                }else{
                    return "内容为空";
                }
            }
            if ($reply_type == "event") {
                //签到事件
                if ($re['extra'] != "" || $re['extra'] != null) {
                    $checkin_txt = json_decode($re['extra'], true);
                    $re = $this->checkin($this->wechat->getRev()->getRevFrom());
                    if ($re) {
                        if ($re['status']) {
                            //签到成功
                            $result = str_replace(array("%scount", "%lcount", "%smark", "%cmark"), array($re['scount'], $re['lcount'], $re['smark'], $re['cmark']), $checkin_txt['reply_success']);
                            if (isset($re['lottery'])) {
                                // $result .= "您已经积攒".$re['lottery']."次抽奖机会了.";
                            }
                            return $result;
                        }else if(!$re['status']){
                            return str_replace(array("%smark"), array($re['smark']), $checkin_txt['reply_failed']);
                        }
                    }
                }

            }
            if ($reply_type == "code") {
                //发码
                if ($re['extra'] != "" || $re['extra'] != null) {
                    $code_txt = json_decode($re['extra'], true);
                    //先判是否领取过码
                    if ($oCode = $this->isDrawCode($this->wechat->getRev()->getRevFrom(), $code_txt['reply_event'])) {
                        return "您已经领取过本次活动的礼包码，码为".$oCode;//
                    }
                    //先判断是Apibox发码还是本地发码
                    if ($code_txt['method'] == "apibox") {//如果是apibox
                        $wechat_info = $this->setting->setting_get(array('flag' => $this->flag));
                        $options  = array('http' => array('user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36'));
                        $context  = stream_context_create($options);
                        if ($code_txt['language'] == "simple") {
                            //简体中文apibox发码渠道
                            $simpleApi = sprintf("http://apibox.mobage.cn/api/index.php/kit/api_comp/mid_post/32?__APP=%s&phone=%s&event=%s", $wechat_info['packageAppid'], $this->wechat->getRev()->getRevFrom(), $code_txt['reply_event']);
                            $packageRe = file_get_contents($simpleApi, false, $context);
                            if ($packageRe) {
                                    $packageReDe = json_decode($packageRe, true);
                                    if ($packageReDe['error_code'] == "0" || $packageReDe['error_code'] == "10") {//发码API无错误
                                        $code = $packageReDe['code'];
                                        $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code);
                                        return str_replace(array("%code"), array($code), $code_txt['reply_success']);
                                    }else{
                                        return "发码接口异常";
                                    }
                            }
                        }
                        if ($code_txt['language'] == "trad") {
                            //繁体中文apibox发码渠道
                            $tradApi = sprintf("http://apibox.mobage.cn/api/index.php/kit/api_comp/mid_post/49?__APP=%s&phone=%s&event=%s", $wechat_info['packageAppid'], $this->wechat->getRev()->getRevFrom(), $code_txt['reply_event']);
                            $packageRe = file_get_contents($tradApi, false, $context);
                            if ($packageRe) {
                                if ($packageRe) {
                                    $packageReDe = json_decode($packageRe, true);
                                    if ($packageReDe['error_code'] == "0" || $packageReDe['error_code'] == "10") {//发码API无错误
                                        $code = $packageReDe['code'];
                                        $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code);
                                        return str_replace(array("%code"), array($code), $code_txt['reply_success']);
                                    }else{
                                        return "发码接口异常";
                                    }
                                }
                            }
                        }
                    }
                    if ($code_txt['method'] == "direct") {//如果是本地直接发码
                        $event = $this->event->event_get(array('event_name' => $code_txt['reply_event']));
                        if ($event) {
                            $code = $this->code->code_get_random($this->wid, $event['gid']);
                            if ($code) {
                                $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));
                                //标记用户已经领取的礼包活动和礼包码
                                $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code['code']);
                                return str_replace(array("%code"), array($code['code']), $code_txt['reply_success']);
                            }
                        }
                        return $code_txt['reply_failed'];
                    }
                }
            }
            if ($reply_type == "lottery") {
                if ($re['extra'] != "" || $re['extra'] != null) {
                    $lottery_txt = json_decode($re['extra'], true);
                    if ($lottery_txt['method'] == "local") {//判断下奖池来源 为了将来可能拓展到apibox
                        //先判断是否在有效期
                        $start_time = $lottery_txt['start_time'] / 1000; //除以1000 把时间戳还原回标准格式
                        $end_time = $lottery_txt['end_time'] / 1000;//同上
                        $now_time = time();

                        if ($now_time < $start_time || $now_time > $end_time) {
                            return $lottery_txt['reply_pending'];
                        }

                        //获取用户详细信息
                        $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $this->wechat->getRev()->getRevFrom()));
                        $user_info_detail = $this->user_details->user_details_get(array('uid' => $user_info['uid']));

                        if ($lottery_txt['lmethod'] == "marks") {//如果是积分抽奖
                            if (($user_info_detail['integration'] > $lottery_txt['marks'])) {
                                //从奖池拿一个奖品出来先 代码等同于领礼包码
                                $event = $this->event->event_get(array('event_name' => $lottery_txt['lottery_event']));
                                if ($event) {
                                    $code = $this->code->code_get_random($this->wid, $event['gid']);
                                    if ($code) {
                                        //减掉一个抽奖机会
                                        $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('integration' => $user_info_detail['integration'] - $lottery_txt['marks']));
                                        $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));//将奖品置为已领取
                                        return str_replace(array("%code"), array($code['code']), $lottery_txt['reply_success']);
                                    }else{
                                        return "您来晚了，奖品都被抢光了";
                                    }
                                }
                            }else{
                                return str_replace(array("%dmark"), array($user_info_detail['integration']-$lottery_txt['marks']), $lottery_txt['reply_failed']);
                            }
                        }

                        if ($lottery_txt['lmethod'] == "lottery") {//如果是抽奖机会抽奖
                            //判断是否具有抽奖资格
                            //先判断用户现在是否有抽奖机会
                            //判断用户总积分是否达到条件
                            if (($user_info_detail['integration'] > $lottery_txt['rules']) && ($user_info_detail['lottery'] > 0)) {
                                //从奖池拿一个奖品出来先 代码等同于领礼包码
                                $event = $this->event->event_get(array('event_name' => $lottery_txt['lottery_event']));
                                if ($event) {
                                    $code = $this->code->code_get_random($this->wid, $event['gid']);
                                    if ($code) {
                                        //减掉一个抽奖机会
                                        $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('lottery' => $user_info_detail['lottery']-1));
                                        $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));//将奖品置为已领取
                                        return str_replace(array("%code"), array($code['code']), $lottery_txt['reply_success']);
                                    }else{
                                        return "您来晚了，奖品都被抢光了";
                                    }
                                }
                            }else{
                                return $lottery_txt['reply_failed'];
                            }
                        }

                    }
                }
            }
        }else{//查找默认条目的回复信息
            $re = $this->reply->reply_get(array('wid'=>$this->wid,'alias1' => 'default'), array('alias2' => '默认回复信息'));
            if ($re) {
                $reply_type = $re['reply_type'];
                //文本型回复 返回处理过的文字内容
                if ($reply_type == "text") {
                    return $re['reply'];
                }
                if ($reply_type == "news") {
                    //不return 在这里直接reply
                    if ($re['extra'] != "" || $re['extra'] != null) {
                        $img_msg = json_decode($re['extra'], true);
                        //回复内容
                        // var_dump($img_msg);
                        $this->wechat->news($img_msg)->reply();
                    }else{
                        return "内容为空";
                    }
                }
                if ($reply_type == "event") {
                    //签到事件
                    if ($re['extra'] != "" || $re['extra'] != null) {
                        $checkin_txt = json_decode($re['extra'], true);
                        $re = $this->checkin($this->wechat->getRev()->getRevFrom());
                        if ($re) {
                            if ($re['status']) {
                                //签到成功
                                $result = str_replace(array("%scount", "%lcount", "%smark", "%cmark"), array($re['scount'], $re['lcount'], $re['smark'], $re['cmark']), $checkin_txt['reply_success']);
                                if (isset($re['lottery'])) {
                                    // $result .= "您已经积攒".$re['lottery']."次抽奖机会了.";
                                }
                                return $result;
                            }else if(!$re['status']){
                                return str_replace(array("%smark"), array($re['smark']), $checkin_txt['reply_failed']);
                            }
                        }
                    }
                }
                if ($reply_type == "code") {
                    //发码
                    if ($re['extra'] != "" || $re['extra'] != null) {
                        $code_txt = json_decode($re['extra'], true);
                        //先判是否领取过码
                        if ($oCode = $this->isDrawCode($this->wechat->getRev()->getRevFrom(), $code_txt['reply_event'])) {
                            return "您已经领取过本次活动的礼包码，码为".$oCode;//
                        }
                        //先判断是Apibox发码还是本地发码
                        if ($code_txt['method'] == "apibox") {//如果是apibox
                            $wechat_info = $this->setting->setting_get(array('flag' => $this->flag));
                            $options  = array('http' => array('user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36'));
                            $context  = stream_context_create($options);
                            if ($code_txt['language'] == "simple") {
                                //简体中文apibox发码渠道
                                $simpleApi = sprintf("http://apibox.mobage.cn/api/index.php/kit/api_comp/mid_post/32?__APP=%s&phone=%s&event=%s", $wechat_info['packageAppid'], $this->wechat->getRev()->getRevFrom(), $code_txt['reply_event']);
                                $packageRe = file_get_contents($simpleApi, false, $context);
                                if ($packageRe) {
                                        $packageReDe = json_decode($packageRe, true);
                                        if ($packageReDe['error_code'] == "0" || $packageReDe['error_code'] == "10") {//发码API无错误
                                            $code = $packageReDe['code'];
                                            $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code);
                                            return str_replace(array("%code"), array($code), $code_txt['reply_success']);
                                        }else{
                                            return "发码接口异常";
                                        }
                                }
                            }
                            if ($code_txt['language'] == "trad") {
                                //繁体中文apibox发码渠道
                                $tradApi = sprintf("http://apibox.mobage.cn/api/index.php/kit/api_comp/mid_post/49?__APP=%s&phone=%s&event=%s", $wechat_info['packageAppid'], $this->wechat->getRev()->getRevFrom(), $code_txt['reply_event']);
                                $packageRe = file_get_contents($tradApi, false, $context);
                                if ($packageRe) {
                                    if ($packageRe) {
                                        $packageReDe = json_decode($packageRe, true);
                                        if ($packageReDe['error_code'] == "0" || $packageReDe['error_code'] == "10") {//发码API无错误
                                            $code = $packageReDe['code'];
                                            $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code);
                                            return str_replace(array("%code"), array($code), $code_txt['reply_success']);
                                        }else{
                                            return "发码接口异常";
                                        }
                                    }
                                }
                            }
                        }
                        if ($code_txt['method'] == "direct") {//如果是本地直接发码
                            $event = $this->event->event_get(array('event_name' => $code_txt['reply_event']));
                            if ($event) {
                                $code = $this->code->code_get_random($this->wid, $event['gid']);
                                if ($code) {
                                    $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));
                                    //标记用户已经领取的礼包活动和礼包码
                                    $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code['code']);
                                    return str_replace(array("%code"), array($code['code']), $code_txt['reply_success']);
                                }
                            }
                            return $code_txt['reply_failed'];
                        }
                    }
                }
                if ($reply_type == "lottery") {
                    if ($re['extra'] != "" || $re['extra'] != null) {
                        $lottery_txt = json_decode($re['extra'], true);
                        if ($lottery_txt['method'] == "local") {//判断下奖池来源 为了将来可能拓展到apibox
                            //先判断是否在有效期
                            $start_time = $lottery_txt['start_time'] / 1000; //除以1000 把时间戳还原回标准格式
                            $end_time = $lottery_txt['end_time'] / 1000;//同上
                            $now_time = time();

                            if ($now_time < $start_time || $now_time > $end_time) {
                                return $lottery_txt['reply_pending'];
                            }

                            //获取用户详细信息
                            $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $this->wechat->getRev()->getRevFrom()));
                            $user_info_detail = $this->user_details->user_details_get(array('uid' => $user_info['uid']));

                            if ($lottery_txt['lmethod'] == "marks") {//如果是积分抽奖
                                if (($user_info_detail['integration'] > $lottery_txt['marks'])) {
                                    //从奖池拿一个奖品出来先 代码等同于领礼包码
                                    $event = $this->event->event_get(array('event_name' => $lottery_txt['lottery_event']));
                                    if ($event) {
                                        $code = $this->code->code_get_random($this->wid, $event['gid']);
                                        if ($code) {
                                            //减掉一个抽奖机会
                                            $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('integration' => $user_info_detail['integration'] - $lottery_txt['marks']));
                                            $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));//将奖品置为已领取
                                            return str_replace(array("%code"), array($code['code']), $lottery_txt['reply_success']);
                                        }else{
                                            return "您来晚了，奖品都被抢光了";
                                        }
                                    }
                                }else{
                                    return str_replace(array("%dmark"), array($user_info_detail['integration']-$lottery_txt['marks']), $lottery_txt['reply_failed']);
                                }
                            }

                            if ($lottery_txt['lmethod'] == "lottery") {//如果是抽奖机会抽奖
                                //判断是否具有抽奖资格
                                //先判断用户现在是否有抽奖机会
                                //判断用户总积分是否达到条件
                                if (($user_info_detail['integration'] > $lottery_txt['rules']) && ($user_info_detail['lottery'] > 0)) {
                                    //从奖池拿一个奖品出来先 代码等同于领礼包码
                                    $event = $this->event->event_get(array('event_name' => $lottery_txt['lottery_event']));
                                    if ($event) {
                                        $code = $this->code->code_get_random($this->wid, $event['gid']);
                                        if ($code) {
                                            //减掉一个抽奖机会
                                            $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('lottery' => $user_info_detail['lottery']-1));
                                            $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));//将奖品置为已领取
                                            return str_replace(array("%code"), array($code['code']), $lottery_txt['reply_success']);
                                        }else{
                                            return "您来晚了，奖品都被抢光了";
                                        }
                                    }
                                }else{
                                    return $lottery_txt['reply_failed'];
                                }
                            }

                        }
                    }
                }
            }
        }
    }

    /**
     * 接收事件型事件并且响应之
     * @param  array  $eventInfo 传入的事件信息
     * @return mixed            字符串或者数组
     */
    private function handleEvent($eventInfo){
        $event_name = $eventInfo['event'];
        $event_key = $eventInfo['key'];
        switch ($event_name) {
            case 'subscribe':
                //用户信息入库
                $nickname = $this->getUserInfomation($this->wechat->getRev()->getRevFrom());
                //从库中取一个叫subscribe的值 用以显示回复
                $subscribe_content = $this->reply->subscribe_get($this->wid);
                if ($subscribe_content) {
                    $pos = strpos($subscribe_content, '%nickname');
                    if ($pos !== false) {
                        //替换nickname
                        return str_replace('%nickname', $nickname, $subscribe_content);
                    }
                    return $subscribe_content;
                }
                return "感谢您的关注!";
                break;
            case 'unsubscribe':
                //取消关注事件 返回回复信息
                break;
            case 'click':
            case 'CLICK':
                return $this->handleClick($event_key);
                //自定义菜单事件 继续接着处理 传入$event_key
                break;
            default:
                return $content = "未识别事件";
                break;
        }
    }

    private function handleClick($eventKey){
        $event_key = $eventKey;
        switch ($event_key) {
            case 'account':
                return $this->accountBind($this->wechat->getRev()->getRevFrom());
                break;
            case 'reboundslist':
            case 'reboundlist':
            case 'Reboundslist':
            case 'ReboundsList':
                //篮板榜单
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=2&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "篮板");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'PointsList':
            case 'PointList':
            case 'pointslist':
            case 'pointlist':
                //得分
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=1&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "得分");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'AssistsList':
            case 'assistslist':
            case 'assistlist':
            case 'assistslist':
                //助攻榜单
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=3&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "助攻");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'StealsList':
            case 'stealslist':
            case 'stealslist':
            case 'steallist':
                //抢断榜单
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=4&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "抢断");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'TurnoversList':
            case 'Turnoverslist':
            case 'Turnoverlist':
            case 'turnoverslist':
            case 'turnoverlist':
                //失误榜单
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=6&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "失误");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'FieldGoalList':
            case 'fieldgoallist':
            case 'Fieldgoallist':
            case 'fieldGoalList':
            case 'fieldGoallist':
                //神投榜单
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=7&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "神投");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'ThreePointList':
            case 'threepointpist':
            case 'ThreePointlist':
            case 'ThreepointList':
            case 'threePointList':
                //三分榜单
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=8&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "三分榜单");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'FreeThrowList':
            case 'freethrowlist':
            case 'freeThrowList':
            case 'FreethrowList':
            case 'FreeThrowlist':
                //罚球
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=playerorder&start=0&end=10&type=place&category=9&qq-pf-to=pcqq.c2c';
                $returnStr = $this->GetBoundList($target_url, "三分榜单");
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            case 'EastWestList':
            case 'eastwestlist':
                $target_url = 'http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=basketball&_sport_s_=nba&_sport_a_=teamorder&type=seed&qq-pf-to=pcqq.c2c';
                $reboundslist = file_get_contents($target_url);
                $json_decode_arr = json_decode($reboundslist, true);
                $returnStr = "东西部排名榜单\r\n东部排名\r\n胜场\t败场\t队伍\t\t类别\r\n";
                foreach ($json_decode_arr['result']['data']['east'] as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if ($key2 == 'wins') {
                            $returnStr .= $value2."\t\t";
                        }
                        if ($key2 == 'losses') {
                            $returnStr .= $value2."\t\t";
                        }
                        if ($key2 == 'name_cn') {
                            $returnStr .= $value2."\t\t";
                        }
                        if ($key2 == 'kind_cn') {
                            $returnStr .= $value2."\r\n";
                        }
                    }
                }
                $returnStr .= "西部排名\r\n胜场\t败场\t队伍\t\t类别\r\n";
                foreach ($json_decode_arr['result']['data']['west'] as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if ($key2 == 'wins') {
                            $returnStr .= $value2."\t\t";
                        }
                        if ($key2 == 'losses') {
                            $returnStr .= $value2."\t\t";
                        }
                        if ($key2 == 'name_cn') {
                            $returnStr .= $value2."\t\t";
                        }
                        if ($key2 == 'kind_cn') {
                            $returnStr .= $value2."\r\n";
                        }
                    }
                }
                return $returnStr;
                break;
            case 'TodaySchedule':
            case 'todaySchedule':
            case 'Todayschedule':
            case 'todayschedule':
                $url = "http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=livecast&__version__=3.0.1.05&__os__=android&show_extra=1&f=livecast_id,LeagueType,status,Team1Id,Team2Id,Score1,Score2,MatchId,LiveMode,Discipline,data_from,Title,date,time,Team1,Team2,Flag1,Flag2,NewsUrl,VideoUrl,LiveUrl,LiveStatusExtra,VideoLiveUrl,VideoBeginTime,narrator,LeagueType_cn,Discipline_cn,comment_id,odds_id,VideoEndTime,if_rotate_video,LiveStatusExtra_cn,m3u8,android,period_cn,program,penalty1,penalty1,Round_cn,extrarec_ovxVideoId&_sport_a_=typeMatches&l_type=nba";
                $returnStr = $this->GetTodaySchedule($url);
                return ($returnStr != false) ? $returnStr : "获取失败，请再次尝试获取信息";
                break;
            //http://platform.sina.com.cn/sports_all/client_api?app_key=2586208540&_sport_t_=livecast&_sport_a_=hotMatches&__version__=2.5.1.8&__os__=android
            //处理数据库点击事件
            case $event_key:
                $keyword = $event_key;
                $re = $this->reply->reply_get(array('wid'=>$this->wid,'alias1' => $keyword), array('alias2' => $keyword));
                //查询到关键词的信息
                if ($re) {
                    $reply_type = $re['reply_type'];
                    //文本型回复 返回处理过的文字内容
                    if ($reply_type == "text") {
                        return $re['reply'];
                    }
                    if ($reply_type == "news") {
                        //不return 在这里直接reply
                        if ($re['extra'] != "" || $re['extra'] != null) {
                            $img_msg = json_decode($re['extra'], true);
                            //回复内容
                            // var_dump($img_msg);
                            $this->wechat->news($img_msg)->reply();
                        }else{
                            return "内容为空";
                        }
                    }
                    if ($reply_type == "event") {
                        //签到事件
                        if ($re['extra'] != "" || $re['extra'] != null) {
                            $checkin_txt = json_decode($re['extra'], true);
                            $re = $this->checkin($this->wechat->getRev()->getRevFrom());
                            if ($re) {
                                if ($re['status']) {
                                    //签到成功
                                    $result = str_replace(array("%scount", "%lcount", "%smark", "%cmark"), array($re['scount'], $re['lcount'], $re['smark'], $re['cmark']), $checkin_txt['reply_success']);
                                    if (isset($re['lottery'])) {
                                        // $result .= "您已经积攒".$re['lottery']."次抽奖机会了.";
                                    }
                                    return $result;
                                }else if(!$re['status']){
                                    return str_replace(array("%smark"), array($re['smark']), $checkin_txt['reply_failed']);
                                }
                            }else{
                                return "签到出错";
                            }
                        }

                    }
                    if ($reply_type == "code") {
                        //发码
                        if ($re['extra'] != "" || $re['extra'] != null) {
                            $code_txt = json_decode($re['extra'], true);
                            //先判是否领取过码
                            if ($oCode = $this->isDrawCode($this->wechat->getRev()->getRevFrom(), $code_txt['reply_event'])) {
                                return "您已经领取过本次活动的礼包码".$oCode;//
                            }
                            //先判断是Apibox发码还是本地发码
                            if ($code_txt['method'] == "apibox") {//如果是apibox
                                $wechat_info = $this->setting->setting_get(array('flag' => $this->flag));
                                $options  = array('http' => array('user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36'));
                                $context  = stream_context_create($options);
                                if ($code_txt['language'] == "simple") {
                                    //简体中文apibox发码渠道
                                    $simpleApi = sprintf("http://apibox.mobage.cn/api/index.php/kit/api_comp/mid_post/32?__APP=%s&phone=%s&event=%s", $wechat_info['packageAppid'], $this->wechat->getRev()->getRevFrom(), $code_txt['reply_event']);
                                    $packageRe = file_get_contents($simpleApi, false, $context);
                                    if ($packageRe) {
                                        if ($packageRe) {
                                            $packageReDe = json_decode($packageRe, true);
                                            if ($packageReDe['error_code'] == "0" || $packageReDe['error_code'] == "10" ) {//发码API无错误
                                                $code = $packageReDe['code'];
                                                $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code);
                                                return str_replace(array("%code"), array($code), $code_txt['reply_success']);
                                            }else{
                                                return "发码接口异常";
                                            }
                                        }
                                    }
                                }
                                if ($code_txt['language'] == "trad") {
                                    //繁体中文apibox发码渠道
                                    $tradApi = sprintf("http://apibox.mobage.cn/api/index.php/kit/api_comp/mid_post/49?__APP=%s&phone=%s&event=%s", $wechat_info['packageAppid'], $this->wechat->getRev()->getRevFrom(), $code_txt['reply_event']);
                                    $packageRe = file_get_contents($tradApi, false, $context);
                                    if ($packageRe) {
                                        if ($packageRe) {
                                            $packageReDe = json_decode($packageRe, true);
                                            if ($packageReDe['error_code'] == "0" || $packageReDe['error_code'] == "10") {//发码API无错误
                                                $code = $packageReDe['code'];
                                                $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code);
                                                return str_replace(array("%code"), array($code), $code_txt['reply_success']);
                                            }else{
                                                return "发码接口异常";
                                            }
                                        }
                                    }
                                }
                            }
                            if ($code_txt['method'] == "direct") {//如果是本地直接发码
                                $event = $this->event->event_get(array('event_name' => $code_txt['reply_event']));
                                if ($event) {
                                    $code = $this->code->code_get_random($this->wid, $event['gid']);
                                    if ($code) {
                                        $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));
                                        $this->tagDrawed($this->wechat->getRev()->getRevFrom(),  $code_txt['reply_event'], $code['code']);
                                        return str_replace(array("%code"), array($code['code']), $code_txt['reply_success']);
                                    }
                                }
                                return $code_txt['reply_failed'];
                            }
                        }
                    }
                    if ($reply_type == "lottery") {
                        if ($re['extra'] != "" || $re['extra'] != null) {
                            $lottery_txt = json_decode($re['extra'], true);
                            if ($lottery_txt['method'] == "local") {//判断下奖池来源 为了将来可能拓展到apibox
                                //先判断是否在有效期
                                $start_time = $lottery_txt['start_time'] / 1000; //除以1000 把时间戳还原回标准格式
                                $end_time = $lottery_txt['end_time'] / 1000;//同上
                                $now_time = time();

                                if ($now_time < $start_time || $now_time > $end_time) {
                                    return $lottery_txt['reply_pending'];
                                }

                                //获取用户详细信息
                                $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $this->wechat->getRev()->getRevFrom()));
                                $user_info_detail = $this->user_details->user_details_get(array('uid' => $user_info['uid']));

                                if ($lottery_txt['lmethod'] == "marks") {//如果是积分抽奖
                                    if (($user_info_detail['integration'] > $lottery_txt['marks'])) {
                                        //从奖池拿一个奖品出来先 代码等同于领礼包码
                                        $event = $this->event->event_get(array('event_name' => $lottery_txt['lottery_event']));
                                        if ($event) {
                                            $code = $this->code->code_get_random($this->wid, $event['gid']);
                                            if ($code) {
                                                //减掉一个抽奖机会
                                                $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('integration' => $user_info_detail['integration'] - $lottery_txt['marks']));
                                                $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));//将奖品置为已领取
                                                return str_replace(array("%code"), array($code['code']), $lottery_txt['reply_success']);
                                            }else{
                                                return "您来晚了，奖品都被抢光了";
                                            }
                                        }
                                    }else{
                                        return str_replace(array("%dmark"), array($user_info_detail['integration']-$lottery_txt['marks']), $lottery_txt['reply_failed']);
                                    }
                                }

                                if ($lottery_txt['lmethod'] == "lottery") {//如果是抽奖机会抽奖
                                    //判断是否具有抽奖资格
                                    //先判断用户现在是否有抽奖机会
                                    //判断用户总积分是否达到条件
                                    if (($user_info_detail['integration'] > $lottery_txt['rules']) && ($user_info_detail['lottery'] > 0)) {
                                        //从奖池拿一个奖品出来先 代码等同于领礼包码
                                        $event = $this->event->event_get(array('event_name' => $lottery_txt['lottery_event']));
                                        if ($event) {
                                            $code = $this->code->code_get_random($this->wid, $event['gid']);
                                            if ($code) {
                                                //减掉一个抽奖机会
                                                $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('lottery' => $user_info_detail['lottery']-1));
                                                $this->code->code_mod(array('cid' => $code['cid']), array('isUsed' => 1, 'usedTime' => time()));//将奖品置为已领取
                                                return str_replace(array("%code"), array($code['code']), $lottery_txt['reply_success']);
                                            }else{
                                                return "您来晚了，奖品都被抢光了";
                                            }
                                        }
                                    }else{
                                        return $lottery_txt['reply_failed'];
                                    }
                                }

                            }
                        }
                    }
                }else{
                    $re = $this->reply->reply_get(array('wid'=>$this->wid,'alias1' => 'default'), array('alias2' => '默认回复信息'));
                    if ($re) {
                        $reply_type = $re['reply_type'];
                        //文本型回复 返回处理过的文字内容
                        if ($reply_type == "text") {
                            return $re['reply'];
                        }
                    }
                }
                break;
            default:

                break;
        }

    }

    /**
     * 获取用户信息 新入库/更新
     * @param  string $openid 用户OpenID
     * @return string         用户的昵称 或 空白
     */
    private function getUserInfomation($openid){
        $infofull = $this->user->user_union_info_full($this->wid, $openid);
        if (!$infofull) {
            //读取一次用户信息更新入库，以获取unionid等额外信息
            $user_info = $this->wechat->getUserInfo($openid);
            if ($user_info) {
                $db_data = array();
                $db_data['wid'] = $this->wid;
                $db_data['openid'] = isset($user_info['openid']) && !empty($user_info['openid']) ? $user_info['openid'] : '';
                $db_data['unionid'] = isset($user_info['unionid']) && !empty($user_info['unionid']) ? $user_info['unionid'] : '';
                $db_data['nickname'] = isset($user_info['nickname']) && !empty($user_info['nickname']) ? urlencode($user_info['nickname']) : '';//urlencode处理昵称包含表情
                $db_data['city'] = isset($user_info['city']) && !empty($user_info['city']) ? $user_info['city'] : '';
                $db_data['province'] = isset($user_info['province']) && !empty($user_info['province']) ? $user_info['province'] : '';
                $db_data['country'] = isset($user_info['country']) && !empty($user_info['country']) ? $user_info['country'] : '';
                $db_data['headimgurl'] = isset($user_info['headimgurl']) && !empty($user_info['headimgurl']) ? $user_info['headimgurl'] : '';
                $db_data['subscribe_time'] = isset($user_info['subscribe_time']) && !empty($user_info['subscribe_time']) ? $user_info['subscribe_time'] : '';
                $db_data['subscribe'] = $user_info['subscribe'];
                switch ($user_info['sex']) {
                    case 0:
                        $db_data['sex'] = "未填写";
                        break;
                    case 1:
                        $db_data['sex'] = "男";
                        break;
                    case 2:
                        $db_data['sex'] = "女";
                        break;
                    default:
                        $db_data['sex'] = "未知";
                        break;
                }
                switch ($user_info['language']) {
                    case 'zh_CN':
                        $db_data['language'] = "中国";
                        break;
                    case '':
                        $db_data['language'] = "未填写";
                        break;
                    case 'en':
                        $db_data['language'] = "美国";
                        break;
                    default:
                        # code...
                        break;
                }
                $exist = $this->user->user_is_exist($this->wid, $user_info['openid']);
                if (!$exist) {
                    $uid = $this->user->user_add($db_data);
                    //联动创建user_details
                    $db_data_user_details = array('uid' => $uid, 'wid' => $this->wid, 'raw_openid' => $openid);
                    $this->user_details->user_details_add($db_data_user_details);
                }else{
                    $user_base_info = $this->user->user_get(array('wid' => $this->wid,'openid'=>$openid));
                     $user_detail_info = $this->user_details->user_details_get(array('wid' => $this->wid, 'raw_openid' => $openid));
                    if(!$user_detail_info){//联动创建user_details
                     $db_data_user_details = array('uid' => $user_base_info['uid'], 'wid' => $this->wid, 'raw_openid' => $openid);
                    $add_res = $this->user_details->user_details_add($db_data_user_details);
                        
                    }
                    if($user_info['subscribe'] == 0){//取消关注
                        $this->user->user_update($this->wid, $user_info['openid'], array('subscribe'=>0));
                    }else{
                        $this->user->user_update($this->wid, $user_info['openid'], $db_data);
                    }
                    
                }

                return $user_info['nickname'];
            }
            return "";
        }else{
            $user = $this->user->user_get(array("wid" => $this->wid, "openid" => $openid));
            return urldecode($user['nickname']);//urldecode处理昵称包含表情
        }
        return "";
    }



    /**
     * 签到函数
     * @param  string $openid 用户OpenID
     * @return string         返回签到成功,失败的字样
     */
    private function checkin($openid){
       // $infofull = $this->user->user_union_info_full($this->wid, $openid);
       // if (!$infofull) {
            //读取一次用户信息更新入库，以获取unionid等额外信息
        //    $this->getUserInfomation($openid);
       // }
        $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $openid));
        //返回的数据
        $return_data = array();
        if ($user_info) {//如果有数据 开始拿东西签到
            $now_time = time();
                //insert data
            $db_data = array(
                'lastcheckin' => $now_time
            );
            $user_info_detail = $this->user_details->user_details_get(array('uid' => $user_info['uid']));
            if ($user_info_detail['lastcheckin'] && $user_info_detail['lastcheckin'] != null) {
                $lastcheckin_timestamp = intval($user_info_detail['lastcheckin']);
                // var_dump($lastcheckin_timestamp);
                //24h timestamp
                $checkin_month = date('m', $lastcheckin_timestamp);
                $now_month = date('m');
                $day = date('d', $lastcheckin_timestamp);
                $tomorrow_date_one = date('Y').'-'.date('m').'-';
                $tomorrow_date_two = $day+1;
                if ($tomorrow_date_two > 31) {//mod by zhouyuqi 2015/07/31
                    $next_month = date('m', strtotime('next month',$now_time));
                    $tomorrow_date_one = date('Y').'-'.$next_month.'-';
                    $tomorrow_date_two = 1;
                }
                $valid_checkin_timestamp = $tomorrow_zero_hours_timestamp = strtotime($tomorrow_date_one.$tomorrow_date_two);
                // var_dump($tomorrow_zero_hours_timestamp);
                //calculate diff time length from now to end of today
                // $diff_timestamp = $tomorrow_zero_hours_timestamp - $now_time;
                // $valid_checkin_timestamp = $lastcheckin_timestamp + $diff_timestamp;
                if ($checkin_month < $now_month) {
                    // 签到月份小于现在的月份 说明跨月了
                    // 那么直接给他签到 不用判断天了
                    // if ($now_time <= $valid_checkin_timestamp) {
                    //     $return_data['status'] = false;
                    //     $return_data['smark'] = $user_info_detail['integration'];
                    //     return $return_data;
                    // }
                }else{
                    //如果签到月份等于现在的月份
                    if ($now_time <= $valid_checkin_timestamp) {
                        $return_data['status'] = false;
                        $return_data['smark'] = $user_info_detail['integration'];
                        return $return_data;
                    }
                }
            }
            $wechatinfo = $this->setting->setting_get(array('wid' => $this->wid));
            $old_signcount = $user_info_detail['signcount'];//累计签到
            $old_linearcount = $user_info_detail['linercount'];//连续签到
            if ($old_signcount == 0) {
                $old_signcount = 0;
                $old_integration = 0;
                $old_linearcount = 0;
                $ever_checkin_marks = $wechatinfo['checkinvalue'];
                $new_signcount = $old_signcount+1;
                $new_linearcount = $old_linearcount+1;
                $extra_integration = $wechatinfo['first_checkin_value'];
                $new_integration = $old_integration+$ever_checkin_marks+$extra_integration;
                $db_data['signcount'] = $new_signcount;
                $db_data['integration'] = $new_integration;
                if ($this->user_details->user_details_mod(array('uid' =>$user_info['uid']), $db_data)) {
                    $return_data['status'] = true;
                    $return_data['scount'] = $new_signcount;
                    $return_data['lcount'] = $new_linearcount;
                    $return_data['smark'] = $new_integration;
                    $return_data['cmark'] = $extra_integration+$ever_checkin_marks;

                    //判断当前签到总积分 来给增加抽奖机会
                    $every_marks = floor($new_signcount / $wechatinfo['lucky_rule']);
                    if ($every_marks > $user_info_detail['lottery_current_times']) {
                        //倍数大于当前的倍数状态 可以增加抽奖机会
                        $new_lottery_num = $user_info_detail['lottery'] + $wechatinfo['lucky_num'];
                        $return_data['lottery'] = $new_lottery_num;
                        $update_data = array('lottery' => $new_lottery_num);
                        $this->user_details->user_details_mod(array('uid' =>$user_info['uid']), $update_data);//更新最新的抽奖次数
                        $this->setting->setting_mod(array('wid' => $this->wid), array('lucky_current_times' => $every_marks));

                    }

                    // return $returnStr = sprintf("恭喜您首次签到成功，累计签到%u次，连续签到天数为%u，累计签到积分为%u，其中首次签到额外赠送%u分", $new_signcount, $new_linearcount, $new_integration, $extra_integration);
                }
            }else{
                $new_linearcount = 0;
                if (time() - $user_info_detail['lastcheckin'] > 24*60*60*2) {//设置间隔大于24小时 则置空连续签到 再+1
                    $db_data['linercount'] = 1;
                }else{
                    $new_linearcount = $db_data['linercount'] = $old_linearcount + 1;
                }
                $old_integration = $user_info_detail['integration'];//取之前的分数
                $ever_checkin_marks = $wechatinfo['checkinvalue'];
                $new_signcount =$old_signcount+1;
                $new_integration = $old_integration+$ever_checkin_marks;
                $db_data['signcount'] = $new_signcount;
                $db_data['integration'] = $new_integration;
                if ($this->user_details->user_details_mod(array('uid' =>$user_info['uid']), $db_data)) {
                    $return_data['status'] = true;
                    $return_data['scount'] = $new_signcount;
                    $return_data['lcount'] = $new_linearcount;
                    $return_data['smark'] = $new_integration;
                    $return_data['cmark'] = $ever_checkin_marks;
                    // return $returnStr = sprintf("恭喜您签到成功，累计签到%u次，连续签到天数为%u， 累计签到积分为%u，", $new_signcount, $new_linearcount, $new_integration);
                    //
                    //判断当前签到总积分 来给增加抽奖机会
                    $every_marks = floor($new_integration / $wechatinfo['lucky_rule']);
                    var_dump($every_marks);
                    if ($every_marks > $user_info_detail['lottery_current_times']) {
                        //倍数大于当前的倍数状态 可以增加抽奖机会
                        $new_lottery_num = $user_info_detail['lottery'] + $wechatinfo['lucky_num'];
                        $return_data['lottery'] = $new_lottery_num;
                        $update_data = array('lottery' => $new_lottery_num, 'lottery_current_times' =>$every_marks);
                        $this->user_details->user_details_mod(array('uid' =>$user_info['uid']), $update_data);//更新最新的抽奖次数和抽奖比例
                    }
                }
            }
        }
        if (!empty($return_data)) {
            return $return_data;
        }
        return false;
    }

    /**
     * 处理用户绑定数据
     * @param  string $openid 用户的openid 直接从wechat信息中get出来
     * @return string         返回的提示信息
     */
    private function accountBind($openid){
        // $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $openid));
        // $wechatinfo = $this->wechat_model->wechat_setting_info($wid);
        $user_info_detail = $this->user_details->user_details_get(array('raw_openid' => $openid));
        // var_dump($user_details);
        if ($user_info_detail['server'] == "" || $user_info_detail['server'] == null || $user_info_detail['gameuid'] == "" || $user_info_detail['gameuid'] == null ) {
            return $returnStr = "您还没有绑定游戏账号信息, 请回复\r\n#服务器#游戏ID#\r\n进行绑定";
        }else{
            // $json_decode_arr = json_decode($user_info_detail['bindinfo'], true);
            return $returnStr = "您绑定的账号信息内容如下：\r\n服务器:{$user_info_detail['server']}\r\n游戏ID:{$user_info_detail['gameuid']}\r\n更新绑定请回复\r\n#服务器#游戏ID#\r\n进行换绑";
        }
    }


    /**
     * 常规替换占位符
     * @param  string $text 传入的待替换字符串
     * @return string       替换后的字符串
     */
    private function strReplace($text){
        return str_replace(array('%tab', '%nl'), array("\t", "\r\n"), $text);
    }

    /**
     * 是否已经领取过本批次的礼包码
     * @param  string  $openid    用户的openid
     * @param  string  $eventname 礼包码活动名称
     * @return mixed            已经领取过的的礼包信息 键值对数组 或者 false
     */
    private function isDrawCode($openid, $eventname){
        $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $openid));
        $user_info_detail = $this->user_details->user_details_get(array('uid' => $user_info['uid']));
        if ($user_info_detail) {
            $draw_info = json_decode($user_info_detail['isdrawed'], true);
            if (array_key_exists($eventname, $draw_info)) {//如果获取到键名 就返回键值
                return $draw_info[$eventname];
            }
            if (strpos($user_info_detail['isdrawed'], $eventname)) {//字符串检索的方式来处理
                // $draw_info = json_decode($user_info_detail['isdrawed'], true);
                return $draw_info[$eventname];
            }
        }
        return false;
    }

    /**
     * 标记已经领取的礼包码
     * @param  string $openid    字符串
     * @param  string $eventname 礼包码活动名称
     * @param  string $code      礼包码信息
     * @return none            none
     */
    private function tagDrawed($openid, $eventname, $code){
        $user_info = $this->user->user_get(array("wid" => $this->wid, "openid" => $openid));
        $user_info_detail = $this->user_details->user_details_get(array('uid' => $user_info['uid']));
        if ($user_info_detail) {
            if ($user_info_detail['isdrawed'] != "" || $user_info_detail['isdrawed'] != null) {
                $old_drawed = json_decode($user_info_detail['isdrawed'], true);
                // var_dump($old_drawed);
                // array_push($old_drawed, array($eventname => $code));
                $old_drawed[$eventname] = $code;
                $new_drawed = json_encode($old_drawed);
                $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('isdrawed' => $new_drawed));
            }else{
                $str = json_encode(array($eventname => $code));
                $this->user_details->user_details_mod(array('uid' => $user_info['uid']), array('isdrawed' => $str));
            }
        }
    }

    private function GetBoundList($targeturl, $title){
        $target_url = $targeturl;
        $reboundslist = file_get_contents($target_url);
        if ($reboundslist) {
            $json_decode_arr = json_decode($reboundslist, true);
            $returnStr = "实时{$title}榜单\r\n排名\t\t姓名\t\t\t队伍\r\n";
            foreach ($json_decode_arr['result']['data']['data'] as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if ($key2 == 'ranking') {
                        $returnStr .= $value2."\t\t";
                    }
                    if ($key2 == 'fullname_cn') {
                        $returnStr .= $value2."\t\t";
                    }
                    if ($key2 == 'team_name_cn') {
                        $returnStr .= $value2."\r\n";
                    }
                }
            }
            return $returnStr;
        }
        return false;
    }

    /**
     * 获取新浪赛程函数
     * @param string $url 新浪体育赛程信息api接口 数据来源于新浪体育APP
     */
    private function GetTodaySchedule($url){
        $schedule = file_get_contents($url);
        if ($schedule) {
            $schedule_decode_arr = json_decode($schedule, true);
            $returnStr = "赛程/比分：\r\n";
            $returnStr .= "---------------------------\r\n\r\n";
            $returnStr .= "今日赛程：" . date("Y-m-d") . "\r\n\r\n";
            if (!empty($schedule_decode_arr['result']['data']['full'])) {
                $arrCount = count($schedule_decode_arr['result']['data']['full']);
                $copyArr = $schedule_decode_arr['result']['data']['full'];
                // var_dump($copyArr);
                for ($index=0; $index < $arrCount; $index++) {
                    if ($copyArr[$index]['date'] == date("Y-m-d")) {
                        $returnStr .= $copyArr[$index]['Team1'] . "\t\t" . $copyArr[$index]['Score1'] . "-" . $copyArr[$index]['Score2'] ."\t\t" . $copyArr[$index]['Team2'] . "\r\n\r\n";
                    }
                }
                $returnStr .= "\r\n---------------------------";

            }else{
                return "今天没有赛事";
            }
            if (!empty($schedule_decode_arr['result']['data']['cur'])) {
                $arrCount = count($schedule_decode_arr['result']['data']['cur']);
                $copyArr = $schedule_decode_arr['result']['data']['cur'];

                $returnStr .= "\r\n正在进行：" . date("Y-m-d") . "\r\n\r\n";
                for ($index=0; $index < $arrCount; $index++) {
                    $returnStr .= $copyArr[$index]['Title'] . "\r\n";
                }
                $returnStr .= "---------------------------";
            }
            //date("Y-m-d",strtotime("+1 day"));
            if (!empty($schedule_decode_arr['result']['data']['pre'])) {
                $arrCount = count($schedule_decode_arr['result']['data']['pre']);
                $copyArr = $schedule_decode_arr['result']['data']['pre'];

                $tomorrow_time = date("Y-m-d",strtotime("+1 day"));
                $returnStr .= "\r\n\r\n下一个比赛日：" . $tomorrow_time . "\r\n\r\n";
                for ($index=0; $index < $arrCount; $index++) {
                    if ($copyArr[$index]['date'] == $tomorrow_time) {
                        $returnStr .= $copyArr[$index]['Team1'] . " VS " . $copyArr[$index]['Team2'] . "\r\n";
                    $returnStr .= $copyArr[$index]['time'] . "\r\n\r\n";
                    }

                }
                $returnStr .= "---------------------------";
            }

            return $returnStr;
        }


        return false;
    }

}

/* End of file entrance.php */
/* Location: ./application/controllers/entrance.php */
 ?>
