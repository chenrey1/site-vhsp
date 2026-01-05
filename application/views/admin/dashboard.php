<div id="layoutSidenav_content">
  <main>
    <div class="container-fluid">
      <div class="page-title">
      </div> <?php $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row(); ?> <?php if (isPerm($user->role_id, 'seeHome')): ?> <?php if ($update_info["status"] != false) { ?> <div class="update-now" data-toggle="modal" data-target="#modalUpdate">
        <h5>Güncelleme var, tıkla ve güncelle!</h5>
        <i class="fa fa-chevron-right"></i>
      </div> <?php } ?> <?php if ($update_info["exp_date"] != "0000-00-00"){
                    $endDate = date("d/m/Y",strtotime($update_info["exp_date"]));
                }else{
                    $endDate = "Sınırsız";
                } ?> <div class="stats-four">
        <div class="row g-4">
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="stat-card">
              <div class="stat-card__content">
                <div class="stat-card__icon-wrapper">
                  <div class="stat-card__icon bg-primary">
                    <i class="fa fa-coins"></i>
                  </div>
                </div>
                <div class="stat-card__info">
                  <h6 class="stat-card__title">Kazanç</h6>
                  <div class="stat-card__stats">
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Bugün</span>
                      <span class="stat-card__value"> <?= isset($todayInvoicesAmount) ? $todayInvoicesAmount : 0 ?>₺ </span>
                    </div>
                    <div class="stat-card__divider"></div>
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Toplam</span>
                      <span class="stat-card__value"> <?= isset($invoiceAllTimeAmount) ? $invoiceAllTimeAmount : 0 ?>₺ </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="stat-card">
              <div class="stat-card__content">
                <div class="stat-card__icon-wrapper">
                  <div class="stat-card__icon bg-success">
                    <i class="fa fa-shopping-basket"></i>
                  </div>
                </div>
                <div class="stat-card__info">
                  <h6 class="stat-card__title">Satış</h6>
                  <div class="stat-card__stats">
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Bugün</span>
                      <span class="stat-card__value"> <?= isset($invoiceTodaySell) ? $invoiceTodaySell : 0 ?> </span>
                    </div>
                    <div class="stat-card__divider"></div>
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Toplam</span>
                      <span class="stat-card__value"> <?= isset($invoiceAllTimeSell) ? $invoiceAllTimeSell : 0 ?> </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="stat-card">
              <div class="stat-card__content">
                <div class="stat-card__icon-wrapper">
                  <div class="stat-card__icon bg-warning">
                    <i class="fa fa-wallet"></i>
                  </div>
                </div>
                <div class="stat-card__info">
                  <h6 class="stat-card__title">Bakiye</h6>
                  <div class="stat-card__stats">
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Bugün</span>
                      <span class="stat-card__value"> <?= isset($todayBal) ? $todayBal : 0 ?>₺ </span>
                    </div>
                    <div class="stat-card__divider"></div>
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Toplam</span>
                      <span class="stat-card__value"> <?= isset($balanceAllTimeAmount) ? $balanceAllTimeAmount : 0 ?>₺ </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="stat-card">
              <div class="stat-card__content">
                <div class="stat-card__icon-wrapper">
                  <div class="stat-card__icon bg-info">
                    <i class="fa fa-user"></i>
                  </div>
                </div>
                <div class="stat-card__info">
                  <h6 class="stat-card__title">Üyeler</h6>
                  <div class="stat-card__stats">
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Bugün</span>
                      <span class="stat-card__value"> <?= isset($dayUsers) ? $dayUsers : 0 ?> </span>
                    </div>
                    <div class="stat-card__divider"></div>
                    <div class="stat-card__stat">
                      <span class="stat-card__label">Toplam</span>
                      <span class="stat-card__value"> <?= isset($allUsers) ? $allUsers : 0 ?> </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                  <div class="header-pulse me-3"></div>
                  <h5 class="mb-0 ml-2">Canlı Site Aktivitesi</h5>
                </div>
              </div>
              <div class="row g-4">
                <div class="col-md-3">
                  <div class="live-stats text-center">
                    <div class="live-count" id="onlineUserCount">0</div>
                    <div class="live-label mb-3">AKTİF ÜYELER</div>
                    <div class="live-compare">
                      <div class="compare-item">
                        <span class="compare-label">Geçen Gün Bu Saat</span>
                        <span class="compare-value">
                          <span id="yesterdayCount">0</span>
                          <small class="compare-change" id="yesterdayChange">0%</small>
                        </span>
                      </div>
                      <div class="compare-item mt-2">
                        <span class="compare-label">Geçen Hafta Bu Saat</span>
                        <span class="compare-value">
                          <span id="lastWeekCount">0</span>
                          <small class="compare-change" id="lastWeekChange">0%</small>
                        </span>
                      </div>
                    </div>
                    <div class="refresh-status mt-3">
                      <span class="timer-text">
                        <span id="refreshTimer">30</span>s
                      </span>
                      <button type="button" onclick="manualRefresh()" class="btn-refresh" title="Yenile">
                        <i class="fa fa-sync-alt"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="active-pages-section">
                    <h6 class="section-title">EN AKTİF SAYFALAR</h6>
                    <div id="activePages" class="active-pages-list">
                      <!-- JavaScript ile doldurulacak -->
                      <div id="noActiveUsers" class="no-active-users" style="display: none;">
                        <div class="empty-state">
                          <div class="empty-state-icon">
                            <i class="fa fa-user-clock"></i>
                          </div>
                          <div class="empty-state-content">
                            <h6>Şu an aktif üye bulunmuyor</h6>
                            <p>Aktif üyeler olduğunda burada en çok ziyaret edilen sayfalar listelenecektir.</p>
                            <div class="empty-state-action">
                              <button onclick="window.open('<?= base_url() ?>', '_blank')" class="preview-btn">
                                <i class="fa fa-external-link-alt me-2"></i>
                                <span>Siteyi Önizle</span>
                                <small class="preview-hint">Nasıl göründüğünü görmek için tıkla</small>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <button type="button" class="btn btn-view-details w-100" data-toggle="modal" data-target="#onlineUsersModal">
                      Detaylı Görüntüle
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal -->
      <div class="modal fade" id="onlineUsersModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Online Kullanıcı Detayları</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="modalContent">
              <!-- JavaScript ile doldurulacak -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-white">
              <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="daily-stats">
                  <h5 class="mb-3">Günlük İstatistikler</h5>
                  <div class="time-frame-selector mb-3">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-light active" data-value="daily">Günlük</button>
                      <button type="button" class="btn btn-light" data-value="weekly">Haftalık</button>
                      <button type="button" class="btn btn-light" data-value="monthly">Aylık</button>
                    </div>
                  </div>
                  <div class="stat-pills">
                    <span class="stat-pill success" onclick="updateChartType('successful')" role="button">
                      <i class="fa fa-check-circle"></i>
                      <div class="stat-pill__content">
                        <div class="stat-pill__title">Başarılı Satışlar</div>
                        <div class="stat-pill__stats">
                          <span id="successfulSales">0 adet</span>
                          <span class="stat-pill__divider">•</span>
                          <span id="successfulEarnings">0₺</span>
                        </div>
                      </div>
                    </span>
                    <span class="stat-pill danger" onclick="updateChartType('failed')" role="button">
                      <i class="fa fa-times-circle"></i>
                      <div class="stat-pill__content">
                        <div class="stat-pill__title">Başarısız Satışlar</div>
                        <div class="stat-pill__stats">
                          <span id="unsuccessfulSales">0 adet</span>
                          <span class="stat-pill__divider">•</span>
                          <span id="unsuccessfulEarnings">0₺</span>
                        </div>
                      </div>
                    </span>
                    <span class="stat-pill warning" onclick="updateChartType('cancelled')" role="button">
                      <i class="fa fa-exclamation-circle"></i>
                      <div class="stat-pill__content">
                        <div class="stat-pill__title">İptal Edilen Satışlar</div>
                        <div class="stat-pill__stats">
                          <span id="cancelledSales">0 adet</span>
                          <span class="stat-pill__divider">•</span>
                          <span id="cancelledEarnings">0₺</span>
                        </div>
                      </div>
                    </span>
                    <span class="stat-pill info" onclick="updateChartType('pending')" role="button">
                      <i class="fa fa-clock"></i>
                      <div class="stat-pill__content">
                        <div class="stat-pill__title">Bekleyen Ürünler</div>
                        <div class="stat-pill__stats">
                          <span id="pendingProducts">0 adet</span>
                          <span class="stat-pill__divider">•</span>
                          <span id="pendingEarnings">0₺</span>
                        </div>
                      </div>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div id="myChart"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-12">
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cron Sistemi</h5>
                <div class="d-flex align-items-center">
                  <?php if (isset($last_cron_run)): ?>
                    <button class="btn btn-sm btn-outline-success" id="run-cron-btn" onclick="manualRunCron()">
                      <i class="fas fa-play mr-1"></i> Manuel Çalıştır
                    </button>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-primary" onclick="setupCron()">
                      <i class="fas fa-cog mr-1"></i> Kurulumu Tamamla
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="cron-status-card">
                    <div class="cron-status-icon <?php echo isset($last_cron_run) ? 'active' : 'inactive'; ?>">
                      <i class="fas <?php echo isset($last_cron_run) ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    </div>
                    <div class="cron-status-info">
                      <?php if (isset($last_cron_run) && is_object($last_cron_run) && isset($last_cron_run->date)): 
                        $last_run_time = strtotime($last_cron_run->date);
                        $now = time();
                        $diff = $now - $last_run_time;
                        $status_class = $diff < 3600 ? 'text-success' : ($diff < 7200 ? 'text-warning' : 'text-danger');
                      ?>
                        <h6 class="mb-1">Son Çalışma Zamanı</h6>
                        <p class="mb-2 font-weight-bold"><?php echo date('d.m.Y H:i:s', $last_run_time); ?></p>
                        <div class="cron-time-ago <?php echo $status_class; ?>">
                          <i class="fas fa-history mr-1"></i>
                          <?php 
                            if ($diff < 60) {
                              echo 'Az önce';
                            } elseif ($diff < 3600) {
                              echo floor($diff / 60) . ' dakika önce';
                            } elseif ($diff < 86400) {
                              echo floor($diff / 3600) . ' saat önce';
                            } else {
                              echo floor($diff / 86400) . ' gün önce';
                            }
                          ?>
                        </div>
                      <?php else: ?>
                        <h6 class="mb-1">Cron Durumu</h6>
                        <p class="mb-2 text-warning">Henüz çalıştırılmadı</p>
                        <div class="cron-setup-info">
                          <a href="#" class="text-primary" data-toggle="modal" data-target="#cronHelpModal">
                            <i class="fas fa-info-circle mr-1"></i> Nasıl kurulur?
                          </a>
                        </div>
                      <?php endif; ?>
                      
                      <!-- Sonuç mesajı için div ekleyelim -->
                      <div id="cron-run-result" class="mt-2"></div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-8">
                  <?php if (isset($last_cron_run)): ?>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="cron-tasks">
                          <h6 class="cron-section-title">Görevler</h6>
                          <div class="cron-task-list">
                            <div class="cron-task">
                              <div class="cron-task-icon bg-success">
                                <i class="fas fa-truck"></i>
                              </div>
                              <div class="cron-task-info">
                                <div class="cron-task-name">API Ürün Teslimatı</div>
                                <div class="cron-task-time">Her 1 dakikada bir</div>
                              </div>
                            </div>
                            <div class="cron-task">
                              <div class="cron-task-icon bg-primary">
                                <i class="fas fa-envelope"></i>
                              </div>
                              <div class="cron-task-info">
                                <div class="cron-task-name">E-posta Gönderimi</div>
                                <div class="cron-task-time">Her 1 dakikada bir (5 gönderim)</div>
                              </div>
                            </div>
                            <div class="cron-task">
                              <div class="cron-task-icon bg-warning">
                              <i class="fas fa-sync-alt"></i>
                              </div>
                              <div class="cron-task-info">
                                <div class="cron-task-name">Abonelik Yenileme</div>
                                <div class="cron-task-time">Her 30 dakikada bir kez</div>
                              </div>
                            </div>
                            <div class="cron-task">
                              <div class="cron-task-icon bg-danger">
                                <i class="fas fa-times-circle"></i>
                              </div>
                              <div class="cron-task-info">
                                <div class="cron-task-name">Abonelik İptali</div>
                                <div class="cron-task-time">Günde bir kez</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="cron-history">
                          <h6 class="cron-section-title">Son Çalışma Geçmişi</h6>
                          <div class="cron-history-list">
                            <?php if (isset($cron_history) && !empty($cron_history)): 
                              foreach ($cron_history as $history): 
                                // Tüm kayıtları başarılı olarak göster
                                $history_status = 'success';
                                
                                // Tarih farkını hesapla
                                $history_time = strtotime($history->date);
                                $now = time();
                                $history_diff = $now - $history_time;
                                
                                // Olay metnini kısalt (30 karakterle sınırla)
                                $event_text = strlen($history->event) > 30 ? substr($history->event, 0, 30) . '...' : $history->event;
                            ?>
                              <div class="cron-history-item">
                                <div class="history-status-dot bg-<?php echo $history_status; ?>"></div>
                                <div class="history-info">
                                  <div class="history-date" title="<?php echo $history->event; ?>"><?php echo $event_text; ?></div>
                                </div>
                                <div class="history-status text-<?php echo $history_status; ?>">
                                  Başarılı
                                  <span class="ml-1 text-muted small">
                                    <?php 
                                      if ($history_diff < 60) {
                                        echo 'Az önce';
                                      } elseif ($history_diff < 3600) {
                                        echo floor($history_diff / 60) . ' dk önce';
                                      } elseif ($history_diff < 86400) {
                                        echo floor($history_diff / 3600) . ' sa önce';
                                      } else {
                                        echo floor($history_diff / 86400) . ' gün önce';
                                      }
                                    ?>
                                  </span>
                                </div>
                              </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle mr-1"></i> Henüz cron çalışma kaydı bulunmuyor.
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="cron-setup-guide">
                      <div class="setup-header">
                        <i class="fas fa-tasks"></i>
                        <h6>Cron Görevleri Nedir?</h6>
                      </div>
                      <p>Cron görevleri, sunucunuzda belirli aralıklarla otomatik olarak çalışan zamanlanmış işlemlerdir. Bu görevler sayesinde:</p>
                      <div class="setup-benefits">
                        <div class="benefit-item">
                          <i class="fas fa-check-circle"></i>
                          <span>Daha önce teslim edilmeyen api ürünlerinin teslimatı yapılır</span>
                        </div>
                        <div class="benefit-item">
                          <i class="fas fa-check-circle"></i>
                          <span>Bildirimler ve e-postalar zamanında gönderilir</span>
                        </div>
                        <div class="benefit-item">
                          <i class="fas fa-check-circle"></i>
                          <span>Süresi dolan abonelikler yenilenir ya da iptal edilir</span>
                        </div>
                      </div>
                      <div class="setup-action">
                        <button class="btn btn-primary" onclick="setupCron()">
                          <i class="fas fa-cog mr-1"></i> Kurulumu Başlat
                        </button>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                  <i class="fas fa-server mr-1"></i> Sunucu Saati: <?php echo date('H:i:s'); ?>
                </small>
                <small class="text-muted">
                  <i class="fas fa-info-circle mr-1"></i> Cron görevleri sistem performansını ve veri bütünlüğünü korumak için önemlidir.
                </small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Cron Yardım Modalı -->
        <div class="modal fade" id="cronHelpModal" tabindex="-1" role="dialog" aria-labelledby="cronHelpModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="cronHelpModalLabel">Cron Kurulum Rehberi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="cron-help-steps">
                  <div class="cron-help-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                      <h6>Sunucu Kontrol Paneline Giriş Yapın</h6>
                      <p>cPanel, Plesk veya VPS/Dedicated sunucunuzun kontrol paneline giriş yapın.</p>
                    </div>
                  </div>
                  <div class="cron-help-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                      <h6>Cron Job Bölümünü Bulun</h6>
                      <p>Kontrol panelinizde "Cron Jobs" veya "Zamanlanmış Görevler" bölümünü bulun.</p>
                    </div>
                  </div>
                  <div class="cron-help-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                      <h6>API Anahtarınızı Alın</h6>
                      <p>Siteniz için benzersiz API anahtarı:</p>
                      <div class="cron-command">
                        <code id="api-key-display"><?php echo hash('sha256', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'default_site'); ?></code>
                      </div>
                    </div>
                  </div>
                  <div class="cron-help-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                      <h6>Yeni Cron Job Ekleyin</h6>
                      <p>Aşağıdaki komutlardan birini ekleyin ve her <b>1 dakikada</b> bir çalışacak şekilde ayarlayın (*/1 * * * *):</p>
                      <div class="cron-command">
                        <code>wget -q -O /dev/null "<?php echo base_url('cronapi/run/' . hash('sha256', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'default_site')); ?>" >/dev/null 2>&1</code>
                      </div>
                      <p class="mt-2 small">veya curl kullanarak:</p>
                      <div class="cron-command">
                        <code>curl -s "<?php echo base_url('cronapi/run/' . hash('sha256', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'default_site')); ?>" > /dev/null</code>
                      </div>
                      <p class="mt-2 small">veya PHP CLI kullanarak:</p>
                      <div class="cron-command">
                        <code>php -q <?php echo FCPATH; ?>index.php cronapi run <?php echo hash('sha256', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'default_site'); ?></code>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                <!-- Test butonunu kaldırıyoruz -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
                if ($update_info["exp_date"] != "0000-00-00") {
                    $today = new DateTime();
                    $expiry = new DateTime($update_info["exp_date"]);
                    $remainingDays = $today->diff($expiry)->days;
                    $endDate = date("d/m/Y", strtotime($update_info["exp_date"]));
                    $totalDays = 365;
                    $percentage = ($remainingDays / $totalDays) * 100;

                    // Renk ve durum belirleme
                    if ($remainingDays > 180) {
                        $progressColor = "linear-gradient(to right, #11998e, #38ef7d)";
                        $status = "success";
                    } elseif ($remainingDays > 90) {
                        $progressColor = "linear-gradient(to right, #f2994a, #f2c94c)";
                        $status = "warning";
                    } else {
                        $progressColor = "linear-gradient(to right, #eb3349, #f45c43)";
                        $status = "danger";
                    }
                    ?> <div class="license-info-card">
        <div class="row align-items-center">
          <div class="col-auto">
            <div class="license-icon 
							<?= $status ?>">
              <i class="fa fa-shield-alt"></i>
            </div>
          </div>
          <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="d-flex align-items-center">
                <h6 class="mb-0 me-3">Lisans Sunucusu</h6>
                <span class="license-badge 
									<?= $update_info['is_offline'] != "false" ? 'license-badge-active' : 'license-badge-inactive' ?>"> <?= $update_info['is_offline'] != "false" ? 'Aktif' : 'Pasif' ?> </span>
              </div>
              <div class="license-info">
                <span class="text-
									<?= $status ?>">
                  <strong> <?= $remainingDays ?> </strong> gün </span>
                <span class="text-muted ms-2">( <?= $endDate ?>) </span>
              </div>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar" role="progressbar" style="width: 
								<?= $percentage ?>%; background: 
								<?= $progressColor ?>;" aria-valuenow="
								<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </div>
        </div>
      </div> <?php } else { ?> <div class="license-info-card">
        <div class="row align-items-center">
          <div class="col-auto">
            <div class="license-icon success">
              <i class="fa fa-infinity"></i>
            </div>
          </div>
          <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="d-flex align-items-center">
                <h6 class="mb-0 me-3">Lisans Sunucusu</h6>
                <span class="license-badge 
									<?= $update_info['is_offline'] != "false" ? 'license-badge-active' : 'license-badge-inactive' ?>"> <?= $update_info['is_offline'] != "false" ? 'Aktif' : 'Pasif' ?> </span>
              </div>
              <div class="license-info">
                <span class="text-success">Sınırsız Lisans</span>
              </div>
            </div>
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: 100%; background: linear-gradient(to right, #11998e, #38ef7d);" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>
      </div> <?php } ?> <?php endif ?>
    </div>
  </main>

    <!-- Global değişkenler -->
    <script>
        var BASE_URL = '<?= base_url() ?>';
        var ADMIN_URL = BASE_URL + 'admin/';
    </script>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- SweetAlert2 kütüphanesini dahil et -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Sayı formatlama fonksiyonları
        function formatMoney(amount, showFull = false) {
            if (showFull) {
                return new Intl.NumberFormat('tr-TR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount) + ' ₺';
            }
            
            if (amount >= 1000000) {
                return (amount / 1000000).toFixed(1).replace(/\.0$/, '') + 'M ₺';
            } else if (amount >= 1000) {
                return (amount / 1000).toFixed(1).replace(/\.0$/, '') + 'B ₺';
            } else {
                return amount + ' ₺';
            }
        }

        function formatNumber(number, showFull = false) {
            if (showFull) {
                return new Intl.NumberFormat('tr-TR').format(number);
            }
            
            if (number >= 1000000) {
                return (number / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            } else if (number >= 1000) {
                return (number / 1000).toFixed(1).replace(/\.0$/, '') + 'B';
            } else {
                return number.toString();
            }
        }

        // Sayıları formatla
        document.addEventListener('DOMContentLoaded', function() {
            const moneyElements = document.querySelectorAll('.stat-card__value');
            moneyElements.forEach(element => {
                const value = element.textContent.trim().replace('₺', '').trim();
                if (!isNaN(value) && value !== '0') {
                    const originalValue = parseFloat(value);
                    if (element.textContent.includes('₺')) {
                        element.textContent = formatMoney(originalValue);
                        // Tooltip ekle
                        element.setAttribute('title', formatMoney(originalValue, true));
                        element.style.cursor = 'help';
                    } else {
                        element.textContent = formatNumber(originalValue);
                        // Tooltip ekle
                        element.setAttribute('title', formatNumber(originalValue, true));
                        element.style.cursor = 'help';
                    }
                }
            });
        });

        var currentChartType = 'successful';
        var chart;
        var timeFrame = 'daily';
        var chartInitialized = false;

        function updateChartType(type) {
            if (currentChartType === type) return;
            
            currentChartType = type;
            
            document.querySelectorAll('.stat-pill').forEach(pill => {
                pill.classList.remove('active');
            });
            document.querySelector(`.stat-pill.${type === 'successful' ? 'success' : 
                                          type === 'failed' ? 'danger' : 
                                          type === 'cancelled' ? 'warning' : 'info'}`).classList.add('active');
            
            let chartTitle = '';
            switch(type) {
                case 'successful':
                    chartTitle = 'Başarılı Satışlar';
                    break;
                case 'failed':
                    chartTitle = 'Başarısız Satışlar';
                    break;
                case 'cancelled':
                    chartTitle = 'İptal Edilen Satışlar';
                    break;
                case 'pending':
                    chartTitle = 'Bekleyen Ürünler';
                    break;
            }

            if (chartInitialized) {
                let color = type === 'successful' ? '#1cc88a' :
                           type === 'failed' ? '#e74a3b' :
                           type === 'cancelled' ? '#f6c23e' : '#36b9cc';

                fetchEarningsData(timeFrame, type).then(chartData => {
                    chart.updateOptions({
                        title: {
                            text: chartTitle
                        },
                        colors: [color],
                        xaxis: {
                            categories: chartData.labels
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return formatMoney(value);
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return formatMoney(value);
                                }
                            }
                        }
                    });
                    
                    chart.updateSeries([{
                        name: chartTitle,
                        data: chartData.earnings
                    }]);
                });
            }
        }

        function updateTimeFrame(newTimeFrame) {
            if (timeFrame === newTimeFrame) return;
            
            timeFrame = newTimeFrame;
            
            document.querySelectorAll('.time-frame-selector .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.time-frame-selector .btn[data-value="${timeFrame}"]`).classList.add('active');
            
            loadInitialStats();
            
            fetchEarningsData(timeFrame, currentChartType).then(chartData => {
                if (!chartInitialized) {
                    initChart(chartData);
                } else {
                    updateChartData(chartData);
                }
            });
        }

        function updateChartData(chartData) {
            if (!chartInitialized) return;

            chart.updateOptions({
                xaxis: {
                    categories: chartData.labels
                }
            });
            
            chart.updateSeries([{
                name: currentChartType === 'successful' ? 'Başarılı Satışlar' :
                      currentChartType === 'failed' ? 'Başarısız Satışlar' :
                      currentChartType === 'cancelled' ? 'İptal Edilen Satışlar' : 'Bekleyen Ürünler',
                data: chartData.earnings
            }]);
        }

        function fetchEarningsData(timeFrame, type) {
            return fetch(`${BASE_URL}admin/API/get${type.charAt(0).toUpperCase() + type.slice(1)}Sales?timeFrame=${timeFrame}`)
                .then(response => response.json())
                .then(data => {
                    return {
                        labels: data.map(item => new Date(item.date).toLocaleDateString('tr-TR')),
                        earnings: data.map(item => parseFloat(item.total))
                    };
                });
        }

        function loadInitialStats() {
            fetch(`${BASE_URL}admin/API/getSalesStatus?timeFrame=${timeFrame}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('successfulSales').textContent = data.successful + ' adet';
                    document.getElementById('successfulEarnings').textContent = formatNumber(data.successfulEarnings) + '₺';
                    
                    document.getElementById('unsuccessfulSales').textContent = data.unsuccessful + ' adet';
                    document.getElementById('unsuccessfulEarnings').textContent = formatNumber(data.unsuccessfulEarnings) + '₺';
                    
                    document.getElementById('cancelledSales').textContent = data.cancelled + ' adet';
                    document.getElementById('cancelledEarnings').textContent = formatNumber(data.cancelledEarnings) + '₺';
                    
                    document.getElementById('pendingProducts').textContent = data.pending + ' adet';
                    document.getElementById('pendingEarnings').textContent = formatNumber(data.pendingEarnings) + '₺';
                });
        }

        function initChart(chartData) {
            if (chartInitialized) return;

            var options = {
                series: [{
                    name: 'Başarılı Satışlar',
                    data: chartData.earnings
                }],
                chart: {
                    type: 'area',
                    height: 400,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                title: {
                    text: 'Başarılı Satışlar',
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        fontWeight: 600,
                        color: '#2c3e50'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: [
                    currentChartType === 'successful' ? '#1cc88a' :
                    currentChartType === 'failed' ? '#e74a3b' :
                    currentChartType === 'cancelled' ? '#f6c23e' : '#36b9cc'
                ],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                xaxis: {
                    categories: chartData.labels,
                    labels: {
                        style: {
                            colors: '#6e7985',
                            fontSize: '12px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        show: true,
                        width: 1,
                        position: 'back',
                        opacity: 0.9,
                        stroke: {
                            color: '#b6b6b6',
                            width: 1,
                            dashArray: 3
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6e7985',
                            fontSize: '12px'
                        },
                        formatter: function(value) {
                            return formatMoney(value);
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'inherit'
                    },
                    y: {
                        formatter: function(value, { series, seriesIndex, dataPointIndex }) {
                            var currentValue = value;
                            var previousValue = dataPointIndex > 0 ? series[seriesIndex][dataPointIndex - 1] : currentValue;
                            var difference = currentValue - previousValue;
                            var percentage = previousValue !== 0 ? (difference / previousValue) * 100 : 0;

                            return [
                                '<div class="tooltip-title">Kazanç Detayı</div>',
                                '<div class="tooltip-row"><span>Toplam:</span> ' + formatMoney(currentValue, true) + '</div>',
                                '<div class="tooltip-row"><span>Kısaltma:</span> ' + formatMoney(currentValue) + '</div>',
                                '<div class="tooltip-row"><span>Fark:</span> ' + (difference >= 0 ? '+' : '') + formatMoney(difference, true) + '</div>',
                                '<div class="tooltip-row"><span>Değişim:</span> ' + (percentage >= 0 ? '+' : '') + percentage.toFixed(1) + '%</div>'
                            ].join('');
                        }
                    }
                },
                markers: {
                    size: 4,
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    strokeOpacity: 0.9,
                    strokeDashArray: 0,
                    fillOpacity: 1,
                    discrete: [],
                    shape: "circle",
                    radius: 2,
                    offsetX: 0,
                    offsetY: 0,
                    hover: {
                        size: 7
                    }
                }
            };

            try {
                chart = new ApexCharts(document.querySelector("#myChart"), options);
                chart.render().then(() => {
                    chartInitialized = true;
                    document.querySelector('.stat-pill.success').classList.add('active');
                });
            } catch (error) {
                console.error('Chart initialization error:', error);
                setTimeout(() => {
                    if (!chartInitialized) {
                        initChart(chartData);
                    }
                }, 3000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.time-frame-selector .btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    updateTimeFrame(this.dataset.value);
                });
            });
            
            loadInitialStats();
            fetchEarningsData(timeFrame, currentChartType).then(chartData => {
                initChart(chartData);
            }).catch(error => {
                console.error('Initial data fetch error:', error);
            });
        });

        // Karşılaştırma verilerini güncelleyen fonksiyon
        function updateComparisonData(data) {
            // Geçen gün karşılaştırması
            const yesterdayCount = document.getElementById('yesterdayCount');
            const yesterdayChange = document.getElementById('yesterdayChange');
            
            if (yesterdayCount) yesterdayCount.textContent = data.yesterdayCount;
            
            if (yesterdayChange && data.yesterdayCount > 0) {
                const yesterdayDiff = ((data.currentCount - data.yesterdayCount) / data.yesterdayCount * 100).toFixed(1);
                yesterdayChange.textContent = `${yesterdayDiff > 0 ? '+' : ''}${yesterdayDiff}%`;
                yesterdayChange.className = `compare-change ${yesterdayDiff >= 0 ? 'positive' : 'negative'}`;
            } else if (yesterdayChange) {
                yesterdayChange.textContent = '0%';
                yesterdayChange.className = 'compare-change';
            }

            // Geçen hafta karşılaştırması
            const lastWeekCount = document.getElementById('lastWeekCount');
            const lastWeekChange = document.getElementById('lastWeekChange');
            
            if (lastWeekCount) lastWeekCount.textContent = data.lastWeekCount;
            
            if (lastWeekChange && data.lastWeekCount > 0) {
                const lastWeekDiff = ((data.currentCount - data.lastWeekCount) / data.lastWeekCount * 100).toFixed(1);
                lastWeekChange.textContent = `${lastWeekDiff > 0 ? '+' : ''}${lastWeekDiff}%`;
                lastWeekChange.className = `compare-change ${lastWeekDiff >= 0 ? 'positive' : 'negative'}`;
            } else if (lastWeekChange) {
                lastWeekChange.textContent = '0%';
                lastWeekChange.className = 'compare-change';
            }
        }

        function updateActivePages(pages) {
            const activePagesContainer = document.getElementById('activePages');
            const noActiveUsers = document.getElementById('noActiveUsers');
            
            if (!pages || pages.length === 0) {
                if (noActiveUsers) {
                    noActiveUsers.style.display = 'block';
                }
                return;
            }

            if (noActiveUsers) {
                noActiveUsers.style.display = 'none';
            }

            let html = '<div class="active-pages-content">';
            pages.forEach(page => {
                html += `
                    <div class="active-page-item">
                        <div class="page-info">
                            <div class="page-title">${page.last_page || 'Ana Sayfa'}</div>
                        </div>
                        <div class="visitor-count">
                            <span class="count">${page.visit_count}</span>
                            <span class="label">ziyaretçi</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            activePagesContainer.innerHTML = html;
        }

        function updateOnlineUsers(count) {
            const onlineUserCount = document.getElementById('onlineUserCount');
            const noActiveUsers = document.getElementById('noActiveUsers');
            const activePages = document.getElementById('activePages');
            
            onlineUserCount.textContent = count;
            
            // Aktif üye sayısı sıfır ise uyarı mesajını göster
            if (parseInt(count) === 0) {
                if (noActiveUsers) {
                    noActiveUsers.style.display = 'block';
                    // Varsa mevcut aktif sayfa listesini gizle
                    const existingList = activePages.querySelector('.active-pages-content');
                    if (existingList) existingList.style.display = 'none';
                }
            } else {
                if (noActiveUsers) {
                    noActiveUsers.style.display = 'none';
                    // Varsa mevcut aktif sayfa listesini göster
                    const existingList = activePages.querySelector('.active-pages-content');
                    if (existingList) existingList.style.display = 'block';
                }
            }
        }

        function refreshData() {
            fetch(`${BASE_URL}admin/dashboard/getOnlineUsers`)
                .then(response => response.json())
                .then(data => {
                    updateOnlineUsers(data.count || 0);
                    if (data.comparisonData) {
                        updateComparisonData(data.comparisonData);
                    }
                    if (data.activePages) {
                        updateActivePages(data.activePages);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    updateOnlineUsers(0);
                });
        }

        // Sayfa yüklendiğinde ilk veriyi çek
        document.addEventListener('DOMContentLoaded', function() {
            refreshData();
            // Her 30 saniyede bir yenile
            setInterval(refreshData, 30000);
            
            // Zamanlayıcı güncelleme
            const timerElement = document.getElementById('refreshTimer');
            let timeLeft = 30;
            
            setInterval(() => {
                timeLeft -= 1;
                if (timeLeft <= 0) {
                    timeLeft = 30;
                }
                if (timerElement) {
                    timerElement.textContent = timeLeft;
                }
            }, 1000);
        });

        function manualRefresh() {
            // Zamanlayıcıyı sıfırla
            const timerElement = document.getElementById('refreshTimer');
            if (timerElement) {
                timerElement.textContent = '30';
            }
            
            // Yenileme ikonuna dönme animasyonu ekle
            const refreshIcon = document.querySelector('.btn-refresh i');
            if (refreshIcon) {
                refreshIcon.style.transform = 'rotate(360deg)';
                setTimeout(() => {
                    refreshIcon.style.transform = '';
                }, 1000);
            }
            
            // Verileri yenile
            refreshData();
        }
    </script>

    <!-- Online Users Modal Script -->
    <script>
        // Modal işlemleri
        $(document).on('show.bs.modal', '#onlineUsersModal', function() {
            let modalHtml = '';
            const loadingHtml = '<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>';
            $('#modalContent').html(loadingHtml);

            // Online kullanıcıları getir
            $.ajax({
                url: ADMIN_URL + 'API/getOnlineUsersDetails',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (!response || !response.length) {
                        $('#modalContent').html('<div class="alert alert-info">Aktif kullanıcı bulunamadı.</div>');
                        return;
                    }

                    // Kullanıcıları sayfalara göre grupla
                    const pageGroups = {};
                    response.forEach(user => {
                        const page = user.last_page || 'Ana Sayfa';
                        if (!pageGroups[page]) {
                            pageGroups[page] = [];
                        }
                        pageGroups[page].push(user);
                    });

                    // Her sayfa için kart oluştur
                    Object.keys(pageGroups).forEach(page => {
                        const users = pageGroups[page];
                        modalHtml += `
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fa fa-window-maximize me-2"></i> ${page}</span>
                                        <span class="badge badge-success">${users.length} üye</span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th>Kullanıcı</th>
                                                    <th>Rol</th>
                                                    <th>IP Adresi</th>
                                                    <th>Son Aktivite</th>
                                                    <th>Günlük Ziyaret</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                        users.forEach(user => {
                            modalHtml += `
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-info">
                                                <div class="fw-bold">${user.name} ${user.surname}</div>
                                                <div class="small text-muted">${user.email}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-${user.role_id == 1 ? 'danger' : 'info'}">${user.role_name}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">${user.ip_address}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted" title="${user.last_activity}">${user.last_activity_text}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">${user.daily_visits} ziyaret</span>
                                    </td>
                                </tr>`;
                        });

                        modalHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>`;
                    });

                    $('#modalContent').html(modalHtml);
                },
                error: function(xhr, status, error) {
                    console.error("Hata:", error);
                    $('#modalContent').html('<div class="alert alert-danger">Veriler yüklenirken bir hata oluştu.</div>');
                }
            });
        });
    </script>

    <style>
    /* Animasyonlar */
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes pulse-fade {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .btn-refresh i {
        transition: transform 1s ease;
    }

    .btn-refresh:hover i {
        transform: rotate(30deg);
    }

    /* Diğer stiller */
    .stat-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stat-card__content {
        padding: 1.25rem;
        display: flex;
        align-items: flex-start;
    }

    .stat-card__icon-wrapper {
        margin-right: 1rem;
        padding-top: 0.25rem;
    }

    .stat-card__icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-card__icon i {
        font-size: 1.25rem;
        color: #fff;
    }

    .stat-card__info {
        flex: 1;
    }

    .stat-card__title {
        margin: 0 0 0.75rem 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card__stats {
        display: flex;
        align-items: center;
    }

    .stat-card__stat {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .stat-card__label {
        font-size: 0.75rem;
        color: #95a5a6;
        margin-bottom: 0.25rem;
    }

    .stat-card__value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2c3e50;
        transition: all 0.3s ease;
        cursor: help;
        position: relative;
    }

    .stat-card__value:hover {
        color: #4e73df;
    }

    .stat-card__divider {
        width: 1px;
        height: 30px;
        background: rgba(0,0,0,0.1);
        margin: 0 1rem;
    }

    .bg-primary { background: linear-gradient(45deg, #4e73df, #224abe) !important; }
    .bg-success { background: linear-gradient(45deg, #1cc88a, #13855c) !important; }
    .bg-warning { background: linear-gradient(45deg, #f6c23e, #dda20a) !important; }
    .bg-info { background: linear-gradient(45deg, #36b9cc, #258391) !important; }

    @media (max-width: 576px) {
        .stat-card__content {
            flex-direction: column;
            text-align: center;
        }
        
        .stat-card__icon-wrapper {
            margin: 0 0 1rem 0;
        }
        
        .stat-card__icon {
            margin: 0 auto;
        }
    }

    .stat-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .stat-pill {
        display: inline-flex;
        align-items: flex-start;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        background: #f8f9fa;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }

    .stat-pill:hover {
        transform: translateY(-2px);
    }

    .stat-pill i {
        font-size: 1.25rem;
        margin-right: 0.75rem;
        margin-top: 0.25rem;
    }

    .stat-pill__content {
        display: flex;
        flex-direction: column;
    }

    .stat-pill__title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .stat-pill__stats {
        display: flex;
        align-items: center;
        color: #6c757d;
        font-size: 0.8125rem;
    }

    .stat-pill__divider {
        margin: 0 0.5rem;
        opacity: 0.5;
    }

    .stat-pill.success {
        background: rgba(28, 200, 138, 0.1);
    }

    .stat-pill.success i,
    .stat-pill.success .stat-pill__title {
        color: #1cc88a;
    }

    .stat-pill.danger {
        background: rgba(231, 74, 59, 0.1);
    }

    .stat-pill.danger i,
    .stat-pill.danger .stat-pill__title {
        color: #e74a3b;
    }

    .stat-pill.warning {
        background: rgba(246, 194, 62, 0.1);
    }

    .stat-pill.warning i,
    .stat-pill.warning .stat-pill__title {
        color: #f6c23e;
    }

    .stat-pill.info {
        background: rgba(54, 185, 204, 0.1);
    }

    .stat-pill.info i,
    .stat-pill.info .stat-pill__title {
        color: #36b9cc;
    }

    .stat-pill.active {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .activity-stats__number {
        font-size: 2.5rem;
        font-weight: 600;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .activity-stats__label {
        font-size: 0.875rem;
    }

    .active-pages {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
    }

    .pulse-dot {
        width: 10px;
        height: 10px;
        background: #1cc88a;
        border-radius: 50%;
        position: relative;
    }

    .pulse-dot::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(28, 200, 138, 0.4);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(3);
            opacity: 0;
        }
    }

    @media (max-width: 768px) {
        .stat-pills {
            margin-top: 1rem;
            width: 100%;
        }

        .stat-pill {
            width: calc(50% - 0.5rem);
            padding: 0.625rem 0.875rem;
        }

        .stat-pill i {
            font-size: 1.125rem;
            margin-right: 0.5rem;
        }

        .stat-pill__title {
            font-size: 0.8125rem;
        }

        .stat-pill__stats {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .stat-pill {
            width: 100%;
        }
    }

    /* Tooltip Stilleri */
    [title] {
        position: relative;
        text-decoration: none;
    }

    [title]:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 5px 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 5px;
    }

    [title]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: rgba(0, 0, 0, 0.8);
        margin-bottom: -5px;
    }

    .daily-stats {
        margin-bottom: 1rem;
    }

    .time-frame-selector .btn-group {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .time-frame-selector .btn {
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        background: #fff;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .time-frame-selector .btn:hover {
        background: #f8f9fa;
        color: #2c3e50;
    }

    .time-frame-selector .btn.active {
        background: #4e73df;
        color: #fff;
    }

    @media (max-width: 576px) {
        .time-frame-selector .btn {
            padding: 0.4rem 1rem;
            font-size: 0.8125rem;
        }
    }

    .activity-status .badge {
        padding: 0.5rem 1rem;
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 20px;
    }

    .refresh-timer {
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 8px;
    }

    .refresh-timer .badge {
        font-size: 0.875rem;
        padding: 0.35rem 0.65rem;
    }

    .active-pages {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        min-height: 150px;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: #1cc88a;
        border-radius: 50%;
        position: relative;
    }

    .pulse-dot::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(28, 200, 138, 0.4);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(3);
            opacity: 0;
        }
    }

    .activity-stats__number {
        font-size: 2.25rem;
        font-weight: 600;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .activity-stats__number {
            font-size: 1.75rem;
        }
        
        .refresh-timer {
            padding: 0.35rem;
        }
        
        .refresh-timer .badge {
            font-size: 0.75rem;
        }
        
        .active-pages {
            min-height: 120px;
        }
    }

    /* Canlı Site Aktivitesi Stilleri */
    .header-pulse {
        width: 12px;
        height: 12px;
        background: #00c853;
        border-radius: 50%;
        position: relative;
    }

    .header-pulse::before,
    .header-pulse::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 200, 83, 0.4);
        border-radius: 50%;
        animation: pulse-ring 3s linear infinite;
    }

    .header-pulse::after {
        animation-delay: 1.5s;
    }

    @keyframes pulse-ring {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }
        50% {
            transform: scale(2.5);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 0.8;
        }
    }

    .live-stats {
        padding: 1.5rem;
        background: #f8fafb;
        border-radius: 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .live-count {
        font-size: 2.75rem;
        font-weight: 600;
        color: #00c853;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .live-label {
        color: #637381;
        font-size: 0.813rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .live-label::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 2px;
        background: #e0e0e0;
    }

    .live-compare {
        width: 100%;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
    }

    .compare-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
    }

    .compare-label {
        color: #637381;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .compare-value {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #2c3e50;
    }

    .compare-change {
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 12px;
        background: #f5f5f5;
        color: #637381;
    }

    .compare-change.positive {
        background: #e8f5e9;
        color: #00c853;
    }

    .compare-change.negative {
        background: #ffebee;
        color: #f44336;
    }

    .refresh-status {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.8);
        padding: 6px 12px;
        border-radius: 16px;
        gap: 8px;
        margin-top: auto;
    }

    .timer-text {
        color: #637381;
        font-size: 0.813rem;
    }

    .btn-refresh {
        background: none;
        border: none;
        color: #637381;
        padding: 4px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .btn-refresh:hover {
        transform: rotate(30deg);
        color: #00c853;
    }

    .section-title {
        color: #637381;
        font-size: 0.813rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }

    .active-pages-list {
        background: #f8fafb;
        border-radius: 10px;
        padding: 1.25rem;
        min-height: 180px;
        margin-bottom: 1rem;
        height: calc(100% - 70px);
    }

    .btn-view-details {
        background: #fff;
        border: 1px solid #e0e0e0;
        color: #637381;
        padding: 0.5rem;
        font-size: 0.813rem;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .btn-view-details:hover {
        background: #f8fafb;
        border-color: #00c853;
        color: #00c853;
    }

    @media (max-width: 768px) {
        .live-count {
            font-size: 2.25rem;
        }

        .live-stats {
            padding: 1.25rem;
        }

        .live-compare {
            padding: 0.5rem;
        }

        .compare-item {
            padding: 0.35rem 0;
        }

        .refresh-status {
            padding: 5px 10px;
        }

        .active-pages-list {
            min-height: 150px;
        }
    }

    .preview-btn {
        background: linear-gradient(135deg, #00c853 0%, #00b248 100%);
        border: none;
        color: #fff;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(0, 200, 83, 0.2);
        position: relative;
    }

    .preview-btn span {
        position: relative;
        z-index: 1;
    }

    .preview-btn i {
        font-size: 0.875rem;
        position: relative;
        z-index: 1;
    }

    .preview-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 200, 83, 0.3);
    }

    .preview-btn:hover::before {
        opacity: 1;
    }

    .preview-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #00b248 0%, #00c853 100%);
        border-radius: 25px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .preview-hint {
        position: absolute;
        bottom: -24px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.75rem;
        color: #637381;
        white-space: nowrap;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .preview-btn:hover + .preview-hint {
        opacity: 1;
        bottom: -28px;
    }

    @media (max-width: 768px) {
        .empty-state {
            padding: 1.5rem;
            min-height: 150px;
        }

        .empty-state-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
        }

        .empty-state-icon i {
            font-size: 1.5rem;
        }

        .empty-state h6 {
            font-size: 1rem;
        }

        .empty-state p {
            font-size: 0.813rem;
            margin-bottom: 1.25rem;
        }

        .preview-btn {
            padding: 10px 20px;
            font-size: 0.813rem;
        }
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 180px;
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 200, 83, 0.1);
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(0, 200, 83, 0.1) 0%, rgba(0, 200, 83, 0.05) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .empty-state-icon::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px dashed rgba(0, 200, 83, 0.2);
        animation: rotate 30s linear infinite;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .empty-state-icon i {
        font-size: 2rem;
        color: #00c853;
        animation: pulse-fade 2s ease-in-out infinite;
    }

    .empty-state-content {
        max-width: 320px;
    }

    .empty-state h6 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
    }

    .empty-state p {
        font-size: 0.875rem;
        color: #637381;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .empty-state-action {
        position: relative;
    }

    .active-page-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .active-page-item:hover {
        transform: translateX(5px);
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .active-page-item:last-child {
        margin-bottom: 0;
    }

    .page-info {
        flex: 1;
        min-width: 0;
    }

    .page-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .page-url {
        color: #637381;
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .visitor-count {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-left: 16px;
    }

    .visitor-count .count {
        font-weight: 600;
        color: #00c853;
        font-size: 1rem;
    }

    .visitor-count .label {
        color: #637381;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .active-page-item {
            padding: 10px;
        }

        .page-title {
            font-size: 0.813rem;
        }

        .page-url {
            font-size: 0.688rem;
        }

        .visitor-count .count {
            font-size: 0.875rem;
        }

        .visitor-count .label {
            font-size: 0.688rem;
        }
    }

    /* Mobil Uyum için Ek Stiller */
    @media (max-width: 991.98px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .stat-card__content {
            padding: 1rem;
        }
        
        .stat-card__icon {
            width: 36px;
            height: 36px;
        }
        
        .stat-card__icon i {
            font-size: 1rem;
        }
        
        .stat-card__title {
            font-size: 0.8rem;
        }
        
        .stat-card__value {
            font-size: 1rem;
        }
        
        .stat-card__label {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 575.98px) {
        .stat-card__content {
            flex-direction: row;
            align-items: center;
        }
        
        .stat-card__icon-wrapper {
            margin-right: 0.75rem;
            margin-bottom: 0;
        }
        
        .stat-card__info {
            flex: 1;
        }
        
        .stat-card__stats {
            margin-top: 0.25rem;
        }
    }
    </style>

    <!-- Cron Sistemi Script -->
    <script>
        function copyCommand(button) {
            // Mevcut kopyalama fonksiyonu
        }

        function setupCron() {
            // Cron kurulum modalını göster
            $('#cronHelpModal').modal('show');
        }

        // testCronSetup fonksiyonunu ve ilgili AJAX kodlarını kaldırıyoruz
        
        // Manuel cron çalıştırma butonu
        $(document).ready(function() {
            $('#run-cron-btn').on('click', function() {
                manualRunCron();
            });
        });
    </script>

    <style>
    /* Cron Sistemi Stilleri */
    .cron-status-card {
        display: flex;
        align-items: flex-start;
        padding: 0.5rem 0;
    }
    
    .cron-status-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .cron-status-icon.active {
        background: linear-gradient(135deg, #1cc88a, #13855c);
        box-shadow: 0 4px 10px rgba(28, 200, 138, 0.3);
    }
    
    .cron-status-icon.inactive {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
        box-shadow: 0 4px 10px rgba(246, 194, 62, 0.3);
    }
    
    .cron-status-icon i {
        font-size: 1.5rem;
        color: #fff;
    }
    
    .cron-status-info {
        flex: 1;
    }
    
    .cron-status-info h6 {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .cron-status-info p {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .cron-time-ago {
        font-size: 0.813rem;
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        background: rgba(28, 200, 138, 0.1);
    }
    
    .cron-time-ago.text-warning {
        background: rgba(246, 194, 62, 0.1);
    }
    
    .cron-time-ago.text-danger {
        background: rgba(231, 74, 59, 0.1);
    }
    
    .cron-setup-info {
        font-size: 0.813rem;
    }
    
    .cron-section-title {
        font-size: 0.813rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .cron-task-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .cron-task {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .cron-task:hover {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transform: translateX(5px);
    }
    
    .cron-task-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .cron-task-icon i {
        font-size: 0.875rem;
        color: #fff;
    }
    
    .cron-task-info {
        flex: 1;
    }
    
    .cron-task-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.125rem;
    }
    
    .cron-task-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    /* Cron Geçmişi Stilleri */
    .cron-history-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .cron-history-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .cron-history-item:hover {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .history-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .history-info {
        flex: 1;
    }
    
    .history-date {
        font-size: 0.813rem;
        font-weight: 600;
        color: #2c3e50;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    
    .history-ago {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .history-status {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        background: #f8f9fa;
        white-space: nowrap;
    }
    
    /* Cron Kurulum Rehberi Stilleri */
    .cron-setup-guide {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        height: 100%;
    }
    
    .setup-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .setup-header i {
        font-size: 1.5rem;
        color: #4e73df;
        margin-right: 0.75rem;
    }
    
    .setup-header h6 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .cron-setup-guide p {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    .setup-benefits {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .benefit-item {
        display: flex;
        align-items: center;
    }
    
    .benefit-item i {
        color: #1cc88a;
        margin-right: 0.5rem;
    }
    
    .benefit-item span {
        font-size: 0.875rem;
        color: #2c3e50;
    }
    
    .setup-action {
        text-align: center;
    }
    
    /* Cron Yardım Modal Stilleri */
    .cron-help-steps {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .cron-help-step {
        display: flex;
        align-items: flex-start;
    }
    
    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #4e73df;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .step-content {
        flex: 1;
    }
    
    .step-content h6 {
        font-size: 0.938rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .step-content p {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .cron-command {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.5rem;
        position: relative;
    }
    
    .cron-command code {
        font-size: 0.813rem;
        color: #e83e8c;
        word-break: break-all;
        flex: 1;
    }
    
    .btn-copy {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        margin-left: 0.5rem;
    }
    
    .btn-copy:hover {
        background: #e9ecef;
        color: #4e73df;
    }
    
    .btn-copy.copied {
        background: #1cc88a;
        color: #fff;
    }
    
    @media (max-width: 768px) {
        .cron-status-icon {
            width: 40px;
            height: 40px;
        }
        
        .cron-status-icon i {
            font-size: 1.25rem;
        }
        
        .cron-status-info h6 {
            font-size: 0.813rem;
        }
        
        .cron-status-info p {
            font-size: 0.938rem;
        }
        
        .cron-time-ago {
            font-size: 0.75rem;
        }
        
        .cron-task-icon {
            width: 28px;
            height: 28px;
        }
        
        .cron-task-name {
            font-size: 0.813rem;
        }
        
        .cron-task-time {
            font-size: 0.688rem;
        }
        
        .cron-history-item {
            padding: 0.375rem;
        }
        
        .history-date {
            font-size: 0.75rem;
        }
        
        .history-ago {
            font-size: 0.688rem;
        }
        
        .history-status {
            font-size: 0.688rem;
            padding: 0.188rem 0.375rem;
        }
    }
    </style>

    <!-- Cron kurulum rehberi için JavaScript -->
    <script>
        // Cron test butonuna tıklandığında
        $(document).ready(function() {
            // Cron test butonuna tıklandığında
            $('#test-cron-btn').on('click', function() {
                $.ajax({
                    url: '<?= base_url("admin/dashboard/test_cron") ?>',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#cron-test-result').html('<div class="alert alert-info">Cron testi yapılıyor...</div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            var html = '';
                            
                            // Cron durumu kartı
                            var statusClass = response.data.cron_status.is_working ? 'success' : (response.data.cron_status.is_setup ? 'warning' : 'danger');
                            var statusIcon = response.data.cron_status.is_working ? 'check-circle' : (response.data.cron_status.is_setup ? 'exclamation-triangle' : 'times-circle');
                            
                            html += '<div class="alert alert-' + statusClass + '">';
                            html += '<i class="fas fa-' + statusIcon + ' mr-2"></i> ';
                            html += response.data.cron_status.message;
                            html += '</div>';
                            
                            // Son çalışma zamanı
                            if (response.data.cron_status.last_run) {
                                html += '<div class="mb-3">';
                                html += '<strong>Son Çalışma Zamanı:</strong> ' + response.data.cron_status.last_run;
                                html += '</div>';
                            }
                            
                            // Kurulum bilgileri kartı
                            html += '<div class="card mt-3">';
                            html += '<div class="card-header">Cron Kurulum Bilgileri</div>';
                            html += '<div class="card-body">';
                            
                            // API Key bilgisi
                            html += '<div class="form-group">';
                            html += '<label>API Key:</label>';
                            html += '<div class="input-group">';
                            html += '<input type="text" class="form-control" value="' + response.data.api_key + '" readonly id="api-key-input">';
                            html += '</div></div>';
                            
                            // Cron URL bilgisi
                            html += '<div class="form-group mt-3">';
                            html += '<label>Cron URL:</label>';
                            html += '<div class="input-group">';
                            html += '<input type="text" class="form-control" value="' + response.data.cron_url + '" readonly id="cron-url-input">';
                            html += '</div></div>';
                            
                            // Cron komutları
                            html += '<div class="mt-4"><h5>Cron Komutları</h5></div>';
                            
                            // wget komutu
                            html += '<div class="form-group mt-2">';
                            html += '<label>wget ile:</label>';
                            html += '<div class="input-group">';
                            html += '<input type="text" class="form-control" value="' + response.data.commands.wget + '" readonly id="wget-command">';
                            html += '</div></div>';
                            
                            // curl komutu
                            html += '<div class="form-group mt-2">';
                            html += '<label>curl ile:</label>';
                            html += '<div class="input-group">';
                            html += '<input type="text" class="form-control" value="' + response.data.commands.curl + '" readonly id="curl-command">';
                            html += '</div></div>';
                            
                            // php komutu
                            html += '<div class="form-group mt-2">';
                            html += '<label>php ile:</label>';
                            html += '<div class="input-group">';
                            html += '<input type="text" class="form-control" value="' + response.data.commands.php + '" readonly id="php-command">';
                            html += '</div></div>';
                            
                            // Cache durumu
                            if (Object.keys(response.data.cache_status).length > 0) {
                                html += '<div class="mt-4"><h5>Cron Görevleri Durumu</h5></div>';
                                html += '<div class="table-responsive">';
                                html += '<table class="table table-sm table-bordered">';
                                html += '<thead><tr><th>Görev</th><th>Son Çalışma</th><th>Durum</th></tr></thead>';
                                html += '<tbody>';
                                
                                for (var job in response.data.cache_status) {
                                    var jobStatus = response.data.cache_status[job];
                                    var ageClass = jobStatus.age_minutes <= 15 ? 'success' : (jobStatus.age_minutes <= 60 ? 'warning' : 'danger');
                                    var statusText = jobStatus.age_minutes <= 15 ? 'Aktif' : (jobStatus.age_minutes <= 60 ? 'Uyarı' : 'Pasif');
                                    
                                    html += '<tr>';
                                    html += '<td>' + job + '</td>';
                                    html += '<td>' + jobStatus.last_run + '</td>';
                                    html += '<td><span class="badge badge-' + ageClass + '">' + statusText + '</span></td>';
                                    html += '</tr>';
                                }
                                
                                html += '</tbody></table></div>';
                            }
                            
                            // Kurulum talimatları
                            html += '<div class="alert alert-info mt-4">';
                            html += '<h5>Kurulum Talimatları:</h5>';
                            html += '<ol>';
                            html += '<li>Yukarıdaki API anahtarı, sitenizin alan adının SHA256 ile şifrelenmiş halidir.</li>';
                            html += '<li>Yukarıdaki komutlardan birini seçip kopyalayın.</li>';
                            html += '<li>Hosting kontrol panelinizden cron job ayarlarına gidin.</li>';
                            html += '<li>Yeni bir cron job ekleyin ve komutu yapıştırın.</li>';
                            html += '<li>Cron job\'u her <b>1 dakikada</b> bir çalışacak şekilde ayarlayın (*/1 * * * *).</li>';
                            html += '</ol>';
                            html += '</div>';
                            
                            html += '</div></div>';
                            
                            $('#cron-test-result').html(html);
                        } else {
                            $('#cron-test-result').html('<div class="alert alert-danger">Hata: ' + response.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#cron-test-result').html('<div class="alert alert-danger">Sunucu hatası! Lütfen daha sonra tekrar deneyin.</div>');
                    }
                });
            });
            
            // Manuel cron çalıştırma butonu
            $('#run-cron-btn').on('click', function() {
                $.ajax({
                    url: '<?= base_url("admin/dashboard/run_cron") ?>',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#cron-run-result').html('<div class="alert alert-info">Cron görevleri çalıştırılıyor...</div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#cron-run-result').html('<div class="alert alert-success">Cron görevleri başarıyla çalıştırıldı.</div>');
                        } else {
                            $('#cron-run-result').html('<div class="alert alert-danger">Hata: ' + response.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#cron-run-result').html('<div class="alert alert-danger">Sunucu hatası! Lütfen daha sonra tekrar deneyin.</div>');
                    }
                });
            });
        });
    </script>

    <!-- Clipboard.js kütüphanesini ekleyin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

    <!-- Modal açıldığında Clipboard.js'i yeniden initialize et -->
    <script>
        $('#cronHelpModal').on('shown.bs.modal', function () {
            // Önceki clipboard instance'ını temizle
            if (window.cronClipboard) {
                window.cronClipboard.destroy();
            }
            
            // Yeni bir clipboard instance'ı oluştur
            window.cronClipboard = new ClipboardJS('.cron-command .btn-copy', {
                text: function(trigger) {
                    // Butonun yanındaki code elementinin içeriğini al
                    return trigger.previousElementSibling.textContent.trim();
                }
            });
            
            // Başarılı kopyalama için event listener
            window.cronClipboard.on('success', function(e) {
                // Kopyalandı efekti
                const button = e.trigger;
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('copied');
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('copied');
                }, 2000);
                e.clearSelection();
            });
            
            // Hata durumu için event listener
            window.cronClipboard.on('error', function(e) {
                console.error('Kopyalama hatası:', e.action);
                alert('Kopyalama işlemi başarısız oldu. Lütfen metni manuel olarak seçip kopyalayın.');
            });
        });

        // Modal kapandığında Clipboard.js instance'ını temizle
        $('#cronHelpModal').on('hidden.bs.modal', function () {
            if (window.cronClipboard) {
                window.cronClipboard.destroy();
                window.cronClipboard = null;
            }
        });
    </script>

    <script>
        // Sayfa yüklendiğinde Clipboard.js'i initialize et
        $(document).ready(function() {
            // Global clipboard instance
            window.mainClipboard = new ClipboardJS('.copy-btn');
            
            window.mainClipboard.on('success', function(e) {
                $(e.trigger).text('Kopyalandı!');
                setTimeout(function() {
                    $(e.trigger).html('<i class="fas fa-copy"></i>');
                }, 2000);
                e.clearSelection();
            });
            
            window.mainClipboard.on('error', function(e) {
                $(e.trigger).text('Hata!');
                setTimeout(function() {
                    $(e.trigger).html('<i class="fas fa-copy"></i>');
                }, 2000);
            });
        });
    </script>

    <!-- Daha basit ve güvenilir bir kopyalama fonksiyonu -->
    <script>
        function simpleCopy(text) {
            // Geçici bir textarea elementi oluştur
            const textarea = document.createElement('textarea');
            textarea.value = text;
            
            // Textarea'yı görünmez yap ve sayfaya ekle
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            
            // Metni seç ve kopyala
            textarea.select();
            let success = false;
            
            try {
                success = document.execCommand('copy');
            } catch (err) {
                console.error('Kopyalama hatası:', err);
            }
            
            // Textarea'yı kaldır
            document.body.removeChild(textarea);
            
            return success;
        }

        // Kopyalama butonları için event listener
        $(document).ready(function() {
            // API key kopyalama butonu
            $(document).on('click', '.btn-copy', function() {
                const button = $(this);
                const text = button.data('copy-text') || button.prev('code').text().trim();
                
                if (simpleCopy(text)) {
                    // Başarılı kopyalama efekti
                    const originalHtml = button.html();
                    button.html('<i class="fas fa-check"></i>');
                    
                    setTimeout(function() {
                        button.html(originalHtml);
                    }, 2000);
                } else {
                    // Hata durumunda
                    alert('Kopyalama işlemi başarısız oldu. Lütfen metni manuel olarak seçip kopyalayın.');
                }
            });
            
            // Test sonuçları için kopyalama butonları
            $(document).on('click', '.copy-btn', function() {
                const button = $(this);
                const targetId = button.data('clipboard-target');
                let text = '';
                
                if (targetId) {
                    // Hedef element varsa onun içeriğini al
                    text = $(targetId).val() || $(targetId).text();
                } else {
                    // Yoksa bir önceki elementin içeriğini al
                    text = button.closest('.input-group').find('input').val();
                }
                
                if (simpleCopy(text.trim())) {
                    // Başarılı kopyalama efekti
                    const originalText = button.text();
                    button.text('Kopyalandı!');
                    
                    setTimeout(function() {
                        button.text(originalText);
                    }, 2000);
                } else {
                    // Hata durumunda
                    button.text('Hata!');
                    setTimeout(function() {
                        button.text('Kopyala');
                    }, 2000);
                }
            });
        });
    </script>

    <!-- Alternatif olarak, manualRunCron fonksiyonunu tekrar tanımlayabiliriz -->
    <script>
    function manualRunCron() {
        // Butona dönme ikonu ekle
        var $button = $('#run-cron-btn');
        var originalHtml = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin mr-1"></i> Çalıştırılıyor...');
        $button.prop('disabled', true);
        
        $.ajax({
            url: '<?= base_url("admin/dashboard/run_cron") ?>',
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#cron-run-result').html('<div class="alert alert-info d-flex align-items-center"><i class="fas fa-info-circle mr-2"></i><span>Cron görevleri çalıştırılıyor...</span></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#cron-run-result').html('<div class="alert alert-success d-flex align-items-center"><i class="fas fa-check-circle mr-2"></i><span>Cron görevleri başarıyla çalıştırıldı.</span></div>');
                    // Başarılı olduğunda sayfayı yenile
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $('#cron-run-result').html('<div class="alert alert-danger d-flex align-items-center"><i class="fas fa-times-circle mr-2"></i><span>Hata: ' + response.message + '</span></div>');
                    // Butonu eski haline getir
                    $button.html(originalHtml);
                    $button.prop('disabled', false);
                }
            },
            error: function() {
                $('#cron-run-result').html('<div class="alert alert-danger d-flex align-items-center"><i class="fas fa-times-circle mr-2"></i><span>Sunucu hatası! Lütfen daha sonra tekrar deneyin.</span></div>');
                // Butonu eski haline getir
                $button.html(originalHtml);
                $button.prop('disabled', false);
            }
        });
    }

    // Sayfa yüklendiğinde buton için event listener ekleyelim
    $(document).ready(function() {
        $('#run-cron-btn').on('click', function() {
            manualRunCron();
        });
    });
    </script>