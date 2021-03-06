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
            <div class="offset-1 col-sm-10 media-nav">
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6 media-align-center">
                        <!--<a href="<?= site_url('detail_absensi/absensi_forum/') . $this->session->userdata('username') ?>" class="btn btn-outline-success">Forum</a>-->
                        <a href="<?= site_url('detail_absensi/absensi_tugas/') . $this->session->userdata('username') ?>" class="btn btn-outline-success">Tugas</a>
                        <a href="<?= site_url('detail_absensi/absensi_oc/') . $this->session->userdata('username') ?>" class="btn btn-success">Kelas Online</a>
                        <a href="<?= site_url('detail_absensi/absensi_kc/') . $this->session->userdata('username') ?>" class="btn btn-outline-success">Kelas Komunitas</a>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($mapel as $dtmapel) {
                    ?>
                        <div class="col-sm-6">
                            <div class="card mapel">
                                <div class="card-header bg-primary ">
                                    <?= $dtmapel['nm_mapel'] ?>
                                </div>
                                <div class="card-body" style="padding-top: 0;">
                                    <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Absensi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $dataoc = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $dtmapel['id_pelajaran']])->row_array();
                                            if (!empty($dataoc)) {
                                                if ($dataoc['dt_oc'] !== null) {
                                                    $dt_unser = unserialize($dataoc['dt_oc']);
                                                    foreach ($dt_unser as $dt_oc) { ?>
                                                        <tr>
                                                            <td><?= $dt_oc['tgl'] ?></td>
                                                            <td>
                                                                <?php
                                                                if (($key = array_search($this->session->userdata('username'), array_column($dt_oc['data'], 'nis'))) !== false) {
                                                                    echo $dt_oc['data'][$key]['absensi'];
                                                                } else {
                                                                    echo 'Belum di Absen';
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                            <?PHP }
                                                } else {
                                                    echo '<tr><td colspan="4">Belum ada data</td><tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="4">Belum ada data</td><tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div><!-- /.container-fluid -->
        </div><!-- /.content -->
    </div>
</div>
<!-- /.content-wrapper -->

<?php $this->load->view('siswa/layout/v_js'); ?>