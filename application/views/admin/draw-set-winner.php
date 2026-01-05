<?php $this->load->view('admin/includes/header'); ?>
<?php $this->load->view('admin/includes/sidebar'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manuel Kazanan Belirleme</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/draw') ?>">Çekilişler</a></li>
                        <li class="breadcrumb-item active">Manuel Kazanan Belirleme</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= $draw->name ?> - Manuel Kazanan Belirleme</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Dikkat! Bu işlem, seçilen kullanıcıyı çekiliş tamamlanmadan önce kazanan olarak belirleyecektir.
                            </div>
                            
                            <?php echo form_open('admin/draw/set_winner/' . $draw->id); ?>
                                <div class="form-group">
                                    <label>Katılımcı Seçin</label>
                                    <select name="participant_id" class="form-control select2" required>
                                        <option value="">Katılımcı Seçin</option>
                                        <?php foreach ($participants as $p): ?>
                                            <option value="<?= $p->id ?>"><?= $p->name ?> (<?= $p->email ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Ödül Seçin</label>
                                    <select name="reward_id" class="form-control select2" required>
                                        <option value="">Ödül Seçin</option>
                                        <?php foreach ($rewards as $r): ?>
                                            <option value="<?= $r->id ?>">
                                                <?php if ($r->type == 'bakiye'): ?>
                                                    <?= number_format($r->amount, 2, ',', '.') ?> ₺ Bakiye
                                                <?php else: ?>
                                                    <?= $r->product_name ?? 'Ürün #' . $r->product_id ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Kazanan Olarak Belirle</button>
                                    <a href="<?= base_url('admin/draw/detail/' . $draw->id) ?>" class="btn btn-default">İptal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $this->load->view('admin/includes/footer'); ?>

<script>
$(function() {
    $('.select2').select2();
});
</script> 
