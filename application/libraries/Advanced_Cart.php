<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Advanced_Cart extends CI_Cart {

	private $has_discount = FALSE;
	private $discount_type, $discount_amount;
	private $extra_data = [];

	public function __construct($params = array()) {
		$this->_cart_contents = array('cart_total' => 0, 'total_items' => 0, 'cart_first_total' => 0, 'cart_tax' => 0);
		parent::__construct($params);
		$cart_advanced = $this->CI->session->userdata('cart_advanced');
		if ($cart_advanced !== NULL)
		{
			$this->discount_type = $cart_advanced["discount_type"] ?? null;
			$this->discount_amount = $cart_advanced["discount_amount"] ?? 0;
			$this->has_discount = $cart_advanced["has_discount"] ?? null;
			$this->extra_data = $cart_advanced["extra_data"] ?? [];
		}
	}

	public function set_product_name_safe($boolean) {
		$this->product_name_safe = $boolean;
	}

	public function cart_extra($key, $value=null) {
		if (is_array($key)) {
			$this->extra_data = array_merge($this->extra_data, $key);
		} else {
			$this->extra_data[$key] = $value;
		}
		$this->_save_cart();
	}

	public function remove_cart_extra($key) {
		unset($this->extra_data[$key]);
		$this->_save_cart();
	}

	public function get_cart_extra($key=null) {
		if ($key == null) {
			return $this->extra_data;
		}  else {
			return $this->extra_data[$key];
		}
	}

	public function has_cart_extra($key) {
		return (array_key_exists($key, $this->extra_data) && $this->extra_data[$key] != null);
	}

	public function discount($type, $amount, $min=0, $max=-1) {
		$ct = $this->calc_cart_total();
		if ($ct["cart_total"]<$min) return;
		if ($max != -1 && $ct["cart_total"]>=$max) return;
		//if (!($ct["cart_total"]>$min && ($max != -1 && $ct["cart_total"]<$max))) return;
		if ($this->has_discount) $this->reset_cart_discount();
		$this->discount_type = $type;
		$this->discount_amount = $amount;
		$this->has_discount = true;
		if ($type == "percentage") {
			$this->discount_percentage($amount, true);
		} else {
			$this->discount_amount($amount, true);
		}
		$this->_save_cart();
	}

	public function discount_percentage($amount, $without_reset=false) {
		if ($this->has_discount && !$without_reset) $this->reset_cart_discount();

		$cart = $this->contents();
		foreach ($cart as $key => $value) {
			$discounted_price = $value["price"]-($value["price"]*$amount/100);
			if ($discounted_price<0) $discounted_price = 0;
			$this->_update([
				'rowid' => $value["rowid"],
				'first_price' => $value["price"],
				'price' => $discounted_price,
				'tax' => $this->calc_tax($value, $discounted_price, true)
			]);
		}
	}

	private function calc_tax($item, $discounted_price, $for_discount=false) {
		if (!isset($item['tax_type'], $item['tax_amount'])) return 0;
		if ($item['tax_type'] == "rate") {
			$tax = (($discounted_price*$item['tax_amount'])/100);
		} else {
			$tax = $item['tax_amount'];
		}

		if ($for_discount) {
			$discount_type = $this->get_discount_type();
			$discount_amount = $this->get_discount_amount();
			if ($discount_type == "percentage") {
				$tax = $tax-($tax*$discount_amount/100);
			} else {
				$tax = $tax-$discount_amount;
			}
		}
		if ($tax < 0) $tax = 0;
		return $tax;
	}

	public function discount_amount($amount, $without_reset=false) {
		if ($this->has_discount && !$without_reset) $this->reset_cart_discount();

		$cart = $this->contents();
		$total_items = $this->total_items();
		$discount_per_item = $amount/$total_items;
		foreach ($cart as $key => $value) {
			$discounted_price = $value["price"]-$discount_per_item;
			if ($discounted_price<0) $discounted_price = 0;
			$this->_update([
				'rowid' => $value["rowid"],
				'first_price' => $value["price"],
				'price' => $discounted_price,
				'tax' => $this->calc_tax($value, $discounted_price, true)
			]);
		}
	}

	public function has_discount() {
		return $this->has_discount;
	}

	public function get_discount_type() {
		return $this->discount_type;
	}

	public function get_discount_amount() {
		return $this->discount_amount;
	}

	public function tax()
	{
		return $this->_cart_contents['cart_tax'] ?? 0;
	}

	public function reset_cart_discount() {
		$this->discount_type = null;
		$this->discount_amount = null;
		$this->has_discount = false;
		$cart = $this->contents();
		foreach ($cart as $key => $value) {
			$price = $value["first_price"] ?? $value["price"];
			$this->_update([
				'rowid' => $value["rowid"],
				'price' => $value["first_price"] ?? $value["price"],
				'tax' => $this->calc_tax($value, $price)
			]);
		}
		$this->_save_cart();
	}

	protected function _save_cart() {
		// Let's add up the individual prices and set the cart sub-total
		$this->_cart_contents['total_items'] = $this->_cart_contents['cart_total'] = $this->_cart_contents['cart_first_total'] = $this->_cart_contents['cart_tax'] = 0;
		foreach ($this->_cart_contents as $key => $val)
		{
			// We make sure the array contains the proper indexes
			if ( ! is_array($val) OR ! isset($val['price'], $val['qty']))
			{
				continue;
			}
			if (!isset($val["first_price"])) {
				$this->_cart_contents[$key]["first_price"] = $val["price"];
			}
			if (!isset($val["first_subtotal"])) {
				$this->_cart_contents[$key]["first_subtotal"] = ($this->_cart_contents[$key]['first_price'] * $this->_cart_contents[$key]['qty']);
			}

			$this->_cart_contents['cart_total'] += ($val['price'] * $val['qty']);
			$this->_cart_contents['cart_first_total'] += (($val['first_price'] ?? $val['price']) * $val['qty']);
			$this->_cart_contents['total_items'] += $val['qty'];
			$this->_cart_contents[$key]['subtotal'] = ($this->_cart_contents[$key]['price'] * $this->_cart_contents[$key]['qty']);

			$this->_cart_contents[$key]['tax'] = $this->calc_tax($val, $val['price']);

			$this->_cart_contents['cart_tax'] += ($this->_cart_contents[$key]['tax'] * $val['qty']);
		}

		// Is our cart empty? If so we delete it from the session
		if (count($this->_cart_contents) <= 3)
		{
			$this->CI->session->unset_userdata('cart_contents');

			// Nothing more to do... coffee time!
			return FALSE;
		}

		// If we made it this far it means that our cart has data.
		// Let's pass it to the Session class so it can be stored
		$this->CI->session->set_userdata(array('cart_contents' => $this->_cart_contents));
		$this->CI->session->set_userdata(array('cart_advanced' => [
			"discount_type" => $this->discount_type,
			"discount_amount" => $this->discount_amount,
			"has_discount" => $this->has_discount,
			"extra_data" => $this->extra_data
		]));

		// Woot!
		return TRUE;
	}

	private function calc_cart_total() {
		$arr = [
			"cart_total" => 0,
			"cart_first_total" => 0,
			"total_items" => 0,
			"cart_tax" => 0,
		];
		foreach ($this->_cart_contents as $key => $val)
		{
			// We make sure the array contains the proper indexes
			if ( ! is_array($val) OR ! isset($val['price'], $val['qty']))
			{
				continue;
			}

			$arr['cart_total'] += ($val['price'] * $val['qty']);
			$arr['cart_first_total'] += (($val['first_price'] ?? $val['price']) * $val['qty']);
			$arr['total_items'] += $val['qty'];

			$tax = $this->calc_tax($val, $val['price']);

			$arr['cart_tax'] += ($this->_cart_contents[$key]['tax'] * $val['qty']);
		}
		return $arr;
	}

	public function destroy() {
		$this->_cart_contents = array('cart_total' => 0, 'total_items' => 0, 'cart_first_total' => 0, 'cart_tax' => 0);
		$this->discount_type = null;
		$this->discount_amount = null;
		$this->has_discount = false;
		$this->extra_data = [];
		$this->CI->session->unset_userdata('cart_contents');
		$this->CI->session->unset_userdata('cart_advanced');
	}

	/**
	 * Cart Contents
	 *
	 * Returns the entire cart array
	 *
	 * @param	bool
	 * @return	array
	 */
	public function contents($newest_first = FALSE)
	{
		// do we want the newest first?
		$cart = ($newest_first) ? array_reverse($this->_cart_contents) : $this->_cart_contents;

		// Remove these so they don't create a problem when showing the cart table
		unset($cart['total_items']);
		unset($cart['cart_total']);
		unset($cart['cart_first_total']);
		unset($cart['cart_tax']);

		return $cart;
	}

	public function first_total()
	{
		return $this->_cart_contents['cart_first_total'];
	}
}
