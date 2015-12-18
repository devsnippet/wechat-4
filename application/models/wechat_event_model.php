<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_event_model extends CI_Model {

    private $table_event_name = "game_event";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function event_get_all($where = array()){
        $this->db->select('*')->from($this->table_event_name)->where($where);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function event_get($params = array()){
        $this->db->select('*')->from($this->table_event_name)->where($params);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->row_array() : false;
    }

    public function event_add($data = array()){
        $this->db->insert($this->table_event_name, $data);
        return $this->db->insert_id();
    }

    public function event_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_event_name, $data);
        return $this->db->affected_rows();
    }

    public function event_del($where = array()){
        $this->db->where($where);
        $this->db->delete($this->table_event_name);
        return $this->db->affected_rows();
    }

}

/* End of file wechat_event_model.php */
/* Location: ./application/models/wechat_event_model.php */ ?>