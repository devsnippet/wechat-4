<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_code_model extends CI_Model {

    private $table_code_name = "game_code";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function code_get_all($where = array()){
        $this->db->select('*')->from($this->table_code_name)->where($where);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function code_add_batch($data){
        $this->db->insert_batch($this->table_code_name, $data);
        return $this->db->insert_id();
    }

    public function code_del($where = array()){
        $this->db->where($where);
        $this->db->delete($this->table_code_name);
        return $this->db->affected_rows();
    }

    public function code_get_random($wid, $gid){
        $sql = "SELECT * FROM game_code WHERE wid = $wid and gid = $gid and isUsed = 0 ORDER BY rand() LIMIT 1";
        $query = $this->db->query($sql);
        return $query->num_rows()>0 ? $query->row_array() : false;
    }

    public function code_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_code_name, $data);
        return $this->db->affected_rows();
    }

}

/* End of file wechat_code_model.php */
/* Location: ./application/models/wechat_code_model.php */ ?>