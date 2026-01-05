<div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Toplu Mail Gönder</h5>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <form action="" method="POST">
                                    <div class="card-mail">
                                        <h2 class="cm-title"><?= $properties->name ?></h2>
                                        <textarea name="content" rows="5" class="form-control" placeholder="E-Posta mesajını yazınız"></textarea>
                                    </div>
                                        <small>*Bu mail, mail almayı kabul eden tüm aktif üyelere gönderilecektir.</small>
                                    <button type="submit" class="btn btn-primary btn-block">Gönder</button>
                                </form>
                            </div>
                        </div>


                    </div>
                </main>