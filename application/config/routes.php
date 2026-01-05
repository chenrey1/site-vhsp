<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home/index';
$route['sitemap.xml'] = 'home/sitemap';
$route['tum-kategoriler'] = 'home/categories';
$route['ilan-pazari'] = 'home/marketPlace';
$route['ilan-pazari/(:num)'] = 'home/marketPlace/$1';
$route['kategori/(:any)'] = 'home/category/$1';
$route['kategori/(:any)/(:any)'] = 'home/category/$1/$2';
$route['makale-listesi'] = 'home/blogs';
$route['yayinci/(:any)'] = 'home/streamer/$1';
$route['yayincilar'] = 'home/streamers';
$route['donation/(:any)'] = 'client/dashboard/streamer_donation/$1';
$route['makale-listesi/(:any)'] = 'home/blogs/$1';
$route['sifremi-unuttum'] = 'home/reNewPassword';
$route['newPassword/(:any)'] = 'home/newPassword/$1';
$route['hesap'] = 'login/index';
$route['magaza/(:any)'] = 'home/shop/$1';
$route['sepet'] = 'home/cart';
$route['tc-dogrulama'] = 'home/addTc';
$route['payment'] = 'payment/index';
$route['callback'] = 'client/dashboard/callback';
$route['makale/(:any)'] = 'home/blog/$1';
$route['sayfa/(:any)'] = 'home/page/$1';
$route['paketler'] = 'home/packages';
$route['paketler/(:any)'] = 'home/packages/$1'; // Filtreleme için
$route['paket/(:any)'] = 'home/package/$1'; // Paket detay sayfası
$route['admin'] = 'Panel';
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/overView'] = 'admin/dashboard/overView';

// Dealer kontrolörü için özel rota
$route['admin/dealer'] = 'admin/dealer/index';
$route['admin/dealer/(:any)'] = 'admin/dealer/$1';
$route['admin/credit_management'] = 'admin/credit_management/index';

// Diğer admin rotaları
$route['admin/(:any)'] = 'admin/product/$1';

// Balance Controller için özel yönlendirmeler
$route['client/balance'] = 'client/balance/index';
$route['client/balance/changeBank'] = 'client/balance/changeBank';
$route['client/balance/addTransfer'] = 'client/balance/addTransfer';
$route['client/balance/transferBalance'] = 'client/balance/transferBalance';
$route['client/balance/withdrawBalance'] = 'client/balance/withdrawBalance';
$route['client/balance/transferBetweenBalances'] = 'client/balance/transferBetweenBalances';
$route['client/balance/acceptCreditOffer'] = 'client/balance/acceptCreditOffer';
$route['client/balance/payCreditDebt'] = 'client/balance/payCreditDebt';

$route['client'] = 'client/dashboard/index';
$route['client/(:any)'] = 'client/dashboard/$1';
$route['client/(:any)/(:any)'] = 'client/dashboard/$1/$2';


// Çekiliş onur

$route['donation/(:any)']                   = 'client/dashboard/streamer_donation/$1';
$route['cekilisler']                        = 'home/draws';
$route['client/cekilis-kazanclari']         = 'client/dashboard/cekilis_kazanclari';
$route['cekilis/cekilis-kazanclari']        = 'home/cekilis_kazanclari';
$route['cronapi/run/(:any)']                = 'CronApi/run/$1';
$route['cronapi/draw_finish_expired']       = 'CronApi/draw_finish_expired';
$route['cekilis/katil/(:num)'] = 'home/joinDraw/$1';
$route['cekilis/(:num)'] = 'home/drawDetail/$1';


$route['reNewPassword'] = 'home/reNewPassword';
$route['mail-onay/(:any)'] = 'home/confirmUserMail/$1';
$route['cronapi/run/(:any)'] = 'CronApi/run/$1';


/* API ROUTES */
$route['api/v1/(:any)'] = 'api/ExternalAPI/$1';
$route['api/v1/products/(:any)'] = 'api/ExternalAPI/products_by_id/$1';
$route['api/v1/categories/(:any)'] = 'api/ExternalAPI/categories_by_id/$1';
$route['api/v1/(:any)/(:any)'] = 'api/ExternalAPI/$1/$2';
$route['api/docs'] = 'api/ExternalAPIDocs';
$route['api/docs/openapi'] = 'api/ExternalAPIDocs/openapi';
$route['api/(:any)'] = 'api/API/$1';
$route['provider-callback'] = 'API/provider_callback';
$route['provider-callback/hyper'] = 'API/provider_callback_hyper';
$route['provider-callback/hyper/(:any)'] = 'API/provider_callback_hyper/$1';

$route['(:any)'] = 'home/getProduct/$1';

$route['404_override'] = 'home/error';
$route['translate_uri_dashes'] = FALSE;
