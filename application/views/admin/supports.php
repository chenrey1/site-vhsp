
<div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Destek</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Destek</li>
                            </ol>
                        </nav>

                        <div class="ons-card">
                            <div class="head border-bottom">
                                <h2 class="title">Destek Talepleri</h2>
                                <span class="desc text-muted"><?= $this->db->where('seller_id', 0)->where('status', 1)->count_all_results('ticket'); ?> Destek Talebi Yanıt Bekliyor</span>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="left border-right">
                                        <div class="buttons border-bottom">
                                            <ul class="nav nav-pills nav-justified" id="myTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" id="tab1" data-toggle="tab" href="#alan1" role="tab" aria-controls="alan1" aria-selected="true">Bekleyen</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="tab2" data-toggle="tab" href="#alan2" role="tab" aria-controls="alan2" aria-selected="false">Okunmuş</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="tab3" data-toggle="tab" href="#alan3" role="tab" aria-controls="alan3" aria-selected="false">Pazaryeri</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="tab4" data-toggle="tab" href="#alan4" role="tab" aria-controls="alan4" aria-selected="false">Kapanmış</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="scroll">
                                            <div class="tab-content" id="tabss">

                                                <div class="tab-pane fade show active" id="alan1" role="tabpanel">
                                                    <?php foreach ($tickets as $ticket) { ?>
                                                        <div class="support-box border-bottom" onclick="showTicket(<?=$ticket->id?>)">
                                                            <img src="<?=base_url()?>/assets/img/icons/profile.png" alt="Profil Resmi">
                                                            <div class="sp-content">
                                                                <h6><?= $ticket->title ?></h6>
                                                                <small><?= $ticket->name . " " . $ticket->surname . " - " . $ticket->date ?></small>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div class="tab-pane fade" id="alan2" role="tabpanel">
                                                    <?php foreach ($readTickets as $ticket) { ?>
                                                        <div class="support-box border-bottom" onclick="showTicket(<?=$ticket->id?>)">
                                                            <img src="<?=base_url()?>/assets/img/icons/profile.png" alt="Profil Resmi">
                                                            <div class="sp-content">
                                                                <h6><?= $ticket->title ?></h6>
                                                                <small><?= $ticket->name . " " . $ticket->surname . " - " . $ticket->date ?></small>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div class="tab-pane fade" id="alan3" role="tabpanel">
                                                    <?php foreach ($shopTickets as $ticket) { ?>
                                                        <div class="support-box border-bottom" onclick="showTicket(<?=$ticket->id?>)">
                                                            <img src="<?=base_url()?>/assets/img/icons/profile.png" alt="Profil Resmi">
                                                            <div class="sp-content">
                                                                <h6><?= $ticket->title ?></h6>
                                                                <small><?= $ticket->name . " " . $ticket->surname . " - " . $ticket->date ?></small>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div class="tab-pane fade" id="alan4" role="tabpanel">
                                                    <?php foreach ($closedTickets as $ticket) { ?>
                                                        <div class="support-box border-bottom" onclick="showTicket(<?=$ticket->id?>)">
                                                            <img src="<?=base_url()?>/assets/img/icons/profile.png" alt="Profil Resmi">
                                                            <div class="sp-content">
                                                                <h6><?= $ticket->title ?></h6>
                                                                <small><?= $ticket->name . " " . $ticket->surname . " - " . $ticket->date ?></small>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div id="showTicketPlace"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>

<script type="text/javascript">
    function showTicket(id)
    {
        $.post({
          url: "<?= base_url('admin/product/getTicket'); ?>",
          type: "POST",
          data: {"id": id},          
          success:function(e){ 
            $("#showTicketPlace").html();
            $("#showTicketPlace").html(e);
          }
        });
    }
</script>