<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_setting_model extends CI_Model {

    private $table_setting_name = "wechat_setting";

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * 微信号添加
     * @param  array  $data 欲添加的微信号信息数据 关联数组
     * @return int       插入条目的自增ID
     */
    public function setting_add($data = array()){
        $this->db->insert($this->table_setting_name, $data);
        return $this->db->insert_id();
    }

    /**
     * 微信号修改
     * @param  array  $where 修改位置判断条件
     * @param  array  $data  欲修改的数据内容
     * @return mixed        影响的行数
     */
    public function setting_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_setting_name, $data);
        return $this->db->affected_rows();
    }

    /**
     * 获取设置的内容
     * @param  array  $params 所在位置判断条件
     * @return mixed         返回数据 或者 false
     */
    public function setting_get($params = array()){
        $this->db->select("*")->from($this->table_setting_name)->where($params);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

    /**
     * 微信设置条目删除
     * @param  array  $where 所在位置判断 自定数组
     * @return int        影响的行数
     */
    public function setting_del($where = array()){
        $this->db->where($where);
        $this->db->delete($this->table_setting_name);
        return $this->db->affected_rows();
    }

    /**
     * 获取全部微信账号信息
     * @param  int $gameid 游戏ID
     * @return mixed         数组或者false
     */
    public function wechat_list_get_all($gameid){
        $this->db->select("*")->from($this->table_setting_name)->where(array('game_id' => $gameid));
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    /**
     * 缓存设置
     * @param  string $type      类型 at => "accesstoken", jt => "jssdkticket"
     * @param  string $cachename 缓存名称 都是appid
     * @param  string $value     token内容
     * @param  string $expired   过期日期
     * @return mixed            影响的数据行
     */
    public function cache_set($type = "at", $cachename, $value, $expired){
        $where = array('appid' => $cachename);
        if ($type == "at") {
            $data = array('access_token_cache' => $value, 'access_token_expired' => $expired);
        }else{
            //type == "jt"
            $data = array('jssdk_token_cache' => $value, 'jssdk_token_expired' => $expired);
        }
        return $this->setting_mod($where, $data);
    }

    /**
     * 移除缓存内容
     * @param  string $type      类型 at => "accesstoken", jt => "jssdkticket"
     * @param  string $cachename 缓存名称 都是appid
     * @return mixed            影响的行数
     */
    public function cache_remove($type = "at", $cachename){
        $where = array('appid' => $cachename);
        if ($type == "at") {
            $data = array('access_token_cache' => '', 'access_token_expired' => '');
        }else{
            //type == "jt"
            $data = array('jssdk_token_cache' => '', 'jssdk_token_expired' => '');
        }
        return $this->setting_mod($where, $data);
    }

    /**
     * 获取缓存数据
     * @param  string $cachename 缓存名称 都是appid
     * @param  string $type      类型 
     * @return mixed            数据内容或者false
     */
    public function cache_get($type = "at", $cachename, $kind = "token"){
        $where = array('appid' => $cachename);
        $raw = $this->setting_get($where);
        if ($raw) {
            if ($type == "at") {
                if ($kind == "token") {
                    return $raw['access_token_cache'];
                }else{
                    return $raw['access_token_expired'];
                }
            }else{
                if ($kind == "token") {
                    return $raw['jssdk_token_cache'];
                }else{
                    return $raw['jssdk_token_expired'];
                }
            }
        }
        return false;
    }

}

/* End of file wechat_setting_model.php */
/* Location: ./application/models/admin/wechat_setting_model.php */

 ?>