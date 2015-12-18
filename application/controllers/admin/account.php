<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*  
*/
class Account extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }
    public function index(){
        $callback = site_url('/admin/account/callback');//回调 以接收南天门传回的数据
        redirect("http://ntm.mobage.cn/login?__url={$callback}&__aid=339");//重定向到南天门认证
    }

    public function callback(){
        $email = $this->input->get('__email', TRUE);//南天门传回三个三处 __email __ut __ex 如果不做具体的权限验证的话 只要判断是否拿到__email就OK了
        $email_splited = explode('@', $email);
        $email = $email_splited[0];
        $token = $this->input->get('__ut', TRUE);
        $expired = $this->input->get('__ex', TRUE);

        if ($email != '' && $token != '' && $expired != '') {
            $this->session->set_userdata(
                array(
                    'uid' => 0,
                    'nick' => $email,
                    'token' => $token,
                    'logged' => true
                    ));
            redirect('/manage', 'location', 301);
        }else{
            echo "登录失败";
        }
    }

    public function logout(){
        $this->session->sess_destroy();
        $this->session->unset_userdata('uid');
        $this->session->unset_userdata('nick');
        $this->session->unset_userdata('token');
        $this->session->unset_userdata('logged');
        $this->load->helper('cookie');
        delete_cookie('uid');
        delete_cookie('nick');
        delete_cookie('token');
        delete_cookie('logged');
        // var_dump($this->session->all_userdata());
        redirect('/admin/account/', 'location', 301);
    }
}


/* End of file account.php */
/* Location: ./application/controllers/admin/account.php */
 ?>