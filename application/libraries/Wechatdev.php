<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechatdev{
	/**
	 * [$ci CI原始对象]
	 * @var null
	 */
	private $CI;

	public function __construct(){
		$this->CI =& get_instance();
		//TODO load model
		$this->CI->load->model('wechat_model');
	}


	/**
	 * [wechat_get_access_token request tencet dev-testing paltform for access_token]
	 * @param  [int] wechat dev index id
	 * @return [string] wechat access token for other request's authentication
	 */
	public function wechat_get_access_token($wid){
        $access_token_arr = $this->CI->wechat_model->get_access_token($wid);
        //check access_token in database expired or not
        if(empty($access_token_arr['access_token']) || (time()-$access_token_arr['addtime'])>7000){  //重新获取
            $wechat_info = $this->CI->wechat_model->wechat_setting_info($wid);
            $url_access_token_get = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wechat_info['appid']}&secret={$wechat_info['appsecret']}";
            $return_info = $this->curl_get($url_access_token_get);
            $json = !empty($return_info)?$return_info:"";
            $arr = json_decode($json,true);
            if(!empty($arr['access_token'])){
                if(empty($access_token_arr['access_token'])){
                    $db_data = array('wid'=>$wid,'access_token'=>$arr['access_token'],'addtime'=>time());
                    $this->CI->wechat_model->wechat_access_token_insert($db_data);
                }else{
                    $db_data = array('access_token'=>$arr['access_token'],'addtime'=>time());
                    $this->CI->wechat_model->wechat_access_token_update($db_data,$wid);
                }
            }else{
            	$arr['access_token'] = "";
            	log_message('error','appid or appsecret wrong, please edit your wechat settings');
            }
            //TODO if appid&appsecret wrong, this may return 
            return $arr['access_token'];
        }else{
            return $access_token_arr['access_token'];
        }
    }

    /**
     * [wechat_get_custom_menu request tencet dev-testing paltform for now custom menu]
     * @param  [int] wechat dev index id
     * @return [string] now custom menu json data
     */
    public function wechat_get_custom_menu($wid){
    	$wechat_access_token = $this->wechat_get_access_token($wid);
        $url_custom_menu_get = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$wechat_access_token}";
        $return_info = $this->curl_get($url_custom_menu_get);
       	$json = !empty($return_info)?$return_info:"";
        return $json;
    }

    /**
     * [wechat_add_custom_menu request tencet dev-testing paltform for adding custom menu]
     * @param  [int] $wid              [wechat dev index id]
     * @param  array  $custom_menu_data [custom menu array]
     * @return [stirng]                   [operation status json data]
     */
    public function wechat_add_custom_menu($wid, $custom_menu_data){
    	$pre_custom_menu = $custom_menu_data;
    	$wechat_access_token = $this->wechat_get_access_token($wid);
    	$curl_custom_menu_add = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$wechat_access_token}";
    	$return_info = $this->curl_post($curl_custom_menu_add, $pre_custom_menu);
    	$json = !empty($return_info)?$return_info:"";
        return $json;

    }

    /**
     * [wechat_del_custom_menu request tencet dev-testing paltform for deleting current custom menu]
     * @param  [int] wechat dev index id 
     * @return [string] operation status json data
     */
    public function wechat_del_custom_menu($wid){
		$wechat_access_token = $this->wechat_get_access_token($wid);
        $url_custom_menu_del = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$wechat_access_token}";
        $return_info = $this->curl_get($url_custom_menu_del);
       	$json = !empty($return_info)?$return_info:"";
        return $json;
    }

    /**
     * [wechat_retrieve_user_info retrieve user information]
     * @param  [int] $wid    [wechat dev index id]
     * @param  [string] $openid [user's openid]
     * @return [string]         [user's informaiton json data]
     */
    public function wechat_retrieve_user_info($wid, $openid){
		$wechat_access_token = $this->wechat_get_access_token($wid);
		$url_user_info_get = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$wechat_access_token}&openid={$openid}";
		$return_info = $this->curl_get($url_user_info_get);
		$json = !empty($return_info)?$return_info:"";
        return $json;
    }

    /**
     * [wechat_retrieve_user_list retrieve user list subscribed]
     * @param  [int] $wid    [wechat dev index id]
     * @param  string $offset_openid [offset openid(option)]
     * @return [string]         [subscribed user list]
     */
    public function wechat_retrieve_user_list($wid, $offset_openid = ""){
    	$wechat_access_token = $this->wechat_get_access_token($wid);
		$url_user_info_get = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$wechat_access_token}&next_openid={$offset_openid}";
		$return_info = $this->curl_get($url_user_info_get);
		$json = !empty($return_info)?$return_info:"";
        return $json;
    }

    /**
     * [wechat_get_callback_ip retrieve wechat server ip address list]
     * @param  [int] $wid [wechat dev index id]
     * @return [string]      [ip address json data]
     */
    public function wechat_get_callback_ip($wid){
    	$wechat_access_token = $this->wechat_get_access_token($wid);
    	$url_user_info_get = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token={$wechat_access_token}";
		$return_info = $this->curl_get($url_user_info_get);
		$json = !empty($return_info)?$return_info:"";
        return $json;
    }


	/**
	 * [curl get method]
	 * @param  [string] target url
	 * @return [string/boolean] if success return content or return false
	 */
	public function curl_get($url){
		$ccurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($ccurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ccurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ccurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($ccurl, CURLOPT_URL, $url);
		curl_setopt($ccurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($ccurl);
		$aStatus = curl_getinfo($ccurl);
		curl_close($ccurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * [curl post method]
	 * @param  [string] target url
	 * @param  [string/array] post data string example(key1=value1&key2=value2) OR php array
	 * @param  boolean send file or not
	 * @return [string/boolean] if success return content or return false
	 */
	public function curl_post($url,$param,$post_file=false){
		$ccurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($ccurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ccurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ccurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
        // var_dump($strPOST);
		curl_setopt($ccurl, CURLOPT_URL, $url);
		curl_setopt($ccurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ccurl, CURLOPT_POST,true);
		curl_setopt($ccurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($ccurl);
		$aStatus = curl_getinfo($ccurl);
		curl_close($ccurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	//From http://www.thinkphp.cn/topic/7857.html
	//Important!! Add custom menu must pass on menu json data without unicode encode
	/**************************************************************
       *
       *    使用特定function对数组中所有元素做处理
       *    @param  string  &$array     要处理的字符串
       *    @param  string  $function   要执行的函数
       *    @return boolean $apply_to_keys_also     是否也应用到key上
       *    @access public
       *
     *************************************************************/
    function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
     
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
     
    /**************************************************************
     *
     *    将数组转换为JSON字符串（兼容中文）
     *    @param  array   $array      要转换的数组
     *    @return string      转换得到的json字符串
     *    @access public
     *
     *************************************************************/
    function JSON($array) {
        arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }


}


/* End of file WechatDev.php */
/* Location: ./application/libraries/WechatDev.php */
 ?>