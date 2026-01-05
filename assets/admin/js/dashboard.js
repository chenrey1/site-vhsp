// dashboard.js - Dashboard sayfasına özel kodlar

// Global değişkenler ve yardımcı fonksiyonlar
let refreshTimeout;
let timeLeft = 30;

function timeago(datetime) {
    const now = new Date();
    const past = new Date(datetime);
    const diff = Math.floor((now - past) / 1000);

    if (diff < 60) return 'Şu an';
    if (diff < 3600) return Math.floor(diff / 60) + ' dk önce';
    if (diff < 86400) return Math.floor(diff / 3600) + ' sa önce';
    return Math.floor(diff / 86400) + ' gün önce';
}

// Online kullanıcıları güncelleme
function updateLiveSummary() {
    $.ajax({
        url: ADMIN_URL + 'API/get_live_summary',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Online kullanıcı sayısını güncelle
            $('#onlineUserCount').html(`
                <div>
                    <h3 class="mb-0">${data.online_users}</h3>
                </div>
            `);

            // Aktif sayfaları güncelle
            let activePagesHtml = '';
            data.active_pages.forEach(page => {
                const percentage = (page.user_count / data.online_users) * 100;
                activePagesHtml += `
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-dark">${page.last_page}</span>
                            <span class="text-muted">${page.user_count} üye</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
            });

            $('#activePages').html(activePagesHtml);
            window.activePages = data.active_pages;
        }
    });
}

// Manuel yenileme fonksiyonu
function manualRefresh() {
    clearTimeout(refreshTimeout);
    timeLeft = 30;
    $('#refreshTimer').text(timeLeft);

    updateLiveSummary();

    const btn = $('.fa-sync-alt').closest('button');
    btn.prop('disabled', true);
    $('.fa-sync-alt').addClass('fa-spin');

    setTimeout(() => {
        btn.prop('disabled', false);
        $('.fa-sync-alt').removeClass('fa-spin');
    }, 1000);
}

// Satış durumu güncelleme
function updateSalesStatus() {
    $.ajax({
        url: ADMIN_URL + 'API/getSalesStatus',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#successfulSales').text(data.successful);
            $('#unsuccessfulSales').text(data.unsuccessful);
            $('#cancelledSales').text(data.cancelled);
            $('#pendingProducts').text(data.pending);
            $('#depositProducts').text(data.deposit);

            $('#successfulEarnings').text(data.successfulEarnings + '₺');
            $('#unsuccessfulEarnings').text(data.unsuccessfulEarnings + '₺');
            $('#cancelledEarnings').text(data.cancelledEarnings + '₺');
            $('#pendingEarnings').text(data.pendingEarnings + '₺');
            $('#depositEarnings').text(data.depositEarnings + '₺');
        }
    });
}

// Modal işlemleri
$(document).on('show.bs.modal', '#onlineUsersModal', function() {
    let modalHtml = '';
    const loadingHtml = '<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>';
    $('#modalContent').html(loadingHtml);

    // Online kullanıcıları getir
    $.ajax({
        url: ADMIN_URL + 'dashboard/getOnlineUsers',
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
                                <span><i class="fa fa-window-maximize me-2"></i>${page}</span>
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

// Sayfa yüklendiğinde
$(document).ready(function() {
    // Online kullanıcı güncelleme
    updateLiveSummary();
    setInterval(updateLiveSummary, 30000);

    // Sayaç güncelleme
    function updateTimer() {
        timeLeft--;
        $('#refreshTimer').text(timeLeft);

        if(timeLeft <= 0) {
            timeLeft = 30;
            updateLiveSummary();
        }
    }
    setInterval(updateTimer, 1000);

    // Satış durumu güncelleme
    updateSalesStatus();
    setInterval(updateSalesStatus, 60000); // Her dakika

    // Grafik başlatma
    if($('#myChart').length) {
        updateChart();
    }
});