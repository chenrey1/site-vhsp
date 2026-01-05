<?php

function getProductStar($product_id, $gived_star='<i class="fa fa-star"></i>', $non_gived_star='<i class="fa fa-star gray"></i>') {
  	$ci = &get_instance();
	$star_res = $ci->db->where(["product_id" => $product_id, "isActive" => 1])->select_avg("star")->get('product_comments')->row();
	$stars = $star_res->star ?? 5;
	$ceiled_star = ceil($stars);

	$res = str_repeat($gived_star, $ceiled_star);
	$res .= str_repeat($non_gived_star, 5-$ceiled_star);
	return $res;
}

function getSellerStar($seller_id, $gived_star='<i class="fa fa-star"></i>', $non_gived_star='<i class="fa fa-star gray"></i>') {
  	$ci = &get_instance();

  	$products = $ci->db->where('seller_id', $seller_id)->get('product')->result();
  	$countStar = 0;

  	foreach($products as $product)
  	{
  		$productStar = $ci->db->where(["product_id" => $product->id, "isActive" => 1])->select_avg("star")->get('product_comments')->row();
  		$stars = $productStar->star ?? 5;
  		$countStar = ($countStar + $stars ?? 5) / 2;
  	}
	$ceiled_star = ceil($countStar);

	$res = str_repeat($gived_star, $ceiled_star);
	$res .= str_repeat($non_gived_star, 5-$ceiled_star);
	return $res;
}

function getTextWithType($text, $type) {
	switch($type) {
		case 0:
			$text = '<p class="text-danger">'.$text[0].'</p>';
			break;
		case 1:
			$text = '<p class="text-success">'.$text[1].'</p>';
			break;
		case 2:
			$text = '<p class="text-warning">'.$text[2].'</p>';
			break;
		case 3:
			$text = '<p class="text-info">'.$text[3].'</p>';
			break;
		default:
			$text = '<p class="text-success">'.$text[0].'</p>';
	}
	return $text;
}

function getTicketOtherUser($ticket, $uid) {
	$ci = &get_instance();
	switch($uid) {
		case $ticket->user_id:
			$user = $ci->db->where('id', $ticket->seller_id)->get('user')->row();
			return [$user, $user->shop_name];
		case $ticket->seller_id:
			$user = $ci->db->where('id', $ticket->user_id)->get('user')->row();
			return [$user, $user->name." ".$user->surname];
	}
	return [$user, $user->name." ".$user->surname];
}

function paySellersPayments() {
	$ci = &get_instance();

	$success = 0;
	$total = 0;

	// Ödenmesi gereken faturaları belirle (son 1 haftalık)
	$payable_invoices = [];
	$one_week_ago = date('Y-m-d H:i:s', strtotime('-1 week'));
	
	$invoices = $ci->db->where('isActive', 0)
					  ->where('payed', 2)
					  ->where('seller_id !=', 0)
					  ->where('date >=', $one_week_ago) // Son 1 haftalık faturaları al
					  ->get('invoice')
					  ->result(); 
	
	foreach ($invoices as $invoice) {
		if (strtotime($invoice->last_refund) <= time()) {
			$seller = $ci->db->where('id', $invoice->seller_id)->get('user')->row();
			if ($seller && $seller->isAdmin != 1) {
				$payable_invoices[] = $invoice;
			}
		}
	}
	
	// Marketplace işlemlerini gerçekleştir
	if (!empty($payable_invoices)) {
		$marketplace_results = processMarketplaceTransactions($payable_invoices);
		$success = $marketplace_results[0];
		$total = $marketplace_results[1];
	}

	return [$success, $total];
}

/**
 * Bekleyen marketplace işlemlerini ve ödenecek faturaları işler
 * 
 * @param array $payable_invoices Ödenmesi gereken faturalar
 * @return array İşlem sonuçları [başarılı işlem sayısı, toplam işlem sayısı]
 */
