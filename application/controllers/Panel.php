<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panel extends G_Controller
{

    public function index()
    {
		if (!empty($this->session->userdata('info')) && $this->session->userdata('info')['isAdmin'] == 1) {
			redirect(base_url('admin/dashboard'), 'refresh');
		}else{
			redirect(base_url(), 'refresh');
    	}
    }

    public function logOut()
    {
        $this->session->unset_userdata('info');
        $this->session->sess_destroy();
        $this->session->set_flashdata('adminMsg', "Başarıyla Çıkış Yaptın.");
        redirect(base_url('admin'), 'refresh');
    }

    public function error()
    {
        $this->load->view('404');
    }

}

