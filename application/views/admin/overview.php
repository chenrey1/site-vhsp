<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satış Raporları</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Özel stil tanımlamaları */
        body {
            background-color: #f8f9fa;
        }
        .card-body-icon {
            position: absolute;
            top: -1.25rem;
            right: -1rem;
            opacity: 0.4;
            font-size: 5rem;
            transform: rotate(15deg);
        }
        .btn-group .btn.active {
            background-color: #007bff;
            color: white;
        }
        .btn-group .btn {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Sayfa Başlığı -->
    <h1 class="mt-4">Satış Raporları</h1>

    <!-- Filtreleme Seçenekleri -->
    <div class="btn-group mb-4" role="group" aria-label="Zaman Dilimi Seçimi">
        <button type="button" class="btn btn-primary">Günlük</button>
        <button type="button" class="btn btn-secondary">Haftalık</button>
        <button type="button" class="btn btn-secondary">Aylık</button>
        <button type="button" class="btn btn-secondary">Yıllık</button>
    </div>

    <!-- Özet Kartlar -->
    <div class="row">
        <!-- Toplam Satışlar Kartı -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body position-relative">
                    <div class="card-body-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="mr-5">Toplam Satışlar</div>
                    <h2>₺50,000</h2>
                </div>
            </div>
        </div>
        <!-- Diğer kartlar buraya eklenebilir -->
    </div>

    <!-- Satış Grafiği -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line mr-1"></i>
            Satış Grafiği
        </div>
        <div class="card-body">
            <canvas id="salesChart" width="100%" height="40"></canvas>
        </div>
    </div>

    <!-- Satış Tablosu -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Satış Detayları
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="salesTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Ürün Adı</th>
                        <th>Adet</th>
                        <th>Tutar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Satış verileri buraya dinamik olarak eklenecek -->
                    <tr>
                        <td>01/01/2021</td>
                        <td>Ürün A</td>
                        <td>10</td>
                        <td>₺1,000</td>
                    </tr>
                    <!-- Diğer satırlar -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap ve gerekli JS kütüphaneleri -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<!-- Popper.js, Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<!-- Font Awesome JS (Eğer ikonların JS sürümünü kullanıyorsanız) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>

<!-- JavaScript Kodu -->
<script>
    // Satış Grafiği Oluşturma
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line', // 'bar' olarak değiştirebilirsiniz
        data: {
            labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
            datasets: [{
                label: 'Satış Tutarı',
                data: [12000, 19000, 3000, 5000, 20000, 30000],
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    // Filtreleme Butonları
    const buttons = document.querySelectorAll('.btn-group .btn');
    buttons.forEach(button => {
        button.addEventListener('click', function () {
            // Aktif buton stilini güncelle
            buttons.forEach(btn => btn.classList.remove('btn-primary'));
            buttons.forEach(btn => btn.classList.add('btn-secondary'));
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');

            // Burada AJAX çağrısı yaparak verileri güncelleyebilirsiniz
            // Örnek:
            // var timeframe = this.textContent.toLowerCase();
            // updateChartData(timeframe);
            // updateTableData(timeframe);
        });
    });
</script>
</body>
</html>
