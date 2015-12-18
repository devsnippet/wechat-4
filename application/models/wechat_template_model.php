<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_template_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
//获取配置过的模板类型
   public function get_template_type(){
       $query = $this->db->get_where('template_msg_type',array('disabled'=>0));
       return $query->result_array();
   }
   
   //更新微信公众号下的模板
   public function update_template_info($game_id,$wxid,$data){
       $temp_query = $this->db->get_where('wechat_template_msg',array('wid'=>$wxid,'game_id'=>$game_id));
       $temp_data = $temp_query->row_array();
       if(!empty($temp_data)){//存在记录更新
           $this->db->update('wechat_template_msg',$data,array('wid'=>$wxid,'game_id'=>$game_id));
       }else{
           $this->db->insert('wechat_template_msg',$data);
       }
       
   }
   
   //获取公众号的下的消息模板
   
   public function get_template_arr($wxid,$game_id){
       $query = $this->db->get_where('wechat_template_msg',array('wid'=>$wxid,'game_id'=>$game_id));
       return $query->row_array();
   }
   
   public function get_template($where){
       $query = $this->db->get_where('wechat_template_msg',$where);
       return $query->row_array();
   }
   
   public function add_push_log($data){
       $this->db->insert('wechat_push_template_log',$data);
   }

}

/* End of file wechat_template_model.php */
/* Location: ./application/models/wechat_template_model.php */ ?>