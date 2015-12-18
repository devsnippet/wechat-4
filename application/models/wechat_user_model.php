<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_user_model extends CI_Model {

    private $table_user_name = "wechat_user";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 添加用户
     * @param  array $data 传入添加的数据数组
     * @return it       插入的ID
     */
    public function user_add($data){
        $this->db->insert($this->table_user_name, $data);
        return $this->db->insert_id();
    }

    /**
     * 用户信息更新
     * @param  int $wid    微信账号ID
     * @param  string $openid 唯一OpenID
     * @param  array $data   修改的内容
     * @return mixed         影响的行数
     */
    public function user_update($wid, $openid, $data){
        $this->db->where('wid', $wid);
        $this->db->where('openid', $openid);
        $this->db->update($this->table_user_name, $data);
        return $this->db->affected_rows();
    }

    /**
     * 获取用户信息
     * @param  array $params 传入的判断信息 数组 
     * @return mixed         获取到的数据或者false
     */
    public function user_get($params = array()){
        $this->db->select("*")->from($this->table_user_name)->where($params);
        $query = $this->db->get();
        return ($query->num_rows()==1) ? $query->row_array() : false;
    }

    /**
     * 检查用户是否存在
     * @param  int $wid    微信ID
     * @param  string $openid 传入的微信唯一ID
     * @return bool         存在返回true 不存在返回false
     */
    public function user_is_exist($wid, $openid){
        $this->db->select("*")->from($this->table_user_name)->where(array("wid" => $wid, "openid" => $openid));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function user_is_full($wid, $openid){
        $this->db->select("*")->from($this->table_user_name)->where(array("wid" => $wid, "openid" => $openid, "sex !=" => 'NULL'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }
    //验证用户信息是否完整，包括unionid等等
    public function user_union_info_full($wid, $openid){
        $detail_info =  $this->db->get_where('wechat_user_details',array('wid'=>$wid,'raw_openid'=>$openid));
        $temp_res = $detail_info->row_array();
        if(empty($temp_res)){
            return false;
        }
        $user_base_info = $this->db->get_where($this->table_user_name,array("wid" => $wid, "openid" => $openid));
        $result = $user_base_info->row_array();
        if(!empty($result) && (!empty($result['sex']) && !empty($result['unionid']))){
            return true;
        }else{
            return false;
        }
    }

}

/* End of file wechat_user_model.php */
/* Location: ./application/models/wechat_user_model.php */ ?>