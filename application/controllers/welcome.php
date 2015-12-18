<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }

    public function index(){
        echo current_url();
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */
 ?>