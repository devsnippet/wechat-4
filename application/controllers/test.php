<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        // $this->load->model('admin/wechat_model');
    }

    public function index(){
        // $test = $this->wechat_model->wechat_reply_get_by_target('subscribe', 1);
        // var_dump($test);
        echo "hello test";

        $tomorrow_date_one = date('Y').'-'.date('m').'-';
        $tomorrow_date_two = date('d')+1;
        $tomorrow_zero_hours_timestamp = strtotime($tomorrow_date_one.$tomorrow_date_two);
        $diff_timestamp = $tomorrow_zero_hours_timestamp - time();
        $valid_checkin_timestamp = 1429609805 + $diff_timestamp;
        var_dump($valid_checkin_timestamp);

        $day = date('d', 1429609805);
        $tomorrowT = date('Y').'-'.date('m').'-';
        $tomorrowT .= $day+1;
        $valid_time = strtotime($tomorrowT);
        var_dump($valid_time);
    }


}

/* End of file test.php */
/* Location: ./application/controllers/test.php */
 ?>