<div id="layoutSidenav_content">

	<main>
		<div class="container-fluid">

			<div class="page-title">
				<h5 class="mb-0">Yetkili Listesi</h5>
			</div>

			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
					<li class="breadcrumb-item active" aria-current="page">Yetkili Listesi</li>
				</ol>
			</nav>

			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover table-bordered border dataTable table-product">
							<thead class="thead-light">
							<tr>
								<th>Yetki Sahibi</th>
								<th>Yetki</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($roles as $role) { ?>
								<tr>
									<td><?= $role->name . " " . $role->surname . " (" . $role->email . ")" ?></td>
									<td><?= $role->role ?></td>
									<td><?php if ($role->role != "Admin") { ?>
										<a href="<?= base_url('admin/users').'?edit_user='.$role->id ?>"><i class="fa fa-edit"></i></a>
									<?php } ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</main>
