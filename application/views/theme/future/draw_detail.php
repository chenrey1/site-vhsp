<?php $this->load->view('theme/future/includes/header'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    :root {
        --background-color: #ffffff;
        --text-color: #212529;
        --header-bg: #f8f9fa;
        --header-shadow: rgba(0, 0, 0, 0.1);
        --header-text-color: #212529;
        --header-text-shadow: rgba(0, 0, 0, 0.2);
        --text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        --card-bg: #f8f9fa;
        --card-border: #dee2e6;
        --image-overlay: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.5));
        --countdown-bg: #f1f1f1;
        --countdown-text: #212529;
        --badge-bg: #f8f9fa;
        --badge-text: #212529;
        --prize-item-bg: #ffffff;
        --prize-border: #dee2e6;
        --prize-name-bg: rgba(255, 255, 255, 0.8);
        --prize-name-text: #212529;
        --prize-badge-bg: #00dc82;
        --prize-badge-text: #000;
        --participant-border: #dee2e6;
        --participant-avatar-bg: #2d9faf;
        --participant-date-text: #6c757d;
        --winner-highlight: rgba(0, 220, 130, 0.05);
        --winner-border: #00dc82;
        --winner-text: #00dc82;
        --btn-join-bg: #2d9faf;
        --btn-join-text: #ffffff;
        --btn-details-bg: rgba(45, 159, 175, 0.5);
        --btn-details-text: #fff;
        --btn-disabled-bg: #e9ecef;
        --btn-disabled-text: #6c757d;
        --search-bg: rgba(255, 255, 255, 0.8);
        --search-border: rgba(0, 0, 0, 0.1);
        --search-text: #212529;
    }

    [data-theme="dark"] {
        --background-color: #121212;
        --text-color: #ffffff;
        --header-bg: #1d1d1d;
        --header-shadow: rgba(0, 0, 0, 0.2);
        --header-text-color: #ffffff;
        --header-text-shadow: rgba(0, 0, 0, 0.5);
        --text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        --card-bg: #191919;
        --card-border: #262626;
        --image-overlay: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7));
        --countdown-bg: #222;
        --countdown-text: #ffffff;
        --badge-bg: #1d1d1d;
        --badge-text: #ffffff;
        --prize-item-bg: #1E1E1E;
        --prize-border: #262626;
        --prize-name-bg: rgba(30, 30, 30, 0.8);
        --prize-name-text: #ffffff;
        --prize-badge-bg: #00dc82;
        --prize-badge-text: #000;
        --participant-border: #262626;
        --participant-avatar-bg: #2d9faf;
        --participant-date-text: #888;
        --winner-highlight: rgba(0, 220, 130, 0.05);
        --winner-border: #00dc82;
        --winner-text: #00dc82;
        --btn-join-bg: #2d9faf;
        --btn-join-text: #ffffff;
        --btn-details-bg: rgba(45, 159, 175, 0.5);
        --btn-details-text: #fff;
        --btn-disabled-bg: #333;
        --btn-disabled-text: #888;
        --search-bg: rgba(0, 0, 0, 0.2);
        --search-border: rgba(255, 255, 255, 0.1);
        --search-text: #ffffff;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
    }

    .cekilis-detail-header {
        background-color: var(--header-bg);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30px;
        padding: 30px 20px;
        box-shadow: 0 10px 20px var(--header-shadow);
        position: relative;
        overflow: hidden;
    }

    .cekilis-detail-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--header-text-color);
        text-align: center;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
        text-shadow: var(--text-shadow);
    }

    .cekilis-detail-header p {
        color: var(--header-text-color);
        opacity: 0.9;
        text-align: center;
        max-width: 800px;
        position: relative;
        z-index: 2;
    }

    .cekilis-detail-card {
        background-color: var(--card-bg);
        border-radius: 10px;
        border: 1px solid var(--card-border);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .cekilis-detail-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .cekilis-detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cekilis-detail-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--image-overlay);
    }

    .cekilis-detail-info {
        padding: 20px;
    }

    .cekilis-detail-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }

    .cekilis-detail-badge {
        background-color: var(--badge-bg);
        color: var(--badge-text);
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .cekilis-detail-badge i {
        margin-right: 8px;
    }

    .cekilis-countdown-box {
        background-color: var(--countdown-bg);
        color: var(--countdown-text);
        padding: 15px;
        text-align: center;
        font-weight: 500;
        border-bottom: 1px solid var(--card-border);
        margin-bottom: 20px;
    }

    .cekilis-prizes-section {
        margin-bottom: 20px;
    }

    .cekilis-prizes-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #00dc82;
        display: flex;
        align-items: center;
        text-shadow: var(--text-shadow);
    }

    .cekilis-prizes-title i {
        margin-right: 10px;
    }

    .cekilis-prizes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }

    .cekilis-prize-item {
        background-color: var(--prize-item-bg);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        height: 200px;
        border: 1px solid var(--prize-border);
    }

    .cekilis-prize-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .cekilis-prize-item:hover img {
        transform: scale(1.1);
    }

    .cekilis-prize-badge {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: var(--prize-badge-bg);
        color: var(--prize-badge-text);
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 500;
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .cekilis-prize-name {
        padding: 10px;
        text-align: center;
        font-weight: 500;
        font-size: 14px;
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: var(--prize-name-bg);
        color: var(--prize-name-text);
    }

    .cekilis-participants {
        margin-bottom: 20px;
    }

    .cekilis-participants-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #00dc82;
        display: flex;
        align-items: center;
        text-shadow: var(--text-shadow);
    }

    .cekilis-participants-title i {
        margin-right: 10px;
    }

    .cekilis-participants-list {
        background-color: var(--prize-item-bg);
        border-radius: 8px;
        padding: 15px;
        max-height: 200px;
        overflow-y: auto;
    }

    .cekilis-participant {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--participant-border);
    }

    .cekilis-participant:last-child {
        border-bottom: none;
    }

    .cekilis-participant-avatar {
        color: #fff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: var(--participant-avatar-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-weight: 500;
        font-size: 12px;
    }

    .cekilis-participant-name {
        flex-grow: 1;
        color: var(--text-color);
    }

    .cekilis-participant-date {
        font-size: 12px;
        color: var(--participant-date-text);
    }

    .cekilis-actions {
        display: flex;
    }

    .cekilis-btn {
        flex: 1;
        padding: 15px;
        font-weight: 500;
        font-size: 16px;
        text-align: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .cekilis-btn-join {
        background-color: var(--btn-join-bg);
        color: var(--btn-join-text);
    }

    .cekilis-btn-details {
        background-color: var(--btn-details-bg);
        color: var(--btn-details-text);
    }

    .cekilis-btn-join:hover {
        background-color: rgba(45, 160, 175, 0.86);
        color: #ffffff;
    }

    .cekilis-btn-details:hover {
        background-color: rgba(45, 160, 175, 0.34);
        color: #fff;
    }

    .cekilis-btn-disabled {
        background-color: var(--btn-disabled-bg);
        color: var(--btn-disabled-text);
        cursor: not-allowed;
    }
    
    /* Kazananlar için özel stil */
    .cekilis-participant[style*="border-left: 3px solid #00dc82"] {
        border-left: 3px solid var(--winner-border) !important;
        padding-left: 10px;
        background-color: var(--winner-highlight);
    }
    
    .cekilis-participant[style*="border-left: 3px solid #00dc82"] .cekilis-participant-avatar {
        background-color: var(--winner-border);
    }
    
    .cekilis-participant[style*="border-left: 3px solid #00dc82"] [style*="color: #fff"] {
        color: var(--winner-text) !important;
    }
    
    /* Arama kutusu için */
    #searchParticipants {
        background-color: var(--search-bg);
        border: 1px solid var(--search-border);
        color: var(--search-text);
    }
