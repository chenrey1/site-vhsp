

<div class="card card-support2">
    <?php 
        $motherTicket = $this->db->where('id', $id)->get('ticket')->row();
        $user = $this->db->where('id', $motherTicket->user_id)->get('user')->row();
     ?>
    <div class="card-header">
        <img src="<?php echo base_url('assets/img/icons/profile.png'); ?>" alt="">
        <span>
            <h6 class="mb-0"><?= $user->name . " " . $user->surname ?></h6>
            <a href="<?= base_url('admin/product/closeTicket/') . $motherTicket->id ?>" class="text-danger">Desteği Sonlandır</a>
        </span>
    </div>
    <div class="card-body">
        <div class="sp2-content">

            <div class="sp2-messages">
                <div class="message-left">
                    <img src="<?=base_url()?>/assets/img/icons/profile.png" alt="Profil Resmi" class="img-profile">
                    <div class="sp2-message message-left">
                        <?= $motherTicket->message ?>
                    </div>
                    <small><?= $motherTicket->date ?></small>
                </div>
                <?php if ($this->db->where('ticket_id', $motherTicket->id)->count_all_results('ticket_answer') > 0) { ?>
                <?php foreach ($ticket as $t) { ?>
                	<?php $user = $this->db->where("id", $t->user_id)->get("user")->row(); ?>
                	<div class="message<?= ($user->isAdmin == 0) ? "-left" : '-right'; ?>">
                     <?php if ($user->isAdmin == 0): ?>
                        <?php if ($user->id == $motherTicket->seller_id) {
                            $image = 'seller.jpg';
                        }else{
                            $image = 'profile.png';
                        } ?>
                       <img src="<?= base_url('assets/img/icons/') . $image ?>" alt="Profil Resmi" class="img-profile"> 
                    <?php endif ?>
                    <div class="sp2-message <?= ($user->isAdmin == 0) ? 'message-left' : NULL; ?>">
                        <?= $t->answer ?>
                    </div>
                    <small><?= $t->date ?></small>
                </div>
            <?php } ?>
        <?php } ?>
            </div>

            <div class="sp2-form">
                <form action="<?= base_url('admin/product/answerTicket/') . $motherTicket->id; ?>" method="POST">
                    <div class="form-row">
                        <div class="col-11"><textarea class="form-control" rows="2" name="answer"></textarea></div>
                        <div class="col-1"><button type="submit" class="btn btn-primary btn-block"><i class="fa fa-chevron-right"></i></button></div>
                    </div>
                </form>

        </div>
    </div>
</div>