<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_tool_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 统计签到人数
     * @param  string $startTime 开始时间戳
     * @param  string $endTime   结束时间戳
     * @param  int $wid       微信id
     * @return int            数据条目数 整数
     */
    public function count_checkin($startTime, $endTime, $wid){
        $this->db->from("wechat_user_details")->where(array('wid' => $wid, 'lastcheckin >' => $startTime, 'lastcheckin <' => $endTime));
        return $this->db->count_all_results();
    }

}

/* End of file wechat_tool_model.php */
/* Location: ./application/models/wechat_tool_model.php */ ?>