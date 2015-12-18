<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_user_details_model extends CI_Model {

    private $table_user_details_name = "wechat_user_details";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 用户详细信息添加
     * @param  array  $data 添加的用户详细信息内容
     * @return int       插入的详细信息的序列id
     */
    public function user_details_add($data = array()){
        $this->db->insert($this->table_user_details_name, $data);
        return $this->db->insert_id();
    }

    /**
     * 用户详细信息获取
     * @param  array  $params 判断条件 自定数组
     * @return mixed         数组或者false
     */
    public function user_details_get($params = array()){
        $this->db->select("*")->from($this->table_user_details_name)->where($params);
        $query = $this->db->get();
        return ($query->num_rows()==1) ? $query->row_array() : false;
    }

    /**
     * 用户详细信息修改
     * @param  array  $where 判断条件 自定数组
     * @param  array  $data  变更信息的关联数组
     * @return int        影响的行数
     */
    public function user_details_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_user_details_name, $data);
        return $this->db->affected_rows();
    }

}

/* End of file wechat_user_details_model.php */
/* Location: ./application/models/wechat_user_details_model.php */ ?>