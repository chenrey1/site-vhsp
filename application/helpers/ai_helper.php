<?php 

	function getAIProducts($home_products)
	{
		$th = get_instance();
		$properties = $th->db->where('id', 1)->get('properties')->row();

		if ($properties->autoShow == 0 || empty($th->session->userdata('info'))) { // Yapay zeka kapalıysa ya da kullanıcı giriş yapmadıysa

			$productList = [];
			foreach ($home_products as $hp) {

			if ($hp->type == "lastProduct") {
                $type = "Son Çıkanlar";
                $products = $th->db->where('isActive',1)->where_in('seller_id', ["1", "0"])->order_by('id', 'DESC')->limit($hp->amount)->get('product')->result();

                $data = [
                	'title' => $type,
                	'products' => $products
                ];

				array_push($productList, $data);

              }else if($hp->type == "bestSell") {

                $type = "Çok Satılanlar";
                $products = $th->db->where('isActive',1)->where_in('seller_id', ["1", "0"])->select("COUNT(product_id) as total, product_id")->from('invoice')->group_by('product_id')->order_by('total','desc')->limit($hp->amount)->get()->result();
		          	 $data = [
		                'title' => $type,
		                'products' => []
		              ];
		              
		              foreach($products as $product)
		              {
		                  $p = $th->db->where('id', $product->product_id)->get('product')->row();
		                  array_push($data['products'], $p);
		              }

					array_push($productList, $data);

              }else{
                $productCategory = $th->db->where('id', $hp->type)->get('category')->row();
                $type = $productCategory->name;
                $products = $th->db->where('isActive',1)->where_in('seller_id', ["1", "0"])->order_by('id', 'DESC')->limit($hp->amount)->where('category_id', $hp->type)->get('product')->result();

                $data = [
                	'title' => $type,
                	'products' => $products
                ];

				array_push($productList, $data);

              }
			}

			return $productList;

		}else{ // Kullanıcı giriş yaptıysa ve yapay zeka açıksa

			$home_products = [];

			if ($th->db->where('user_id', $th->session->userdata('info')['id'])->count_all_results('category_review') < 30) {
	        $chanceCategory = $th->db->order_by('id', 'RANDOM')->where('isActive', 1)->limit(8)->get('category')->result();

	        $products = $th->db->where('isActive',1)->where_in('seller_id', ["1", "0"])->select("COUNT(product_id) as total, product_id")->from('invoice')->group_by('product_id')->order_by('total','desc')->limit(4)->get()->result();

                $type = "Çok Satılanlar";
		          	 $data = [
		                'title' => $type,
		                'products' => []
		              ];
		              
		              foreach($products as $product)
		              {
		                  $p = $th->db->where('id', $product->product_id)->get('product')->row();
		                  array_push($data['products'], $p);
		              }

				array_push($home_products, $data);
	         


	          $type = "Son Çıkanlar";
              $products = $th->db->where('isActive',1)->where_in('seller_id', ["1", "0"])->order_by('id', 'DESC')->limit(4)->get('product')->result();

                $data = [
                	'title' => $type,
                	'products' => $products
                ];


	        array_push($home_products, $data);
	        foreach ($chanceCategory as $cg) {

	        $productCategory = $th->db->where('id', $cg->id)->get('category')->row();
            $type = $productCategory->name;
            $products = $th->db->where('isActive',1)->where_in('seller_id', ["1", "0"])->order_by('id', 'DESC')->limit(4)->where('category_id', $cg->id)->get('product')->result();

            $data = [
            	'title' => $type,
            	'products' => $products
            ];

	          $categoryCount = $th->db->where('category_id', $cg->id)->where('isActive', 1)->count_all_results('product');
	          if ($categoryCount > 1) {
	          	array_push($home_products, $data);
	          }
	        }

	      }else{

	        $rand = rand(0, 3);
	        $remaining = 3 - $rand;

	        if ($rand != 0) {
	          $productList = [];
	          $products = $th->db->where('isActive', 1)->select("COUNT(product_id) as total, product_id")->from('invoice')->group_by('product_id')->order_by('total','desc')->limit(4)->get()->result();
	          $type = "Çok Satılanlar";
		          	 $data = [
		                'title' => $type,
		                'products' => []
		              ];
		              
		              foreach($products as $product)
		              {
		                  $p = $th->db->where('id', $product->product_id)->get('product')->row();
		                  array_push($data['products'], $p);
		              }

				array_push($home_products, $data);
	        }

	        $reviewProduct = $th->db->where('user_id', $th->session->userdata('info')['id'])->select("COUNT(category_id) as total, category_id")->from('category_review')->group_by('category_id')->limit(7)->order_by('total','desc')->get()->result(); 
	        foreach ($reviewProduct as $rp) {
	          $chance = rand(0,25);
	          if ($chance > 90) {
	            $chanceCategory = $th->db->where('isActive', 1)->order_by('rand()')->limit(1)->get('category')->row();
	            array_push($home_products, convertToObject(['type' => $chanceCategory->id, 'amount' => 4, 'category_id' => $chanceCategory->id]));
	          }
	          $category = $th->db->where('id', $rp->category_id)->get('category')->row();
	          if ($category->isActive == 1) {
	          	$products = $th->db->order_by('id', 'DESC')->limit(4)->where('isActive', 1)->where('category_id', $rp->category_id)->get('product')->result();
	          $product = $th->db->where('id', $rp->category_id)->get('category')->row();
	          array_push($home_products, [
	          	'title' => $product->name,
	          	'products' => $products
	          ]);
	          }
	        
	        }
	      }
		
		return $home_products;









		
		}
	}

?>