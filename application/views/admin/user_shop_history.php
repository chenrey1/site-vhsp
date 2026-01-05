<div id="layoutSidenav_content">
    <main class="py-4">
        <div class="container-fluid">
        <!-- Başlık -->
        <h5 class="mb-2">Kullanıcı Detayları</h5>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/users'); ?>">Kullanıcılar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kullanıcı Detayları</li>
                </ol>
            </nav>

        <!-- Kullanıcı Profil Kartı -->
        <div class="card mb-4">
                <div class="card-body">
            <!-- Üst Kısım - Temel Bilgiler -->
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center mb-4">
              <div class="user-avatar mb-3 mb-md-0 mr-md-3">
                <div class="user-history-avatar">
                  <span class="user-history-initials"><?= substr($user->name, 0, 1) . substr($user->surname, 0, 1) ?></span>
                </div>
              </div>
              <div class="user-info flex-grow-1 mb-3 mb-md-0">
                <h4 class="mb-1"><?= $user->name . ' ' . $user->surname ?></h4>
                <div class="d-flex flex-wrap">
                  <span class="badge badge-soft-primary mr-2 mb-2">
                    <i class="fas fa-envelope mr-1"></i> <?= $user->email ?>
                  </span>
                  <span class="badge badge-soft-info mr-2 mb-2">
                    <i class="fas fa-phone mr-1"></i> <?= $user->phone ?>
                  </span>
                  <?php if($user->tc != "11111111111"): ?>
                    <span class="badge badge-soft-success mb-2"><i class="fas fa-id-card mr-1"></i> Kimlik Doğrulanmış</span>
                  <?php else: ?>
                    <span class="badge badge-soft-warning mb-2"><i class="fas fa-id-card mr-1"></i> Kimlik Doğrulanmamış</span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="user-actions text-left text-md-right">
                <?php if($user->isConfirmMail == 1): ?>
                  <span class="badge badge-success"><i class="fas fa-check"></i> E-posta Doğrulanmış</span>
                <?php else: ?>
                  <span class="badge badge-warning mb-2 d-block"><i class="fas fa-exclamation"></i> E-posta Doğrulanmamış</span>
                  <a href="<?= base_url('admin/product/sendVerificationMail/'.$user->id) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-paper-plane"></i> Doğrulama Maili Gönder
                  </a>
                <?php endif; ?>
                <button class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#resetPasswordModal">
                  <i class="fas fa-key"></i> Şifre Sıfırla
                </button>
              </div>
            </div>

            <!-- Orta Kısım - Durum ve İstatistikler -->
            <div class="row mb-4">
              <div class="col-12 col-md-6 mb-4 mb-md-0">
                <div class="status-card p-3 rounded bg-light h-100">
                  <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle text-primary mr-2"></i>Durum Bilgileri</h6>
                  <div class="row mb-3">
                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                      <small class="text-muted d-block">Son IP</small>
                      <span><i class="fas fa-network-wired text-muted mr-1"></i> <?= $user->last_ip ?? 'Bilinmiyor' ?></span>
                    </div>
                    <div class="col-12 col-sm-6 text-left text-sm-right">
                      <small class="text-muted d-block">Son Giriş</small>
                      <span>
                        <i class="fas fa-clock text-muted mr-1"></i> 
                        <?= $user->last_login ?? 'Bilinmiyor' ?>
                        <?php if($is_online): ?>
                          <span class="badge badge-success pulse ml-1">Online</span>
                        <?php endif; ?>
                      </span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 col-sm-6">
                      <small class="text-muted d-block">Kayıt Tarihi</small>
                      <span><i class="fas fa-calendar text-muted mr-1"></i> <?= $user->date ?></span>
                    </div>
                    <div class="col-12 col-sm-6 text-left text-sm-right mt-2 mt-sm-0">
                      <small class="text-muted d-block">Hesap Yaşı</small>
                      <span><i class="fas fa-hourglass-half text-muted mr-1"></i> <?= floor((strtotime(date('Y-m-d')) - strtotime($user->date)) / (60 * 60 * 24)) ?> gün</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="balance-card p-3 rounded bg-light h-100">
                  <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-wallet text-primary mr-2"></i>Bakiye Bilgileri</h6>
                  <div class="row mb-3">
                    <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                      <small class="text-muted d-block">Ana Bakiye</small>
                      <span class="h5 mb-0 text-primary"><?= number_format($user->balance, 2) ?> TL</span>
                    </div>
                    <div class="col-12 col-sm-6 text-left text-sm-right">
                      <small class="text-muted d-block">Kazanç Bakiyesi</small>
                      <span class="h5 mb-0 text-success"><?= number_format($user->balance2, 2) ?> TL</span>
                    </div>
                  </div>
                        <div class="row">
                    <div class="col-12 col-sm-6">
                      <small class="text-muted d-block">Toplam Yükleme</small>
                      <span><i class="fas fa-arrow-up text-success mr-1"></i> <?= $balance_loads ?> kez</span>
                                        </div>
                    <div class="col-12 col-sm-6 text-left text-sm-right mt-2 mt-sm-0">
                      <small class="text-muted d-block">Referans Kazancı</small>
                      <span><i class="fas fa-users text-info mr-1"></i> <?= number_format($total_earnings, 2) ?> TL</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

            <!-- Alt Kısım - Timeline ve Referans -->
            <div class="row">
              <div class="col-12 col-lg-9 mb-4 mb-lg-0">
                <div class="activity-timeline p-3 rounded bg-light">
                  <h6 class="border-bottom pb-2 mb-3">
                    <i class="fas fa-history text-primary mr-2"></i>Son Aktiviteler
                    <small class="text-muted ml-2">(Son 5 aktivite)</small>
                  </h6>
                  <div class="user-history-timeline">
                    <?php foreach(array_slice($last_logs, 0, 5) as $log): ?>
                    <div class="user-history-timeline-item">
                      <div class="user-history-timeline-date"><?= $log->date ?></div>
                      <div class="user-history-timeline-content">
                        <strong><?= $log->event ?></strong>
                        <p class="mb-0"><?= $log->function ?></p>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              <div class="col-12 col-lg-3">
                <div class="reference-card p-3 rounded bg-light h-100">
                  <h6 class="border-bottom pb-2 mb-3">
                    <i class="fas fa-user-plus text-primary mr-2"></i>Referans
                  </h6>
                  <div class="reference-info">
                    <div class="d-flex align-items-center mb-2">
                      <div class="reference-code-badge">
                        <small class="text-muted">Kod:</small>
                        <span class="badge badge-soft-primary ml-1"><?= $user->ref_code ?></span>
                      </div>
                    </div>

                    <?php if($referrer): ?>
                      <div class="referrer-info mb-3">
                        <small class="text-muted d-block mb-1">Referans Eden:</small>
                        <a href="<?= base_url('admin/product/userShopHistory/'.$referrer->referrer_id) ?>" target="_blank" class="btn btn-sm btn-outline-primary btn-block text-left">
                          <i class="fas fa-user mr-1"></i>
                          <?= $referrer->name . ' ' . $referrer->surname ?>
                          <small class="d-block text-muted mt-1">Ref Kodu: <?= $referrer->ref_code ?></small>
                        </a>
                      </div>
                    <?php endif; ?>

                    <?php if(!empty($last_references)): ?>
                      <div class="referrals-info">
                        <small class="text-muted d-block mb-2">Son Referanslar (<?= count($last_references) ?>/<?= count($references) ?>):</small>
                        <div class="referral-list">
                          <?php foreach($last_references as $ref): ?>
                            <a href="<?= base_url('admin/product/userShopHistory/'.$ref->buyer_id) ?>" 
                               target="_blank"
                               class="btn btn-sm btn-outline-info btn-block text-left mb-1">
                              <i class="fas fa-user-check mr-1"></i>
                              <?= $ref->name . ' ' . $ref->surname ?>
                              <small class="d-block text-muted"><?= $ref->email ?></small>
                            </a>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php else: ?>
                      <?php if(empty($referrer)): ?>
                        <div class="no-referrer text-center p-2 rounded border">
                          <i class="fas fa-user-slash text-muted mb-1"></i>
                          <small class="d-block text-muted">Referans Bağlantısı Yok</small>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mt-4">
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card bg-primary text-white h-100">
              <div class="card-body">
                <h6 class="mb-2">Toplam Harcama</h6>
                <h4 class="mb-0"><?= number_format($total_spent, 2) ?> TL</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card bg-success text-white h-100">
              <div class="card-body">
                <h6 class="mb-2">Toplam Bakiye Yükleme</h6>
                <h4 class="mb-0"><?= number_format($total_deposit, 2) ?> TL</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card bg-info text-white h-100">
              <div class="card-body">
                <h6 class="mb-2">Ortalama İşlem</h6>
                <h4 class="mb-0"><?= $successful_purchases > 0 ? number_format($total_spent / $successful_purchases, 2) : '0.00' ?> TL</h4>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card bg-warning text-white h-100">
              <div class="card-body">
                <h6 class="mb-2">Toplam Referans Kazancı</h6>
                <h4 class="mb-0"><?= number_format($total_earnings, 2) ?> TL</h4>
              </div>
            </div>
          </div>
        </div>

        <?php if ($pending_orders_count > 0): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <div>
                    <strong>Bekleyen Siparişler:</strong> 
                    <?= $pending_orders_count ?> adet teslim edilmeyen sipariş (<?= number_format($pending_orders_total, 2) ?> TL) bulunmaktadır.
                    Teslim edilmesi halinde toplam harcama <?= number_format($total_spent + $pending_orders_total, 2) ?> TL olacaktır.
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="card mt-4">
          <div class="card-body p-0">
            <ul class="nav nav-tabs" id="userTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab">
                  <i class="fas fa-exchange-alt"></i> İşlem Geçmişi
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="references-tab" data-toggle="tab" href="#references" role="tab">
                  <i class="fas fa-users"></i> Referanslar
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="subscriptions-tab" data-toggle="tab" href="#subscriptions" role="tab">
                  <i class="fas fa-star"></i> Abonelikler
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="wallet-transactions-tab" data-toggle="tab" href="#wallet-transactions" role="tab">
                  <i class="fas fa-wallet"></i> Bakiye Hareketleri
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="logs-tab" data-toggle="tab" href="#logs" role="tab">
                  <i class="fas fa-history"></i> Olay Kayıtları
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab">
                  <i class="fas fa-edit"></i> Düzenle
                </a>
              </li>
            </ul>

            <div class="tab-content p-4" id="userTabContent">
              <!-- İşlem Geçmişi Tab -->
              <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                <div class="filter-cards mb-4">
                  <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                      <div class="user-history-filter-card" data-filter="all">
                        <div class="user-history-filter-body">
                          <div class="user-history-filter-icon">
                            <i class="fas fa-list"></i>
                          </div>
                          <div class="user-history-filter-content">
                            <h3 class="user-history-filter-title"><?= count($transactions) ?></h3>
                            <p class="user-history-filter-text">Tüm İşlemler</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                      <div class="user-history-filter-card" data-filter="success">
                        <div class="user-history-filter-body">
                          <div class="user-history-filter-icon success">
                            <i class="fas fa-check"></i>
                          </div>
                          <div class="user-history-filter-content">
                            <h3 class="user-history-filter-title"><?= $successful_purchases ?></h3>
                            <p class="user-history-filter-text">Başarılı Satışlar</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                      <div class="user-history-filter-card" data-filter="failed">
                        <div class="user-history-filter-body">
                          <div class="user-history-filter-icon danger">
                            <i class="fas fa-times"></i>
                          </div>
                          <div class="user-history-filter-content">
                            <h3 class="user-history-filter-title"><?= $failed_purchases ?></h3>
                            <p class="user-history-filter-text">Başarısız Satışlar</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                      <div class="user-history-filter-card" data-filter="deposit">
                        <div class="user-history-filter-body">
                          <div class="user-history-filter-icon info">
                            <i class="fas fa-wallet"></i>
                          </div>
                          <div class="user-history-filter-content">
                            <h3 class="user-history-filter-title"><?= $balance_loads ?></h3>
                            <p class="user-history-filter-text">Bakiye Yüklemeleri</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <div id="transactionsGrid" class="mb-4"></div>
                </div>
              </div>

              <!-- Abonelikler Tab -->
              <div class="tab-pane fade" id="subscriptions" role="tabpanel">
                <div class="table-responsive">
                  <div id="subscriptionsGrid" class="mb-4"></div>
                </div>
              </div>

              <!-- Referanslar Tab -->
              <div class="tab-pane fade" id="references" role="tabpanel">
                <div class="table-responsive">
                  <div id="referencesGrid" class="mb-4"></div>
                </div>
              </div>

              <!-- Olay Kayıtları Tab -->
              <div class="tab-pane fade" id="logs" role="tabpanel">
                <div class="table-responsive">
                  <div id="logsGrid" class="mb-4"></div>
                </div>
              </div>

              <!-- Bakiye Hareketleri Tab -->
              <div class="tab-pane fade" id="wallet-transactions" role="tabpanel">
                <div class="wallet-transactions-tabs mb-3">
                  <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                      <a class="nav-link active" id="all-balance-tab" data-toggle="pill" href="#all-balance" role="tab">
                        <i class="fas fa-wallet mr-1"></i> Tüm Bakiyeler
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="spendable-balance-tab" data-toggle="pill" href="#spendable-balance" role="tab">
                        <i class="fas fa-shopping-bag mr-1"></i> Kullanılabilir Bakiye
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="withdrawable-balance-tab" data-toggle="pill" href="#withdrawable-balance" role="tab">
                        <i class="fas fa-money-bill-wave mr-1"></i> Çekilebilir Bakiye
                      </a>
                    </li>
                  </ul>
                </div>
                
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="all-balance" role="tabpanel">
                    <div class="filter-cards mb-4">
                      <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                          <div class="user-history-filter-card wallet-filter active" data-filter="all">
                            <div class="user-history-filter-body">
                              <div class="user-history-filter-icon">
                                <i class="fas fa-exchange-alt"></i>
                              </div>
                              <div class="user-history-filter-content">
                                <h3 class="user-history-filter-title" id="all-wallet-count">0</h3>
                                <p class="user-history-filter-text">Tüm Hareketler</p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                          <div class="user-history-filter-card wallet-filter" data-filter="in">
                            <div class="user-history-filter-body">
                              <div class="user-history-filter-icon success">
                                <i class="fas fa-arrow-down"></i>
                              </div>
                              <div class="user-history-filter-content">
                                <h3 class="user-history-filter-title" id="income-count">0</h3>
                                <p class="user-history-filter-text">Para Girişi</p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                          <div class="user-history-filter-card wallet-filter" data-filter="out">
                            <div class="user-history-filter-body">
                              <div class="user-history-filter-icon danger">
                                <i class="fas fa-arrow-up"></i>
                              </div>
                              <div class="user-history-filter-content">
                                <h3 class="user-history-filter-title" id="expense-count">0</h3>
                                <p class="user-history-filter-text">Para Çıkışı</p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                          <div class="user-history-filter-card wallet-filter" data-filter="transfer">
                            <div class="user-history-filter-body">
                              <div class="user-history-filter-icon info">
                                <i class="fas fa-sync-alt"></i>
                              </div>
                              <div class="user-history-filter-content">
                                <h3 class="user-history-filter-title" id="transfer-count">0</h3>
                                <p class="user-history-filter-text">Transferler</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <div id="walletTransactionsGrid" class="mb-4"></div>
                    </div>
                  </div>
                  
                  <div class="tab-pane fade" id="spendable-balance" role="tabpanel">
                    <div class="balance-info-card mb-4">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <div class="balance-info-title">
                            <i class="fas fa-shopping-bag mr-2 text-primary"></i> Kullanılabilir Bakiye Hareketleri
                          </div>
                          <div class="balance-info-description">
                            Kullanıcının site içinde harcayabileceği bakiye hareketleri
                          </div>
                        </div>
                        <div class="col-md-6 text-md-right">
                          <div class="balance-amount">
                            <span class="balance-amount-label">Güncel Bakiye:</span>
                            <span class="balance-amount-value"><?= number_format($user->balance, 2) ?> TL</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <div id="spendableTransactionsGrid" class="mb-4"></div>
                    </div>
                  </div>
                  
                  <div class="tab-pane fade" id="withdrawable-balance" role="tabpanel">
                    <div class="balance-info-card mb-4">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <div class="balance-info-title">
                            <i class="fas fa-money-bill-wave mr-2 text-info"></i> Çekilebilir Bakiye Hareketleri
                          </div>
                          <div class="balance-info-description">
                            Kullanıcının banka hesabına çekebileceği bakiye hareketleri
                          </div>
                        </div>
                        <div class="col-md-6 text-md-right">
                          <div class="balance-amount">
                            <span class="balance-amount-label">Güncel Bakiye:</span>
                            <span class="balance-amount-value"><?= number_format($user->balance2, 2) ?> TL</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <div id="withdrawableTransactionsGrid" class="mb-4"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Düzenle Tab -->
              <div class="tab-pane fade" id="edit" role="tabpanel">
                <form action="<?= base_url('admin/product/editUser/'.$user->id) ?>" method="POST" enctype="multipart/form-data" class="row">
                    <!-- Kişisel Bilgiler -->
                    <div class="col-12 mb-3">
                        <h6 class="border-bottom pb-2">Kişisel Bilgiler</h6>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Ad</label>
                        <input type="text" name="name" class="form-control" value="<?= $user->name ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Soyad</label>
                        <input type="text" name="surname" class="form-control" value="<?= $user->surname ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>E-posta</label>
                        <input type="email" name="email" class="form-control" value="<?= $user->email ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Telefon</label>
                        <input type="text" name="phone" class="form-control" value="<?= $user->phone ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>TC Kimlik No</label>
                        <input type="text" name="tc" class="form-control" value="<?= $user->tc ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Referans Kodu</label>
                        <input type="text" name="reference_code" class="form-control" value="<?= $user->ref_code ?>">
                    </div>

                    <!-- Yetki Bilgileri -->
                    <div class="col-12 mb-3 mt-4">
                        <h6 class="border-bottom pb-2">Yetki Bilgileri</h6>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Yetki Grubu</label>
                        <select name="role_id" class="form-control" <?= ($user->role_id == 1) ? 'disabled' : '' ?>>
                            <option value="0" <?= ($user->role_id == 0) ? 'selected' : '' ?>>Yetkisiz</option>
                            <?php foreach($roles as $role): ?>
                                <?php if($role->id != 1): ?>
                                    <option value="<?= $role->id ?>" <?= ($user->role_id == $role->id) ? 'selected' : '' ?>><?= $role->role ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <?php if($user->role_id == 1): ?>
                            <small class="form-text text-danger">Bu kullanıcının yetkisi değiştirilemez.</small>
                        <?php else: ?>
                            <small class="form-text text-muted">Kullanıcının yetki grubunu seçin. Yetkisiz seçeneği normal kullanıcılar içindir.</small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Mevcut Yetkiler</label>
                        <?php if($user->role_id > 0 && isset($user_role)): ?>
                            <?php 
                            $roles_array = json_decode($user_role->roles);
                            if(!empty($roles_array)):
                                foreach($roles_array as $perm):
                            ?>
                                <span class="badge badge-info mr-1 mb-1"><?= ucfirst($perm) ?></span>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <small class="mb-0">Bu kullanıcı yetkisiz.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bakiye ve İndirim Bilgileri -->
                    <div class="col-12 mb-3 mt-4">
                        <h6 class="border-bottom pb-2">Bakiye ve İndirim Bilgileri</h6>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Ana Bakiye</label>
                        <input type="number" step="0.01" name="balance" class="form-control" value="<?= $user->balance ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Kazanç Bakiyesi</label>
                        <input type="number" step="0.01" name="balance2" class="form-control" value="<?= $user->balance2 ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>İndirim Oranı (%)</label>
                        <input type="number" name="discount" class="form-control" value="<?= $user->discount ?>">
                    </div>

                    <!-- Hesap Durumu -->
                    <div class="col-12 mb-3 mt-4">
                        <h6 class="border-bottom pb-2">Hesap Durumu</h6>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Hesap Durumu</label>
                        <select name="isActive" class="form-control">
                            <option value="1" <?= $user->isActive == 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $user->isActive == 0 ? 'selected' : '' ?>>Pasif</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>E-posta Doğrulama</label>
                        <select name="isConfirmMail" class="form-control">
                            <option value="1" <?= $user->isConfirmMail == 1 ? 'selected' : '' ?>>Doğrulanmış</option>
                            <option value="0" <?= $user->isConfirmMail == 0 ? 'selected' : '' ?>>Doğrulanmamış</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Kullanıcı Tipi</label>
                        <select name="type" class="form-control">
                            <option value="1" <?= $user->type == 1 ? 'selected' : '' ?>>Normal Kullanıcı</option>
                            <option value="2" <?= $user->type == 2 ? 'selected' : '' ?>>Satıcı</option>
                        </select>
                    </div>

                    <!-- Satıcı Bilgileri (Eğer satıcı ise) -->
                    <div class="col-12 mb-3 mt-4 seller-fields" style="display: <?= $user->type == 2 ? 'block' : 'none' ?>;">
                        <h6 class="border-bottom pb-2">Satıcı Bilgileri</h6>
                    </div>
                    <div class="form-group col-md-4 seller-fields" style="display: <?= $user->type == 2 ? 'block' : 'none' ?>;">
                        <label>Mağaza Adı</label>
                        <input type="text" name="shop_name" class="form-control" value="<?= $user->shop_name ?>">
                    </div>
                    <div class="form-group col-md-4 seller-fields" style="display: <?= $user->type == 2 ? 'block' : 'none' ?>;">
                        <label>Komisyon Oranı (%)</label>
                        <input type="number" step="0.01" name="shop_com" class="form-control" value="<?= $user->shop_com ?>">
                    </div>
                    <div class="form-group col-md-4 seller-fields" style="display: <?= $user->type == 2 ? 'block' : 'none' ?>;">
                        <label>Mağaza Resmi</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="shop_image" name="shop_image" accept="image/*">
                            <label class="custom-file-label" for="shop_image">Resim Seç</label>
                        </div>
                        <?php if(!empty($user->shop_image)): ?>
                            <div class="mt-2">
                                <img src="<?= base_url('assets/img/shop/'.$user->shop_image) ?>" alt="Mağaza Resmi" class="img-thumbnail" style="height: 80px;" name="shop_image">
                            </div>
                        <?php endif; ?>
                        <small class="form-text text-muted">Önerilen boyut: 200x200px, maksimum boyut: 2MB</small>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Değişiklikleri Kaydet
                        </button>
                        <button type="reset" class="btn btn-secondary ml-2">
                            <i class="fas fa-undo mr-1"></i> Sıfırla
                        </button>
                    </div>
                </form>
              </div>
            </div><!-- .tab-content -->
          </div>
        </div>
      </div><!-- .container-fluid -->
    </main>

  <!-- İşlem Detayları Modal -->
  <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="transactionModalLabel" class="modal-title">İşlem Detayları</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- İşlem detayları burada yüklenecek -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Şifre Sıfırlama Modal -->
  <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Şifre Sıfırla</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/product/resetUserPassword/'.$user->id) ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Yeni Şifre</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                        <small class="form-text text-muted">En az 6 karakter olmalıdır.</small>
                    </div>
                    <div class="form-group">
                        <label>Şifre Tekrar</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-warning">Şifreyi Sıfırla</button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <!-- Grid.js CSS -->
  <link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
  
  <!-- Grid.js JavaScript -->
  <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

  <!-- Özel Scriptler -->
  <script>
    $(document).ready(function() {
      // İşlem geçmişi tablosu
      const transactionsGrid = new gridjs.Grid({
        columns: [
          { id: 'id', name: 'İşlem No' },
          { id: 'type', name: 'Tür' },
          { 
            id: 'price', 
            name: 'Tutar',
            formatter: (cell) => `${parseFloat(cell).toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL`
          },
          { id: 'date', name: 'Tarih' },
          { 
            id: 'status', 
            name: 'Durum',
            formatter: (cell, row) => {
              let badge = '';
              if (row.cells[1].data === 'Bakiye Yükleme') {
                if (cell === '0') badge = '<span class="badge badge-success">Başarılı</span>';
                else if (cell === '2') badge = '<span class="badge badge-danger">Başarısız</span>';
                else if (cell === '1') {
                  if (row.cells[6].data <= 5) badge = '<span class="badge badge-warning">Beklemede</span>';
                  else badge = '<span class="badge badge-danger">İptal Edildi</span>';
                }
              } else {
                if (cell === '0') badge = '<span class="badge badge-success">Başarılı</span>';
                else if (cell === '2') badge = '<span class="badge badge-danger">Başarısız</span>';
                else if (cell === '1') badge = '<span class="badge badge-warning">Beklemede</span>';
              }
              return gridjs.html(badge);
            }
          },
          {
            id: 'actions',
            name: 'İşlem',
            formatter: (cell, row) => {
              return gridjs.html(`
                <button type="button" class="user-history-btn-detail" onclick="showTransactionDetails(${row.cells[7].data})">
                  <span class="user-history-btn-icon"><i class="fas fa-info-circle"></i></span>
                  <span class="user-history-btn-text">Detay</span>
                </button>
              `);
            }
          },
          { id: 'minutes_passed', hidden: true },
          { id: 'id', hidden: true }
        ],
        data: <?= json_encode(array_map(function($transaction) {
          return [
            $transaction->id,
            $transaction->type == 'deposit' ? 'Bakiye Yükleme' : 'Ürün Alımı',
            $transaction->price,
            $transaction->date,
            $transaction->status,
            '',
            $transaction->minutes_passed,
            $transaction->id
          ];
        }, $transactions)) ?>,
        search: true,
        sort: true,
        pagination: {
          limit: 25
        },
        language: {
          search: {
            placeholder: 'Ara...'
          },
          pagination: {
            previous: 'Önceki',
            next: 'Sonraki',
            showing: 'Gösteriliyor',
            results: () => 'Kayıt',
            of: '/'
          }
        }
      }).render(document.getElementById('transactionsGrid'));

      // Filtreleme butonları
      $('.user-history-filter-card').click(function() {
        const filter = $(this).data('filter');
        $('.user-history-filter-card').removeClass('active');
        $(this).addClass('active');

        let filteredData = [];
        const allData = <?= json_encode(array_map(function($transaction) {
          return [
            $transaction->id,
            $transaction->type == 'deposit' ? 'Bakiye Yükleme' : 'Ürün Alımı',
            $transaction->price,
            $transaction->date,
            $transaction->status,
            '',
            $transaction->minutes_passed,
            $transaction->id
          ];
        }, $transactions)) ?>;

        if(filter === 'all') {
          filteredData = allData;
        } else if(filter === 'deposit') {
          filteredData = allData.filter(row => 
            row[1] === 'Bakiye Yükleme' && row[4] === '0'
          );
        } else if(filter === 'success') {
          filteredData = allData.filter(row => 
            row[1] !== 'Bakiye Yükleme' && row[4] === '0'
          );
        } else if(filter === 'failed') {
          filteredData = allData.filter(row => {
            const isNotDeposit = row[1] !== 'Bakiye Yükleme';
            const isFailed = row[4] === '2';
            const isCancelled = row[4] === '1' && row[6] > 5 && row[1] === 'Bakiye Yükleme';
            return isNotDeposit && (isFailed || isCancelled);
          });
        }

        transactionsGrid.updateConfig({
          data: filteredData
        }).forceRender();
      });

      // Referanslar tablosu
      new gridjs.Grid({
        columns: [
          { id: 'name', name: 'Referans Olan Kullanıcı' },
          { id: 'email', name: 'E-posta' },
          {
            id: 'actions',
            name: 'Detay',
            formatter: (cell, row) => {
              return gridjs.html(`
                <a href="<?= base_url('admin/product/userShopHistory/') ?>${row.cells[3].data}" class="btn btn-sm btn-info">
                  <i class="fas fa-user"></i>
                </a>
              `);
            }
          },
          { id: 'buyer_id', hidden: true }
        ],
        data: <?= json_encode(array_map(function($ref) {
          return [
            $ref->name . ' ' . $ref->surname,
            $ref->email,
            '',
            $ref->buyer_id
          ];
        }, $references)) ?>,
        search: true,
        sort: true,
        pagination: {
          limit: 10
        },
        language: {
          search: {
            placeholder: 'Ara...'
          },
          pagination: {
            previous: 'Önceki',
            next: 'Sonraki',
            showing: 'Gösteriliyor',
            results: () => 'Kayıt',
            of: '/'
          }
        }
      }).render(document.getElementById('referencesGrid'));

      // Abonelikler tablosu
      new gridjs.Grid({
        columns: [
          { id: 'subscription_name', name: 'Abonelik Adı' },
          { 
            id: 'remaining_days', 
            name: 'Kalan Gün',
            formatter: (cell) => {
              if (cell <= 0) return '<span class="badge badge-danger">Süresi Dolmuş</span>';
              return `${cell} gün`;
            }
          },
          { 
            id: 'total_spent', 
            name: 'Toplam Harcama',
            formatter: (cell) => `${parseFloat(cell).toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL`
          },
          { 
            id: 'total_earned', 
            name: 'Toplam Kazanç',
            formatter: (cell) => `${parseFloat(cell).toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL`
          },
          {
            id: 'actions',
            name: 'İşlemler',
            formatter: (cell, row) => {
              return gridjs.html(`
                <div class="btn-group">
                  <a href="<?= base_url('admin/subscription/ended_subscription/') ?>${row.cells[5].data}" class="btn btn-sm btn-danger">
                    <i class="fas fa-times"></i> Sonlandır
                  </a>
                </div>
              `);
            }
          },
          { id: 'user_id', hidden: true },
          { id: 'subscription_id', hidden: true }
        ],
        data: <?= json_encode(array_map(function($subscription) {
          return [
            $subscription->subscription_name,
            $this->M_Subscription->calculateRemainingDay($subscription->end_date),
            $subscription->total_spent ?? 0,
            $subscription->total_earned ?? 0,
            $subscription->user_id,
            $subscription->id
          ];
        }, $subscriptions)) ?>,
        search: true,
        sort: true,
        pagination: {
          limit: 10
        },
        language: {
          search: {
            placeholder: 'Ara...'
          },
          pagination: {
            previous: 'Önceki',
            next: 'Sonraki',
            showing: 'Gösteriliyor',
            results: () => 'Kayıt',
            of: '/'
          }
        }
      }).render(document.getElementById('subscriptionsGrid'));

      // Loglar tablosu
      new gridjs.Grid({
        columns: [
          { id: 'id', name: 'ID' },
          { id: 'date', name: 'Tarih' },
          { id: 'event', name: 'İşlem' },
          { id: 'function', name: 'Açıklama' },
          { id: 'user_ip', name: 'IP Adresi' }
        ],
        data: <?= json_encode(array_map(function($log) {
          return [
            $log->id,
            $log->date,
            $log->event,
            $log->function,
            $log->user_ip
          ];
        }, $last_logs)) ?>,
        search: true,
        sort: true,
        pagination: {
          limit: 10
        },
        language: {
          search: {
            placeholder: 'Ara...'
          },
          pagination: {
            previous: 'Önceki',
            next: 'Sonraki',
            showing: 'Gösteriliyor',
            results: () => 'Kayıt',
            of: '/'
          }
        }
      }).render(document.getElementById('logsGrid'));

      // Kullanıcı tipi değiştiğinde satıcı alanlarını göster/gizle
      $('select[name="type"]').change(function() {
        if($(this).val() == '2') {
          $('.seller-fields').show();
        } else {
          $('.seller-fields').hide();
        }
      });

      // Custom file input için dosya adını göster
      $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
      });

      // Şifre sıfırlama form validasyonu
      $('#resetPasswordModal form').on('submit', function(e) {
        var password = $('input[name="new_password"]').val();
        var confirm = $('input[name="confirm_password"]').val();
        
        if (password !== confirm) {
          e.preventDefault();
          alert('Şifreler eşleşmiyor!');
          return false;
        }
        
        if (password.length < 6) {
          e.preventDefault();
          alert('Şifre en az 6 karakter olmalıdır!');
          return false;
        }
      });

      // Bakiye Hareketleri tablosu
      $.ajax({
        url: '<?= base_url("admin/product/userWalletTransactions/".$user->id) ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          const walletTransactions = data.transactions || [];
          
          // Sayaçları güncelle
          $('#all-wallet-count').text(walletTransactions.length);
          $('#income-count').text(walletTransactions.filter(t => parseFloat(t.amount) > 0).length);
          $('#expense-count').text(walletTransactions.filter(t => parseFloat(t.amount) < 0).length);
          $('#transfer-count').text(walletTransactions.filter(t => t.transaction_type.includes('transfer')).length);
          
          const walletGrid = new gridjs.Grid({
            columns: [
              { id: 'id', name: 'İşlem No' },
              { 
                id: 'transaction_type', 
                name: 'İşlem Türü',
                formatter: (cell) => {
                  let label, icon, color;
                  
                  if (cell.includes('transfer_in')) {
                    label = 'Transfer Girişi';
                    icon = 'arrow-down';
                    color = 'success';
                  } else if (cell.includes('transfer_out')) {
                    label = 'Transfer Çıkışı';
                    icon = 'arrow-up';
                    color = 'danger';
                  } else if (cell === 'purchase') {
                    label = 'Satın Alma';
                    icon = 'shopping-cart';
                    color = 'danger';
                  } else if (cell === 'refund') {
                    label = 'İade';
                    icon = 'undo';
                    color = 'success';
                  } else if (cell === 'deposit') {
                    label = 'Bakiye Yükleme';
                    icon = 'plus-circle';
                    color = 'success';
                  } else if (cell === 'referral_bonus') {
                    label = 'Referans Bonusu';
                    icon = 'gift';
                    color = 'success';
                  } else if (cell === 'system_adjustment') {
                    label = 'Sistem Ayarlaması';
                    icon = 'cog';
                    color = 'info';
                  } else if (cell === 'subscription_fee') {
                    label = 'Abonelik Ücreti';
                    icon = 'credit-card';
                    color = 'danger';
                  } else {
                    label = cell;
                    icon = 'exchange-alt';
                    color = 'secondary';
                  }
                  
                  return gridjs.html(`<span class="badge badge-${color}"><i class="fas fa-${icon} mr-1"></i> ${label}</span>`);
                }
              },
              { 
                id: 'amount', 
                name: 'Tutar',
                formatter: (cell) => {
                  const amount = parseFloat(cell);
                  return gridjs.html(`<span class="text-${amount >= 0 ? 'success' : 'danger'}">${amount.toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL</span>`);
                }
              },
              { 
                id: 'balance_after', 
                name: 'İşlem Sonrası Bakiye',
                formatter: (cell) => {
                  return gridjs.html(`<span>${parseFloat(cell).toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL</span>`);
                }
              },
              { 
                id: 'balance_type', 
                name: 'Bakiye Türü',
                formatter: (cell) => {
                  if (cell === 'spendable') {
                    return gridjs.html('<span class="badge badge-primary">Kullanılabilir</span>');
                  } else if (cell === 'withdrawable') {
                    return gridjs.html('<span class="badge badge-info">Çekilebilir</span>');
                  }
                  return cell;
                }
              },
              {
                id: 'status',
                name: 'Durum',
                formatter: (cell) => {
                  let statusText, statusClass;
                  
                  switch(parseInt(cell)) {
                    case 0:
                      statusText = 'Beklemede';
                      statusClass = 'warning';
                      break;
                    case 1:
                      statusText = 'Başarılı';
                      statusClass = 'success';
                      break;
                    case 2:
                      statusText = 'Reddedildi';
                      statusClass = 'danger';
                      break;
                    default:
                      statusText = 'Belirsiz';
                      statusClass = 'secondary';
                  }
                  
                  return gridjs.html(`<span class="badge badge-${statusClass}">${statusText}</span>`);
                }
              },
              { id: 'created_at', name: 'Tarih' },
              { id: 'description', name: 'Açıklama' }
            ],
            data: walletTransactions.map(t => [
              t.id, 
              t.transaction_type, 
              t.amount,
              t.balance_after_transaction || 0,
              t.balance_type,
              t.status || 1,
              t.created_at,
              t.description || '-'
            ]),
            search: true,
            sort: true,
            pagination: {
              limit: 25
            },
            className: {
              table: 'wallet-table',
              thead: 'wallet-table-head',
              tbody: 'wallet-table-body',
              th: 'wallet-table-header',
              td: 'wallet-table-cell',
              container: 'wallet-grid-container',
              footer: 'wallet-grid-footer',
              pagination: 'wallet-grid-pagination'
            },
            language: {
              search: {
                placeholder: 'Ara...'
              },
              pagination: {
                previous: 'Önceki',
                next: 'Sonraki',
                showing: 'Gösteriliyor',
                results: () => 'Kayıt',
                of: '/'
              }
            }
          }).render(document.getElementById('walletTransactionsGrid'));
          
          // Bakiye hareketleri filtreleme
          $('.wallet-filter').click(function() {
            const filter = $(this).data('filter');
            $('.wallet-filter').removeClass('active');
            $(this).addClass('active');
            
            let filteredData = [];
            
            if (filter === 'all') {
              filteredData = walletTransactions.map(t => [
                t.id, t.transaction_type, t.amount, t.balance_after_transaction || 0, t.balance_type, t.status || 1, t.created_at, t.description || '-'
              ]);
            } else if (filter === 'in') {
              filteredData = walletTransactions
                .filter(t => parseFloat(t.amount) > 0)
                .map(t => [t.id, t.transaction_type, t.amount, t.balance_after_transaction || 0, t.balance_type, t.status || 1, t.created_at, t.description || '-']);
            } else if (filter === 'out') {
              filteredData = walletTransactions
                .filter(t => parseFloat(t.amount) < 0)
                .map(t => [t.id, t.transaction_type, t.amount, t.balance_after_transaction || 0, t.balance_type, t.status || 1, t.created_at, t.description || '-']);
            } else if (filter === 'transfer') {
              filteredData = walletTransactions
                .filter(t => t.transaction_type.includes('transfer'))
                .map(t => [t.id, t.transaction_type, t.amount, t.balance_after_transaction || 0, t.balance_type, t.status || 1, t.created_at, t.description || '-']);
            }
            
            walletGrid.updateConfig({
              data: filteredData
            }).forceRender();
          });

          // Kullanılabilir ve çekilebilir bakiye işlemlerini ayrı tablolarda gösterme
          const spendableTransactions = walletTransactions.filter(t => t.balance_type === 'spendable');
          const withdrawableTransactions = walletTransactions.filter(t => t.balance_type === 'withdrawable');
          
          // Kullanılabilir bakiye hareketleri tablosu
          new gridjs.Grid({
            columns: [
              { id: 'id', name: 'İşlem No' },
              { 
                id: 'transaction_type', 
                name: 'İşlem Türü',
                formatter: (cell) => {
                  let label, icon, color;
                  
                  if (cell.includes('transfer_in')) {
                    label = 'Transfer Girişi';
                    icon = 'arrow-down';
                    color = 'success';
                  } else if (cell.includes('transfer_out')) {
                    label = 'Transfer Çıkışı';
                    icon = 'arrow-up';
                    color = 'danger';
                  } else if (cell === 'purchase') {
                    label = 'Satın Alma';
                    icon = 'shopping-cart';
                    color = 'danger';
                  } else if (cell === 'refund') {
                    label = 'İade';
                    icon = 'undo';
                    color = 'success';
                  } else if (cell === 'deposit') {
                    label = 'Bakiye Yükleme';
                    icon = 'plus-circle';
                    color = 'success';
                  } else if (cell === 'referral_bonus') {
                    label = 'Referans Bonusu';
                    icon = 'gift';
                    color = 'success';
                  } else if (cell === 'system_adjustment') {
                    label = 'Sistem Ayarlaması';
                    icon = 'cog';
                    color = 'info';
                  } else if (cell === 'subscription_fee') {
                    label = 'Abonelik Ücreti';
                    icon = 'credit-card';
                    color = 'danger';
                  } else {
                    label = cell;
                    icon = 'exchange-alt';
                    color = 'secondary';
                  }
                  
                  return gridjs.html(`<span class="badge badge-${color}"><i class="fas fa-${icon} mr-1"></i> ${label}</span>`);
                }
              },
              { 
                id: 'amount', 
                name: 'Tutar',
                formatter: (cell) => {
                  const amount = parseFloat(cell);
                  return gridjs.html(`<span class="text-${amount >= 0 ? 'success' : 'danger'}">${amount.toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL</span>`);
                }
              },
              { 
                id: 'balance_after', 
                name: 'İşlem Sonrası Bakiye',
                formatter: (cell) => {
                  return gridjs.html(`<span>${parseFloat(cell).toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL</span>`);
                }
              },
              {
                id: 'status',
                name: 'Durum',
                formatter: (cell) => {
                  let statusText, statusClass;
                  
                  switch(parseInt(cell)) {
                    case 0:
                      statusText = 'Beklemede';
                      statusClass = 'warning';
                      break;
                    case 1:
                      statusText = 'Başarılı';
                      statusClass = 'success';
                      break;
                    case 2:
                      statusText = 'Reddedildi';
                      statusClass = 'danger';
                      break;
                    default:
                      statusText = 'Belirsiz';
                      statusClass = 'secondary';
                  }
                  
                  return gridjs.html(`<span class="badge badge-${statusClass}">${statusText}</span>`);
                }
              },
              { id: 'created_at', name: 'Tarih' },
              { id: 'description', name: 'Açıklama' }
            ],
            data: spendableTransactions.map(t => [
              t.id, 
              t.transaction_type, 
              t.amount,
              t.balance_after_transaction || 0,
              t.status || 1,
              t.created_at,
              t.description || '-'
            ]),
            search: true,
            sort: true,
            pagination: {
              limit: 15
            },
            className: {
              table: 'wallet-table',
              thead: 'wallet-table-head',
              tbody: 'wallet-table-body',
              th: 'wallet-table-header',
              td: 'wallet-table-cell',
              container: 'wallet-grid-container',
              footer: 'wallet-grid-footer',
              pagination: 'wallet-grid-pagination'
            },
            language: {
              search: {
                placeholder: 'Ara...'
              },
              pagination: {
                previous: 'Önceki',
                next: 'Sonraki',
                showing: 'Gösteriliyor',
                results: () => 'Kayıt',
                of: '/'
              }
            }
          }).render(document.getElementById('spendableTransactionsGrid'));
          
          // Çekilebilir bakiye hareketleri tablosu
          new gridjs.Grid({
            columns: [
              { id: 'id', name: 'İşlem No' },
              { 
                id: 'transaction_type', 
                name: 'İşlem Türü',
                formatter: (cell) => {
                  let label, icon, color;
                  
                  if (cell.includes('transfer_in')) {
                    label = 'Transfer Girişi';
                    icon = 'arrow-down';
                    color = 'success';
                  } else if (cell.includes('transfer_out')) {
                    label = 'Transfer Çıkışı';
                    icon = 'arrow-up';
                    color = 'danger';
                  } else if (cell === 'purchase') {
                    label = 'Satın Alma';
                    icon = 'shopping-cart';
                    color = 'danger';
                  } else if (cell === 'refund') {
                    label = 'İade';
                    icon = 'undo';
                    color = 'success';
                  } else if (cell === 'deposit') {
                    label = 'Bakiye Yükleme';
                    icon = 'plus-circle';
                    color = 'success';
                  } else if (cell === 'referral_bonus') {
                    label = 'Referans Bonusu';
                    icon = 'gift';
                    color = 'success';
                  } else if (cell === 'system_adjustment') {
                    label = 'Sistem Ayarlaması';
                    icon = 'cog';
                    color = 'info';
                  } else if (cell === 'subscription_fee') {
                    label = 'Abonelik Ücreti';
                    icon = 'credit-card';
                    color = 'danger';
                  } else {
                    label = cell;
                    icon = 'exchange-alt';
                    color = 'secondary';
                  }
                  
                  return gridjs.html(`<span class="badge badge-${color}"><i class="fas fa-${icon} mr-1"></i> ${label}</span>`);
                }
              },
              { 
                id: 'amount', 
                name: 'Tutar',
                formatter: (cell) => {
                  const amount = parseFloat(cell);
                  return gridjs.html(`<span class="text-${amount >= 0 ? 'success' : 'danger'}">${amount.toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL</span>`);
                }
              },
              { 
                id: 'balance_after', 
                name: 'İşlem Sonrası Bakiye',
                formatter: (cell) => {
                  return gridjs.html(`<span>${parseFloat(cell).toLocaleString('tr-TR', { minimumFractionDigits: 2 })} TL</span>`);
                }
              },
              {
                id: 'status',
                name: 'Durum',
                formatter: (cell) => {
                  let statusText, statusClass;
                  
                  switch(parseInt(cell)) {
                    case 0:
                      statusText = 'Beklemede';
                      statusClass = 'warning';
                      break;
                    case 1:
                      statusText = 'Başarılı';
                      statusClass = 'success';
                      break;
                    case 2:
                      statusText = 'Reddedildi';
                      statusClass = 'danger';
                      break;
                    default:
                      statusText = 'Belirsiz';
                      statusClass = 'secondary';
                  }
                  
                  return gridjs.html(`<span class="badge badge-${statusClass}">${statusText}</span>`);
                }
              },
              { id: 'created_at', name: 'Tarih' },
              { id: 'description', name: 'Açıklama' }
            ],
            data: withdrawableTransactions.map(t => [
              t.id, 
              t.transaction_type, 
              t.amount,
              t.balance_after_transaction || 0,
              t.status || 1,
              t.created_at,
              t.description || '-'
            ]),
            search: true,
            sort: true,
            pagination: {
              limit: 15
            },
            className: {
              table: 'wallet-table',
              thead: 'wallet-table-head',
              tbody: 'wallet-table-body',
              th: 'wallet-table-header',
              td: 'wallet-table-cell',
              container: 'wallet-grid-container',
              footer: 'wallet-grid-footer',
              pagination: 'wallet-grid-pagination'
            },
            language: {
              search: {
                placeholder: 'Ara...'
              },
              pagination: {
                previous: 'Önceki',
                next: 'Sonraki',
                showing: 'Gösteriliyor',
                results: () => 'Kayıt',
                of: '/'
              }
            }
          }).render(document.getElementById('withdrawableTransactionsGrid'));

          // Sekme stillerini ekle
          const tabStyles = document.createElement('style');
          tabStyles.textContent = `
            .wallet-transactions-tabs .nav-link {
              border-radius: 8px;
              padding: 10px 15px;
              font-weight: 500;
              color: #495057;
              transition: all 0.2s;
            }
            
            .wallet-transactions-tabs .nav-link.active {
              color: #fff;
              background-color: #007bff;
              box-shadow: 0 4px 10px rgba(0,123,255,0.2);
            }
            
            .wallet-transactions-tabs .nav-link:not(.active):hover {
              background-color: #f8f9fa;
            }
            
            .balance-info-card {
              background: #f8f9fa;
              border-radius: 8px;
              padding: 15px;
              box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            
            .balance-info-title {
              font-size: 1.2rem;
              font-weight: 600;
              margin-bottom: 5px;
            }
            
            .balance-info-description {
              color: #6c757d;
              font-size: 0.9rem;
            }
            
            .balance-amount {
              background: #fff;
              border-radius: 8px;
              padding: 8px 15px;
              display: inline-block;
              box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            }
            
            .balance-amount-label {
              color: #6c757d;
              margin-right: 8px;
            }
            
            .balance-amount-value {
              font-weight: 600;
              font-size: 1.2rem;
              color: #28a745;
            }
          `;
          document.head.appendChild(tabStyles);
        },
        error: function() {
          console.error('Bakiye hareketleri yüklenirken bir hata oluştu.');
          $('#walletTransactionsGrid').html('<div class="alert alert-danger">Bakiye hareketleri yüklenirken bir hata oluştu.</div>');
        }
      });
    });

    // İşlem Detayları Modal fonksiyonu
    function showTransactionDetails(id) {
      $.ajax({
        url: '<?= base_url("admin/product/getTransactionDetails/") ?>' + id,
        type: 'GET',
        success: function(response) {
          $('#transactionModal .modal-body').html(response);
          $('#transactionModal').modal('show');
        },
        error: function() {
          alert('İşlem detayları yüklenirken bir hata oluştu.');
        }
      });
    }
  </script>

</body>
</html>
