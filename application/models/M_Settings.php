<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Settings extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function getAllSettings() {
        $query = $this->db->get('settings');
        
        $settings = [];
        foreach ($query->result() as $row) {
            $settings[$row->key] = $row;
        }
        
        return $settings;
    }
    
    public function getSetting($key) {
        $this->db->where('key', $key);
        $query = $this->db->get('settings');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return null;
    }
    
    public function getSettingValue($key, $default = null) {
        $this->db->select('value');
        $this->db->where('key', $key);
        $query = $this->db->get('settings');
        
        if ($query->num_rows() > 0) {
            return $query->row()->value;
        }
        
        return $default;
    }
    
    public function updateSetting($key, $value, $description = null) {
        $this->db->where('key', $key);
        $query = $this->db->get('settings');
        
        $data = [
            'value' => $value,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($description !== null) {
            $data['description'] = $description;
        }
        
        if ($query->num_rows() > 0) {
            $this->db->where('key', $key);
            return $this->db->update('settings', $data);
        } else {
            $data['key'] = $key;
            $data['created_at'] = date('Y-m-d H:i:s');
            
            if ($description === null) {
                $data['description'] = 'Bakiye modülü ayarı';
            }
            
            return $this->db->insert('settings', $data);
        }
    }
    
    public function getBalanceSettings() {
        $balance_settings = [
            'enable_balance_transfer',
            'enable_balance_exchange',
            'enable_credit_operations',
            'usable2withdraw_commission'
        ];
        
        $settings = [];
        foreach ($balance_settings as $key) {
            $settings[$key] = $this->getSettingValue($key);
        }
        
        return $settings;
    }
} 