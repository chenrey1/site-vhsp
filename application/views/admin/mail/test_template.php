<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Test Mail Gönder</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/mail/test_template/' . $template_id) ?>" method="POST">
                        <div class="form-group">
                            <label for="test_email">Mail Adresi:</label>
                            <input type="email" class="form-control" id="test_email" name="test_email" required 
                                   placeholder="Test mailinin gönderileceği adresi giriniz">
                        </div>
                        <div class="text-right">
                            <a href="<?= base_url('admin/mail/templates') ?>" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-primary">Gönder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 