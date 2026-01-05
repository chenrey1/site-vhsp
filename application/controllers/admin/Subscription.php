<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends G_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
        (isPermFunction('seeNotification') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->model('M_Subscription');
    }

    // Mevcut abonelik planlarını listeleme
    public function list_subscriptions() {
        $subscriptions = $this->M_Subscription->getAllSubscriptions();
        return $subscriptions;
    }

    public function addSubscription() {
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'subList',
        ];

        $this->adminView('add-subscription', $data);
    }

    public function subList($subscription_id = null) {

        //eğer subscription_id varsa sadece o aboneliği listele. Yoksa tüm abonelikleri listele
        if ($subscription_id) {
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'status' => 'subList',
                'subscriptions' => $this->M_Subscription->getSubscriptionsUsers($subscription_id),
            ];
        } else {
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'status' => 'subList',
                'subscriptions' => $this->M_Subscription->getAllSubscriptionsUsers(),
            ];
        }

        $data['users'] = $this->db->where('isActive', 1)->get('user')->result();

        $this->adminView('subscription-list', $data);
    }

    public function search_users()
    {
        $query = $this->input->get('query');
        $this->db->like('name', $query);
        $this->db->or_like('surname', $query);
        $this->db->or_like('email', $query);
        $query = $this->db->get('user');
        echo $query->result();
    }

    public function subSettings() {
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'subList',
            'subscriptions' => $this->list_subscriptions(),
        ];

        $this->adminView('subscription-settings', $data);
    }

    // Abonelik planı ekleme
    public function add_subscription() {
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $duration = $this->input->post('duration');
        $price = $this->input->post('price');

        // Aboneliği ekleyin
        $subscriptionId = $this->M_Subscription->addSubscription($name, $description, $duration, $price);

        // Komisyon indirimi ekleme
        if ($this->input->post('discount_commission') == '1') {
            $discountCommissionValue = $this->input->post('discount_commission_value');
            $this->M_Subscription->addSubscriptionFeature($subscriptionId, 'commission_value', $discountCommissionValue);
        }

        // Bakiye iadesi ekleme
        if ($this->input->post('refund_balance') == '1') {
            $refundBalanceValue = $this->input->post('refund_balance_value');
            $refundMaxBalanceValue = $this->input->post('refund_max_balance_value');
            $this->M_Subscription->addSubscriptionFeature($subscriptionId, 'refund_value', $refundBalanceValue);
            $this->M_Subscription->addSubscriptionFeature($subscriptionId, 'max_refund_value', $refundMaxBalanceValue);
        }

        // Başarılı mesajını göster
        flash('Başarılı', 'Abonelik başarıyla eklendi.');
        redirect(base_url('admin/subscription/subSettings'), 'refresh');
    }

    public function edit_subscription($subscription_id) {
        $subscription = $this->M_Subscription->getSubscriptionById($subscription_id);
        $features = $this->M_Subscription->getSubscriptionFeatures($subscription_id);

        $subscription_data = [
            'id' => $subscription->id,
            'name' => $subscription->name,
            'description' => $subscription->description,
            'duration' => $subscription->duration,
            'price' => $subscription->price,
            'commission_value' => null,
            'refund_value' => null,
            'max_refund_value' => null,
        ];

        foreach ($features as $feature) {
            if ($feature->feature_name == 'commission_value') {
                $subscription_data['commission_value'] = $feature->value;
            }
            if ($feature->feature_name == 'refund_value') {
                $subscription_data['refund_value'] = $feature->value;
            }
            if ($feature->feature_name == 'max_refund_value') {
                $subscription_data['max_refund_value'] = $feature->value;
            }
        }

        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'subList',
            'subscription' => (object) $subscription_data,
        ];

        $this->adminView('edit-subscription', $data);
    }

    public function update_subscription() {
        $subscriptionId = $this->input->post('subscription_id');
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $duration = $this->input->post('duration');
        $price = $this->input->post('price');

        // Aboneliği güncelle
        $this->M_Subscription->updateSubscription($subscriptionId, $name, $description, $duration, $price);

        // Komisyon indirimi güncelle
        if ($this->input->post('discount_commission') == '1') {
            //remove old commission value
            $this->M_Subscription->removeSubscriptionFeature($subscriptionId, 'commission_value');
            //add new commission value
            $discountCommissionValue = $this->input->post('discount_commission_value');
            $this->M_Subscription->addSubscriptionFeature($subscriptionId, 'commission_value', $discountCommissionValue);
        } else {
            $this->M_Subscription->removeSubscriptionFeature($subscriptionId, 'commission_value');
        }

        // Bakiye iadesi güncelle
        //$this->input->post == 1 && getSubscriptionFeature($subscriptionId, 'refund_value') != $this->input->post('refund_balance_value')

        if ($this->input->post('refund_balance') == '1') {
            if ($this->M_Subscription->getSubscriptionFeature($subscriptionId, 'refund_value') != $this->input->post('refund_balance_value')){
                //remove old refund values
                $this->M_Subscription->removeSubscriptionFeature($subscriptionId, 'refund_value');
                $refundBalanceValue = $this->input->post('refund_balance_value');
                $this->M_Subscription->addSubscriptionFeature($subscriptionId, 'refund_value', $refundBalanceValue);
            }
            if ($this->M_Subscription->getSubscriptionFeature($subscriptionId, 'max_refund_value') != $this->input->post('refund_max_balance_value')){
                //remove old refund values
                $this->M_Subscription->removeSubscriptionFeature($subscriptionId, 'max_refund_value');
                $refundMaxBalanceValue = $this->input->post('refund_max_balance_value');
                $this->M_Subscription->addSubscriptionFeature($subscriptionId, 'max_refund_value', $refundMaxBalanceValue);
            }
        } else {
            $this->M_Subscription->removeSubscriptionFeature($subscriptionId, 'refund_value');
            $this->M_Subscription->removeSubscriptionFeature($subscriptionId, 'max_refund_value');
        }

        // Başarılı mesajını göster
        flash('Başarılı', 'Abonelik başarıyla güncellendi.');
        redirect(base_url('admin/subscription/subSettings'), 'refresh');
    }

    // delete subscription and its features
    public function delete_subscription($subscription_id) {
        $this->db->where('id', $subscription_id)->delete('subscriptions');
        $this->db->where('subscription_id', $subscription_id)->update('user_subscriptions', ['is_active' => 0]);
        $this->db->where('subscription_id', $subscription_id)->delete('subscription_features');
        flash('Başarılı', 'Abonelik başarıyla silindi.');
        redirect(base_url('admin/subscription/subSettings'), 'refresh');
    }
    // Kullanıcıyı aboneliğe ekleme örneği
    public function add_user_subscription() {
        $userId = $this->input->post('user_id');
        $subscriptionId = $this->input->post('subscription_id');
        $startDate = $this->input->post('start_date');
        $endDate = $this->input->post('end_date');
        $this->M_Subscription->addUserSubscription($userId, $subscriptionId, $startDate, $endDate);
        echo "Kullanıcı aboneliğe eklendi.";
    }

    // Kullanıcının belirli bir özelliğe sahip olup olmadığını kontrol etme örneği
    public function has_user_feature() {
        $userId = $this->input->post('user_id');
        $featureName = $this->input->post('feature_name');
        $hasFeature = $this->M_Subscription->hasUserFeature($userId, $featureName);

        if ($hasFeature) {
            echo json_encode(['has_feature' => true]);
        } else {
            echo json_encode(['has_feature' => false]);
        }
    }

    // Kullanıcının ödeme komisyonunu hesaplama örneği
    public function calculate_user_commission() {
        $userId = $this->input->post('user_id');
        $amount = $this->input->post('amount');
        $commission = $this->M_Subscription->calculateUserCommission($userId, $amount);
        echo json_encode(['commission' => $commission]);
    }

    // Kullanıcının iade tutarını hesaplama örneği
    public function calculate_user_refund() {
        $userId = $this->input->post('user_id');
        $purchaseAmount = $this->input->post('purchase_amount');
        $refund_value = $this->M_Subscription->calculateUserRefund($userId, $purchaseAmount);
        echo json_encode(['refund_value' => $refund_value]);
    }

    // Belirli bir aboneliğe sahip kişi sayısını çekme
    public function count_users_by_subscription($subscriptionId) {
        $userCount = $this->M_Subscription->countUsersBySubscription($subscriptionId);
        echo json_encode(['subscription_id' => $subscriptionId, 'user_count' => $userCount]);
    }

    public function get_history($user_id) {
        // Kullanıcının geçmiş aboneliklerini al
        $past_subscriptions = $this->M_Subscription->getPastSubscriptions($user_id);
        // Kullanıcının geçmiş kazanımlarını al
        $achievements = $this->M_Subscription->getUserAchievements($user_id);
        echo json_encode([
            'past_subscriptions' => $past_subscriptions,
            'achievements' => $achievements
        ]);
    }

    //Bu fonksiyon user_subscriptons tablosundaki ilgili aboneliğin end_date tarihini bugün yapar.
    public function ended_subscription($subscription_id) {
        $this->db->where('id', $subscription_id)->update('user_subscriptions', ['end_date' => date('Y-m-d')]);
        redirect(base_url('admin/subscription/subList'), 'refresh');
        flash('Başarılı', 'Abonelik sonlanma tarihi bugün olarak ayarlandı.');
    }


}
