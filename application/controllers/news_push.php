<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class News_push extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('tools');
        $this->load->model('wechat_setting_model');
        $this->load->model('wechat_user_model');
        $this->load->model('wechat_user_details_model');
        $this->load->model('wechat_template_model');
    }

    /**
     * @param string $push_type 推送消息的类别，如模板消息template_msg等等
     */
    public function index($push_type = ''){
        if(empty($push_type)){
           return false;
        }

        //$json_data = $this->input->post('data');
        //$data_arr = json_decode($json_data,TRUE);
        $data_arr = array(
            'price'=>-5,
            'type'=>'M币',
            'time'=>time(),
            'total'=>50,
            'unionid'=>'ox1FTswRdBpN_t5VzRCvUqEGzzC4',
            'openid'=>'o4e19jj0-g7DUFxrD7XJHpvNbrzE',
            'user_id'=>'4534',
            'accout'=>'123456789',
            'user_device'=>'ios',
            'ip'=>'10.5.96.163',
            'appid'=>'wx414be44b17b98cf8',
            'sign'=>'sdsadsad'
            
        );
        $sign = isset($data_arr['sign']) && !empty($data_arr['sign']) ? $data_arr['sign'] : '';
        $appid = isset($data_arr['appid'])&& !empty($data_arr['appid']) ? $data_arr['appid'] : '';
        $openid = isset($data_arr['openid'])&& !empty($data_arr['openid']) ? $data_arr['openid'] : '';
        //需要传入发送消息的微信服务号appid
        if(empty($appid)){
            return false;
        }

        $wechat_info = $this->wechat_setting_model->setting_get(array('appid'=>$appid));
        if(empty($wechat_info)){
            return false;
        }
        $wid = $wechat_info['wid'];
        $template_info_temp = $this->wechat_template_model->get_template(array('wid'=>$wid));//获取该服务号下的消息模板
        if(!empty($template_info_temp)){//模板类别和模板ID的对应数组，type1/2/3/4/对应：充值通知，消费通知，登陆情况，绑定账号
        $template_info = json_decode($template_info_temp['template_info'],true);
            foreach($template_info as $key=>$val){

                    $template_arr[$val['type']]['template_id'] = $val['template_id']; 
                    $template_arr[$val['type']]['desc_url'] = $val['desc'];

            }
        }
        $options = array(
            'token' => $wechat_info['token'], //填写你设定的key
            'encodingaeskey' => $wechat_info['EncodingAESKey'], //填写加密用的EncodingAESKey
            'appid' => $wechat_info['appid'], //填写高级调用功能的app id
            'appsecret' => $wechat_info['appsecret'] //填写高级调用功能的密钥
            );
        //载入Wechat接口类并实例化
        $this->load->library('Wechat', $options, 'wechat');
        $user_info = $this->wechat->getUserInfo($openid);
        if(!$user_info || $user_info['subscribe'] == 0){//用户未关注该公众号，无法推送消息
            return false;
        }
        if(!empty($sign)){
            unset($data_arr['sign']);
            $sign_md5 = $this->gen_sign($data_arr);
        }else{
            return false;
            
        }

       // if($sign == $sign_md5){//请求验证通过
       if(1==1){
            if($push_type == 'account'){//账户余额信息的变动通知，充值或消费
                $account_type = isset($data_arr['type'])&& !empty($data_arr['type']) ? $data_arr['type'] : '';
                $total = isset($data_arr['total'])&& !empty($data_arr['total']) ? $data_arr['total'] : '';
                $time = isset($data_arr['time'])&& !empty($data_arr['time']) ? $data_arr['time'] : time();
                if(isset($data_arr['price']) && (int)$data_arr['price'] > 0){//购买行为
                    $template_id = $template_arr[1]['template_id'];
                    $url = $template_arr[1]['desc_url'];
                    $price = $data_arr['price'];
                    $template_push_data = array(
                    'touser'=>$openid,
                    'template_id'=>$template_id,
                    'url'=>$url,
                    'topcolor'=>'#FF0000',
                    'data'=>array(
                        'first'=>array(
                            'value'=>$user_info['nickname']."，"."您的".$account_type."金额有变动信息：",
                            'color'=>'#173177'
                        ),
                        //变动时间
                        'date'=>array(
                            'value'=>date("Y-m-d H:i:s",$time),
                            'color'=>'#173177'
                        ),
                        //变动金额
                        'adCharge'=>array(
                            'value'=>$price,
                            'color'=>'#173177'
                        ),
                        //金额类型，红包，M币，游戏币等
                        'type'=>array(
                           'value'=>$account_type,
                           'color'=>'#173177'
                        ),
                        //账户余额
                        'cashBalance'=>array(
                            'value'=>$total,
                            'color'=>'#173177'
                        ),
                        'remark'=>array(
                            'value'=>'及时关注账户变动，掌握账户安全~',
                            'color'=>'#173177'
                        )
                    )
                    );
                    //推送
                    $push_result = $this->wechat->sendTemplateMessage($template_push_data);
                    
                }else if(isset($data_arr['price']) && (int)$data_arr['price'] < 0){//消费行为
                    $template_id = $template_arr[2]['template_id'];
                    $url = $template_arr[2]['desc_url'];
                    $price = abs($data_arr['price']);//取绝对值
                    $template_push_data = array(
                    'touser'=>$openid,
                    'template_id'=>$template_id,
                    'url'=>$url,
                    'topcolor'=>'#FF0000',
                    'data'=>array(
                        'first'=>array(
                            'value'=>$user_info['nickname'].'，'.'您的'.$account_type.'金额有消费信息：',
                            'color'=>'#173177'
                        ),
                        //变动时间
                        'date'=>array(
                            'value'=>date("Y-m-d H:i:s",$time),
                            'color'=>'#173177'
                        ),
                        //变动金额
                        'adCharge'=>array(
                            'value'=>$price,
                            'color'=>'#173177'
                        ),
                        //金额类型，红包，M币，游戏币等
                        'type'=>array(
                           'value'=>$account_type,
                           'color'=>'#173177'
                        ),
                        //账户余额
                        'cashBalance'=>array(
                            'value'=>$total,
                            'color'=>'#173177'
                        ),
                        'remark'=>array(
                            'value'=>'及时关注账户变动，掌握账户安全~',
                            'color'=>'#173177'
                        )
                    )
                    );
                    //推送
                    $push_result = $this->wechat->sendTemplateMessage($template_push_data);
             
                }
            }else if($push_type == 'login'){//账户异地登录通知
                $account_type = isset($data_arr['type'])&& !empty($data_arr['type']) ? $data_arr['type'] : '';
                $accout = isset($data_arr['accout'])&& !empty($data_arr['accout']) ? $data_arr['accout'] : '';
                $user_device = isset($data_arr['user_device'])&& !empty($data_arr['user_device']) ? $data_arr['user_device'] : '未知';
                $time = isset($data_arr['time'])&& !empty($data_arr['time']) ? $data_arr['time'] : '';
                $ip_addr = isset($data_arr['ip'])&& !empty($data_arr['ip']) ? $data_arr['ip'] : '';
                $template_id = $template_arr[3]['template_id'];
                $url = $template_arr[3]['desc_url'];
                $template_push_data = array(
                    'touser'=>$openid,
                    'template_id'=>$template_id,
                    'url'=>$url,
                    'topcolor'=>'#FF0000',
                    'data'=>array(
                        'first'=>array(
                            'value'=>$user_info['nickname'].'，'.'您的'.$account_type.'账号：'.$accout.'进行了登录',
                            'color'=>'#173177'
                        ),
                        //登陆时间
                        'keyword1'=>array(
                            'value'=>date("Y-m-d H:i:s",$time),
                            'color'=>'#173177'
                        ),
                        //登陆设备
                        'keyword2'=>array(
                            'value'=>$user_device,
                            'color'=>'#173177'
                        ),
                        //登陆IP
                        'keyword3'=>array(
                            'value'=>$ip_addr,
                            'color'=>'#173177'
                        ),
                        'remark'=>array(
                            'value'=>'如非本人操作，请立即修改密码',
                            'color'=>'#173177'
                        )   
                    )
                );
                
                $push_result = $this->wechat->sendTemplateMessage($template_push_data);
                
            }else if($push_type == 'bind_account'){//绑定账户通知 
                $accout = isset($data_arr['accout'])&& !empty($data_arr['accout']) ? $data_arr['accout'] : '';
                $template_id = $template_arr[4]['template_id'];
                $url = $template_arr[4]['desc_url'];
                $template_push_data = array(
                    'touser'=>$openid,
                    'template_id'=>$template_id,
                    'url'=>$url,
                    'topcolor'=>'#FF0000',
                    'data'=>array(
                        'first'=>array(
                            'value'=>$user_info['nickname'].'，'.'你已经成功绑定梦宝谷账号！',
                            'color'=>'#173177'
                        ),
                        //绑定账号
                        'keyword1'=>array(
                            'value'=>$accout,
                            'color'=>'#173177'
                        ),
                        //绑定说明
                        'keyword2'=>array(
                            'value'=>'直接用微信登陆游戏吧！',
                            'color'=>'#173177'
                            
                        ),
                        'remark'=>array(
                            'value'=>'您的信息只作为登陆游戏使用',
                            'color'=>'#173177'
                        )
                    )
                );
                
                $push_result = $this->wechat->sendTemplateMessage($template_push_data);
                
            }
            
            
            if($push_result){//推送成功，记录log
                $insert_data = array(
                    'user_id'=>isset($data_arr['user_id']) ? $data_arr['user_id'] :'',
                    'openid'=>$openid,
                    'unionid'=>isset($data_arr['unionid']) ? $data_arr['unionid'] : '',
                    'push_msg'=>json_encode($data_info),
                    'appid'=>isset($data_arr['appid']) ? $data_arr['appid'] : '',
                    'wid'=>$wid,
                    'template_id'=>$template_id,
                    'msg_id'=>isset($push_result['msgid']) ? $push_result['msgid'] : '',
                    'status'=>isset($push_result['errcode']) ? $push_result['errcode'] : '',
                    'addtime'=>time()                   
                );
                $this->wechat_template_model->add_push_log($insert_data);
            }
        }

    }
    //绑定账号
    public function bind($act = ''){
        $appid = isset($_GET['APPID']) && !empty($_GET['APPID']) ? $_GET['APPID'] : '';
        if(!empty($appid)){
         $wechat_info = $this->wechat_setting_model->setting_get(array('appid'=>$appid));
        }
        $appsecret = isset($wechat_info['appsecret']) && !empty($wechat_info['appsecret']) ? $wechat_info['appsecret'] : '';
        
        
            $act = $this->input->post('act');
            if($act == 'bind_account'){
                $user_id = $this->input->post('user_id');
                $pass_word = $this->input->post('pass_word');
                $openid = $this->input->post('openid');
                $unionid = $this->input->post('unionid');
                $access_token = $this->input->post('access_token');
                $refresh_token =  $this->input->post('refresh_token');
                $scope = $this->input->post('scope');
                $expires = $this->input->post('expires_in');
                $post_data = array(
                    'user_id'=>isset($user_id) && !empty($user_id) ? $user_id : '',
                    'pass_word'=>isset($pass_word) && !empty($pass_word) ? $pass_word : '',
                    'openid'=>isset($openid) && !empty($openid) ? $openid : '',
                    'unionid'=>isset($unionid) && !empty($unionid) ? $unionid : '',
                    'access_token'=>isset($access_token) && !empty($access_token) ? $access_token : '',
                    'refresh_token'=>isset($refresh_token) && !empty($refresh_token) ? $refresh_token : '',
                    'scope'=>isset($scope) && !empty($scope) ? $scope : '',
                    'expires_in'=>isset($expires) && !empty($expires) ? $expires : ''
                );
                
                $sign_md5 = $this->gen_sign($post_data);
                $data['sign'] = $sign_md5;
                $post_data['sign'] = $sign_md5;//签名认证
                $result = $this->http_post('http://cspfdev007.mbgadev.cn/_bind_wechat',$post_data);

                if($result){
                    $result_arr = json_decode($result,TRUE);
                    $data['bind_status'] = $result_arr['bind_status'];
                    $data['bind_msg'] = $result_arr['bind_msg'];
                }else{
                    $data['bind_status'] = 6;
                    $data['bind_msg'] = '发生未知错误，请稍后再试！';
                }
                print_r(json_encode($data));
            }else{
                $data['APPID'] = $appid;
                $data['APPSECRET'] = $appsecret;
                $this->load->view('wechat/bind_page');
            }
            
     
        
    }
    
    
    /* 
        生成签名
    */
    public function gen_sign($data_info = array()){
      
        ksort($data_info);//约定按key升序重新排序
        $sign_str = '';
        foreach($data_info as $key=>$val){//验证为MD5(key1.val1.key2.val2...)
            $sign_str .= $key.$val;
        }
        $sign = MD5($sign_str);
        return $sign;
        
    }
    
    
    /**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean $post_file 是否文件上传
	 * @return string content
	 */
	private function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
        $strPOST = $param;
		 if (is_string($param) || $post_file) {
			
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		} 
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,TRUE);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

}