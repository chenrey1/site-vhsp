
<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Abonelik Ekle</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/dashboard">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/subscription/subSettings">Abonelik Ayarları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Yeni Abonelik</li>
                </ol>
            </nav>


            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url(); ?>admin/subscription/add_subscription" method="POST">
                        <div class="form-group row">
                            <label for="inputPName" class="col-sm-2 col-form-label">Abonelik Adı:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPName" class="col-sm-2 col-form-label">Abonelik Açıklaması:</label>
                            <div class="col-sm-10">
                                <textarea id="editor" rows="10" class="form-control" name="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPPrice" class="col-sm-2 col-form-label">Abonelik Fiyatı:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="1" class="form-control" id="inputPPrice" name="price" placeholder="Örneğin: 19.99" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon1">₺</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPDuration" class="col-sm-2 col-form-label">Abonelik Süresi (Gün):</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number" step="1" class="form-control" id="inputPDuration" name="duration" placeholder="Örneğin: 30" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="discount_commission" class="col-sm-2 col-form-label">Ürün Komisyon İndirimi:</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="discount_commission" name="discount_commission" required>
                                    <option value="0">Komisyon İndirimi Uygulama</option>
                                    <option value="1">Komisyon İndirimi Uygula</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row alert alert-primary" id="discount_commission_input" style="display: none;">
                            <label for="discount_commission_value" class="col-sm-2 col-form-label">Komisyon Miktarı (%):</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="discount_commission_value" min="0" max="100" placeholder="Örneğin: 1,99" name="discount_commission_value" step="any">
                                <small>Şu anki mevcut ödeme komisyonu oranınız: <b>%<?=$properties->commission?></b>. Bu aboneliği alanlar için uygulanmasını istediğiniz komisyonu yazın.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="refund_balance" class="col-sm-2 col-form-label">Satın Alım Bakiye İadesi:</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="refund_balance" name="refund_balance" required>
                                    <option value="0">Bakiye İadesi Uygulama</option>
                                    <option value="1">Bakiye İadesi Uygula</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row alert alert-primary" id="refund_balance_input" style="display: none;">
                            <label for="refund_balance_value" class="col-sm-2 col-form-label">İade Tutarı (Yüzde Olarak):</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="refund_balance_value" min="0" max="100" placeholder="Örneğin: 0.5" name="refund_balance_value" step="0.1">
                            </div>
                        </div>

                        <div class="form-group row alert alert-primary" id="refund_max_balance_input" style="display: none;">
                            <label for="refund_max_balance_value" class="col-sm-2 col-form-label">Maksimum İade Tutarı (TL):</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="refund_max_balance_value" min="0" placeholder="Örneğin: 15" name="refund_max_balance_value" step="0.1">
                                <small>Bir kullanıcı üye olduğunda iade edilebilecek maksimum bakiye miktarını sınırlayabilirsiniz. Sınır koymak istemiyorsanız <b>0</b> olarak bırakın.</small>
                            </div>
                        </div>

                        <button class="btn btn-primary float-right" id="submit_button"><i class="fa fa-plus"></i> Ekle</button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script>
        document.getElementById('discount_commission').addEventListener('change', function () {
            var discountInput = document.getElementById('discount_commission_input');
            if (this.value == '1') {
                discountInput.style.display = 'flex';
                document.getElementById('discount_commission_value').required = true;
            } else {
                discountInput.style.display = 'none';
                document.getElementById('discount_commission_value').required = false;
            }
        });

        document.getElementById('refund_balance').addEventListener('change', function () {
            var refundBalanceInput = document.getElementById('refund_balance_input');
            var refundMaxBalanceInput = document.getElementById('refund_max_balance_input');
            if (this.value == '1') {
                refundBalanceInput.style.display = 'flex';
                refundMaxBalanceInput.style.display = 'flex';
                document.getElementById('refund_balance_value').required = true;
                document.getElementById('refund_max_balance_value').required = true;
            } else {
                refundBalanceInput.style.display = 'none';
                refundMaxBalanceInput.style.display = 'none';
                document.getElementById('refund_balance_value').required = false;
                document.getElementById('refund_max_balance_value').required = false;
            }
        });
    </script>