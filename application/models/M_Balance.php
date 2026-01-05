<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Balance extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Banka havalesi bildirimi
     * 
     * @param int $user_id Kullanıcı ID
     * @param int $bank_id Banka ID
     * @param string $name Gönderen adı
     * @param string $date İşlem tarihi
     * @param float $price Yatırılan tutar
     * @return array İşlem sonucu
     */
    public function addBankTransfer($user_id, $bank_id, $name, $date, $price) {
        // Kullanıcı kontrolü
        $user = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$user) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Banka kontrolü
        $bank = $this->db->get_where('banks', ['id' => $bank_id])->row();
        if (!$bank) {
            return ['status' => false, 'message' => 'Geçersiz banka seçimi.'];
        }
        
        // Havale bildirimi kaydı
        $data = [
            'user_id'    => $user_id,
            'banks_id'   => $bank_id,
            'date'       => $date,
            'amount'     => $price,
            'redMessage' => $name,
            'isActive' => 1,
        ];
        
        $this->db->insert('bank_transfer', $data);
        // Oluşturulan havale kaydının ID'sini al
        $transfer_id = $this->db->insert_id();
        
        // İşlem kaydı
        $transaction_data = [
            'user_id'                => $user_id,
            'amount'                 => $price,
            'payment_method'         => 'havale',
            'status'                 => 0, // Beklemede
            'created_at'             => date('Y-m-d H:i:s'),
            'balance_before'         => $user->balance, // İşlem öncesi bakiye
            'balance_after_transaction' => $user->balance, // Şu anki bakiye (henüz onaylanmadığı için değişmedi)
            'related_id'             => $transfer_id,
        ];
        
        $this->db->insert('wallet_transactions', $transaction_data);
        
        return [
            'status' => true, 
            'message' => 'Havale bildiriminiz başarıyla kaydedilmiştir. Kontrol edildikten sonra bakiyenize yansıtılacaktır.'
        ];
    }
    
    /**
     * Bakiye çekme işlemi
     * 
     * @param int $user_id Kullanıcı ID
     * @param float $amount Çekilecek tutar
     * @param string $iban IBAN numarası
     * @param string $account_holder Hesap sahibi
     * @return array İşlem sonucu
     */
    public function withdrawBalance($user_id, $amount, $iban, $account_holder) {
        // Kullanıcı kontrolü
        $user = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$user) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Tutarı pozitif değere çevir - güvenlik için
        $amount = abs(floatval($amount));
        
        // Properties tablosundan minimum çekim miktarını al
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $min_withdraw = $properties->min_draw;
        
        // Minimum çekim kontrolü
        if ($amount < $min_withdraw) {
            return ['status' => false, 'message' => 'Minimum çekim tutarı ' . $min_withdraw . ' TL\'dir.'];
        }
        
        // Çekilebilir bakiye kontrolü
        if ($user->balance2 < $amount) {
            return ['status' => false, 'message' => 'Yetersiz çekilebilir bakiye.'];
        }
        
        // İşlem başlat (transaction)
        $this->db->trans_start();
        
        // Bakiyeyi rezerve etme (azaltma) - güvenli şekilde
        $updated_balance = $user->balance2 - $amount;
        $this->db->where('id', $user_id);
        $this->db->where('balance2 >=', $amount); // Ek güvenlik kontrolü
        $this->db->update('user', ['balance2' => $updated_balance]);
        
        // Kontrol et - eğer güncellenen satır yoksa, bakiye yetersiz demektir
        if ($this->db->affected_rows() == 0) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'İşlem gerçekleştirilemedi. Bakiye yetersiz olabilir.'];
        }
        
        // Önce request tablosuna para çekme talebini ekle
        $request_data = [
            'amount' => $amount,
            'user_id' => $user_id,
            'status' => 2, // Beklemede
            'date' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('request', $request_data);
        $request_id = $this->db->insert_id(); // Oluşturulan çekim talebinin ID'sini al
        
        // İşlem kaydı - wallet_transactions tablosuna kaydet
        $transition_data = [
            'user_id' => $user_id,
            'transaction_type' => 'withdrawal',
            'balance_type' => 'withdrawable',
            'amount' => -$amount,
            'balance_before' => $user->balance2,
            'balance_after' => $updated_balance,
            'description' => 'Banka hesabına para çekme talebi',
            'related_id' => $request_id, // Çekim talebi ID'sini kaydet
            'payment_method' => 'bank_transfer',
            'status' => 0, // Beklemede
            'meta_data' => json_encode([
                'bank_name' => $user->bank_name,
                'iban' => $iban,
                'account_holder' => $account_holder
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'balance_after_transaction' => $updated_balance
        ];
        
        $this->db->insert('wallet_transactions', $transition_data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return ['status' => false, 'message' => 'Para çekme işlemi sırasında bir hata oluştu.'];
        }
        
        return [
            'status' => true, 
            'message' => 'Bakiye çekme talebiniz başarıyla alınmıştır. 24-48 saat içerisinde işleme alınacaktır.'
        ];
    }
    
    /**
     * Kullanıcılar arası bakiye transferi
     * 
     * @param int $user_id Gönderen kullanıcı ID
     * @param string $recipient_email Alıcı e-posta
     * @param float $amount Transfer tutarı
     * @param string $description Açıklama (opsiyonel)
     * @return array İşlem sonucu
     */
    public function transferBalance($user_id, $recipient_email, $amount, $description = '') {
        // Gönderen kullanıcı kontrolü
        $sender = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$sender) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Tutarı pozitif değere çevir - güvenlik için
        $amount = abs(floatval($amount));
        
        // Alıcı kullanıcı kontrolü
        $recipient = $this->db->get_where('user', ['email' => $recipient_email])->row();
        if (!$recipient) {
            return ['status' => false, 'message' => 'Alıcı e-posta adresi sistemde kayıtlı değil.'];
        }
        
        // Kendisine transfer kontrolü
        if ($sender->id == $recipient->id) {
            return ['status' => false, 'message' => 'Kendinize transfer yapamazsınız.'];
        }
        
        // Minimum transfer kontrolü
        if ($amount < 1) {
            return ['status' => false, 'message' => 'Minimum transfer tutarı 1 TL\'dir.'];
        }
        
        // Bakiye kontrolü
        if ($sender->balance < $amount) {
            return ['status' => false, 'message' => 'Yetersiz bakiye.'];
        }
        
        // İşlem başlat (transaction)
        $this->db->trans_start();
        
        // Gönderen bakiyesinden düş - güvenli şekilde
        $updated_sender_balance = $sender->balance - $amount;
        $this->db->where('id', $sender->id);
        $this->db->where('balance >=', $amount); // Ek güvenlik kontrolü
        $this->db->update('user', ['balance' => $updated_sender_balance]);
        
        // Kontrol et - eğer güncellenen satır yoksa, bakiye yetersiz demektir
        if ($this->db->affected_rows() == 0) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'İşlem gerçekleştirilemedi. Bakiye yetersiz olabilir.'];
        }
        
        // Alıcının bakiyesine ekle - güvenli şekilde
        $updated_recipient_balance = $recipient->balance + $amount;
        $this->db->where('id', $recipient->id);
        $this->db->update('user', ['balance' => $updated_recipient_balance]);
        
        // Transfer kaydını oluştur (gönderen için)
        $sender_transaction = [
            'user_id' => $sender->id,
            'transaction_type' => 'transfer_out',
            'amount' => -$amount,
            'description' => $description ? $description : $recipient->email . ' adresine transfer',
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $sender->balance, // İşlem öncesi bakiye
            'balance_after_transaction' => $updated_sender_balance
        ];
        
        $this->db->insert('wallet_transactions', $sender_transaction);
        
        // Transfer kaydını oluştur (alıcı için)
        $recipient_transaction = [
            'user_id' => $recipient->id,
            'transaction_type' => 'transfer_in',
            'amount' => $amount,
            'description' => $description ? $description : $sender->email . ' adresinden transfer',
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $recipient->balance, // İşlem öncesi bakiye
            'balance_after_transaction' => $updated_recipient_balance
        ];
        
        $this->db->insert('wallet_transactions', $recipient_transaction);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return ['status' => false, 'message' => 'Transfer işlemi sırasında bir hata oluştu.'];
        }
        
        return [
            'status' => true, 
            'message' => $amount . ' TL tutarındaki transfer başarıyla gerçekleştirildi.'
        ];
    }
    
    /**
     * Bakiyeler arası transfer
     * 
     * @param int $user_id Kullanıcı ID
     * @param float $amount Transfer tutarı
     * @param string $transfer_direction Transfer yönü (normal_to_withdrawable veya withdrawable_to_normal)
     * @return array İşlem sonucu
     */
    public function transferBetweenBalances($user_id, $amount, $transfer_direction) {
        // Kullanıcı kontrolü
        $user = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$user) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Tutarı tam sayıya çevir ve pozitif olduğundan emin ol
        $amount = abs(floatval($amount));
        
        // Minimum transfer kontrolü
        if ($amount < 10) {
            return ['status' => false, 'message' => 'Minimum transfer tutarı 10 TL\'dir.'];
        }
        
        // Transfer yönü kontrolü ve işlem
        if ($transfer_direction == 'normal_to_withdrawable') {
            // Kullanılabilir bakiyeden çekilebilir bakiyeye transfer
            
            // Bakiye kontrolü
            if ($user->balance < $amount) {
                return ['status' => false, 'message' => 'Yetersiz kullanılabilir bakiye.'];
            }
            
            // Komisyon oranını settings tablosundan al
            $commission_rate = $this->db->where('key', 'usable2withdraw_commission')->get('settings')->row();
            $commission_percentage = $commission_rate ? floatval($commission_rate->value) : 5;
            
            // Komisyon hesaplama
            $commission = $amount * ($commission_percentage / 100);
            $net_amount = $amount - $commission;
            
            // İşlem başlat
            $this->db->trans_start();
            
            // Kullanılabilir bakiyeden düş - Güvenli şekilde
            $updated_balance = $user->balance - $amount;
            $this->db->where('id', $user_id);
            $this->db->where('balance >=', $amount); // Ek güvenlik kontrolü
            $this->db->update('user', ['balance' => $updated_balance]);
            
            // Kontrol et - eğer güncellenen satır yoksa, bakiye yetersiz demektir
            if ($this->db->affected_rows() == 0) {
                $this->db->trans_rollback();
                return ['status' => false, 'message' => 'İşlem gerçekleştirilemedi. Bakiye yetersiz olabilir.'];
            }
            
            // Çekilebilir bakiyeye ekle - Güvenli şekilde
            $updated_balance2 = $user->balance2 + $net_amount;
            $this->db->where('id', $user_id);
            $this->db->update('user', ['balance2' => $updated_balance2]);
            
            // Kullanılabilir bakiye için işlem kaydını oluştur
            $normal_transaction = [
                'user_id' => $user_id,
                'transaction_type' => 'transfer_out',
                'amount' => -$amount,
                'description' => 'Kullanılabilir bakiyeden çekilebilir bakiyeye transfer (%'.$commission_percentage.' komisyon)',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi kullanılabilir bakiye
                'balance_after_transaction' => $updated_balance, // Güncellenmiş kullanılabilir bakiye
                'balance_type' => 'spendable' // Kullanılabilir bakiye
            ];
            
            $this->db->insert('wallet_transactions', $normal_transaction);
            
            // Çekilebilir bakiye için işlem kaydını oluştur
            $withdraw_transaction = [
                'user_id' => $user_id,
                'transaction_type' => 'transfer_in',
                'amount' => $net_amount,
                'description' => 'Kullanılabilir bakiyeden çekilebilir bakiyeye transfer (Komisyon düşülmüş net tutar)',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance2, // İşlem öncesi çekilebilir bakiye
                'balance_after_transaction' => $updated_balance2, // Güncellenmiş çekilebilir bakiye
                'balance_type' => 'withdrawable' // Çekilebilir bakiye
            ];
            
            $this->db->insert('wallet_transactions', $withdraw_transaction);
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                return ['status' => false, 'message' => 'Transfer işlemi sırasında bir hata oluştu.'];
            }
            
            return [
                'status' => true, 
                'message' => $amount . ' TL tutarındaki transferiniz başarıyla gerçekleştirildi. %'.$commission_percentage.' komisyon düşülerek ' . number_format($net_amount, 2) . ' TL çekilebilir bakiyenize aktarıldı.'
            ];
            
        } else if ($transfer_direction == 'withdrawable_to_normal') {
            // Çekilebilir bakiyeden kullanılabilir bakiyeye transfer
            
            // Bakiye kontrolü
            if ($user->balance2 < $amount) {
                return ['status' => false, 'message' => 'Yetersiz çekilebilir bakiye.'];
            }
            
            // İşlem başlat
            $this->db->trans_start();
            
            // Çekilebilir bakiyeden düş - Güvenli şekilde
            $updated_balance2 = $user->balance2 - $amount;
            $this->db->where('id', $user_id);
            $this->db->where('balance2 >=', $amount); // Ek güvenlik kontrolü
            $this->db->update('user', ['balance2' => $updated_balance2]);
            
            // Kontrol et - eğer güncellenen satır yoksa, bakiye yetersiz demektir
            if ($this->db->affected_rows() == 0) {
                $this->db->trans_rollback();
                return ['status' => false, 'message' => 'İşlem gerçekleştirilemedi. Bakiye yetersiz olabilir.'];
            }
            
            // Kullanılabilir bakiyeye ekle - Güvenli şekilde
            $updated_balance = $user->balance + $amount;
            $this->db->where('id', $user_id);
            $this->db->update('user', ['balance' => $updated_balance]);
            
            // Çekilebilir bakiye için işlem kaydını oluştur
            $withdraw_transaction = [
                'user_id' => $user_id,
                'transaction_type' => 'transfer_out',
                'amount' => -$amount,
                'description' => 'Çekilebilir bakiyeden kullanılabilir bakiyeye transfer',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance2, // İşlem öncesi çekilebilir bakiye
                'balance_after_transaction' => $updated_balance2, // Güncellenmiş çekilebilir bakiye
                'balance_type' => 'withdrawable' // Çekilebilir bakiye
            ];
            
            $this->db->insert('wallet_transactions', $withdraw_transaction);
            
            // Kullanılabilir bakiye için işlem kaydını oluştur
            $normal_transaction = [
                'user_id' => $user_id,
                'transaction_type' => 'transfer_in',
                'amount' => $amount,
                'description' => 'Çekilebilir bakiyeden kullanılabilir bakiyeye transfer',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi kullanılabilir bakiye
                'balance_after_transaction' => $updated_balance, // Güncellenmiş kullanılabilir bakiye
                'balance_type' => 'spendable' // Kullanılabilir bakiye
            ];
            
            $this->db->insert('wallet_transactions', $normal_transaction);
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                return ['status' => false, 'message' => 'Transfer işlemi sırasında bir hata oluştu.'];
            }
            
            return [
                'status' => true, 
                'message' => $amount . ' TL tutarındaki transfer başarıyla gerçekleştirildi ve kullanılabilir bakiyenize aktarıldı.'
            ];
        } else {
            return ['status' => false, 'message' => 'Geçersiz transfer yönü.'];
        }
    }
    
    /**
     * Kredi teklifi kabul etme işlemi
     * 
     * @param int $user_id Kullanıcı ID
     * @param int $offer_id Teklif ID
     * @param float $amount Kabul edilen tutar
     * @return array İşlem sonucu
     */
    public function acceptCreditOffer($user_id, $offer_id, $amount) {
        // Kullanıcı kontrolü
        $user = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$user) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Teklif kontrolü
        $offer = $this->db->get_where('credit_offers', ['id' => $offer_id, 'user_id' => $user_id, 'status' => 1])->row();
        if (!$offer) {
            return ['status' => false, 'message' => 'Geçerli bir kredi teklifi bulunamadı.'];
        }
        
        // Teklif tarihi geçmiş mi kontrol
        if (strtotime($offer->offer_valid_until) < time()) {
            // Teklifi süresi doldu olarak güncelle
            $this->db->where('id', $offer_id);
            $this->db->update('credit_offers', ['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
            
            // Log kaydı
            $this->insertCreditLog($offer_id, NULL, $user_id, 'offer_expire', NULL, 'Cari hesap teklifi süresi doldu');
            
            return ['status' => false, 'message' => 'Bu cari hesap teklifinin süresi dolmuştur.'];
        }
        
        // Kabul edilen tutar kontrolü
        if ($amount <= 0 || $amount > $offer->amount) {
            return ['status' => false, 'message' => 'Geçersiz tutar.'];
        }
        
        // İşlem ücreti hesaplama
        $fee_amount = $amount * ($offer->fee_percentage / 100);
        $net_amount = $amount - $fee_amount;
        
        // İşlem başlat
        $this->db->trans_start();
        
        // Kredi oluşturma
        $credit_data = [
            'user_id' => $user_id,
            'offer_id' => $offer_id,
            'amount' => $amount,
            'fee_amount' => $fee_amount,
            'net_amount' => $net_amount,
            'remaining_amount' => $amount,
            'term_days' => $offer->term_days,
            'due_date' => date('Y-m-d H:i:s', strtotime('+' . $offer->term_days . ' days')),
            'status' => 1, // Aktif
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('user_credits', $credit_data);
        $credit_id = $this->db->insert_id();
        
        // Teklif durumunu güncelle
        $new_status = ($amount == $offer->amount) ? 3 : 2; // 3: Tamamen Kabul Edildi, 2: Kısmen Kabul Edildi
        $this->db->where('id', $offer_id);
        $this->db->update('credit_offers', [
            'status' => $new_status, 
            'accepted_amount' => $amount,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Kullanıcının bakiyesine ekle
        $this->db->where('id', $user_id);
        $this->db->set('balance', 'balance+'.$net_amount, FALSE);
        $this->db->update('user');
        
        // İşlem kaydını oluştur (kullanıcı bakiyesi için)
        $transaction = [
            'user_id' => $user_id,
            'transaction_type' => 'credit',
            'amount' => $net_amount,
            'description' => 'Cari Hesap Limit Kullanımı (İşlem Ücreti: ' . number_format($fee_amount, 2) . ' TL)',
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $user->balance, // İşlem öncesi bakiye
            'balance_after_transaction' => $user->balance + $net_amount // Güncellenmiş bakiye
        ];
        
        $this->db->insert('wallet_transactions', $transaction);
        
        // Log kaydı
        $this->insertCreditLog($offer_id, $credit_id, $user_id, 'offer_accept', $amount, 'Cari Hesap teklifi kabul edildi');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return ['status' => false, 'message' => 'Cari Hesap teklifi kabul işlemi sırasında bir hata oluştu.'];
        }
        
        return [
            'status' => true, 
            'message' => $amount . ' TL tutarındaki cari hesap teklifiniz onaylandı ve ' . number_format($net_amount, 2) . ' TL bakiyenize eklendi. Son ödeme tarihi: ' . date('d.m.Y', strtotime('+' . $offer->term_days . ' days'))
        ];
    }
    
    /**
     * Kredi teklifi reddetme işlemi
     * 
     * @param int $user_id Kullanıcı ID
     * @param int $offer_id Teklif ID
     * @return array İşlem sonucu
     */
    public function rejectCreditOffer($user_id, $offer_id) {
        // Kullanıcı kontrolü
        $user = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$user) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Teklif kontrolü
        $offer = $this->db->get_where('credit_offers', ['id' => $offer_id, 'user_id' => $user_id, 'status' => 1])->row();
        if (!$offer) {
            return ['status' => false, 'message' => 'Geçerli bir cari hesap teklifi bulunamadı.'];
        }
        
        // Teklifi reddet
        $this->db->where('id', $offer_id);
        $this->db->update('credit_offers', ['status' => 4, 'updated_at' => date('Y-m-d H:i:s')]);
        
        // Log kaydı
        $this->insertCreditLog($offer_id, NULL, $user_id, 'offer_reject', NULL, 'Cari hesap teklifi reddedildi');
        
        return [
            'status' => true, 
            'message' => 'Cari hesap teklifi başarıyla reddedildi.'
        ];
    }
    
    /**
     * Kredi ödeme işlemi
     * 
     * @param int $user_id Kullanıcı ID
     * @param int $credit_id Kredi ID
     * @param float $amount Ödeme tutarı
     * @return array İşlem sonucu
     */
    public function payCreditDebt($user_id, $credit_id, $amount) {
        // Kullanıcı kontrolü
        $user = $this->db->get_where('user', ['id' => $user_id])->row();
        if (!$user) {
            return ['status' => false, 'message' => 'Kullanıcı bulunamadı.'];
        }
        
        // Tutarı pozitif değere çevir - güvenlik için
        $amount = abs(floatval($amount));
        
        // Kredi kontrolü
        $credit = $this->db->get_where('user_credits', ['id' => $credit_id, 'user_id' => $user_id])->row();
        if (!$credit) {
            return ['status' => false, 'message' => 'Geçerli bir cari hesap borcu bulunamadı.'];
        }
        
        // Kredi durumu kontrolü
        if ($credit->status != 1 && $credit->status != 3 && $credit->status != 4) {
            return ['status' => false, 'message' => 'Bu cari hesap borcu için ödeme yapamazsınız.'];
        }
        
        // Kalan borç kontrolü
        if ($credit->remaining_amount <= 0) {
            return ['status' => false, 'message' => 'Bu cari hesap borcu zaten tamamen ödenmiş.'];
        }
        
        // Ödeme tutarı kontrolü
        if ($amount <= 0 || $amount > $credit->remaining_amount) {
            return ['status' => false, 'message' => 'Geçersiz ödeme tutarı.'];
        }
        
        // Bakiye kontrolü
        if ($user->balance < $amount) {
            return ['status' => false, 'message' => 'Yetersiz bakiye.'];
        }
        
        // İşlem başlat
        $this->db->trans_start();
        
        // Kullanıcının bakiyesinden düş - güvenli şekilde
        $updated_balance = $user->balance - $amount;
        $this->db->where('id', $user_id);
        $this->db->where('balance >=', $amount); // Ek güvenlik kontrolü
        $this->db->update('user', ['balance' => $updated_balance]);
        
        // Kontrol et - eğer güncellenen satır yoksa, bakiye yetersiz demektir
        if ($this->db->affected_rows() == 0) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'İşlem gerçekleştirilemedi. Bakiye yetersiz olabilir.'];
        }
        
        // Kalan borç hesaplama
        $new_remaining = $credit->remaining_amount - $amount;
        $is_final_payment = ($new_remaining <= 0);
        
        // Ödeme kaydı oluştur
        $payment_data = [
            'credit_id' => $credit_id,
            'user_id' => $user_id,
            'amount' => $amount,
            'payment_method' => 'balance',
            'status' => 1, // Onaylı
            'payment_type' => $is_final_payment ? 'full' : 'partial',
            'is_final_payment' => $is_final_payment ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('credit_payments', $payment_data);
        
        // Kredi durumunu güncelle
        $new_status = $is_final_payment ? 2 : 3; // 2: Ödendi, 3: Kısmi Ödendi
        $this->db->where('id', $credit_id);
        $this->db->update('user_credits', [
            'remaining_amount' => $new_remaining,
            'status' => $new_status,
            'payment_count' => $credit->payment_count + 1,
            'last_payment_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // İşlem kaydını oluştur
        $transaction = [
            'user_id' => $user_id,
            'transaction_type' => 'credit_payment',
            'amount' => -$amount,
            'description' => 'Cari hesap borcu ödemesi' . ($is_final_payment ? ' (Tam ödeme)' : ''),
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $user->balance, // İşlem öncesi bakiye
            'balance_after_transaction' => $updated_balance // Güncellenmiş bakiye
        ];
        
        $this->db->insert('wallet_transactions', $transaction);
        
        // Log kaydı
        $this->insertCreditLog(NULL, $credit_id, $user_id, 'payment', $amount, 'Cari hesap ödemesi yapıldı' . ($is_final_payment ? ' (Tam ödeme)' : ''));
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return ['status' => false, 'message' => 'Ödeme işlemi sırasında bir hata oluştu.'];
        }
        
        if ($is_final_payment) {
            return [
                'status' => true, 
                'message' => 'Cari hesap borcunuz tamamen ödendi. Teşekkür ederiz.'
            ];
        } else {
            return [
                'status' => true, 
                'message' => $amount . ' TL ödeme yapıldı. Kalan borç: ' . number_format($new_remaining, 2) . ' TL'
            ];
        }
    }
    
    /**
     * Kredi log kaydı oluşturma
     * 
     * @param int $offer_id Teklif ID (varsa)
     * @param int $credit_id Kredi ID (varsa)
     * @param int $user_id Kullanıcı ID
     * @param string $action İşlem
     * @param float $amount Tutar (varsa)
     * @param string $description Açıklama
     */
    private function insertCreditLog($offer_id, $credit_id, $user_id, $action, $amount = NULL, $description = '') {
        $log_data = [
            'offer_id' => $offer_id,
            'credit_id' => $credit_id,
            'user_id' => $user_id,
            'action' => $action,
            'amount' => $amount,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        ];
        
        $this->db->insert('credit_logs', $log_data);
    }
    
    /**
     * Kullanıcının aktif kredi tekliflerini getirme
     * 
     * @param int $user_id Kullanıcı ID
     * @return array Aktif teklifler
     */
    public function getActiveOffers($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1); // Aktif
        $this->db->where('offer_valid_until >', date('Y-m-d H:i:s'));
        $query = $this->db->get('credit_offers');
        
        return $query->result();
    }
    
    /**
     * Kullanıcının aktif kredilerini getirme
     * 
     * @param int $user_id Kullanıcı ID
     * @return array Aktif krediler
     */
    public function getActiveCredits($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where_in('status', [1, 3, 4]); // Aktif, Kısmi Ödendi, Gecikti
        $query = $this->db->get('user_credits');
        
        return $query->result();
    }
    
    /**
     * Kullanıcının işlem geçmişini getirme
     * 
     * @param int $user_id Kullanıcı ID
     * @param string $transaction_type İşlem tipi (opsiyonel)
     * @param string $start_date Başlangıç tarihi (opsiyonel)
     * @param string $end_date Bitiş tarihi (opsiyonel)
     * @param int $status Durum (opsiyonel)
     * @return array İşlem geçmişi
     */
    public function getTransactionHistory($user_id, $transaction_type = NULL, $start_date = NULL, $end_date = NULL, $status = NULL) {
        $this->db->where('user_id', $user_id);
        
        if ($transaction_type) {
            $this->db->where('transaction_type', $transaction_type);
        }
        
        if ($start_date) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
        }
        
        if ($end_date) {
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        }
        
        if ($status !== NULL) {
            $this->db->where('status', $status);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('wallet_transactions');
        
        return $query->result();
    }
}