</style>

<div class="container py-4">
    <div class="cekilis-detail-header">
        <h1><?= htmlspecialchars($draw->name) ?></h1>
        <p><?= nl2br(htmlspecialchars($draw->description)) ?></p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="cekilis-detail-card">
                <div class="cekilis-detail-image">
                    <img src="<?= !empty($draw->image) ? base_url($draw->image) : base_url('assets/img/draws/default.jpg') ?>" alt="Çekiliş Görseli">
                    <div class="cekilis-detail-image-overlay"></div>
                </div>

                <div class="cekilis-countdown-box">
                    <?php
                    $now = new DateTime();
                    $end = new DateTime($draw->end_time);
                    $start = new DateTime($draw->start_time);
                    $interval = $now->diff($end);
                    $startInterval = $now->diff($start);
                    $notStartedYet = ($now < $start);
                    $isFinished = ($now > $end);
                    
                    // Süre formatını oluştur - 0 olan değerleri gösterme
                    $timeText = '';
                    if ($interval->d > 0) {
                        $weeks = floor($interval->d / 7);
                        $days = $interval->d % 7;
                        
                        if ($weeks > 0) {
                            $timeText .= $weeks . ' hafta ';
                        }
                        
                        if ($days > 0) {
                            $timeText .= $days . ' gün ';
                        }
                    }
                    
                    if ($interval->h > 0 || empty($timeText)) {
                        $timeText .= $interval->h . ' saat ';
                    }
                    
                    // Dakika ekle
                    if ($interval->i > 0 || (empty($timeText) && $interval->s > 0)) {
                        $timeText .= $interval->i . ' dakika ';
                    }
                    
                    // Eğer hiçbir zaman birimi yoksa, 1 dakikadan az kaldığını belirt
                    if (empty($timeText)) {
                        $timeText = '1 dakikadan az';
                    }
                    
                    // Boşluk temizleme
                    $timeText = trim($timeText);
                    
                    // Başlangıç için süre formatını oluştur
                    $startTimeText = '';
                    if ($startInterval->d > 0) {
                        $weeks = floor($startInterval->d / 7);
                        $days = $startInterval->d % 7;

                        if ($weeks > 0) {
                            $startTimeText .= $weeks . ' hafta ';
                        }

                        if ($days > 0) {
                            $startTimeText .= $days . ' gün ';
                        }
                    }

                    if ($startInterval->h > 0 || empty($startTimeText)) {
                        $startTimeText .= $startInterval->h . ' saat ';
                    }

                    // Dakika ekle
                    if ($startInterval->i > 0 || (empty($startTimeText) && $startInterval->s > 0)) {
                        $startTimeText .= $startInterval->i . ' dakika ';
                    }

                    // Eğer hiçbir zaman birimi yoksa, 1 dakikadan az kaldığını belirt
                    if (empty($startTimeText)) {
                        $startTimeText = '1 dakikadan az';
                    }

                    // Boşluk temizleme
                    $startTimeText = trim($startTimeText);
                    ?>
                    <?php if ($notStartedYet): ?>
                        <i class="fa-solid fa-hourglass-start me-2"></i> <?= $startTimeText ?> sonra başlayacak
                    <?php elseif ($isFinished || $draw->status == 0): ?>
                        <i class="fa-solid fa-flag-checkered me-2"></i> Çekiliş Sona Erdi
                    <?php else: ?>
                        <i class="fa-regular fa-clock me-2"></i> <?= $timeText ?> sonra sona erecek
                    <?php endif; ?>
                </div>

                <div class="cekilis-detail-info">
                    <div class="cekilis-detail-badges">
                        <div class="cekilis-detail-badge">
                            <i class="fa-solid fa-coins"></i> <?= $draw->type == 'bakiye' ? 'Site Bakiyesi' : 'Ürün' ?>
                        </div>
                        <div class="cekilis-detail-badge">
                            <i class="fa-solid fa-users"></i> <?= $draw->participant_count ?> Katılımcı
                        </div>
                        <?php if($draw->max_participants): ?>
                        <div class="cekilis-detail-badge">
                            <i class="fa-solid fa-user-group"></i> Maks. <?= $draw->max_participants ?> Kişi
                        </div>
                        <?php endif; ?>
                        <div class="cekilis-detail-badge">
                            <i class="fa-regular fa-calendar"></i> <?= date('d.m.Y H:i', strtotime($draw->start_time)) ?>
                        </div>
                        <div class="cekilis-detail-badge">
                            <i class="fa-regular fa-calendar-check"></i> <?= date('d.m.Y H:i', strtotime($draw->end_time)) ?>
                        </div>
                        <?php if (isset($rewardPoolTotal) && $rewardPoolTotal > 0): ?>
                        <div class="cekilis-detail-badge" style="background-color: rgba(0, 220, 130, 0.2); color: #00dc82; border: 1px solid rgba(0, 220, 130, 0.3);">
                            <i class="fa-solid fa-sack-dollar"></i> <?= number_format($rewardPoolTotal, 2, ',', '.') ?> TL değerinde
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Ödüller Bölümü -->
                    <div class="cekilis-prizes-section">
                        <div class="cekilis-prizes-title">
                            <i class="fa-solid fa-gift"></i> Ödüller
                        </div>
                        <div class="cekilis-prizes-grid">
                            <?php foreach($draw->rewards as $reward): 
                                $isProduct = ($reward->type == 'urun');
                                
                                // Ürün görselini product_id üzerinden getir
                                if ($isProduct && isset($reward->product_id)) {
                                    $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                    $image = $product && !empty($product->img) ? 'assets/img/product/' . $product->img : 'assets/img/rewards/valorant-vp.jpg';
                                    $text = isset($reward->product_name) ? $reward->product_name : ($product ? $product->name : 'Ürün #' . $reward->product_id);
                                    
                                    // Ödül havuzu hesaplaması (ürün fiyatı * kazanan sayısı)
                                    if (!isset($rewardPoolTotal)) $rewardPoolTotal = 0;
                                    if ($product && isset($product->discount)) {
                                        $winners = getWinnerCount($reward);
                                        $rewardPoolTotal += floatval($product->discount) * $winners;
                                    }
                                } else {
                                    // Bakiye ödülü için özel görsel ve stil
                                    $image = 'balance.webp';
                                    $amount = number_format($reward->amount, 0, '', '.');
                                    $text = $amount . ' ₺';
                                    
                                    // Ödül havuzu hesaplaması (bakiye tutarı * kazanan sayısı)
                                    if (!isset($rewardPoolTotal)) $rewardPoolTotal = 0;
                                    if (isset($reward->amount)) {
                                        $winners = getWinnerCount($reward);
                                        $rewardPoolTotal += floatval($reward->amount) * $winners;
                                    }
                                }
                                
                                // Kazanan sayısını al
                                $winners = getWinnerCount($reward);
                            ?>
                            <div class="cekilis-prize-item">
                                <span class="cekilis-prize-badge"><?= $winners ?> Kişiye</span>
                                <?php if ($isProduct && isset($reward->product_id) && $product): ?>
                                <a href="<?= base_url(($product->slug ?? $reward->product_id)) ?>" target="_blank">
                                    <img src="<?= base_url($image) ?>" alt="<?= htmlspecialchars($text) ?>">
                                </a>
                                <?php else: ?>
                                <img src="<?= base_url($image) ?>" alt="Ödül">
                                <?php endif; ?>
                                <div class="cekilis-prize-name">
                                    <?= $text ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?php if($draw->status == 2): ?>
            <!-- Kazananlar Bölümü -->
            <div class="cekilis-detail-card mb-4">
                <div class="cekilis-winners">
                    <div class="cekilis-detail-info">
                        <div class="cekilis-participants-title" style="color: #00dc82;">
                            <i class="fa-solid fa-trophy"></i> Kazananlar
                        </div>
                        <div class="cekilis-participants-list">
                            <?php
                            // Kazananları getir
                            $this->load->model('M_Draw');
                            $winners = $this->M_Draw->get_draw_results($draw->id);
                            
                            if(empty($winners)): 
                            ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-star mb-2" style="font-size: 24px;"></i>
                                    <p>Kazananlar henüz açıklanmadı.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($winners as $winner): 
                                    // Ödül türünü belirle
                                    $rewardText = '';
                                    if ($winner->reward_type == 'bakiye') {
                                        $rewardText = number_format($winner->amount, 0, '', '.') . ' ₺';
                                    } else if ($winner->reward_type == 'urun' && isset($winner->product_name)) {
                                        $rewardText = $winner->product_name;
                                    } else if ($winner->reward_type == 'urun' && isset($winner->product_id)) {
                                        $rewardText = 'Ürün #' . $winner->product_id;
                                    }
                                    
                                    // Kazanan kullanıcı adını al
                                    $this->db->select('name');
                                    $this->db->where('id', $winner->user_id);
                                    $user = $this->db->get('user')->row();
                                    $winnerName = $user ? $user->name : 'Bilinmeyen Kullanıcı';
                                ?>
                                    <div class="cekilis-participant" style="border-left: 3px solid #00dc82; padding-left: 10px; background-color: rgba(0, 220, 130, 0.05);">
                                        <div class="cekilis-participant-avatar" style="background-color: #00dc82;">
                                            <?= strtoupper(substr($winnerName, 0, 1)) ?>
                                        </div>
                                        <div class="cekilis-participant-name">
                                            <span style="font-weight: 600;"><?= $winnerName ?></span>
                                            <div style="font-size: 12px; color: #00dc82;">
                                                <i class="fa-solid fa-gift me-1"></i> <?= $rewardText ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Katılımcılar Bölümü -->
            <div class="cekilis-detail-card">
                <div class="cekilis-participants">
                    <div class="cekilis-detail-info">
                        <div class="cekilis-participants-title">
                            <i class="fa-solid fa-users"></i> Katılımcılar (<?= $draw->participant_count ?>)
                        </div>
                        
                        <!-- Katılımcı Arama Kutusu -->
                        <div class="participant-search mb-3">
                            <div class="input-group">
                                <input type="text" id="searchParticipants" class="form-control form-control-sm" 
                                       placeholder="Katılımcı ara..." 
                                       style="background-color: rgba(0,0,0,0.2); 
                                              border: 1px solid rgba(255,255,255,0.1); 
                                              color: #fff; 
                                              border-radius: 20px; 
                                              padding: 8px 15px;
                                              font-size: 13px;">
                                <div class="input-group-append" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 5;">
                                    <i class="fa-solid fa-search" style="color: rgba(255,255,255,0.4); font-size: 12px;"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="cekilis-participants-list">
                            <?php 
                            // Katılımcıların detaylı bilgilerini al
                            $this->load->model('M_Draw');
                            $participants = $this->M_Draw->get_draw_participants_with_user($draw->id);
                            
                            if(empty($participants)): 
                            ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-users-slash mb-2" style="font-size: 24px;"></i>
                                    <p>Henüz katılımcı bulunmamaktadır.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($participants as $participant): ?>
                                    <div class="cekilis-participant">
                                        <div class="cekilis-participant-avatar">
                                            <?= strtoupper(substr($participant->name, 0, 1)) ?>
                                        </div>
                                        <div class="cekilis-participant-name">
                                            <?= $participant->name ?>
                                        </div>
                                        <div class="cekilis-participant-date">
                                            <?php if (isset($participant->date_created)): ?>
                                                <?= date('d.m.Y H:i', strtotime($participant->date_created)) ?>
                                            <?php elseif (isset($participant->date)): ?>
                                                <?= date('d.m.Y H:i', strtotime($participant->date)) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Katılım Butonları -->
                <div class="cekilis-actions">
                    <?php if($draw->status == 2): ?>
                        <button class="cekilis-btn cekilis-btn-disabled">
                            <i class="fa-solid fa-lock me-2"></i> Çekiliş Sona Erdi
                        </button>
                    <?php elseif($draw->is_joined): ?>
                        <button disabled class="cekilis-btn cekilis-btn-join">
                            <i class="fa-solid fa-check me-2"></i> Çekilişe Katıldınız
                        </button>
                    <?php elseif(isset($_SESSION['info'])): ?>
                        <?php
                        // Çekiliş başlamamış mı kontrolü
                        $now = new DateTime();
                        $start = new DateTime($draw->start_time);
                        $notStartedYet = ($now < $start);
                        
                        if ($notStartedYet): ?>
                            <button disabled class="cekilis-btn cekilis-btn-disabled" title="Çekiliş henüz başlamadı">
                                <i class="fa-solid fa-clock me-2"></i> Yakında Başlayacak
                            </button>
                        <?php else: ?>
                        <a href="<?= base_url('cekilis/katil/'.$draw->id) ?>" class="cekilis-btn cekilis-btn-join">
                            <i class="fa-solid fa-bolt me-2"></i> Çekilişe Katıl
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= base_url('hesap') ?>" class="cekilis-btn cekilis-btn-join">
                            <i class="fa-solid fa-sign-in-alt me-2"></i> Giriş Yap ve Katıl
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Geri Dön Butonu -->
    <div class="text-center mt-4">
        <a href="<?= base_url('cekilisler') ?>" class="btn btn-outline-light" style="background-color: var(--card-bg); color: var(--text-color); border-color: var(--border-color);">
            <i class="fa-solid fa-arrow-left me-2"></i> Tüm Çekilişlere Geri Dön
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Katılımcı arama fonksiyonu
        const searchInput = document.getElementById('searchParticipants');
        if (searchInput) {
            const participantItems = document.querySelectorAll('.cekilis-participant');
            
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                
                participantItems.forEach(function(item) {
                    const participantName = item.querySelector('.cekilis-participant-name').textContent.toLowerCase();
                    
                    if (participantName.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Hiç sonuç yoksa mesaj göster
                const visibleItems = Array.from(participantItems).filter(item => item.style.display !== 'none');
                const noResultsMessage = document.getElementById('noParticipantsResults');
                
                if (visibleItems.length === 0 && searchTerm !== '') {
                    if (!noResultsMessage) {
                        const newMessage = document.createElement('div');
                        newMessage.id = 'noParticipantsResults';
                        newMessage.className = 'text-center py-3 text-muted';
                        newMessage.innerHTML = '<i class="fa-solid fa-search me-2"></i> Sonuç bulunamadı';
                        document.querySelector('.cekilis-participants-list').appendChild(newMessage);
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            });
        }
        
        // Tema değişikliği için fonksiyonlar
        function applyTheme() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        }
        
        // Sayfa yüklendiğinde tema uygula
        applyTheme();
        
        // Header'daki tema değiştirme düğmelerini dinle
        document.addEventListener('click', function(event) {
            if (event.target.closest('.link-light-theme')) {
                event.preventDefault();
                localStorage.setItem('theme', 'light');
                applyTheme();
            } else if (event.target.closest('.link-dark-theme')) {
                event.preventDefault();
                localStorage.setItem('theme', 'dark');
                applyTheme();
            }
        });
    });
</script>

<?php $this->load->view('theme/future/includes/footer'); ?>

<?php
// Kazanan sayısını doğru şekilde almak için yardımcı fonksiyon 
function getWinnerCount($reward)
{
    // İlk olarak winner_count kontrolü (M_Draw sınıfında bu sütun kullanılıyor)
    if (isset($reward->winner_count) && intval($reward->winner_count) > 0) {
        return intval($reward->winner_count);
    }
    
    // İkinci olarak winners_count kontrolü
    if (isset($reward->winners_count) && intval($reward->winners_count) > 0) {
        return intval($reward->winners_count);
    }
    
    // Varsayılan değer
    return 1;
}
?> 
