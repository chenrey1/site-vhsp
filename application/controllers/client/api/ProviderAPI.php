<?php
// controllers/API.php
class ProviderAPI extends G_Controller {

    public function __construct() {

        parent::__construct();

    }

    /*
     * Get categories from product_provider
     *
     * product_providers = id, name, type, is_active, base_url, api_details, created_at, updated_at (api_details json ve type hyper için api_key, api_token olacak orius için mail, password)
     */
    public function categories($provider_id)
    {
        $provider = $this->db->where('id', $provider_id)->get('product_providers')->row();

        if (!$provider) {
            $this->response(['status' => false, 'message' => 'Provider not found'], 404);
            return;
        }

        $api_details = json_decode($provider->api_details);

        if ($provider->type == 'hyper') {
            $this->load->helper('provider');
            $categories = getHyperCategories($api_details->api_key, $api_details->api_token);
        } else if ($provider->type == 'orius') {
            $categories = getOriusCategories($api_details->api_key, $api_details->api_token);
        } else {
            $this->response(['status' => false, 'message' => 'Provider type not supported'], 400);
            return;
        }

        if ($categories === false) {
            $this->response(['status' => false, 'message' => 'Error while fetching categories'], 500);
            return;
        }

        $this->response(['status' => true, 'categories' => $categories]);
    }

}