function processMarketplaceTransactions($payable_invoices = []) {
	$ci = &get_instance();
	$success = 0;
	$total = 0;
	
	// Toplam işlem sayısını belirle
	$total = count($payable_invoices);
	
	// payable_invoices'dan gelen faturaları işle
	foreach ($payable_invoices as $invoice) {
		$seller = $ci->db->where('id', $invoice->seller_id)->get('user')->row();
		
		if (!$seller || $seller->isAdmin == 1) {
			continue;
		}
		
		$ci->db->trans_begin();
		
		try {
			// Faturayı ödenmiş olarak işaretle
			$ci->db->where('id', $invoice->id)->update('invoice', ['payed' => 1]);
			
			// İlgili wallet_transaction kaydını bul ve güncelle
			$transaction = $ci->db->where('related_id', $invoice->id)
								 ->where('transaction_type', 'marketplace')
								 ->where('status', 0)
								 ->get('wallet_transactions')
								 ->row();
			
			$amount_added = 0; // Eklenen tutar
			$new_balance = 0; // Güncellenmiş bakiye değeri
								 
			if ($transaction) {
				// Satıcının yeni çekilebilir bakiyesini hesapla
				$new_balance = $seller->balance2 + $transaction->amount;
				
				// Wallet transaction durumunu güncelle
				$ci->db->where('id', $transaction->id)->update('wallet_transactions', [
					'status' => 1, // Onaylandı
					'updated_at' => date('Y-m-d H:i:s'),
					'balance_after_transaction' => $new_balance // İşlemden sonraki güncel bakiye
				]);
				
				// Satıcının çekilebilir bakiyesini güncelle
				$ci->db->where('id', $seller->id)->update('user', [
					'balance2' => $new_balance
				]);
				
				$amount_added = $transaction->amount;
			} else {
				// Wallet transaction yoksa, commission hesapla
				$percent = ($invoice->price / 100) * $seller->shop_com;
				$net_amount = $invoice->price - $percent;
				
				// Satıcının yeni çekilebilir bakiyesini hesapla
				$new_balance = $seller->balance2 + $net_amount;
				
				// Satıcının çekilebilir bakiyesini güncelle
				$ci->db->where('id', $seller->id)->update('user', [
					'balance2' => $new_balance
				]);
				
				$amount_added = $net_amount;
			}
			
			// Transaction'ı kontrol et
			if ($ci->db->trans_status() === FALSE) {
				$ci->db->trans_rollback();
				addlog('shop_helper::processMarketplaceTransactions', 'İşlem başarısız. Invoice ID: ' . $invoice->id);
			} else {
				$ci->db->trans_commit();
				$success++;
				
				// Log kaydı ekle
				addlog('shop_helper::processMarketplaceTransactions', 'Satıcı ödemesi başarıyla işlendi. Invoice ID: ' . 
					  $invoice->id . ', Satıcı ID: ' . $seller->id);
				
				// Satıcı için bildirim oluştur
				$notification_data = [
					'user_id' => $seller->id,
					'title' => 'Pazaryeri Bildirimi',
					'contents' => 'Pazaryeri satışlarınızdan bakiyenize ' . number_format($amount_added, 2) . ' ₺ eklendi.',
					'link' => base_url('client/balance'),
					'created_at' => date('Y-m-d H:i:s'),
					'seen_at' => 1,
					'isActive' => 'Active',
					'sender' => 'system'
				];
				
				$ci->db->insert('notifications', $notification_data);
			}
		} catch (Exception $e) {
			$ci->db->trans_rollback();
			addlog('shop_helper::processMarketplaceTransactions', 'Hata: ' . $e->getMessage() . ', Invoice ID: ' . $invoice->id);
		}
	}
	
	return [$success, $total];
}

function getUserTicketWithType($user, $type, $type_id) {
	$ci = &get_instance();
	$tickets = $ci->db->where('user_id', $user)->order_by('id', 'DESC')->get('ticket')->result();

	foreach ($tickets as $ticket) {
		$extra = json_decode($ticket->extra, true);
		if ($extra && array_key_exists("type", $extra) && array_key_exists($extra["type"]."_id", $extra)) {
			if ($extra["type"] == $type && $extra[$extra["type"]."_id"] == $type_id) {
				$ticket->extra = json_decode($ticket->extra);
				return $ticket;
			}
		}
	} 
	return false;
}