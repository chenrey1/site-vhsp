<?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Debug modu aktif - Ödül Kazanan Sayısı Kontrol');
            // Gizli bilgi paneli ekle
            var infoPanel = document.createElement('div');
            infoPanel.style.position = 'fixed';
            infoPanel.style.bottom = '10px';
            infoPanel.style.right = '10px';
            infoPanel.style.backgroundColor = 'rgba(0,0,0,0.8)';
            infoPanel.style.color = '#00dc82';
            infoPanel.style.padding = '10px';
            infoPanel.style.zIndex = '9999';
            infoPanel.style.borderRadius = '5px';
            infoPanel.style.fontSize = '12px';
            infoPanel.innerHTML = '<b>Debug Modu:</b> Ödül bilgilerini debug etmek için aktif edildi.<br>' +
                'Veritabanındaki ilgili sütunlar: <b>winners_count</b> ve <b>winner_count</b>';
            document.body.appendChild(infoPanel);
        });
    </script>
<?php endif; ?>
<?php $this->load->view('theme/future/includes/header'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    :root {
        --background-color: #ffffff;
        --card-bg: #f8f9fa;
        --card-header-bg: #eaeaea;
        --text-color: #212529;
        --border-color: #dee2e6;
        --badge-bg: rgba(0, 0, 0, 0.03);
        --badge-text: #212529;
        --countdown-bg: #f1f1f1;
        --prize-bg: #f8f9fa;
        --prize-item-bg: #ffffff;
        --btn-join-bg: #2d9faf;
        --btn-join-text: #ffffff;
        --btn-details-bg: rgba(45, 159, 175, 0.5);
        --btn-details-text: #ffffff;
        --header-bg: #f8f9fa;
        --header-shadow: rgba(0, 0, 0, 0.1);
        --text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        --tab-btn-bg: rgba(0, 0, 0, 0.05);
        --tab-btn-text: #555;
        --tab-btn-active-bg: #2d9faf;
        --tab-btn-active-text: #fff;
        --search-bg: rgba(255, 255, 255, 0.8);
        --search-border: rgba(0, 0, 0, 0.1);
        --search-text: #212529;
        --search-icon-color: rgba(0, 0, 0, 0.5);
        --footer-stat-label: #6c757d;
    }

    [data-theme="dark"] {
        --background-color: #121212;
        --card-bg: #191919;
        --card-header-bg: #1d1d1d;
        --text-color: #ffffff;
        --border-color: #262626;
        --badge-bg: rgba(255, 255, 255, 0.03);
        --badge-text: #f8f9fa;
        --countdown-bg: #222;
        --prize-bg: #1D1D1D;
        --prize-item-bg: #1E1E1E;
        --btn-join-bg: #2d9faf;
        --btn-join-text: #ffffff;
        --btn-details-bg: rgba(45, 159, 175, 0.5);
        --btn-details-text: #fff;
        --header-bg: #1d1d1d;
        --header-shadow: rgba(0, 0, 0, 0.2);
        --text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        --tab-btn-bg: rgba(29, 29, 29, 0.8);
        --tab-btn-text: #bbb;
        --tab-btn-active-bg: #2d9faf;
        --tab-btn-active-text: #fff;
        --search-bg: rgb(0 0 0 / 78%);
        --search-border: rgba(255, 255, 255, 0.2);
        --search-text: #ffffff;
        --search-icon-color: rgba(255, 255, 255, 0.6);
        --footer-stat-label: #888;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
    }

    .cekilisler-header {
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

    .cekilisler-header .cekilisler-icon {
        display: flex;
        justify-content: center;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
    }

    .cekilisler-header-text {
        text-align: center;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    .cekilisler-header-text h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-color);
        text-shadow: var(--text-shadow);
    }

    .cekilisler-header-text p {
        margin: 10px 0 0;
        opacity: 0.9;
        font-size: 1rem;
        max-width: 700px;
        line-height: 1.5;
        color: var(--text-color);
        text-shadow: var(--text-shadow);
    }

    .tab-buttons {
        display: flex;
        margin-bottom: 0;
        justify-content: center;
        width: 100%;
        position: relative;
        z-index: 2;
        flex-wrap: wrap;
    }

    .tab-btn {
        background-color: var(--tab-btn-bg);
        border: none;
        color: var(--tab-btn-text);
        margin: 0 8px 8px 0;
        padding: 10px 18px;
        cursor: pointer;
        font-weight: 500;
        position: relative;
        border-radius: 6px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .tab-btn i {
        margin-right: 8px;
        font-size: 14px;
    }

    .tab-btn:hover {
        background-color: var(--tab-btn-active-bg);
        color: var(--tab-btn-active-text);
        transform: translateY(-2px);
    }

    .tab-btn.active {
        color: var(--tab-btn-active-text);
        background-color: var(--tab-btn-active-bg);
        box-shadow: 0 4px 10px rgba(45, 159, 175, 0.3);
    }

    .tab-btn.active:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: #00dc82;
        display: none;
    }

    .cekilisler-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .cekilis-card {
        overflow: hidden;
        background-color: var(--card-bg);
        border-radius: 10px;
        border: 1px solid var(--border-color);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .cekilis-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }

    .cekilis-header {
        background-color: var(--card-header-bg);
        padding: 12px;
        display: flex;
        align-items: center;
        position: relative;
    }

    .cekilis-header img {
        width: 52px;
        height: 52px;
        object-fit: cover;
    }

    .cekilis-header-info {
        margin-left: 12px;
        flex-grow: 1;
    }

    .cekilis-header-user {
        font-weight: bold;
        display: flex;
        align-items: center;
    }

    .cekilis-header-user .badge {
        background-color: #6b2cf5;
        margin-left: 8px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .cekilis-header-type {
        color: #00dc82;
        font-size: 14px;
        font-weight: 600;
        margin-top: 4px;
    }

    .cekilis-header-action {
        background-color: #242439;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #5554ea;
        border: none;
        font-size: 18px;
    }

    .cekilis-countdown {
        background-color: var(--countdown-bg);
        color: var(--text-color);
        padding: 10px;
        text-align: center;
        font-weight: 500;
        border-bottom: 1px solid var(--border-color);
    }

    .cekilis-prizes {
        background-color: var(--prize-bg);
        border-radius: 0;
        padding-top: 10px;
    }

    .cekilis-prize {
        position: relative;
        overflow: hidden;
        background-color: var(--prize-item-bg);
        border-radius: 0;
        box-shadow: none;
        margin: 0 auto;
        max-width: 100%;
        transition: transform 0.2s;
    }

    .cekilis-prize:hover {
        transform: none;
    }

    .cekilis-prize img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .cekilis-prize:hover img {
        transform: scale(1.1);
    }

    .cekilis-prize-badge {
        position: absolute;
        top: 5px;
        right: 50px;
        background-color: #00dc82;
        color: #000;
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 500;
        z-index: 2;
    }

    .cekilis-prize-value {
        padding: 8px 5px;
        text-align: center;
        font-weight: 800;
        color: #000;
        font-size: 25px;
        position: absolute;
        top: 80%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        color: #000000;
        /* text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7); */
        font-weight: bold;
        width: 100%;
        text-align: center;
    }

    .cekilis-prizes-swiper {
        width: 100%;
        padding: 15px;
    }

    .cekilis-footer {
        display: flex;
        flex-direction: column;
    }

    .cekilis-footer-stats {
        display: flex;
        border-bottom: 1px solid var(--border-color);
    }

    .cekilis-footer-stat {
        flex: 1;
        padding: 15px 10px;
        text-align: center;
        border-right: 1px solid var(--border-color);
    }

    .cekilis-footer-stat:last-child {
        border-right: none;
    }

    .cekilis-footer-stat-value {
        font-weight: bold;
        font-size: 18px;
        color: var(--text-color);
    }

    .cekilis-footer-stat-label {
        color: var(--footer-stat-label);
        font-size: 12px;
        margin-top: 5px;
    }

    .cekilis-actions mt-3 {
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

    .cekilis-btn-details:horizontal {
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

    @media (max-width: 992px) {
        .cekilisler-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .cekilisler-container {
            grid-template-columns: 1fr;
        }
    }

    .cekilis-prizes .swiper-pagination {
        margin-top: 10px;
        bottom: 0;
    }

    .cekilis-prizes .swiper-pagination-bullet {
        background: #3a77ff;
        opacity: 0.5;
        width: 6px;
        height: 6px;
    }

    .cekilis-prizes .swiper-pagination-bullet-active {
        opacity: 1;
        background: #00dc82;
    }

    /* Bakiye ödülü stilini görseldeki gibi düzenleme */
    .cekilis-prizes .text-center.p-2.mt-2 {
        margin: 0;
        padding: 10px !important;
        background: #1A3228 !important;
        border-left: none !important;
        border-top: 1px solid rgba(0, 220, 130, 0.2);
    }

    .nakit-odul-box {
        background-color: #1A3228;
        padding: 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-top: 1px solid rgba(0, 220, 130, 0.2);
    }

    .nakit-odul-icon {
        color: #00dc82;
        font-size: 18px;
        margin-bottom: 5px;
    }

    .nakit-odul-miktar {
        color: #00dc82;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .nakit-odul-text {
        color: #00dc82;
        font-size: 14px;
        font-weight: 400;
    }

    .cekilis-prizes .swiper-button-next,
    .cekilis-prizes .swiper-button-prev {
        color: #00dc82;
        width: 25px;
        height: 25px;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
    }

    .cekilis-prizes .swiper-button-next:after,
    .cekilis-prizes .swiper-button-prev:after {
        font-size: 14px;
        font-weight: bold;
    }

    .cekilis-prizes .swiper-button-next {
        right: 5px;
    }

    .cekilis-prizes .swiper-button-prev {
        left: 5px;
    }

    /* Arama kutusu için */
    #searchDraws {
        background-color: var(--search-bg);
        border: 1px solid var(--search-border);
        border-radius: 30px;
        padding: 10px 20px;
        color: var(--search-text);
        width: 450px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="container py-4">
    <div class="cekilisler-header">
        <div class="cekilisler-header-text">
            <div class="cekilisler-header-text-content mb-3">
                <i style="font-size: 2rem; border-radius: 10px; background-color: var(--card-bg); padding:10px; color: var(--text-color);"
                    class="fa-solid fa-gift"></i>
            </div>
            <h1>Çekilişler</h1>
            <p>Heyecan dolu çekilişlerle şansınızı deneyin, eşsiz ödüller kazanma fırsatını yakalayın ve oyun dünyasında
                eğlenceye katılın!</p>
        </div>
        <div class="tab-buttons mb-4">
            <button class="tab-btn active" data-bs-toggle="tab" data-bs-target="#active">
                <i class="fa-solid fa-fire"></i> Aktif Çekilişler
            </button>
            <button class="tab-btn" data-bs-toggle="tab" data-bs-target="#finished">
                <i class="fa-solid fa-flag-checkered"></i> Biten Çekilişler
            </button>
        </div>
        <!-- Çekiliş Arama Kutusu -->
        <div class="search-box mt-3" style="z-index:9900;">
            <div class="input-group">
                <input type="text" id="searchDraws" class="form-control" placeholder="Çekiliş ara...">
                <div class="input-group-append"
                    style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); z-index: 5;">
                    <i class="fa-solid fa-search" style="color: var(--search-icon-color);"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="active" role="tabpanel">
            <div class="cekilisler-container">
                <?php foreach ($active_draws as $draw): ?>
                    <div class="cekilis-card">
                        <!-- Çekiliş Başlık Bölümü -->
                        <div class="cekilis-header" style="padding: 15px; display: flex; align-items: center;">
                            <div class="cekilis-image" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; margin-right: 15px; flex-shrink: 0; border: 2px solid var(--btn-join-bg); box-shadow: 0 3px 8px rgba(0,0,0,0.1);">
                                <img src="<?= base_url($draw->image) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="cekilis-info">
                                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-color);"><?= htmlspecialchars($draw->name) ?></h3>
                                <p style="margin: 5px 0 0; font-size: 0.85rem; color: var(--footer-stat-label); line-height: 1.4; opacity: 0.9;">
                                    <?= isset($draw->description) ? (strlen($draw->description) > 100 ? substr(htmlspecialchars($draw->description), 0, 100) . '...' : htmlspecialchars($draw->description)) : 'Heyecan verici ödüller kazanma şansı!' ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- İstatistikler Bölümü -->
                        <div class="cekilis-stats mt-3" style="display: flex; margin: 0 15px 12px; gap: 8px;">
                            <div style="flex: 1; background: var(--card-header-bg); padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; font-size: 1rem; color: var(--text-color);">
                                    <?php
                                    // Ödül havuzu hesaplama
                                    $rewardPoolTotal = 0;
                                    foreach ($draw->rewards as $rwd) {
                                        if (!is_object($rwd)) continue;
                                        
                                        if ($rwd->type == 'bakiye' && isset($rwd->amount)) {
                                            $winners = getWinnerCount($rwd);
                                            $rewardPoolTotal += floatval($rwd->amount) * $winners;
                                        } else if ($rwd->type == 'urun' && isset($rwd->product_id)) {
                                            $product = $this->db->where('id', $rwd->product_id)->get('product')->row();
                                            if ($product && isset($product->discount)) {
                                                $winners = getWinnerCount($rwd);
                                                $rewardPoolTotal += floatval($product->discount) * $winners;
                                            }
                                        }
                                    }
                                    echo isset($rewardPoolTotal) && $rewardPoolTotal > 0 ? number_format($rewardPoolTotal, 2, ',', '.') . ' TL' : (isset($draw->reward_pool) ? number_format($draw->reward_pool, 2, ',', '.') . ' TL' : '25.000,00 TL');
                                    ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--footer-stat-label); margin-top: 2px;">Ödül Havuzu</div>
                            </div>
                            <div style="flex: 1; background: var(--card-header-bg); padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; font-size: 1rem; color: var(--text-color);"><?= $draw->participant_count ?></div>
                                <div style="font-size: 0.75rem; color: var(--footer-stat-label); margin-top: 2px;">Katılımcı</div>
                            </div>
                        </div>
                        
                        <!-- Ödüller Bölümü -->
                        <div class="cekilis-prizes"
                            <?php
                            $prizeCount = count($draw->rewards);
                            // Ürün tipi ödülleri ve bakiye ödüllerini ayır
                        
                            $productRewards = [];
                            $balanceTotal = 0;
                            $balanceWinnersCount = 0; // Bakiye kazanan toplam kişi sayısı
                        
                            foreach ($draw->rewards as $rwd) {
                                if (!is_object($rwd))
                                    continue; // Geçersiz verileri atla
                        
                                if ($rwd->type == 'urun' && isset($rwd->product_id)) {
                                    // Kazanan sayısını düzgün formatta al
                        
                                    if (isset($rwd->winners_count)) {
                                        if (is_string($rwd->winners_count)) {
                                            $rwd->winners_count = (int) $rwd->winners_count;
                                        }
                                        if ($rwd->winners_count <= 0) {
                                            $rwd->winners_count = 1;
                                        }
                                    } else {
                                        $rwd->winners_count = 1;
                                    }
                                    $productRewards[] = $rwd;
                                } else if ($rwd->type == 'bakiye' && isset($rwd->amount)) {
                                    $balanceTotal += floatval($rwd->amount);
                                    // Bakiye kazanan kişilerin sayısını topla
                        
                                    if (isset($rwd->winners_count)) {
                                        if (is_string($rwd->winners_count)) {
                                            $rwd->winners_count = (int) $rwd->winners_count;
                                        }
                                        $balanceWinnersCount += ($rwd->winners_count > 0) ? $rwd->winners_count : 1;
                                    } else {
                                        $balanceWinnersCount += 1;
                                    }
                                }
                            }
                            
                            // Eğer ürün yoksa ve sadece bakiye ödülü varsa, bakiye ödülünü göster
                            if (empty($productRewards) && $balanceTotal > 0) {
                                $productRewards = [new stdClass()];
                                $productRewards[0]->type = 'bakiye';
                                $productRewards[0]->amount = $balanceTotal;
                                $productRewards[0]->winners_count = $balanceWinnersCount > 0 ? $balanceWinnersCount : 1;
                            }
                            // Bakiye ödülünü ayrı bir ürün gibi ekle (eğer bakiye ödülü varsa ve henüz eklenmemişse)
                            if ($balanceTotal > 0 && !empty($productRewards) && $productRewards[0]->type != 'bakiye') {
                                $bakiyeOdul = new stdClass();
                                $bakiyeOdul->type = 'bakiye';
                                $bakiyeOdul->amount = $balanceTotal;
                                $bakiyeOdul->winners_count = $balanceWinnersCount > 0 ? $balanceWinnersCount : 1;
                                $productRewards[] = $bakiyeOdul;
                            }
                            ?>
                            <!-- 3x3 Grid Ödül Gösterimi -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 10px;">
                                <?php 
                                // En fazla 9 ödül göster
                                $displayRewards = array_slice($productRewards, 0, 9);
                                foreach ($displayRewards as $reward):
                                    $isProduct = ($reward->type == 'urun');
                                    
                                    if ($isProduct && isset($reward->product_id)) {
                                        $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                        $image = $product && !empty($product->img) ? 'assets/img/product/' . $product->img : 'assets/img/rewards/valorant-vp.jpg';
                                        $text = isset($reward->product_name) ? $reward->product_name : ($product ? $product->name : 'Ürün #' . $reward->product_id);
                                    } else {
                                        // Bakiye ödülü için eski görsel kullan
                                        $image = 'assets/img/category/bakiyekatagori55126.jpg';
                                        $amount = number_format($reward->amount, 0, '', '.');
                                        $text = $amount . ' TL';
                                    }
                                    
                                    $winners = getWinnerCount($reward);
                                ?>
                                <div style="position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
                                    <span style="width: 50%;text-align: center;position: absolute;top: 0;left: 50%;transform: translateX(-50%);background: rgb(29 29 29);color: #ffffff;padding: 3px 10px;border-radius: 0px 0px 10px 10px;font-size: 10px;font-weight: 600;z-index: 2;"><?= $winners ?> Kişiye</span>
                                    <?php if ($isProduct && isset($reward->product_id) && $product): ?>
                                        <a href="<?= base_url(($product->slug ?? $reward->product_id)) ?>" style="">
                                            <img src="<?= base_url($image) ?>" alt="<?= htmlspecialchars($text) ?>" style="">
                                        </a>
                                    <?php else: ?>
                                        <!-- Bakiye ödülü için sadece bakiye tutarı -->
                                        <div style="position: relative; ">
                                            <img src="<?= base_url($image) ?>" alt="Bakiye Ödülü" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;">
                                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding-top: 30px;">
                                                <div style="color: #ffffff; font-weight: 900; font-size: 2.1rem; text-shadow: 1px 1px 3px rgba(1px 1px 3px rgb(0 0 0 / 13%));"><?= $text ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if (count($productRewards) > 9): ?>
                                <div style="position: relative; display: flex; align-items: center; justify-content: center; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
                                    <a href="<?= base_url('cekilis/' . $draw->id) ?>" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: var(--text-color);">
                                        <i class="fa-solid fa-ellipsis" style="font-size: 1.5rem;"></i>
                                        <div style="font-size: 0.8rem; margin-top: 5px;">Daha Fazla</div>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Geri Sayım Bölümü -->
                        <div class="cekilis-countdown-boxes mt-3" style="display: flex; padding: 0 15px 12px; gap: 8px;">
                            <?php
                            $now = new DateTime();
                            $end = new DateTime($draw->end_time);
                            $start = new DateTime($draw->start_time);
                            $notStartedYet = ($now < $start);
                            $isFinished = ($now > $end);
                            
                            if ($notStartedYet) {
                                $targetDate = $start;
                                $countdownText = "Başlamasına";
                            } elseif (!$isFinished && $draw->status == 1) {
                                $targetDate = $end;
                                $countdownText = "Bitimine";
                            } else {
                                $targetDate = null;
                                $countdownText = "Sona Erdi";
                            }
                            
                            if ($targetDate) {
                                $timestamp = $targetDate->getTimestamp() * 1000; // JavaScript için milisaniye cinsinden
                            }
                            ?>
                            
                            <?php if ($targetDate): ?>
                            <div class="countdown-box" data-target="<?= $timestamp ?>" style="flex: 1; background: var(--card-header-bg); border-radius: 8px; text-align: center; padding: 8px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div class="countdown-value days" style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">0</div>
                                <div style="font-size: 0.7rem; color: var(--footer-stat-label);">Gün</div>
                            </div>
                            <div class="countdown-box" data-target="<?= $timestamp ?>" style="flex: 1; background: var(--card-header-bg); border-radius: 8px; text-align: center; padding: 8px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div class="countdown-value hours" style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">0</div>
                                <div style="font-size: 0.7rem; color: var(--footer-stat-label);">Saat</div>
                            </div>
                            <div class="countdown-box" data-target="<?= $timestamp ?>" style="flex: 1; background: var(--card-header-bg); border-radius: 8px; text-align: center; padding: 8px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div class="countdown-value minutes" style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">0</div>
                                <div style="font-size: 0.7rem; color: var(--footer-stat-label);">Dakika</div>
                            </div>
                            <div class="countdown-box" data-target="<?= $timestamp ?>" style="flex: 1; background: var(--card-header-bg); border-radius: 8px; text-align: center; padding: 8px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div class="countdown-value seconds" style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">0</div>
                                <div style="font-size: 0.7rem; color: var(--footer-stat-label);">Saniye</div>
                            </div>
                            <?php else: ?>
                            <div style="flex: 1; background: var(--card-header-bg); border-radius: 8px; text-align: center; padding: 12px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="font-size: 1rem; font-weight: 600; color: var(--text-color);"><i class="fa-solid fa-flag-checkered me-1"></i> Sona Erdi</div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Aksiyon Butonları -->
                        <div class="cekilis-actions" style="padding: 0 15px 15px; display: flex; gap: 10px;">
                            <?php if (isset($draw->is_joined) && $draw->is_joined): ?>
                                <button class="cekilis-btn cekilis-btn-join" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s ease;">
                                    <i class="fa-solid fa-check me-1"></i> Katıldınız
                                </button>
                                <a href="<?= base_url('cekilis/' . $draw->id) ?>" class="cekilis-btn cekilis-btn-details" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease;">
                                    Detaylar
                                </a>
                            <?php elseif ($notStartedYet): ?>
                                <button class="cekilis-btn cekilis-btn-disabled" disabled title="Çekiliş henüz başlamadı" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; background-color: #6c757d; border: none; cursor: not-allowed; color: #fff;">
                                    <i class="fa-solid fa-clock me-1"></i> Yakında Başlayacak
                                </button>
                                <a href="<?= base_url('cekilis/' . $draw->id) ?>" class="cekilis-btn cekilis-btn-details" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease;">
                                    Detaylar
                                </a>
                            <?php elseif (isset($_SESSION['info'])): ?>
                                <a href="<?= base_url('cekilis/katil/' . $draw->id) ?>" class="cekilis-btn cekilis-btn-join" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease;">
                                    Çekilişe Katıl
                                </a>
                                <a href="<?= base_url('cekilis/' . $draw->id) ?>" class="cekilis-btn cekilis-btn-details" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease;">
                                    Detaylar
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('hesap') ?>" class="cekilis-btn cekilis-btn-join" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease;">
                                    Giriş Yap ve Katıl
                                </a>
                                <a href="<?= base_url('cekilis/' . $draw->id) ?>" class="cekilis-btn cekilis-btn-details" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease;">
                                    Detaylar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($active_draws)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i> Şu anda aktif çekiliş bulunmamaktadır. Lütfen daha
                            sonra tekrar kontrol ediniz.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="finished" role="tabpanel">
            <div class="cekilisler-container">
                <?php foreach ($finished_draws as $draw): ?>
                    <div class="cekilis-card">
                        <!-- Çekiliş Başlık Bölümü -->
                        <div class="cekilis-header" style="padding: 15px; display: flex; align-items: center;">
                            <div class="cekilis-image" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; margin-right: 15px; flex-shrink: 0; border: 2px solid var(--btn-join-bg); box-shadow: 0 3px 8px rgba(0,0,0,0.1);">
                                <img src="<?= !empty($draw->image) ? base_url($draw->image) : base_url('assets/img/draws/default.jpg') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="cekilis-info">
                                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-color);"><?= htmlspecialchars($draw->name) ?></h3>
                                <p style="margin: 5px 0 0; font-size: 0.85rem; color: var(--footer-stat-label); line-height: 1.4; opacity: 0.9;">
                                    <?= isset($draw->description) ? (strlen($draw->description) > 100 ? substr(htmlspecialchars($draw->description), 0, 100) . '...' : htmlspecialchars($draw->description)) : 'Heyecan verici ödüller kazanma şansı!' ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- İstatistikler Bölümü -->
                        <div class="cekilis-stats mt-3" style="display: flex; margin: 0 15px 12px; gap: 8px;">
                            <div style="flex: 1; background: var(--card-header-bg); padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; font-size: 1rem; color: var(--text-color);">
                                    <?php
                                    // Ödül havuzu hesaplama
                                    echo isset($rewardPoolTotal) && $rewardPoolTotal > 0 ? number_format($rewardPoolTotal, 2, ',', '.') . ' TL' : (isset($draw->reward_pool) ? number_format($draw->reward_pool, 2, ',', '.') . ' TL' : '25.000,00 TL');
                                    ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--footer-stat-label); margin-top: 2px;">Ödül Havuzu</div>
                            </div>
                            <div style="flex: 1; background: var(--card-header-bg); padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; font-size: 1rem; color: var(--text-color);"><?= $draw->participant_count ?></div>
                                <div style="font-size: 0.75rem; color: var(--footer-stat-label); margin-top: 2px;">Katılımcı</div>
                            </div>
                        </div>
                        
                        <!-- Durum Bilgisi -->
                        <div class="mt-3" style="display: flex; margin: 0 15px 12px;">
                            <div style="flex: 1; background: var(--card-header-bg); padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <div style="font-size: 1rem; font-weight: 600; color: var(--text-color);">
                                    <i class="fa-solid fa-flag-checkered me-1"></i> Sona Erdi
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ödüller Bölümü -->
                        <div class="cekilis-prizes" style="margin-bottom: 12px;">
                            <?php
                            $prizeCount = count($draw->rewards);
                            // Ürün tipi ödülleri ve bakiye ödüllerini ayır
                        
                            $productRewards = [];
                            $balanceTotal = 0;
                            $balanceWinnersCount = 0; // Bakiye kazanan toplam kişi sayısı
                        
                            foreach ($draw->rewards as $rwd) {
                                if (!is_object($rwd))
                                    continue; // Geçersiz verileri atla
                        
                                if ($rwd->type == 'urun' && isset($rwd->product_id)) {
                                    // Kazanan sayısını düzgün formatta al
                        
                                    if (isset($rwd->winners_count)) {
                                        if (is_string($rwd->winners_count)) {
                                            $rwd->winners_count = (int) $rwd->winners_count;
                                        }
                                        if ($rwd->winners_count <= 0) {
                                            $rwd->winners_count = 1;
                                        }
                                    } else {
                                        $rwd->winners_count = 1;
                                    }
                                    $productRewards[] = $rwd;
                                } else if ($rwd->type == 'bakiye' && isset($rwd->amount)) {
                                    $balanceTotal += floatval($rwd->amount);
                                    // Bakiye kazanan kişilerin sayısını topla
                        
                                    if (isset($rwd->winners_count)) {
                                        if (is_string($rwd->winners_count)) {
                                            $rwd->winners_count = (int) $rwd->winners_count;
                                        }
                                        $balanceWinnersCount += ($rwd->winners_count > 0) ? $rwd->winners_count : 1;
                                    } else {
                                        $balanceWinnersCount += 1;
                                    }
                                }
                            }
                            
                            // Eğer ürün yoksa ve sadece bakiye ödülü varsa, bakiye ödülünü göster
                            if (empty($productRewards) && $balanceTotal > 0) {
                                $productRewards = [new stdClass()];
                                $productRewards[0]->type = 'bakiye';
                                $productRewards[0]->amount = $balanceTotal;
                                $productRewards[0]->winners_count = $balanceWinnersCount > 0 ? $balanceWinnersCount : 1;
                            }
                            // Bakiye ödülünü ayrı bir ürün gibi ekle (eğer bakiye ödülü varsa ve henüz eklenmemişse)
                            if ($balanceTotal > 0 && !empty($productRewards) && $productRewards[0]->type != 'bakiye') {
                                $bakiyeOdul = new stdClass();
                                $bakiyeOdul->type = 'bakiye';
                                $bakiyeOdul->amount = $balanceTotal;
                                $bakiyeOdul->winners_count = $balanceWinnersCount > 0 ? $balanceWinnersCount : 1;
                                $productRewards[] = $bakiyeOdul;
                            }
                            ?>
                            <!-- 3x3 Grid Ödül Gösterimi -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 10px;">
                                <?php 
                                // En fazla 9 ödül göster
                                $displayRewards = array_slice($productRewards, 0, 9);
                                foreach ($displayRewards as $reward):
                                    $isProduct = ($reward->type == 'urun');
                                    
                                    if ($isProduct && isset($reward->product_id)) {
                                        $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                        $image = $product && !empty($product->img) ? 'assets/img/product/' . $product->img : 'assets/img/rewards/valorant-vp.jpg';
                                        $text = isset($reward->product_name) ? $reward->product_name : ($product ? $product->name : 'Ürün #' . $reward->product_id);
                                    } else {
                                        // Bakiye ödülü için eski görsel kullan
                                        $image = 'assets/img/category/bakiyekatagori55126.jpg';
                                        $amount = number_format($reward->amount, 0, '', '.');
                                        $text = $amount . ' TL';
                                    }
                                    
                                    $winners = getWinnerCount($reward);
                                ?>
                                <div style="position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
                                    <span style="width: 50%;text-align: center;position: absolute;top: 0;left: 50%;transform: translateX(-50%);background: rgb(29 29 29);color: #ffffff;padding: 3px 10px;border-radius: 0px 0px 10px 10px;font-size: 10px;font-weight: 600;z-index: 2;"><?= $winners ?> Kişiye</span>
                                    <?php if ($isProduct && isset($reward->product_id) && $product): ?>
                                        <a href="<?= base_url(($product->slug ?? $reward->product_id)) ?>" style="">
                                            <img src="<?= base_url($image) ?>" alt="<?= htmlspecialchars($text) ?>" style="">
                                        </a>
                                    <?php else: ?>
                                        <!-- Bakiye ödülü için sadece bakiye tutarı -->
                                        <div style="position: relative; ">
                                            <img src="<?= base_url($image) ?>" alt="Bakiye Ödülü" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;">
                                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding-top: 30px;">
                                                <div style="color: #ffffff; font-weight: 900; font-size: 2.1rem; text-shadow: 1px 1px 3px rgba(1px 1px 3px rgb(0 0 0 / 13%));"><?= $text ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if (count($productRewards) > 9): ?>
                                <div style="position: relative; display: flex; align-items: center; justify-content: center; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
                                    <a href="<?= base_url('cekilis/' . $draw->id) ?>" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: var(--text-color);">
                                        <i class="fa-solid fa-ellipsis" style="font-size: 1.5rem;"></i>
                                        <div style="font-size: 0.8rem; margin-top: 5px;">Daha Fazla</div>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Aksiyon Butonları -->
                        <div class="cekilis-actions" style="padding: 0 15px 15px; display: flex; gap: 10px;">
                            <a href="<?= base_url('cekilis/' . $draw->id) ?>" class="cekilis-btn cekilis-btn-details" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; display: block; text-align: center; text-decoration: none; transition: all 0.2s ease; background-color: var(--btn-join-bg); color: #fff;">
                                Detayları Gör
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($finished_draws)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i> Şu anda biten çekiliş bulunmamaktadır.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    // Tab geçişleri için script
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const target = this.getAttribute('data-bs-target');
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            document.querySelector(target).classList.add('show', 'active');
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        // Tüm swiper örneklerini başlat
        const prizesSwipers = document.querySelectorAll('.cekilis-prizes-swiper');
        prizesSwipers.forEach(function (swiperEl) {
            // Swiper öğesinde swiper-wrapper kontrolü yap
            const swiperWrapper = swiperEl.querySelector('.swiper-wrapper');
            if (swiperWrapper) {
                new Swiper(swiperEl, {
                    slidesPerView: 3,
                    spaceBetween: 15,
                    pagination: {
                        el: swiperEl.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    navigation: {
                        nextEl: swiperEl.querySelector('.swiper-button-next'),
                        prevEl: swiperEl.querySelector('.swiper-button-prev'),
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 3,
                            spaceBetween: 15,
                        },
                        640: {
                            slidesPerView: 3,
                            spaceBetween: 15,
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 15,
                        }
                    },
                });
            }
        });
        
        // Geri sayım sayacı için fonksiyon
        function updateCountdowns() {
            document.querySelectorAll('.countdown-box').forEach(function(box) {
                const targetTimestamp = parseInt(box.getAttribute('data-target'));
                if (!targetTimestamp) return;
                
                const now = new Date().getTime();
                const distance = targetTimestamp - now;
                
                // Eğer süre dolmuşsa
                if (distance < 0) {
                    if (box.querySelector('.days')) box.querySelector('.days').textContent = '0';
                    if (box.querySelector('.hours')) box.querySelector('.hours').textContent = '0';
                    if (box.querySelector('.minutes')) box.querySelector('.minutes').textContent = '0';
                    if (box.querySelector('.seconds')) box.querySelector('.seconds').textContent = '0';
                    return;
                }
                
                // Zaman hesaplamaları
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // İlgili değerleri güncelle
                if (box.querySelector('.days')) {
                    box.querySelector('.days').textContent = days;
                }
                if (box.querySelector('.hours')) {
                    box.querySelector('.hours').textContent = hours;
                }
                if (box.querySelector('.minutes')) {
                    box.querySelector('.minutes').textContent = minutes;
                }
                if (box.querySelector('.seconds')) {
                    box.querySelector('.seconds').textContent = seconds;
                }
            });
        }
        
        // Sayfa yüklendiğinde ve her saniye geri sayımı güncelle
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
        
        // Çekiliş arama fonksiyonu
        const searchInput = document.getElementById('searchDraws');
        const clearSearch = document.getElementById('clearSearch');
        const allCards = document.querySelectorAll('.cekilis-card');
        searchInput.addEventListener('keyup', filterDraws);
        function filterDraws() {
            const searchTerm = searchInput.value.toLowerCase();
            allCards.forEach(function (card) {
                const description = card.querySelector('h3')?.textContent.toLowerCase() ||
                    card.querySelector('h1')?.textContent.toLowerCase() || '';
                const isVisible = description.includes(searchTerm);
                card.style.display = isVisible ? '' : 'none';
            });
        }
        if (clearSearch) {
            clearSearch.addEventListener('click', function () {
                searchInput.value = '';
                allCards.forEach(function (card) {
                    card.style.display = '';
                });
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
        document.addEventListener('click', function (event) {
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
<?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
    <div
        style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h3>Debug: Çekiliş Ödülleri (draw_rewards)</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Çekiliş ID</th>
                    <th>Ürün ID</th>
                    <th>Ödül Tipi</th>
                    <th>Miktar</th>
                    <th>Kazanan Sayısı</th>
                    <th>Tanım</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Veritabanındaki tüm ödülleri listele
            
                $all_rewards = $this->db->get('draw_rewards')->result();
                foreach ($all_rewards as $rwd):
                    // Sadece ilk 20 ödülü göster
            
                    static $count = 0;
                    if ($count++ >= 20)
                        break;
                    ?>
                    <tr>
                        <td><?= $rwd->id ?></td>
                        <td><?= $rwd->draw_id ?></td>
                        <td><?= isset($rwd->product_id) ? $rwd->product_id : '-' ?></td>
                        <td><?= $rwd->type ?></td>
                        <td><?= isset($rwd->amount) ? $rwd->amount : '-' ?></td>
                        <td><?= isset($rwd->winners_count) ? $rwd->winners_count . ' (' . gettype($rwd->winners_count) . ')' : '-' ?>
                        </td>
                        <td><?= isset($rwd->description) ? $rwd->description : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
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

