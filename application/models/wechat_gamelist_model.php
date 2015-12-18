<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_gamelist_model extends CI_Model {

    private $table_gamelist_name = "game_master";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取全部有些数据列表
     * @return mixed 数组或者false
     */
    public function gamelist_get_all(){
        $this->db->select("{$this->table_gamelist_name}.*,wid")->from($this->table_gamelist_name)->join("wechat_setting","wechat_setting.game_id={$this->table_gamelist_name}.game_id","left")->order_by("wid","DESC");
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    /**
     * 游戏数据获取
     * @param  array  $params 判断条件 自定数组
     * @return mixed         数组或者false
     */
    public function gamelist_get($params = array()){
        $this->db->select("*")->from($this->table_gamelist_name)->where($params);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

    /**
     * 增加游戏到游戏列表
     * @param  array  $data 添加的游戏条目数据
     * @return int       插入后的自增ID
     */
    public function gamelist_add($data = array()){
        $this->db->insert($this->table_gamelist_name, $data);
        return $this->db->insert_id();
    }

    /**
     * 游戏列表中的数据的修改
     * @param  array  $where 修改的地方 判断条件
     * @param  array  $data  更新的游戏
     * @return int        影响的数据行
     */
    public function gamelist_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_gamelist_name, $data);
        return $this->db->affected_rows();
    }


}

/* End of file wechat_gamelist_model.php */
/* Location: ./application/models/wechat_gamelist_model.php */ ?>