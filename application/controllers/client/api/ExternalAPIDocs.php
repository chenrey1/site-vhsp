<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExternalAPIDocs extends G_Controller {
    
    public function __construct() {
        parent::__construct();
        $properties = $this->db->get('properties')->row();
        if ($properties->api_is_active == 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['status' => false, 'message' => 'API is disabled']))
                ->_display();
            exit();
        }
    }

    public function index() {
        $properties = $this->db->get('properties')->row();
        $smtp = $this->db->get('smtp')->row();
        $this->load->view('docs', [
            "logo_url" => ($properties->choose == 0) ? base_url("assets/img/site/" . $properties->img) : null,
            "logo_text" => $properties->name,
            "SITE_NAME" => $properties->name,
            "SUPPORT_MAIL" => $smtp->mail
        ]);
    }

    public function openapi() {
        $properties = $this->db->get('properties')->row();
        $openapi_spec = file_get_contents(FCPATH . 'assets/openapi-spec.yaml');
        $openapi_spec = str_replace('{{SITE_NAME}}', $properties->name, $openapi_spec);
        $openapi_spec = str_replace('{{API_URL}}', base_url('api/v1'), $openapi_spec);
        $this->output->set_content_type('application/yaml')->set_output($openapi_spec);
    }
}