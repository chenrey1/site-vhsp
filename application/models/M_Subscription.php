<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Subscription extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Abonelik planını ID'ye göre alma
    public function getSubscriptionById($subscriptionId) {
        return $this->db->where('id', $subscriptionId)->get('subscriptions')->row();
    }

    // Abonelik planına ait özellikleri alma
    public function getSubscriptionFeatures($subscriptionId) {
        return $this->db->where('subscription_id', $subscriptionId)->get('subscription_features')->result();
    }

    //Abonelik planına ait özelliği alma
    public function getSubscriptionFeature($subscriptionId, $featureName) {
        $this->db->where('subscription_id', $subscriptionId);
        $this->db->where('feature_name', $featureName);
        return $this->db->get('subscription_features')->row();
    }

    // Kullanıcı id'sine göre abonelik planını çekme
    public function getSubscriptionByUserId($userId) {
        $this->db->select('us.subscription_id, s.id as subscription_table_id, s.name, s.description, s.duration, s.price');
        $this->db->from('user_subscriptions us');
        $this->db->join('subscriptions s', 'us.subscription_id = s.id');
        $this->db->where('us.user_id', $userId);
        $this->db->where('(us.end_date IS NULL OR us.end_date > NOW())');
        $this->db->order_by('us.id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    // get user_subscription
    public function getUserSubscription($userId, $subscriptionId) {
        $this->db->where('user_id', $userId);
        $this->db->where('subscription_id', $subscriptionId);
        $this->db->where('end_date IS NULL OR end_date >', date('Y-m-d'));
        return $this->db->get('user_subscriptions')->row();
    }

    // passiveUserSubscription function
    public function passiveUserSubscription($userId, $subscriptionId) {
        $this->db->where('user_id', $userId);
        $this->db->where('id', $subscriptionId);
        $this->db->update('user_subscriptions', [
            'auto_renew' => 'passive'
        ]);
    }

    // Abonelik planına ait tüm özellikleri kaldırma
    public function removeSubscriptionFeatures($subscriptionId) {
        $this->db->where('subscription_id', $subscriptionId)->delete('subscription_features');
    }

    //Abonelik planına ait özelliği kaldırma
    public function removeSubscriptionFeature($subscriptionId, $featureName) {
        $this->db->where('subscription_id', $subscriptionId);
        $this->db->where('feature_name', $featureName);
        $this->db->delete('subscription_features');
    }

    // Abonelik planı ekleme
    public function addSubscription($name, $description, $duration, $price) {
        $this->db->insert('subscriptions', [
            'name' => $name,
            'description' => $description,
            'duration' => $duration,
            'price' => $price
        ]);
        return $this->db->insert_id();
    }

    // Abonelik planına özellik ekleme
    public function addSubscriptionFeature($subscriptionId, $featureName, $value) {
        $this->db->insert('subscription_features', [
            'subscription_id' => $subscriptionId,
            'feature_name' => $featureName,
            'value' => $value
        ]);
    }

    // Abonelik planlarını güncelleme
    public function updateSubscription($id, $name, $description, $duration, $price) {
        $this->db->where('id', $id);
        $this->db->update('subscriptions', [
            'name' => $name,
            'description' => $description,
            'duration' => $duration,
            'price' => $price
        ]);
        return $this->db->affected_rows();
    }

    // Mevcut abonelik planlarını çekme
    public function getAllSubscriptions() {
        $query = $this->db->get('subscriptions');
        return $query->result();
    }

    //getActiveSubscription Function
    public function getActiveSubscription($userId) {
        $this->db->select('us.id, us.user_id, us.start_date, us.end_date, us.duration, us.price, s.name as subscription_name, s.id as subscription_id');
        $this->db->from('user_subscriptions us');
        $this->db->join('subscriptions s', 'us.subscription_id = s.id');
        $this->db->where('us.user_id', $userId);
        $this->db->group_start()
            ->where('us.end_date IS NULL')
            ->or_where('us.end_date >', date('Y-m-d'))
            ->group_end();
        return $this->db->get()->row();
    }
    public function getUserSubscriptions($userId, $limit = null, $offset = null) {
        $this->db->select('user_subscriptions.*, subscriptions.name as subscription_name');
        $this->db->from('user_subscriptions');
        $this->db->join('subscriptions', 'user_subscriptions.subscription_id = subscriptions.id');
        $this->db->where('user_subscriptions.user_id', $userId);
        $this->db->order_by('user_subscriptions.id', 'DESC');

        if (!is_null($limit) && !is_null($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function getTotalUserSubscriptions($userId) {
        $this->db->from('user_subscriptions');
        $this->db->where('user_subscriptions.user_id', $userId);
        return $this->db->count_all_results();
    }

    //Mevcut abone kullanıcıları çekme
    public function getSubscriptionsUsers($subscriptionId) {
        $today = date('Y-m-d');
        $this->db->select('us.id, us.user_id, us.start_date, us.end_date, u.email, u.name, u.surname, s.id as subscription_id, s.name as subscription_name');
        $this->db->from('user_subscriptions us');
        $this->db->join('user u', 'us.user_id = u.id');
        $this->db->join('subscriptions s', 'us.subscription_id = s.id');
        $this->db->where('us.subscription_id', $subscriptionId);
        $this->db->where('us.end_date IS NULL OR us.end_date >', $today);
        $query = $this->db->get();
        return $query->result();
    }

    //Mevcut tüm abone kullanıcıları çekme
    public function getAllSubscriptionsUsers() {
        $today = date('Y-m-d');
        $this->db->select('us.id, us.user_id, us.start_date, us.end_date, u.email, u.name, u.surname, s.id as subscription_id, s.name as subscription_name');
        $this->db->where('us.end_date IS NULL OR us.end_date >', $today);
        $this->db->from('user_subscriptions us');
        $this->db->join('user u', 'us.user_id = u.id');
        $this->db->join('subscriptions s', 'us.subscription_id = s.id');
        $query = $this->db->get();
        return $query->result();
    }

    public function calculateRemainingDay($endDate) {
        $today = date('Y-m-d');
        $diff = abs(strtotime($endDate) - strtotime($today));
        $days = floor($diff / (60 * 60 * 24));
        if ($days < 0) {
            return 0;
        }else {
            return $days;
        }
    }

    // Kullanıcı aboneliği ekleme
    public function addUserSubscription($userId, $subscriptionId, $startDate, $endDate = null)
    {
        $user = $this->db->where('id', $userId)->get('user')->row();
        $subscription = $this->getSubscriptionById($subscriptionId);
        $result = $this->db->insert('user_subscriptions', [
            'user_id' => $userId,
            'subscription_id' => $subscriptionId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $subscription->price,
            'duration' => $subscription->duration
        ]);
        //last insert id
        $userSubscriptionID = $this->db->insert_id();
        if ($result) {
            //load helper api
            $this->load->helper('api');
            createInvoiceforSubscription($user, $subscription, $userSubscriptionID);
            
            // Abonelik satın alımı için bakiye çıkış kaydını oluştur
            $transaction_data = [
                'user_id' => $userId,
                'transaction_type' => 'subscription_fee',
                'amount' => -$subscription->price,
                'description' => $subscription->name . ' aboneliği satın alımı',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $user->balance, // Güncellenmiş bakiye
                'related_id' => $userSubscriptionID
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
            
            //load model M_Earnings
            $this->load->model('M_Earnings');
            $this->M_Earnings->insertEarning(0, 0, [
                'user_id' => $userId,
                'amount' => $subscription->price,
                'payment_method' => 'balance',
                'description' => $subscription->name . ' aboneliği satın alındı.',
                'seller_type' => 'site'
            ]);

            // Get subscription features
            $features = $this->getSubscriptionFeatures($subscriptionId);
            $featuresList = '';
            foreach ($features as $feature) {
                if ($feature->feature_name == 'commission_value') {
                    $featuresList .= '<li>Ödeme komisyonlarında %' . $feature->value . ' ödeme komisyonu avantajı</li>';
                } elseif ($feature->feature_name == 'refund_value') {
                    $featuresList .= '<li>Her alışverişte %' . $feature->value . ' bakiye iadesi</li>';
                }
            }

            // Get site properties
            $properties = $this->db->where('id', 1)->get('properties')->row();

            // Send subscription start email
            $this->load->library('mailer');
            $this->mailer->send($user->email, 'subscription_start', [
                'name' => $user->name,
                'subscription_name' => $subscription->name,
                'start_date' => date('d.m.Y H:i', strtotime($startDate)),
                'end_date' => date('d.m.Y H:i', strtotime($endDate)),
                'amount' => number_format($subscription->price, 2),
                'subscription_features' => $featuresList,
                'date' => date('d.m.Y H:i'),
                'company_name' => $properties->name,
                'company_logo' => base_url('assets/img/') . $properties->logo,
                'company_url' => base_url(),
                'site_url' => base_url()
            ]);
        }
    }

    //update duration Subscription
    public function updateDurationSubscription($userId, $userSubscriptionID, $subscriptionId, $endDate, $duration) {
        $user = $this->db->where('id', $userId)->get('user')->row();
        $subscription = $this->getSubscriptionById($subscriptionId);

        $this->db->where('user_id', $userId);
        $this->db->where('subscription_id', $subscriptionId);
        $this->db->where('id', $userSubscriptionID);
        $this->db->update('user_subscriptions', [
            'duration' => $duration,
            'end_date' => $endDate
        ]);
        //auto renew and status update
        $this->db->where('user_id', $userId);
        $this->db->where('subscription_id', $subscriptionId);
        $this->db->where('id', $userSubscriptionID);
        $this->db->update('user_subscriptions', [
            'auto_renew' => 'active',
            'status' => 'active'
        ]);

        addLog('addUserSubscription', 'User subscription added successfully');

        // Abonelik süresi uzatma için bakiye çıkış kaydını oluştur
        $transaction_data = [
            'user_id' => $userId,
            'transaction_type' => 'subscription_fee',
            'amount' => -$subscription->price,
            'description' => $subscription->name . ' aboneliği süre uzatma',
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $user->balance, // İşlem öncesi bakiye
            'balance_after_transaction' => $user->balance, // Güncellenmiş bakiye
            'related_id' => $userSubscriptionID
        ];
        
        $this->db->insert('wallet_transactions', $transaction_data);

        //load helper api
        $this->load->helper('api');
        createInvoiceforSubscription($user, $subscription, $userSubscriptionID);

        //load model M_Earnings
        $this->load->model('M_Earnings');
        $result = $this->M_Earnings->insertEarning(0, 0, [
            'user_id' => $userId,
            'amount' => $subscription->price,
            'payment_method' => 'balance',
            'description' => $subscription->name . ' aboneliği satın alındı.',
            'seller_type' => 'site'
        ]);
    }

    // endUserSubscription function
    public function endUserSubscription($userId) {
        $this->db->where('user_id', $userId)->update('user_subscriptions', [
            'end_date' => date('Y-m-d H:i:s', strtotime("-1 days")),
            'auto_renew' => 'passive',
            'status' => 'passive'
        ]);
    }

    // Kullanıcının abonelik özelliklerini getirme
    public function getUserSubscriptionFeatures($userId) {
        $this->db->select('sf.feature_name, sf.value');
        $this->db->from('user_subscriptions us');
        $this->db->join('subscription_features sf', 'us.subscription_id = sf.subscription_id');
        $this->db->where('us.user_id', $userId);
        $query = $this->db->get();
        return $query->result();
    }

    //Mevcut tüm aboneleri kontrol et. Süresi dolanların auto_renew sütunu passive olanların bakiyesinden abonelik ücretini çekme ve aboneliği sonlandır.
    public function checkAndEndSubscriptions() {
        $today = date('Y-m-d');
        $subscriptions = $this->db->where('end_date <=', $today)->where('auto_renew', 'passive')->get('user_subscriptions')->result();
        foreach ($subscriptions as $subscription) {
            $this->db->where('id', $subscription->id)->update('user_subscriptions', [
                'end_date' => $today,
                'auto_renew' => 'passive',
                'status' => 'passive'
            ]);
        }
    }

    // Mevcut tüm aboneleri kontrol et. Süresi dolanların auto_renew sütunu active olanların bakiyesinden abonelik ücretini çek ve yenile.
    public function checkAndRenewSubscriptions() {
        $today = date('Y-m-d');
        $subscriptions = $this->db->where('end_date <=', $today)->where('auto_renew', 'active')->get('user_subscriptions')->result();
        foreach ($subscriptions as $subscription) {
            $user = $this->db->where('id', $subscription->user_id)->get('user')->row();
            $subscriptionPlan = $this->getSubscriptionById($subscription->subscription_id);

            //Kullanıcının bakiyesini kontrol et ve yeterli bakiyesi varsa aboneliği yenile
            if ($user->balance >= $subscriptionPlan->price) {
                //Kullanıcının bakiyesini azalt
                $this->db->where('id', $user->id)->update('user', [
                    'balance' => $user->balance - $subscriptionPlan->price
                ]);
                $this->db->where('id', $subscription->id)->update('user_subscriptions', [
                    'end_date' => date('Y-m-d', strtotime('+' . $subscriptionPlan->duration . ' days')),
                    'auto_renew' => 'active',
                    'status' => 'active'
                ]);

                // Abonelik yenilemesi için bakiye çıkış kaydını oluştur
                $transaction_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'subscription_fee',
                    'amount' => -$subscriptionPlan->price,
                    'description' => $subscriptionPlan->name . ' aboneliği otomatik yenileme',
                    'status' => 1, // Onaylı
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance_before' => $user->balance, // İşlem öncesi bakiye
                    'balance_after_transaction' => $user->balance, // Güncellenmiş bakiye
                    'related_id' => $subscription->id
                ];
                
                $this->db->insert('wallet_transactions', $transaction_data);

                //load helper api
                $this->load->helper('api');
                createInvoiceforSubscription($user, $subscriptionPlan, $subscription->id);

                //load model M_Earnings
                $this->load->model('M_Earnings');
                $this->M_Earnings->insertEarning(0, 0, [
                    'user_id' => $user->id,
                    'amount' => $subscriptionPlan->price,
                    'payment_method' => 'balance',
                    'description' => $subscriptionPlan->name . ' aboneliği yenilendi.',
                    'seller_type' => 'site'
                ]);
                sendNotificationSite($user->id, 'Aboneliğiniz yenilendi', 'Aboneliğiniz başarıyla yenilendi. ' . $subscriptionPlan->duration . ' günlük ' . $subscriptionPlan->name . ' aboneliğiniz devam ediyor.', base_url('client/my_subscription'));
                addLog('checkAndRenewSubscriptions', $user->name . ' ' . $subscriptionPlan->name . ' aboneliği sistem tarafından otomatik olarak yenilendi.' . ' Abonelik ücreti: ' . $subscriptionPlan->price . ' TL');
            }else{
                //Kullanıcının bakiyesi yetersiz olduğunda aboneliği sonlandır
                $this->db->where('id', $subscription->id)->update('user_subscriptions', [
                    'end_date' => $today,
                    'auto_renew' => 'passive',
                    'status' => 'passive'
                ]);
                sendNotificationSite($user->id, 'Aboneliğiniz sonlandırıldı', 'Otomatik yenileme sırasında yetersiz bakiyeniz olması sebebiyle aboneliğiniz sonlandırıldı.', base_url('client/my_subscription'));
                addLog('checkAndRenewSubscriptions', $user->name . ' ' . $subscriptionPlan->name . ' aboneliği sistem tarafından otomatik olarak sonlandırıldı. Yetersiz bakiye.');
            }
        }
    }

    // Kullanıcının ödeme komisyonunu hesaplama
    public function calculateUserCommission($userId, $amount) {
        // Genel komisyon oranını properties tablosundan alıyoruz
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $commissionRate = $properties->commission;

        // Kullanıcının abonelik özelliklerini alıyoruz
        $features = $this->getUserSubscriptionFeatures($userId);

        foreach ($features as $feature) {
            if ($feature->feature_name == 'commission_value') {
                $commissionRate = $feature->value;
                break;
            }
        }

        $commission = ($amount * $commissionRate) / 100;
        return $commission;
    }

    // Kullanıcının belirli bir özelliğe sahip olup olmadığını kontrol etme
    public function hasUserFeature($userId, $featureName) {
        // Kullanıcının aktif bir aboneliği var mı kontrol et
        $this->db->select('us.subscription_id');
        $this->db->from('user_subscriptions us');
        $this->db->where('us.user_id', $userId);
        $this->db->where('us.end_date IS NULL OR us.end_date >', date('Y-m-d'));
        $activeSubscription = $this->db->get()->row();

        if (!$activeSubscription) {
            // Kullanıcının aktif bir aboneliği yoksa false döndür
            return false;
        }

        // Kullanıcının abonelik planında belirli bir özellik var mı kontrol et
        $this->db->select('sf.id');
        $this->db->from('subscription_features sf');
        $this->db->where('sf.subscription_id', $activeSubscription->subscription_id);
        $this->db->where('sf.feature_name', $featureName);
        $featureExists = $this->db->get()->row();

        return !empty($featureExists);
    }

    // Kullanıcının iade tutarını hesaplama
    public function calculateUserRefund($userId, $purchaseAmount) {
        $features = $this->getUserSubscriptionFeatures($userId);
        $refundRate = 0.00;
        $maxRefund = 0.00;

        foreach ($features as $feature) {
            if ($feature->feature_name == 'refund_value') {
                $refundRate = $feature->value;
            }
            if ($feature->feature_name == 'max_refund_value') {
                if ($feature->value == 0) {
                    $maxRefund = $purchaseAmount;
                } else {
                    $maxRefund = $feature->value;
                }
            }
        }

        $refundAmount = ($purchaseAmount * $refundRate) / 100;
        if ($refundAmount > $maxRefund) {
            $refundAmount = $maxRefund;
        }

        return $refundAmount;
    }

    public function extract_amount($string) {
        // Düzenli ifade ile miktarı çekmek için preg_match kullanıyoruz
        preg_match('/İade edilen miktar: ([\d\.]+) TL/', $string, $matches);

        // $matches dizisinin ikinci elemanı çekilen miktarı içerir
        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return 0; // Miktar bulunamadığında null döner
        }
    }

    //daha önce bonus verilen ürünün iptal bakiyesini hesapla
    public function calculateUserCancelBonus($userId, $purchaseAmount, $shop_id, $subscription_id) {
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        $user_saving = $this->db->where('user_id', $userId)
            ->where('shop_id', $shop_id)
            ->where('subscription_id', $subscription_id)
            ->get('user_savings')->row();

        $cancelBonusAmount = 0.00;

        // $purchaseAmount içerisindeki fiyat $shop->price altındaki fiyatın yüzde kaçı
        if ($shop->price != 0) {
            $cancelBonusRate = ($purchaseAmount / $shop->price) * 100;
            $cancelBonusRate = round($cancelBonusRate, 2);
        } else {
            $cancelBonusRate = 0;
        }

        // Kullanıcı kazanç miktarına bu oranı uygulayarak bonus miktarını hesapla
        if (isset($user_saving->total_amount)) {
            $cancelBonusAmount = ($user_saving->total_amount * $cancelBonusRate) / 100;
            $cancelBonusAmount = round($cancelBonusAmount, 2);
        }

        $allRefundBonus = $this->extract_amount($user_saving->description);
        $allRefundBonus = round($allRefundBonus, 2);

        $cancelBonusAmount += $allRefundBonus;
        $cancelBonusAmount = round($cancelBonusAmount, 2);

        $newAmount = round($user_saving->amount - $cancelBonusAmount, 2);
        $descriptionAmount = $user_saving->total_amount - $newAmount;

        if ($newAmount < 0) {
            $cancelBonusAmount += $newAmount;
            $descriptionAmount = $user_saving->total_amount;
            $newAmount = 0;
        }

        // Update user_savings amount
        if (isset($user_saving->amount)) {
            $this->db->where('id', $user_saving->id)->update('user_savings', [
                'amount' => $newAmount,
                'status' => 'cancelled',
                'description' => 'Bonus iptali yapıldı. Toplam iade edilen miktar: ' . $descriptionAmount . ' TL',
            ]);
        }

        return $cancelBonusAmount;
    }

    // Belirli bir aboneliğe sahip kişi sayısını çekme
    public function countUsersBySubscription($subscriptionId) {
        $this->db->where('subscription_id', $subscriptionId);
        $this->db->from('user_subscriptions');
        return $this->db->count_all_results();
    }

    //Bir kullanıcının geçmiş aboneliklerini çek (subscription_name, start_date, end_date, duration, price, user.name)
    public function getPastSubscriptions($userId) {
        $this->db->select('s.name as subscription_name, us.start_date, us.end_date, us.duration, us.price, u.name');
        $this->db->from('user_subscriptions us');
        $this->db->join('subscriptions s', 'us.subscription_id = s.id');
        $this->db->join('user u', 'us.user_id = u.id');
        $this->db->where('us.user_id', $userId);
        $query = $this->db->get();
        return $query->result();
    }

    //Bir kullanıcının geçmiş kazanımlarını (user_savings tablosundan) çekme
    public function getUserAchievements($userId, $limit = null, $offset = null) {
        $this->load->helper('url'); // base_url() fonksiyonunu kullanmak için gerekli
        $this->db->select('user_savings.id, subscriptions.name as subscription_name, user_savings.amount, user_savings.transaction_date, user_savings.shop_id, user_savings.reason, user_savings.status, user_savings.description');
        $this->db->from('user_savings');
        $this->db->join('subscriptions', 'user_savings.subscription_id = subscriptions.id');
        $this->db->where('user_savings.user_id', $userId);
        $this->db->order_by('user_savings.transaction_date', 'DESC');

        if (!is_null($limit) && !is_null($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        $results = $query->result();

        // Durumları Türkçeye çevir ve renk ekle
        foreach ($results as &$result) {
            switch ($result->status) {
                case 'successful':
                    $result->status = '<span class="text-success">Başarılı</span>';
                    break;
                case 'pending':
                    $result->status = '<span class="text-warning">Beklemede</span>';
                    break;
                case 'cancelled':
                    $result->status = '<span class="text-danger">İptal Edildi</span>';
                    break;
            }

            // Satın alım numarasına link ekle
            $result->shop_link = base_url('admin/product/invoice/' . $result->shop_id);
        }

        return $results;
    }

    public function getTotalUserAchievements($userId) {
        $this->db->from('user_savings');
        $this->db->where('user_savings.user_id', $userId);
        return $this->db->count_all_results();
    }

    public function cancelProductBonus($pendingProduct) {
        //load model M_Earnings
        $this->load->model('M_Earnings');

        $shop = $this->M_Earnings->getEarningsByShopID($pendingProduct->shop_id);
        $this->M_Earnings->updateEarning($shop->id,
        [
            'amount' => $shop->amount - $pendingProduct->price,
            'total' => $shop->total - $pendingProduct->price
        ]);

        $user_id = $pendingProduct->user_id;
        $price = $pendingProduct->price;
        $user_saving = $this->db->where('shop_id', $pendingProduct->shop_id)->get('user_savings')->row();

        $productRefundAmount = $this->calculateUserCancelBonus($user_id, $price, $pendingProduct->shop_id, $user_saving->subscription_id);
        return round($productRefundAmount, 2);

    }

    //cancelProductBonus noStock Products
    public function cancelProductBonusNoStock($invoice_id) {
        //load model M_Earnings
        $this->load->model('M_Earnings');

        $invoice = $this->db->where('id', $invoice_id)->get('invoice')->row();
        $shop = $this->M_Earnings->getEarningsByShopID($invoice->shop_id);
        $this->M_Earnings->updateEarning($shop->id, [
            'amount' => $shop->amount - $invoice->price,
            'total' => $shop->total - $invoice->price
        ]);

        $user_id = $shop->user_id;
        $price = $invoice->price;
        $user_saving = $this->db->where('shop_id', $invoice->shop_id)->get('user_savings')->row();

        $productRefundAmount = $this->calculateUserCancelBonus($user_id, round($price, 2), $invoice->shop_id, $user_saving->subscription_id);
        return round($productRefundAmount, 2);

    }

    //get commission_value
    public function getCommissionValue($userId) {
        $user_subscription = $this->getSubscriptionByUserId($userId);
        if ($user_subscription) {
            $feature = $this->getSubscriptionFeature($user_subscription->subscription_table_id, 'commission_value');
            return $feature ? $feature->value : 0;
        } else {
            $commissionRate = $this->db->where('id', 1)->get('properties')->row()->commission;
            return $commissionRate;
        }
    }

    //calculate commission saving
    public function calculateSavingCommission($shop_id) {
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        $normalCommission = number_format(($shop->price * $this->db->where('id', 1)->get('properties')->row()->commission) / 100, 2, '.', '');
        $userCommission = number_format(($shop->price * $this->getCommissionValue($shop->user_id)) / 100, 2, '.', '');
        $earning = $normalCommission - $userCommission;
        return round($earning, 2);
    }


}
