<!DOCTYPE html>
<html lang="tr">
...
</html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Dijital Ürün Satış Scripti</title>
		<link rel="icon" href="<?= base_url('icon.ico') ?>" type="image/x-icon" />
        <link rel="stylesheet" href="<?= base_url(); ?>assets/admin/css/styles.css">
        <link rel="stylesheet" href="<?= base_url(); ?>assets/admin/css/custom.css">
        <link rel="stylesheet" href="<?= base_url(); ?>vendor/admin/datatables/dataTables.bootstrap4.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="<?= base_url() ?>vendor/admin/bootstrap/bootstrap.bundle.min.js"></script>
        <script src="<?= base_url() ?>vendor/admin/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url() ?>vendor/admin/datatables/dataTables.bootstrap4.min.js"></script>
		<script src="<?= base_url() ?>/vendor/bootstrap-select/bootstrap-select.js"></script>
        <script src="<?= base_url() ?>assets/admin/js/speedy-init.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/datetime.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
		<link href="<?= base_url() ?>/vendor/bootstrap-select/bootstrap-select.css" rel="stylesheet" type="text/css">

		<!-- Trumbowyg -->
		<link rel="stylesheet" href="<?= base_url() ?>vendor/trumbowyg/ui/trumbowyg.min.css">
		<link rel="stylesheet" href="<?= base_url() ?>vendor/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css">
		<!-- Trumbowyg -->
		<!-- include libraries(jQuery, bootstrap) -->

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

		<script>
			$(document).ready(function(){
				$("#myToast").toast('show');
			});
			var baseURL = '<?= base_url() ?>';
		</script>
    </head>
    
	<div class="bs-example">
		<div style="position: relative; z-index: 2; top: 70px;">
			<div style="position: absolute; top: 0; right: 20px; min-width: 300px;">

				<?= alert() ?>

			</div>
		</div>
	</div>

    <?php if (isset($update_info)): ?>
        <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Yenilikler <small>v<?= $update_info["version"] ?></small></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul>
                        <?php 
                            echo "<li>" . preg_replace('#<br\s*/?>#i', "</li><li>", $update_info["patch_notes"]) . "</li>";
                        ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button onclick="updateScript()" id="updateButton" class="btn btn-success btn-block">Güncelle</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>

