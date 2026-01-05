<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends G_Controller {

	public function index() 
	{
		addlog('Login - index', 'Sayfa ziyaret edildi: Giriş');

		if ($this->session->userdata('info')) {
			flash('Ups.', 'Zaten Giriş Yaptın.');
			redirect(base_url('client'), 'refresh');
		}

		$this->load->helper('form');
		$properties = $this->db->where('id', 1)->get('properties')->row();
		$data = [
			'properties' => $this->db->where('id', 1)->get('properties')->row(),
			'category' => getActiveCategories(),
			'pages' => $this->db->get('pages')->result(),
			'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
			'footerPage' => $this->db->limit(3)->order_by('id', 'DESC')->get('pages')->result(),
			'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
			'title' => 'Hesap - ' . $properties->name,
			'ref_code' => $this->input->get("ref_code")
		];

		$this->view('auth', $data);
	}

	public function loginClient()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules("mail", "Mail Adresi", "required|trim|valid_email");
		$this->form_validation->set_rules("password", "Şifre", "required|trim");


		$message = [
			'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.',
			'valid_email' => 'E-mail adresi geçerli değil.',
		];

		$this->form_validation->set_message($message);

		$result = $this->db->where('email', $this->input->post('mail'))->where('password', paspas($this->input->post('password')))->get('user')->row();
		if($this->form_validation->run() == FALSE) {
			addlog('loginClient', 'Giriş başarısız (Eksik bilgi). Mail - ' . $this->input->post('mail'));
			flash('!', validation_errors());
			redirect(base_url('hesap'), 'refresh');
		}else if(empty($result)) {
			addlog('loginClient', 'Giriş başarısız (Hatalı bilgi). Mail - ' . $this->input->post('mail'));
			flash('Ups.', 'Bilgiler Geçerli Değil');
			redirect(base_url('hesap'), 'refresh');
		}else {
			$properties = $this->db->where('id', 1)->get('properties')->row();
			if ($properties->isConfirmMail == 1 && $result->isConfirmMail == 0) {
			addlog('loginClient', 'Giriş başarısız (Mail onayı yapılmamış). Mail - ' . $this->input->post('mail'));

				$newmailcode = [ 
				   'id' => $result->id,
				   'mail' => $result->email,
				   'newCode' => 1
				];
				$this->session->set_userdata('newmailcode', $newmailcode);
				flash('Ups.', 'Mail adresin onaylı değil. Tekrar mail istiyorsan <a href="'.base_url('home/newMailCode').'">buraya</a> tıklaman yeterli.');
				redirect(base_url('hesap'), 'refresh');
				exit;
			}

			$data = [
				'email' => $this->input->post('mail'),
				'password' => paspas($this->input->post('password'))
			];

			if ($result && $result->isAdmin == 1) {
				$roles = $this->db->where('id', $result->role_id)->get('roles')->row();
				$newdata = [ 
				   'id' => $result->id,
				   'mail' => $result->email,
				   'isAdmin' => 1,
				   'role' => $roles->role
				];

				$this->db->where('id', $result->id)->update('user', [
					'last_login' => date('d-m-Y H:i:s')
				]);
				
				$this->session->set_userdata('info', $newdata);
				flash('Başarıyla Giriş Yaptın', 'Hoş Geldin!');
				redirect(base_url('admin'), 'refresh');
			}else if ($result && $result->isActive == 1) {
				$newdata = [ 
				   'id' => $result->id,
				   'mail' => $result->email,
				   'isAdmin' => 0
				];

				$this->db->where('id', $result->id)->update('user', [
					'last_login' => date('d-m-Y H:i:s')
				]);

				$this->session->set_userdata('info', $newdata);

                // Session'a user_id'yi kaydet
                $this->session->set_userdata('user_id', $result->id);

                // Session tablosunu güncelle
                $session_id = session_id();
                $this->db->where('id', $session_id);
                $this->db->update('ci_sessions', array(
                    'user_id' => $result->id,
                    'last_page' => uri_string(),
                    'last_activity' => date('Y-m-d H:i:s')
                ));

				addlog('loginClient', 'Giriş başarılı. Mail - ' . $this->input->post('mail'));
				flash('Başarılı!', 'Giriş Yaptın.');
				redirect(base_url('client'), 'refresh');
				exit;
			}else{
				addlog('loginClient', 'Giriş başarısız (Hatalı bilgi). Mail - ' . $this->input->post('mail'));
				flash('Ups.', 'Bilgiler Geçerli Değil..');
				redirect(base_url('hesap'), 'refresh');
				exit;
			}
			}
		}

	public function regUser()
	{ 
		addlog('regUser', 'Sayfa ziyaret edildi: Kayıt');
		$this->load->helper(['helpers', 'mail']);
		$this->load->library(['form_validation', 'Referral_System']);

		$properties = $this->db->where('id', 1)->get('properties')->row();
		$this->form_validation->set_rules("email", "E-Mail Adresi", "required|trim|valid_email|is_unique[user.email]");
		$this->form_validation->set_rules("password", "Şifre", "required|trim");
		$this->form_validation->set_rules("name", "İsim", "required|trim");
		$this->form_validation->set_rules("surname", "Soy İsim", "required|trim");
		$this->form_validation->set_rules("phone", "Telefon", "required|trim|is_unique[user.phone]");
		$this->form_validation->set_rules("ref_code", "Referans Kodu", "trim");
		if ($properties->isConfirmTc == 1) {
			$this->form_validation->set_rules("tc", "TC NO", "required|trim|min_length[11]|max_length[11]|is_unique[user.tc]");
			$this->form_validation->set_rules("birthday", "Doğum Yılı", "required|trim|min_length[4]|max_length[4]");
			$tc = trim($this->input->post('tc'));
			$birthday = trim($this->input->post('birthday'));

			$result = ConfirmTC(trim($this->input->post('tc')), trim($this->input->post('name')), trim($this->input->post('surname')), trim($this->input->post('birthday')));
			if ($result == FALSE) {
				addlog('regUser', 'Kayıt başarısız (TC bilgileri eşleşmiyor)');
				flash('Ups.', 'TC kimlik bilgilerin eşleşmiyor.');
				redirect(base_url('hesap'), 'refresh');
				exit;
			}
		}else{
			$tc = "11111111111";
			$birthday = "0000";
		}

		if ($this->input->post('confirm') != "on") {
			flash('Hata.', 'Sözleşmeyi onaylamadın');
			redirect(base_url('hesap'), 'refresh');
			exit;
		}

		$message = [
			'trim' => '{field} Alanında boşluk bırakılamaz.',
			'required' => '{field} Alanı boş bırakılamaz.',
			'valid_email' => 'E-mail adresi geçerli değil.',
			'is_unique' => 'Bu {field} daha önce kullanılmış.',
			'min_length' => '{field} Alanı belirtilen karakterden az olamaz..',
			'max_length' => '{field} Alanı belirtilen karakterden fazla olamaz.'
		];

		$this->form_validation->set_message($message);

		if($this->form_validation->run() == FALSE) {
			flash('Ups.', validation_errors());
			redirect(base_url('hesap'), 'refresh');
			exit;
		}else {
			// Referans kodu kontrolleri
			$withRef = !empty($this->input->post('ref_code'));
			$referrer = null;
			
			if ($withRef) {
				if (!$this->referral_system->isSystemEnabled()) {
					flash('Ups.', 'Referans sistemi aktif değil.');
					redirect(base_url('hesap'), 'refresh');
					exit;
				}
				
				$referrer = $this->referral_system->getUserByReferralCode($this->input->post('ref_code'));
				if(!$referrer) {
					flash('Ups.', 'Referans kodu bulunamadı.');
					redirect(base_url('hesap'), 'refresh');
					exit;
				}
			}

			// Kullanıcı verilerini hazırla
			$userData = [
				'name' => strip_tags(trim($this->input->post('name'))),
				'surname' => strip_tags(trim($this->input->post('surname'))),
				'email' => strip_tags(trim($this->input->post('email'))),
				'password' => trim(paspas($this->input->post('password'))),
				'phone' => strip_tags(trim($this->input->post('phone'))),
				'date' => date('d.m.Y H:i:s'),
				'balance' => 0,
				'tc' => $tc,
				'birthday' => $birthday
			];

			// Mail doğrulama ayarına göre işle
			if ($properties->isConfirmMail == 1) {
				addlog('regUser', 'Kayıt başarılı. Mail doğrulaması gönderildi - ' . $this->input->post('email'));
				$randString = randString(25);
				$randString = md5($this->input->post('name') . $randString);
				$userData['isConfirmMail'] = 0;
				$userData['mail_code'] = $randString;
			} else {
				addlog('regUser', 'Kayıt başarılı. Mail:' . $this->input->post('email'));
				$userData['isConfirmMail'] = 1;
			}

			// Kullanıcıyı veritabanına kaydet
			$this->db->insert('user', $userData);
			$user_id = $this->db->insert_id();

			// Referans işlemleri
			if ($withRef && $referrer) {
				// Referans ilişkisini kur
				$referral_result = $this->referral_system->createReferralRelation($referrer->id, $user_id);
				
				if ($referral_result['success']) {
					// Referans ayarlarını al
					$referral_settings = $this->referral_system->getSettings();
					$require_purchase = $referral_settings['require_purchase'] == '1';
					$register_bonus = floatval($referral_settings['register_bonus']);
					
					// Eğer kayıt bonusu için alışveriş gerekmiyorsa anında bonus ver
					if (!$require_purchase && $register_bonus > 0) {
						$this->referral_system->giveRegistrationBonus($referrer->id, $user_id);
						addlog('regUser', 'Kayıt bonusu anında verildi. Referrer: ' . $referrer->id . ', Referred: ' . $user_id . ', Bonus: ' . $register_bonus);
					} else {
						addlog('regUser', 'Kayıt bonusu ilk alışveriş sonrasında verilecek. Referrer: ' . $referrer->id . ', Referred: ' . $user_id);
					}
				} else {
					addlog('regUser', 'Referans ilişkisi kurulamadı: ' . $referral_result['message']);
				}
			}

			// Mail gönderim işlemleri
			if ($properties->isConfirmMail == 1) {
				// Mail doğrulama maili gönder
				$this->load->library('mailer');
				$this->mailer->send(
					$this->input->post('email'),
					'mail_verification',
					[
						'name' => $userData['name'],
						'surname' => $userData['surname'],
						'verification_link' => base_url('mail-onay/') . $randString
					]
				);
				flash('Harika.', 'Kayıt başarılı. Mail adresine gelen kodu doğruladıktan sonra giriş yapabilirsin.');
			} else {
				flash('Harika', 'Kayıt başarılı. Lütfen giriş yapın.');
			}

			// Hoşgeldin maili gönder
			sendWelcomeMail(
				$this->input->post('email'),
				[
					'name' => $userData['name'],
					'surname' => $userData['surname'],
					'email' => $userData['email'],
					'company_name' => $properties->name,
					'company_logo' => base_url('assets/img/') . $properties->img,
					'company_url' => base_url(),
					'support_email' => $properties->email
				]
			);

			redirect(base_url('hesap'), 'refresh');
			exit;
		}
	}

	public function regGuest()
	{ 
		addlog('regGuest', 'Sayfa ziyaret edildi: Misafir girişi.');
		$this->load->helper('helpers');
		$this->load->library('form_validation');

		$properties = $this->db->where('id', 1)->get('properties')->row();
		$this->form_validation->set_rules("email", "E-Mail", "required|trim|valid_email|is_unique[user.email]");
		$this->form_validation->set_rules("name", "İsim", "required|trim");
		$this->form_validation->set_rules("surname", "Soy İsim", "required|trim");
		$this->form_validation->set_rules("phone", "Telefon", "required|trim|is_unique[user.phone]");
		if ($properties->isConfirmTc == 1) {
			$this->form_validation->set_rules("tc", "TC NO", "required|trim|min_length[11]|max_length[11]|is_unique[user.tc]");
			$this->form_validation->set_rules("birthday", "Doğum Yılı", "required|trim|min_length[4]|max_length[4]");
			$tc = $this->input->post('tc');
			$birthday = trim($this->input->post('birthday'));

			$result = ConfirmTC(trim($this->input->post('tc')), trim($this->input->post('name')), trim($this->input->post('surname')), trim($this->input->post('birthday')));
			if ($result == FALSE) {
				addlog('regGuest', 'Misafir girişi başarısız. TC kimlik bilgileri eşleşmiyor');
				flash('Ups.', 'TC kimlik bilgilerin eşleşmiyor.');
				redirect(base_url('sepet'), 'refresh');
			exit;
			}
		}else{
			$tc = "11111111111";
			$birthday = "0000";
		}

		$message = [
			'trim' => '{field} Alanında boşluk bırakılamaz.',
			'required' => '{field} Alanı boş bırakılamaz.',
			'valid_email' => 'E-mail adresi geçerli değil.',
			'is_unique' => 'Bu {field} daha önce kullanılmış.',
			'min_length' => '{field} Alanı belirtilen karakterden az olamaz..',
			'max_length' => '{field} Alanı belirtilen karakterden fazla olamaz.'
		];

		$this->form_validation->set_message($message);

		if($this->form_validation->run() == FALSE) {
			addlog('regGuest', 'Misafir girişi başarısız.' . validation_errors());
			flash('Ups.', validation_errors());
			redirect(base_url('sepet'), 'refresh');
			exit;
		}else {
			addlog('regGuest', 'Misafir girişi başarılı. Mail - '. $this->input->post('email'));
			if (!empty($this->input->post('password'))) {
				$password = $this->input->post('password');
			}else{
				$password = randString(10);
			}
			$data = [
				'name' => trim($this->input->post('name')),
				'surname' => trim($this->input->post('surname')),
				'email' => trim($this->input->post('email')),
				'name' => trim($this->input->post('name')),
				'password' => trim(paspas($password)),
				'phone' => trim($this->input->post('phone')),
				'date' => date('d.m.Y'),
				'tc' => $tc,
				'birthday' => $birthday
			];

			// Send welcome email with login credentials
			$this->load->library('mailer');
			$this->mailer->send(
				$this->input->post('email'),
				'guest_registration',
				[
					'site_name' => $properties->name,
					'user_email' => $this->input->post('email'),
					'user_password' => $password,
					'site_url' => base_url()
				]
			);

			$this->db->insert('user', $data);
			$insert_id = $this->db->insert_id();

			// Misafir kullanıcı için wallet_transactions kaydı oluştur
			$guest_transaction = [
				'user_id' => $insert_id,
				'transaction_type' => 'referral_bonus',
				'balance_type' => 'spendable',
				'amount' => 0,
				'balance_before' => 0,
				'balance_after_transaction' => 0,
				'description' => 'Misafir kullanıcı hesabı oluşturuldu',
				'status' => 1,
				'created_at' => date('Y-m-d H:i:s')
			];
			$this->db->insert('wallet_transactions', $guest_transaction);

			$newdata = [ 
				   'id' => $insert_id,
				   'mail' => $this->input->post('email'),
				   'isAdmin' => 0
				];

			$this->session->set_userdata('info', $newdata);

			redirect(base_url('payment/buyOnCart'), 'refresh');
			exit;
		}
	}

	public function addTc()
	{
		addlog('addTc', 'Sayfa ziyaret edildi: TC ekleme');
		$this->load->library('form_validation');

			$properties = $this->db->where('id', 1)->get('properties')->row();
			$this->form_validation->set_rules("tc", "TC NO", "trim|min_length[11]|max_length[11]|is_unique[user.tc]|required");
			$this->form_validation->set_rules("birthday", "Doğum Yılı", "trim|min_length[4]|max_length[4]|required");

			$user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
			$tc = trim($this->input->post('tc'));
			$birthday = trim($this->input->post('birthday'));


			$result = ConfirmTC(trim($this->input->post('tc')), $user->name, $user->surname, trim($this->input->post('birthday')));
			if ($result == FALSE) {
				addlog('addTc', 'TC ekleme başarısız. TC bilgileri eşleşmiyor.');
				flash('Ups.', 'TC kimlik bilgilerin eşleşmiyor.');
				redirect(base_url('client'), 'refresh');
			exit;
			}
		

		$message = [
			'trim' => '{field} Alanında boşluk bırakılamaz.',
			'required' => '{field} Alanı boş bırakılamaz.',
			'valid_email' => 'E-mail adresi geçerli değil.',
			'is_unique' => 'Bu {field} daha önce kullanılmış.',
			'min_length' => '{field} Alanı belirtilen karakterden az olamaz..',
			'max_length' => '{field} Alanı belirtilen karakterden fazla olamaz.'
		];

		$this->form_validation->set_message($message);

		if($this->form_validation->run() == FALSE) {
			flash('Ups.', validation_errors());
			redirect(base_url('client'), 'refresh');
			exit;
		}else {
		addlog('addTc', 'TC ekleme başarılı.');

			$data = [
				'tc' => $this->input->post('tc'),
				'birthday' => $this->input->post('birthday'),
			];

			$this->db->where('id', $user->id)->update('user', $data);

			flash('Harika.', 'Artık sitemizi kullanmaya devam edebilirsin.');
			redirect(base_url('hesap'), 'refresh');
			exit;

		}
	}


	public function logOut()
	{
		addlog('logOut', 'Kullanıcı çıkış yaptı.');
		$this->session->unset_userdata('info');
		$this->session->sess_destroy();
		flash('Harika', 'Başarıyla Çıkış Yaptın.');
		redirect(base_url(), 'refresh');
	}
}
