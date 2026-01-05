<?php
class M_Sessions extends CI_Model {

    public function update_user_activity($user_id, $current_page) {
        $session_id = session_id();

        $data = array(
            'user_id' => $user_id,
            'last_page' => $current_page,
            'last_activity' => date('Y-m-d H:i:s')
        );

        $this->db->where('id', $session_id);
        $this->db->update('ci_sessions', $data);
    }

    public function get_online_users($minutes = 5) {
        $timeout = date('Y-m-d H:i:s', strtotime('-'.$minutes.' minutes'));

        $this->db->select('u.*, s.last_page, s.last_activity');
        $this->db->from('ci_sessions s');
        $this->db->join('users u', 'u.id = s.user_id');
        $this->db->where('s.last_activity >', $timeout);
        $this->db->where('s.user_id IS NOT NULL');
        $this->db->group_by('s.user_id');

        return $this->db->get()->result();
    }
}