            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Referans Ayarları</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">Referans</li>
                                <li class="breadcrumb-item active" aria-current="page">Referans Ayarları</li>
                            </ol>
                        </nav>

                        <div class="card card-referance">
                            <div class="card-body">
                                <form action="<?= base_url('admin/product/referencechange/allsales') ?>" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box">
                                                <h2>Davet eden kullanıcı;</h2>
                                                <div class="form-group form-flex">
                                                    <label for="">Her satıştan</label>
                                                    <input type="number" min="0" step=".01" value="<?= $reference_settings["all_sales"]->percent_referrer ?>" name="percent_referrer" class="form-control">
                                                    <label for="">yüzde alsın</label>
                                                </div>
                                                <div class="boxes row">
                                                    <div class="col-md-6">
                                                        <!--<div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck1" name="only_first_time_ref" <?= $reference_settings["all_sales"]->only_first_time_ref ? "checked" : "" ?>>
                                                            <label class="custom-control-label" for="customCheck1">Sadece ilk sefer</label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="box leftborder">
                                                <h2>Davet edilen kullanıcı;</h2>
                                                <div class="form-group form-flex">
                                                    <label for="">Her alıştan</label>
                                                    <input type="number" min="0" step=".01" value="<?= $reference_settings["all_sales"]->percent_user ?>" name="percent_user" class="form-control">
                                                    <label for="">yüzde alsın</label>
                                                </div>
                                                <div class="boxes row">
                                                    <div class="col-md-6">
                                                        <!--<div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck2" name="only_first_time" <?= $reference_settings["all_sales"]->only_first_time ? "checked" : "" ?>>
                                                            <label class="custom-control-label" for="customCheck2">Sadece ilk sefer</label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card card-referance">
                            <div class="card-body">
                                <form action="<?= base_url('admin/product/referencechange/register') ?>" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box">
                                                <h2>Davet eden kullanıcı;</h2>
                                                <div class="form-group form-flex">
                                                    <label for="">Her kayıttan</label>
                                                    <input type="number" min="0" step=".01" value="<?= $reference_settings["register"]->percent_referrer ?>" name="percent_referrer" class="form-control">
                                                    <label for="">TL bonus alsın</label>
                                                </div>
                                                <div class="boxes row">
                                                    <div class="col-md-6">
                                                        <!--<div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck3" name="only_first_time_ref" <?= $reference_settings["register"]->only_first_time_ref ? "checked" : "" ?>>
                                                            <label class="custom-control-label" for="customCheck3">Sadece ilk sefer</label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="box leftborder">
                                                <h2>Davet edilen kullanıcı;</h2>
                                                <div class="form-group form-flex">
                                                    <label for="">İlk kayıttan</label>
                                                    <input type="number" min="0" step=".01" name="percent_user" value="<?= $reference_settings["register"]->percent_user ?>" class="form-control">
                                                    <label for="">TL bonus alsın</label>
                                                </div>
                                                <div class="boxes row">
                                                    <div class="col-md-6">
                                                        <!--<div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck4" disabled>
                                                            <label class="custom-control-label" for="customCheck4">Sadece ilk sefer</label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </main>
