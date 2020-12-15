<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container">
            <!-- Announcement -->
            <?php $notif = $this->db->get('tbl_pengumuman')->row_array();
            if ($notif['aktifkan'] > 0) { ?>
                <div class="row">
                    <div class="offset-1 col-sm-10">
                        <div class="alert alert-info" role="alert">
                            <h4 class="alert-heading">Announcement!</h4>
                            <p><?= $notif['pengumuman_deskripsi'] ?></p>
                            <hr>
                            <p class="mb-0">&copy; Anak Panah Cyber Scholl.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- Next Agenda -->
            <div class="row">
                <div class="offset-1 col-sm-10">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Absensi Tugas ke <?= $this->uri->segment(3) ?> <?= $nm_mapel ?></h5>
                        </div>
                        <div class="card-body ">
                            <div class="box">
                                <div class="row">
                                    <div class="box-body">
                                        <div class="col-xs-12" style="width: 100%;">
                                            <div class="box-body">
                                                <table id="table" class="table table-striped table-hover" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 15px">No</th>
                                                            <th>NIS</th>
                                                            <th>Nama Siswa</th>
                                                            <th style="width: 30%">status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1;
                                                        foreach ($dt_siswa as $li) { ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td><?= $li['siswa_nis'] ?></td>
                                                                <td><?= $li['siswa_nama'] ?></td>
                                                                <td>

                                                                    <?php
                                                                    $data = $this->db->get_where('tbl_abs_model', ['siswa_nis' => $li['siswa_nis']])->row_array();
                                                                    // var_dump(unserialize($data['tgs_abs']));

                                                                    if (!empty($data['tgs_abs'])) {
                                                                        $dt_unser = unserialize($data['tgs_abs']);
                                                                        // var_dump($dt_unser);
                                                                        if (($key1 = array_search($this->uri->segment(2), array_column($dt_unser, 'idtg'))) !== false) {
                                                                            if (($key2 = array_search($this->uri->segment(3), array_column($dt_unser[$key1]['data'], 'tgk'))) !== false) {
                                                                                echo $dt_unser[$key1]['data'][$key2]['abs'];
                                                                            }else{
                                                                                echo 'Belum Diabsen';
                                                                            }
                                                                        } else {
                                                                            echo 'Belum Diabsen';
                                                                        }
                                                                    } else {
                                                                        echo 'Belum di absen';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal<?= $li['siswa_nis'] ?>">Absensi</button></td>
                                                            </tr>

                                                            <div class="modal fade bd-example-modal-lg" id="modal<?= $li['siswa_nis'] ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content" style="display: inline-block; text-align: center;">
                                                                        <div class="modal-header" style="display:block ruby; text-align: center;">
                                                                            <h5 class="modal-title">
                                                                                Apakah anda ingin mengabsensi <?= $li['siswa_nama'] ?>
                                                                            </h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <form action="<?= site_url('absensi/submit_absensi_tgs') ?>" method="post" autocomplete="off" enctype="multipart/form-data">
                                                                            <input type="hidden" name="nis" id="id" value="<?= $li['siswa_nis'] ?>">
                                                                            <input type="hidden" name="idtg" id="id_forum" value="<?= $this->uri->segment(2) ?>">
                                                                            <input type="hidden" name="idtgk" id="user_komen" value="<?= $this->uri->segment(3) ?>">
                                                                            <!-- <input type="hidden" name="idfk" id="user_komen" value="<?= $this->uri->segment(3) ?>"> -->
                                                                            <div class="form-check form-check-inline" style="margin-bottom: 20px; margin-top: 20px;">
                                                                                <input class="form-check-input" type="radio" name="absensi" id="inlineRadio1" value="Hadir">
                                                                                <label class="radio-inline form-check-label" for="inlineRadio1">Hadir</label>
                                                                                <input style="margin-left: 20px;" class="form-check-input" type="radio" name="absensi" id="inlineRadio2" value="Tidak Hadir">
                                                                                <label class="radio-inline form-check-label" for="inlineRadio2">Tidak Hadir</label>
                                                                            </div>


                                                                            <div class="input-group-append">
                                                                                <button style="width: 100%;" class="btn btn-primary" type="submit"> Submit</button>
                                                                            </div>

                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.box -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php $this->load->view('pengajar/layout/v_js'); ?>
    <script>
        $(document).ready(function() {
            $('#table').DataTable();
        });
    </script>