<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
class Wechat_model extends CI_Model{
    
    function __construct(){
        $this->load->database();
    }
    
    public function game_list(){
        $this->db->select("*")->from('game_master');
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }
    public function game_info($game_id){
        $this->db->select("*")->from('game_master')->where('game_id',$game_id);
        $query = $this->db->get();
        return ($query->num_rows()==1) ? $query->row_array() : false;
    }

    public function game_code_list($wid, $gid){
        $this->db->select("*")->from('game_code')->where('wid', $wid)->where('gid', $gid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function game_code_insert($data){
        $this->db->insert_batch('game_code', $data);
        return $this->db->insert_id();
    }

    public function game_code_delete($cid){
        $this->db->where('cid', $cid);
        $this->db->delete('game_code');
        return $this->db->affected_rows();
    }

    //礼包码活动列表
    public function event_list($gameid = false, $wid = false){
        $this->db->select("*")->from('game_event')->where('game_id', $gameid)->where('wid', $wid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    //礼包码活动列表
    public function event_list_reply($wid = false){
        $this->db->select("*")->from('game_event')->where('wid', $wid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    //礼包码活动信息
    public function event_info($event_name){
        $this->db->select("*")->from('game_event')->where('event_name',$event_name);
        $query = $this->db->get();
        return ($query->num_rows()==1) ? $query->row_array() : false;
    }

    public function event_info_gid($gid){
        $this->db->select("*")->from('game_event')->where('gid',$gid);
        $query = $this->db->get();
        return ($query->num_rows()==1) ? $query->row_array() : false;
    }

    public function wechat_esetting_add($data){
        $this->db->insert('game_event',$data);
        return $this->db->insert_id();
    }
    public function wechat_esetting_edit($data, $gid, $game_id, $wid){
        $this->db->where('gid', $gid);
        $this->db->where('game_id', $game_id);
        $this->db->where('wid', $wid);
        $this->db->update('game_event', $data);
        return $this->db->affected_rows();
    }

    public function wechat_gsetting_add($data){
        $this->db->insert('game_master',$data);
        return $this->db->insert_id();
    }
    public function wechat_gsetting_edit($data,$gameid){
        $this->db->where('game_id', $gameid);
        $this->db->update('game_master', $data);
        return $this->db->affected_rows();
    }

    public function wechat_setting_add($data){
        $this->db->insert('wechat_setting',$data);
        return $this->db->insert_id();
    }
    public function wechat_setting_edit($data,$wid){
        $this->db->where('wid', $wid);
        $this->db->update('wechat_setting', $data);
        return $this->db->affected_rows();
    }

    public function wechat_setting_list($game_id){
        $this->db->select("*")->from('wechat_setting')->where('game_id',$game_id);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }
    public function wechat_setting_info($wid){
        $this->db->select("*")->from('wechat_setting')->where('wid',$wid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->row_array() : false;
    }
    public function get_access_token($wid){
        $this->db->select("*")->from('wechat_access_token')->where('wid',$wid);
        $query = $this->db->get();
        return ($query->num_rows()==1) ? $query->row_array() : false;
    }
    public function wechat_access_token_insert($data){
        $this->db->insert('wechat_access_token',$data);
        return $this->db->insert_id();
    }
    public function wechat_access_token_update($data,$wid){
        $this->db->where('wid', $wid);
        $this->db->update('wechat_access_token', $data);
        return $this->db->affected_rows();
    }
    public function wechat_reply_list($wid){
        $this->db->select("*")->from('wechat_reply')->where('wid',$wid)->order_by("addtime","DESC");
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }
    public function wechat_reply_add($data){
        $this->db->insert('wechat_reply',$data);
        return $this->db->insert_id();
    }
    public function wechat_reply_update($data,$rid){
        $this->db->where('rid', $rid);
        $this->db->update('wechat_reply', $data);
        return $this->db->affected_rows();
    }
    public function wechat_reply_delete($rid){
        $this->db->where('rid', $rid);
        $this->db->delete('wechat_reply', array('disabled' => 1));
        return $this->db->affected_rows();
    }
    //TODO
    public function wechat_reply_switch($rid){
        $this->db->where('rid', $rid);
        $this->db->delete('wechat_reply', array('disabled' => 1));
        return $this->db->affected_rows();
    }
    public function wechat_reply_get_by_target($target, $wid){
        $this->db->select('*')->from('wechat_reply')->where('disabled', 0)->where('wid', $wid)->like('target', $target, 'after')->order_by("addtime", "desc");//desc
        // $this->db->select('*')->from('wechat_reply')->where('target', $target)->where('disabled', 0)->where('wid', $wid)->like('target', $target)->order_by("addtime", "desc");//desc
        $query = $this->db->get();
        // $str = $this->db->last_query();
        // echo $str;
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }
    public function wechat_check_wid_exist($wid)
    {
        $this->db->select("*")->from('wechat_setting')->where('wid',$wid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? true : false;
    }
    public function register_list($game_id){
        $this->db->select("*")->from('p_game')->where('game_id',$game_id)->where('disabled',0)->where('end_time >=',time())->order_by("addtime","DESC");
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }
    public function wechat_pgame_get_by_gid($gid){
        $this->db->select("*")->from('p_game')->where('gid',$gid)->where('disabled',0)->where('end_time >=',time())->order_by("addtime","DESC");
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    //wechat user subscribe
    public function wechat_user_subscribe_add($data){
        $this->db->insert('wechat_user', $data);
        return $this->db->insert_id();
    }

    public function wechat_user_subscribe_update($data, $openid){
        $this->db->where('openid', $openid);
        $this->db->update('wechat_user', $data);
        return $this->db->affected_rows();
    }

    public function wechat_user_get_nickname_by_openid($openid){
        $this->db->select('nickname')->from('wechat_user')->where('openid', $openid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function wechat_user_get_uid_by_openid($openid){
        $this->db->select('uid')->from('wechat_user')->where('openid', $openid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    //details model (including checkin)
    public function wechat_details_add($data){
        $this->db->insert('wechat_user_details', $data);
        return $this->db->insert_id();
    }

    public function wechat_details_update($data, $uid)
    {
        $this->db->where('uid', $uid);
        $this->db->update('wechat_user_details', $data);
        return $this->db->affected_rows();
    }

    public function wechat_details_del($data, $uid)
    {
        $this->db->where('uid', $uid);
        $this->db->delete('wechat_user_details');
        return $this->db->affected_rows();
    }

    public function wechat_details_get_by_user($uid)
    {
        $this->db->select('*')->from('wechat_user_details')->where('uid', $uid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function wechat_details_get_by_wechat($wid)//wid as same as game id
    {
        $this->db->select('*')->from('wechat_user_details')->where('wid', $wid);
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function wechat_custom_menu_add_multiple($data)
    {
        $this->db->insert_batch('wechat_custom_menu', $data);
        return $this->db->insert_id();
    }
    public function wechat_custom_menu_add_single($data)
    {
        $this->db->insert('wechat_custom_menu', $data);
        return $this->db->insert_id();
    }
    public function wechat_custom_menu_update($data, $id){//$custom menu item id
        $this->db->where('id', $id);
        $this->db->update('wechat_custom_menu', $data);
        return $this->db->affected_rows();
    }
    public function wehcat_custom_menu_empty(){
        $this->db->truncate('wechat_custom_menu'); 
        return $this->db->affected_rows();
        //clear table
    }
    public function wechat_custom_menu_get($wid){
        $this->db->where('wid', $wid);
        $this->db->select('*')->from('wechat_custom_menu');
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function wechat_custom_menu_del_by_id($itemid){
        $this->db->where('id', $itemid);
        $this->db->delete('wechat_custom_menu');
        return $this->db->affected_rows();
    }

    public function wechat_custom_menu_del_by_wid($wid){
        $this->db->where('wid', $wid);
        $this->db->delete('wechat_custom_menu');
        return $this->db->affected_rows();
    }

    public function wechat_custom_menu_update_by_id($data, $itemid){
        $this->db->where('id', $itemid);
        $this->db->update('wechat_custom_menu', $data);
        return $this->db->affected_rows();
    }

    //checkin
    public function wechat_checkin_get_by_uid($uid){
        $this->db->where('uid', $uid);
        $this->db->select('*');
        $this->db->from('wechat_user_details');
        $query = $this->db->get();
        return ($query->num_rows()>0) ? $query->result_array() : false;
    }

    public function wechat_checkin_update_by_uid($data, $uid){
        $this->db->where('uid', $uid);
        $this->db->update('wechat_user_details', $data);
        return $this->db->affected_rows();
    }

    public function wechat_checkin_add($data){
        $this->db->insert('wechat_user_details', $data);
        return $this->db->insert_id();
    }


    public function wechat_random_giftcode($wid, $gid){
        $sql = "SELECT * FROM game_code WHERE wid = $wid and gid = $gid and isUsed = 0 ORDER BY rand() LIMIT 1";
        $query = $this->db->query($sql);
        return $query->num_rows()>0 ? $query->row_array() : false;
    }
    public function wechat_giftcode_tag($wid, $gid, $cid){
        $this->db->where('wid', $wid);
        $this->db->where('gid', $gid);
        $this->db->where('cid', $cid);
        $this->db->update('game_code', array('isUsed' => 1, 'usedTime' => time()));
        return $this->db->affected_rows();
    }

    
    /* End of file wechat_model.php */
    /* Location: ./application/models/admin/wechat_model.php */
    
}