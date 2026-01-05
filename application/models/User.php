<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {
    function __construct() {
        $this->tableName = 'user';
        $this->primaryKey = 'id';
    }
    
    /*
     * Insert / Update facebook profile data into the database
     * @param array the data for inserting into the table
     */
    public function checkUser($userData = array()){
        if(!empty($userData)){
            //check whether user data already exists in database with same oauth info
            $this->db->select($this->primaryKey);
            $this->db->from($this->tableName);
            $this->db->where(array('oauth_uid'=>$userData['oauth_uid']));
            $prevQuery = $this->db->get();
            $prevCheck = $prevQuery->num_rows();
            
            if($prevCheck > 0){
                $prevResult = $prevQuery->row_array();
                
                //update user data
                $userData['modified'] = date("Y-m-d");
                $userData['isActive'] = '1';
                $update = $this->db->update($this->tableName, $userData, array('id' => $prevResult['id']));
                
                //get user ID
                $userID = $prevResult['id'];
            }else{
                //insert user data
                $rand = rand(1,9999);
                $username = $userData['name'] . $rand;
                if ($this->db->where(['mail' => $userData['mail']])->count_all_results('user') > 0 || $this->db->where(['username' => $username])->count_all_results('user') > 0) {
                    flash('Ups.', 'Bu bilgiler daha önce kullanılmış');
                    redirect(base_url());
                }

                $userData['regDate']  = date("Y-m-d");
                $userData['modified'] = date("Y-m-d");
                $userData['username'] = $username;
                $userData['pasword'] = paspas(date("Y-m-d H:i:s"));
                $insert = $this->db->insert($this->tableName, $userData);
                
                //get user ID
                $userID = $this->db->insert_id();
            }
        }
        
        //return user ID
        return $userID?$userID:FALSE;
    }
} 
?>