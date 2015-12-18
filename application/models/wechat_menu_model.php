<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_menu_model extends CI_Model {

    private $table_menu_name = "wechat_custom_menu";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取菜单条目信息
     * @param  array  $params 判断条件 自定数组
     * @return mixed         数组或者false
     */
    public function menu_get($params = array()){
        $this->db->select('*')->from($this->table_menu_name)->where($params);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    /**
     * 添加菜单条目信息
     * @param  array  $data 欲插入的数据条目
     * @return int       插入信息后返回的int
     */
    public function menu_add($data = array()){
        $this->db->insert($this->table_menu_name, $data);
        return $this->db->insert_id();
    }

    /**
     * 删除菜单条目
     * @param  array  $params 判断条件 自定数组
     * @return int         影响的行数
     */
    public function menu_del($params = array()){
        $this->db->where($params);
        $this->db->delete($this->table_menu_name);
        return $this->db->affected_rows();
    }

    /**
     * 菜单条目修改
     * @param  array  $where 判断条件 自定数组
     * @param  array  $data  欲修改的信息条目数组
     * @return int        影响的行数
     */
    public function menu_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_menu_name, $data);
        return $this->db->affected_rows();
    }
    

}

/* End of file wechat_menu_model.php */
/* Location: ./application/models/wechat_menu_model.php */ ?>