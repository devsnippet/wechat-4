<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat_reply_model extends CI_Model {

    private $table_reply_name = "wechat_reply";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取用户关注时返回的自定义响应文字
     * @param  int $wid 微信ID
     * @return mixed      响应的文字内容或者false
     */
    public function subscribe_get($wid){
        $sql = "SELECT * from wechat_reply where (wid = $wid and disabled = 0) and ((alias1 = 'subscribe' or alias1 = '订阅') or (alias2 = 'subscribe' or alias2 = '订阅'))";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $rows = $query->row_array();
            return $rows['reply'];
        }
        return false;
    }

    /**
     * 事件型获取返回数据 供自定义菜单使用
     * @param  string $target target名称
     * @param  int $wid    微信ID
     * @return mixed         回复内容或者false
     */
    public function reply_event_get($target, $wid){
        $this->db->select("*")->from($this->table_reply_name)->where(array("wid" => $wid, "alias1" => $target, "reply_type" => "event", "disabled" => 0));
        $this->db->or_where(array("alias2" => $target));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $rows = $query->row_array();
            return $rows['reply'];
        }
        return false;
    }

    /**
     * 获取全部回复列表
     * @param  array  $where 判断条件 自定数组
     * @return mixed        数组或者false
     */
    public function reply_get_all($where = array()){
        $this->db->select("*")->from($this->table_reply_name)->where($where);
        $this->db->order_by("addtime", "desc"); 
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function reply_get($where = array(), $orwhere){
        $query = $this->db->query("SELECT * from wechat_reply where wid=? AND disabled=? AND (alias1 = ? or alias2=?) ORDER BY addtime desc",array($where['wid'],0,$where['alias1'],$orwhere['alias2']));
        //$this->db->select("*")->from($this->table_reply_name)->where($where);
       // $this->db->where(array('disabled' => 0));
        // $this->db->or_where($orwhere);
        //$this->db->order_by("addtime", "desc"); 
        //$query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

    public function get_containing_records($wid, $keyword) {
        $padded_keyword = '%' . $keyword . '%';
        $query = $this->db->query("SELECT * from wechat_reply where wid=? AND disabled=0 AND (alias1 like ? or alias2 like ?)", 
            array($wid, $padded_keyword, $padded_keyword)
        );
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function get_by_wid_cat_name($wid, $cat_name)
    {
        $query = $this->db->get_where($this->table_reply_name, array('wid' => $wid, 'cat_name' => $cat_name, 'disabled' => 0));
        return $query->num_rows() > 0 ? $query->result_array() : array();
    }

    public function get_exact_matches($wid, $keyword)
    {
        $query = $this->db->select('*')->from($this->table_reply_name)
            ->where('wid', $wid)->where('cat_name', 'exact_match')->where('disabled', 0)
            ->where('alias1', $keyword)->or_where('alias2', $keyword);
        return $query->num_rows() > 0 ? $query->result_array() : array();
    }

    public function reply_add($data = array()){
        $this->db->insert($this->table_reply_name, $data);
        return $this->db->insert_id();
    }

    public function reply_mod($where = array(), $data = array()){
        $this->db->where($where);
        $this->db->update($this->table_reply_name, $data);
        return $this->db->affected_rows();
    }

    public function reply_del($where = array()){
        $this->db->where($where);
        $this->db->delete($this->table_reply_name);
        return $this->db->affected_rows();
    }

}

/* End of file wechat_reply_model.php */
/* Location: ./application/models/wechat_reply_model.php */ 
?>
