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

	$invoices = $ci->db->where('isActive', 0)->where('payed', 2)->where('seller_id !=', 0)->get('invoice')->result(); 
	$total = count($invoices);
	foreach ($invoices as $invoice) {
		if (strtotime($invoice->last_refund) <= time()) {
			$seller = $ci->db->where('id', $invoice->seller_id)->get('user')->row();
			if ($seller->isAdmin!=1) {
				$percent = ($invoice->price / 100) * $seller->shop_com;
				$ci->db->trans_begin();
				$ci->db->where('id', $invoice->id)->update('invoice', ['payed' => 1]);
				$ci->db->where('id', $seller->id)->update("user", [
					"balance2" => $seller->balance2+($invoice->price - $percent)
				]);
				if ($ci->db->trans_status() === FALSE) {
			        $ci->db->trans_rollback();
				} else {
			        $ci->db->trans_commit();
			        $success++;
				}
			}
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