<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
 
class User_Authentication extends CI_Controller { 
    function __construct() { 
        parent::__construct(); 
         
        // Load facebook oauth library 
        $this->load->library('Facebook'); 
         
        // Load user model 
        $this->load->model('user'); 
    } 
     
    public function index(){ 
        $userData = array(); 
         
        // Authenticate user with facebook 
        if($this->facebook->is_authenticated()){ 
            // Get user info from facebook 
            $fbUser = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,gender,picture'); 
 
            // Preparing data for database insertion 
            $userData['oauth_uid']    = !empty($fbUser['id'])?$fbUser['id']:'';; 
            $userData['name']    = !empty($fbUser['first_name'])?$fbUser['first_name']:''; 
            $userData['surname']    = !empty($fbUser['last_name'])?$fbUser['last_name']:''; 
            $userData['mail']        = !empty($fbUser['email'])?$fbUser['email']:''; 
             
            // Insert or update user data to the database 
            $userID = $this->user->checkUser($userData); 
             
            // Check user data insert or update status 
            if(!empty($userID)){ 
                $data['userData'] = $userData; 
                 $newData = [
                    'email' => $userData['mail'],
                    'logged_in' => TRUE
                    ];

                    $this->session->set_userdata('clientValue', $newData);
                 
                // Store the user profile info into session 
                $this->session->set_userdata('userData', $userData); 
            }else{ 
               $data['userData'] = array(); 
            } 
             
            // Facebook logout URL 
            $data['logoutURL'] = $this->facebook->logout_url(); 
        }else{ 
            // Facebook authentication url 
            $data['authURL'] =  $this->facebook->login_url(); 
        } 
         
        // Load login/profile view 
        redirect(base_url()); 
    } 
 
    public function logout() { 
        // Remove local Facebook session 
        $this->facebook->destroy_session(); 
        // Remove user data from session 
        $this->session->unset_userdata('userData'); 
        $this->session->unset_userdata('clientValue'); 
        // Redirect to login page 
        flash('success', 'Güvenli Çıkış', 'Hesaptan Çıkış Yaptın.');
        redirect(base_url()); 
    } 
}