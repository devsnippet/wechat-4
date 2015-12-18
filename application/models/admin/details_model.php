<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Details_model extends CI_Model{

	function __construct(){
        $this->load->database();
	}

	public function wechat_details_add($data){
		$this->db->insert('wechat_user_details', $data);
		return $this->db->insert_id();
	}

	public function wechat_details_update($data, $uid)
	{
		$this->db->update('wechat_user_details', $data)->where('uid', $uid);
		return $this->affected_rows();
	}

	public function wechat_details_del($data, $uid)
	{
		$this->db->delete('wechat_user_details')->where('uid', $uid);
		return $this->affected_rows();
	}

	public function wechat_details_get_by_user($uid)
	{
		$this->db->select('*')->from('wechat_user_details')->where('uid', $uid);
		$query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
	}

	public function wechat_details_get_by_wechat(wid)//wid as same as game id
	{
		$this->db->select('*')->from('wechat_user_details')->where('wid', $uid);
		$query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
	}





}


/* End of file checkin_model.php */
/* Location: ./application/models/admin/checkin_model.php */

 ?>