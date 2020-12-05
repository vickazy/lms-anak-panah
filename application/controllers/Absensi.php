<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Absensi extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('M_forum', 'm_forum');
        $this->load->helper('text');
        $this->load->model('M_course', 'm_course');

        if ($this->session->userdata('masuk') != TRUE) {
            $url = base_url('login');
            redirect($url);
        };
        $akses = $this->session->userdata('akses');
        if ($akses == 2) {
            $url = base_url('course');
            redirect($url);
        }
    }

    function attendent_fr($key, $mgg)
    {

        $where = $this->db->get_where('tbl_pelajaran', ['id_pelajaran' => $key])->row_array();
        $mapel = $this->db->get_where('tbl_mapel', ['kd_mapel' => $where['kd_mapel']])->row_array();
        
        $sql = $this->db->distinct()->select('siswa_nama,siswa_nis')->from('tbl_siswa a')->join('tbl_komen_forum b', 'a.siswa_nis=b.user_komen', 'inner')->where(['b.id_forum' => $key, 'b.pertemuan' => $mgg])->get()->result_array();
        
        $data['nm_mapel'] = $mapel['nm_mapel'];
        $data['dt_siswa'] = $sql;
        
        $this->load->view('pengajar/layout/v_header');
        $this->load->view('pengajar/layout/v_navbar');
        $this->load->view('pengajar/absensi/v_absensi', $data);
    }

    function submit_absensi_fr()
    {
        $dataserialize = $this->db->get_where('tbl_abs_model', ['siswa_nis' => $this->input->post('nis')]);

        $result = array();
        $new_abs = array();
        $status = true;
        $sts_new = true;

        if ($dataserialize->num_rows() > 0) {
            $unser = $dataserialize->row_array();
            $dataunser = unserialize($unser['fr_abs']);

            if ($dataunser == null) {
                $new_abs1 = array(
                    array(
                        'idf' => $this->input->post('idf'),
                        'data' => array(
                            array(
                                'frk' => $this->input->post('idfk'),
                                'abs' => $this->input->post('absensi')
                            )
                        )
                    )
                );
                $this->db->update('tbl_abs_model', ['fr_abs' => serialize($new_abs1)], ['siswa_nis' => $this->input->post('nis')]);
                echo "<script>window.history.go(-1);location.reload();</script>";
                // die;
            } else {
                foreach ($dataunser as $dtunser) {
                    $data1 = array();
                    // update absensi
                    if ($status === true) {
                        if ($dtunser['idf'] == $this->input->post('idf')) {
                            foreach ($dtunser['data'] as $val) {
                                if ($sts_new === true) {
                                    if ($val['frk'] === $this->input->post('idfk')) {
                                        $val['abs'] = $this->input->post('absensi');
                                    } else {
                                        //update sub absensi jika minggu ke sama
                                        $data1[] =
                                            array(
                                                'frk' => $this->input->post('idfk'),
                                                'abs' => $this->input->post('absensi')
                                            );
                                        $sts_new = false;
                                    }
                                }

                                $data1[] = $val;
                            }
                            $temp = array_unique(array_column($data1, 'frk'));
                            $unique_arr = array_intersect_key($data1, $temp);
                            $dtunser['data'] = $unique_arr;
                            $status = false;
                        } else {
                            $new_abs = array(
                                'idf' => $this->input->post('idf'),
                                'data' => array(
                                    array(
                                        'frk' => $this->input->post('idfk'),
                                        'abs' => $this->input->post('absensi')
                                    )
                                )
                            );
                            $result[] = $new_abs;
                        }
                    }
                    // $merge = array_merge($dtunser , $new_abs)

                    $result[] = $dtunser;
                }
                // var_dump($result); die;
                $this->db->update('tbl_abs_model', ['fr_abs' => serialize($result)], ['siswa_nis' => $this->input->post('nis')]);
                // var_dump($result[0]);
                echo "<script>window.history.go(-1);location.reload();</script>";
                // die;
            }
        } else {
            $new_abs1 = array(
                array(
                    'idf' => $this->input->post('idf'),
                    'data' => array(
                        array(
                            'frk' => $this->input->post('idfk'),
                            'abs' => $this->input->post('absensi')
                        )
                    )
                )
            );
            $this->db->insert('tbl_abs_model', ['siswa_nis' => $this->input->post('nis'), 'fr_abs' => serialize($new_abs1)]);
            echo "<script>window.history.go(-1);location.reload();</script>";
        }
    }

    function attendent_tgs($key, $mgg)
    {

        $where = $this->db->get_where('tbl_pelajaran', ['id_pelajaran' => $key])->row_array();
        $mapel = $this->db->get_where('tbl_mapel', ['kd_mapel' => $where['kd_mapel']])->row_array();


        $sql = $this->db->distinct()->select('siswa_nama,siswa_nis')->from('tbl_siswa a')->join('tbl_komen_tugas b', 'a.siswa_nis=b.user_komen', 'inner')->where(['b.id_forum' => $key, 'b.pertemuan' => $mgg])->get()->result_array();

        $data['nm_mapel'] = $mapel['nm_mapel'];
        $data['dt_siswa'] = $sql;

        $this->load->view('pengajar/layout/v_header');
        $this->load->view('pengajar/layout/v_navbar');
        $this->load->view('pengajar/v_absensi_tgs', $data);
    }

    function submit_absensi_tgs()
    {
        $dataserialize = $this->db->get_where('tbl_abs_model', ['siswa_nis' => $this->input->post('nis')]);

        $result = array();
        $new_abs = array();
        $status = true;
        $sts_new = true;

        if ($dataserialize->num_rows() > 0) {
            $unser = $dataserialize->row_array();
            $dataunser = unserialize($unser['tgs_abs']);

            if ($dataunser == null) {
                $new_abs1 = array(
                    array(
                        'idtg' => $this->input->post('idtg'),
                        'data' => array(
                            array(
                                'tgk' => $this->input->post('idtgk'),
                                'abs' => $this->input->post('absensi')
                            )
                        )
                    )
                );
                $this->db->update('tbl_abs_model', ['tgs_abs' => serialize($new_abs1)], ['siswa_nis' => $this->input->post('nis')]);
                echo "<script>window.history.go(-1);location.reload();</script>";
                // die;
            } else {
                foreach ($dataunser as $dtunser) {
                    $data1 = array();
                    // update absensi
                    if ($status === true) {
                        if ($dtunser['idtg'] == $this->input->post('idtg')) {
                            foreach ($dtunser['data'] as $val) {
                                if ($sts_new === true) {
                                    if ($val['tgk'] === $this->input->post('idtgk')) {
                                        $val['abs'] = $this->input->post('absensi');
                                    } else {
                                        //update sub absensi jika minggu ke sama
                                        $data1[] =
                                            array(
                                                'tgk' => $this->input->post('idtgk'),
                                                'abs' => $this->input->post('absensi')
                                            );
                                        $sts_new = false;
                                    }
                                }

                                $data1[] = $val;
                            }
                            $temp = array_unique(array_column($data1, 'tgk'));
                            $unique_arr = array_intersect_key($data1, $temp);

                            $dtunser['data'] = $unique_arr;
                            $status = false;
                        } else {
                            $new_abs = array(
                                'idtg' => $this->input->post('idtg'),
                                'data' => array(
                                    array(
                                        'tgk' => $this->input->post('idtgk'),
                                        'abs' => $this->input->post('absensi')
                                    )
                                )
                            );
                            $result[] = $new_abs;
                        }
                    }

                    $result[] = $dtunser;
                }
                $this->db->update('tbl_abs_model', ['tgs_abs' => serialize($result)], ['siswa_nis' => $this->input->post('nis')]);
                // var_dump($result[0]);
                echo "<script>window.history.go(-1);location.reload();</script>";
                // die;
            }
        } else {
            $new_abs1 = array(
                array(
                    'idtg' => $this->input->post('idtg'),
                    'data' => array(
                        array(
                            'tgk' => $this->input->post('idtgk'),
                            'abs' => $this->input->post('absensi')
                        )
                    )
                )
            );
            $this->db->insert('tbl_abs_model', ['siswa_nis' => $this->input->post('nis'), 'tgs_abs' => serialize($new_abs1)]);
            echo "<script>window.history.go(-1);location.reload();</script>";
        }
    }

    public function attendent_oc($key)
    {
        $sql1 = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $key])->row_array();
        $unser = unserialize($sql1['dt_oc']);
        // $data['nm_mapel'] = $mapel['nm_mapel'];
        $data['dt_tgl'] = $unser;

        // var_dump($unser); die;


        $this->load->view('pengajar/layout/v_header');
        $this->load->view('pengajar/layout/v_navbar');
        if ($unser == null) {
            $this->load->view('pengajar/absensi/v_absensi_oc');
        } else {
            $this->load->view('pengajar/absensi/v_absensi_oc', $data);
        }
    }

    public function list_siswa_oc($idpel, $tgl)
    {
        // $where1 =  $this->db->get_where('tbl_pelajaran', ['id_oc' => $idpel])->row_array();
        // $where = $this->db->get_where('tbl_abs_oc', ['id_oc' => $idpel])->row_array();
        $dtsiswa = $this->db->select('siswa_nis,siswa_nama')->from('tbl_siswa a')->join('tbl_pelajaran b', 'a.siswa_kelas_id=b.id_kelas', 'inner')->where(['b.id_pelajaran' => $idpel, 'a.oc' => '1'])->get()->result_array();
        $data['dt_siswa'] = $dtsiswa;
        $this->load->view('pengajar/layout/v_header');
        $this->load->view('pengajar/layout/v_navbar');
        $this->load->view('pengajar/absensi/v_absensi_oc_list', $data);
    }

    function submit_absensi_oc()
    {
        $dataserialize = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $this->input->post('idoc')]);

        $result = array();
        $new_abs = array();
        $status = true;
        $sts_new = true;


        if ($dataserialize->num_rows() > 0) {
            $unser = $dataserialize->row_array();
            $dataunser = unserialize($unser['dt_oc']);
            if ($dataunser == null) {
                $new_abs1 = array(
                    array(
                        'tgl' => $this->input->post('tgl'),
                        'data' => array(
                            array(
                                'nis' => $this->input->post('nis'),
                                'abs' => $this->input->post('absensi')
                            )
                        )
                    )
                );
                $this->db->update('tbl_abs_model', ['dt_oc' => serialize($new_abs1)], ['id_pelajaran' => $this->input->post('idoc')]);
                echo "<script>window.history.go(-1);location.reload();</script>";
                // die;
            } else {
                foreach ($dataunser as $dtunser) {
                    $data1 = array();
                    // update absensi
                    if ($status === true) {
                        if ($dtunser['tgl'] == $this->input->post('tgl')) {
                            foreach ($dtunser['data'] as $val) {

                                if ($sts_new === true) {
                                    if ($val['nis'] === $this->input->post('nis')) {
                                        $val['abs'] = $this->input->post('absensi');
                                    } else {
                                        // 
                                        //update sub absensi
                                        $data1[] =
                                            array(
                                                'nis' => $this->input->post('nis'),
                                                'abs' => $this->input->post('absensi')
                                            );

                                        $sts_new = false;
                                    }
                                }
                                $data1[] = $val;
                            }
                            // var_dump($data1);
                            if (($key = array_search('null', array_column($data1, 'nis'))) !== false) {
                                unset($data1[$key]);
                            }
                            // unset($data1[1]);
                            // var_dump($data1);
                            $temp = array_unique(array_column($data1, 'nis'));
                            $unique_arr = array_intersect_key($data1, $temp);

                            $dtunser['data'] = $unique_arr;
                            $status = false;
                        } else {
                            $new_abs = array(
                                'tgl' => $this->input->post('nis'),
                                'data' => array(
                                    array(
                                        'nis' => $this->input->post('nis'),
                                        'abs' => $this->input->post('absensi')
                                    )
                                )
                            );
                        }
                    }


                    // var_dump($dtunser);

                    $result[] = $dtunser;
                }
                $this->db->update('tbl_abs_oc', ['dt_oc' => serialize($result)], ['id_pelajaran' => $this->input->post('idoc')]);

                echo "<script>window.history.go(-1);location.reload();</script>";
                // var_dump($result);
                // die;
            }
            // var_dump($result[1]);
        } else {
            $new_abs1 = array(
                array(
                    'tgl' => $this->input->post('tgl'),
                    'data' => array(
                        array(
                            'nis' => $this->input->post('nis'),
                            'abs' => $this->input->post('absensi')
                        )
                    )
                )
            );
            $this->db->insert('tbl_abs_oc', ['id_pelajaran' => $this->input->post('idoc'), 'tgs_abs' => serialize($new_abs1)]);
            echo "<script>window.history.go(-1);location.reload();</script>";
        }
    }

    function hapus_tgl_oc($idpel, $tgl)
    {
        // var_dump($idpel); die;
        $sql = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $idpel])->row_array();
        $dtunsersql = unserialize($sql['dt_oc']);
        foreach ($dtunsersql as $key => $value) {
            if ($value['tgl'] == $tgl) {
                unset($dtunsersql[$key]);
            }
        }
        $dtfix = array_merge($dtunsersql);
        // var_dump($dtfix); die;
        $this->db->update('tbl_abs_oc', ['dt_oc' => serialize($dtfix)], ['id_pelajaran' => $idpel]);
        echo "<script>window.history.go(-1);location.reload();</script>";
        // a:3:{i:0;a:2:{s:3:"tgl";s:10:"2020-09-08";s:4:"data";a:4:{i:0;a:2:{s:3:"nis";s:7:"2019638";s:3:"abs";s:5:"hadir";}i:1;a:2:{s:3:"nis";s:7:"2019639";s:3:"abs";s:5:"hadir";}i:2;a:2:{s:3:"nis";s:7:"2019636";s:3:"abs";s:5:"hadir";}i:3;a:2:{s:3:"nis";s:7:"2019644";s:3:"abs";s:5:"hadir";}}}i:1;a:2:{s:3:"tgl";s:10:"2020-08-20";s:4:"data";a:2:{i:0;a:2:{s:3:"nis";s:7:"2019638";s:3:"abs";s:5:"hadir";}i:1;a:2:{s:3:"nis";s:7:"2019644";s:3:"abs";s:5:"hadir";}}}i:2;a:2:{s:3:"tgl";s:10:"2020-08-28";s:4:"data";a:1:{i:0;a:2:{s:3:"nis";s:7:"2019638";s:3:"abs";s:5:"hadir";}}}}
    }

    function attendent_kc($key)
    {
        // var_dump($_POST); die;
        $sql1 = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $key])->row_array();
        if ($sql1 === null) {
            $this->db->insert('tbl_abs_oc', ['id_pelajaran' => $key]);
            echo "<script>location.reload();</script>";
        }
        $unser = unserialize($sql1['dt_kc']);
        $where = $this->db->get_where('tbl_pelajaran', ['id_pelajaran' => $key])->row_array();
        $mapel = $this->db->get_where('tbl_mapel', ['kd_mapel' => $where['kd_mapel']])->row_array();
        $data1['nm_mapel'] = $mapel['nm_mapel'];
        $data['dt_tgl'] = $unser;
        $data['nm_mapel'] = $data1['nm_mapel'];



        $this->load->view('pengajar/layout/v_header');
        $this->load->view('pengajar/layout/v_navbar');
        if ($unser == null) {
            $this->load->view('pengajar/absensi/v_absensi_kc', $data1);
        } else {
            $this->load->view('pengajar/absensi/v_absensi_kc', $data);
        }
    }

    function add_jadwal_kc()
    {
        // var_dump($_POST); die;
        $sql = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $this->input->post('id')])->row_array();
        if ($sql == null) {
            $this->db->insert('tbl_abs_oc', ['id_pelajaran' => $this->input->post('id')]);
            $new_abs1 = array(
                array(
                    'tgl' => $this->input->post('jdl_kelas'),
                    'start' => $this->input->post('start_on'),
                    'end' => $this->input->post('end_on'),
                    'data' => array(
                        array(
                            'nis' => 'null',
                            'absensi' => 'null'
                        )
                    )
                )
            );
            $this->db->update('tbl_abs_oc', ['dt_kc' => serialize($new_abs1)], ['id_pelajaran' => $this->input->post('id')]);
        }

        //data yang lama
        if ($sql['dt_kc'] != null) {
            $dtunser = unserialize($sql['dt_kc']);
            foreach ($dtunser as $datuns) {
                if ($datuns['tgl'] == $this->input->post('jdl_kelas')) {
                    echo "<script>alert('data sudah ada');window.history.go(-1);location.reload();</script>";
                    die;
                }
            }
            $new_abs1 = array(
                array(
                    'tgl' => $this->input->post('jdl_kelas'),
                    'start' => $this->input->post('start_on'),
                    'end' => $this->input->post('end_on'),
                    'data' => array(
                        array(
                            'nis' => 'null',
                            'absensi' => 'null'
                        )
                    )
                )
            );
            $dtfix = array_merge($dtunser, $new_abs1);
            $this->db->update('tbl_abs_oc', ['dt_kc' => serialize($dtfix)], ['id_pelajaran' => $this->input->post('id')]);
            echo "<script>alert('Data Berhasil Disimpan');window.history.go(-1);location.reload();</script>";
        }

        if ($sql['dt_kc'] == null) {
            $new_abs1 = array(
                array(
                    'tgl' => $this->input->post('jdl_kelas'),
                    'start' => $this->input->post('start_on'),
                    'end' => $this->input->post('end_on'),
                    'data' => array(
                        array(
                            'nis' => 'null',
                            'absensi' => 'null'
                        )
                    )
                )
            );
            $this->db->update('tbl_abs_oc', ['dt_kc' => serialize($new_abs1)], ['id_pelajaran' => $this->input->post('id')]);
            echo "<script>alert('Data Berhasil Disimpan');window.history.go(-1);location.reload();</script>";
            die;
        }

        //end of data lama

    }

    function hapus_tgl_kc($idpel, $tgl)
    {
        // var_dump($idpel); die;
        $sql = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $idpel])->row_array();
        $dtunsersql = unserialize($sql['dt_kc']);
        foreach ($dtunsersql as $key => $value) {
            if ($value['tgl'] == $tgl) {
                unset($dtunsersql[$key]);
            }
        }
        $dtfix = array_merge($dtunsersql);
        // var_dump($dtfix); die;
        $this->db->update('tbl_abs_oc', ['dt_kc' => serialize($dtfix)], ['id_pelajaran' => $idpel]);
        echo "<script>window.history.go(-1);location.reload();</script>";
        // a:3:{i:0;a:2:{s:3:"tgl";s:10:"2020-09-08";s:4:"data";a:4:{i:0;a:2:{s:3:"nis";s:7:"2019638";s:3:"abs";s:5:"hadir";}i:1;a:2:{s:3:"nis";s:7:"2019639";s:3:"abs";s:5:"hadir";}i:2;a:2:{s:3:"nis";s:7:"2019636";s:3:"abs";s:5:"hadir";}i:3;a:2:{s:3:"nis";s:7:"2019644";s:3:"abs";s:5:"hadir";}}}i:1;a:2:{s:3:"tgl";s:10:"2020-08-20";s:4:"data";a:2:{i:0;a:2:{s:3:"nis";s:7:"2019638";s:3:"abs";s:5:"hadir";}i:1;a:2:{s:3:"nis";s:7:"2019644";s:3:"abs";s:5:"hadir";}}}i:2;a:2:{s:3:"tgl";s:10:"2020-08-28";s:4:"data";a:1:{i:0;a:2:{s:3:"nis";s:7:"2019638";s:3:"abs";s:5:"hadir";}}}}
    }

    function list_siswa_kc($idpel, $tgl)
    {
        // $where1 =  $this->db->get_where('tbl_pelajaran', ['id_oc' => $idpel])->row_array();
        // $where = $this->db->get_where('tbl_abs_oc', ['id_oc' => $idpel])->row_array();
        $dtsiswa = $this->db->select('siswa_nis,siswa_nama')->from('tbl_siswa a')->join('tbl_pelajaran b', 'a.siswa_kelas_id=b.id_kelas', 'inner')->where(['b.id_pelajaran' => $idpel, 'a.kc' => '1'])->get()->result_array();
        $data['dt_siswa'] = $dtsiswa;
        $this->load->view('pengajar/layout/v_header');
        $this->load->view('pengajar/layout/v_navbar');
        $this->load->view('pengajar/absensi/v_absensi_kc_list', $data);
    }

    function submit_absensi_kc()
    {
        $dataserialize = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $this->input->post('idkc')]);

        $result = array();
        $new_abs = array();
        $status = true;
        $sts_new = true;


        if ($dataserialize->num_rows() > 0) {
            $unser = $dataserialize->row_array();
            $dataunser = unserialize($unser['dt_kc']);
            if ($dataunser == null) {
                $new_abs1 = array(
                    array(
                        'tgl' => $this->input->post('tgl'),
                        'data' => array(
                            array(
                                'nis' => $this->input->post('nis'),
                                'abs' => $this->input->post('absensi')
                            )
                        )
                    )
                );
                $this->db->update('tbl_abs_model', ['dt_kc' => serialize($new_abs1)], ['id_pelajaran' => $this->input->post('idkc')]);
                echo "<script>window.history.go(-1);location.reload();</script>";
                // die;
            } else {
                foreach ($dataunser as $dtunser) {
                    $data1 = array();
                    // update absensi
                    if ($status === true) {
                        if ($dtunser['tgl'] == $this->input->post('tgl')) {
                            foreach ($dtunser['data'] as $val) {

                                if ($sts_new === true) {
                                    if ($val['nis'] === $this->input->post('nis')) {
                                        $val['abs'] = $this->input->post('absensi');
                                    } else {
                                        // 
                                        //update sub absensi
                                        $data1[] =
                                            array(
                                                'nis' => $this->input->post('nis'),
                                                'abs' => $this->input->post('absensi')
                                            );

                                        $sts_new = false;
                                    }
                                }
                                $data1[] = $val;
                            }
                            // var_dump($data1);
                            if (($key = array_search('null', array_column($data1, 'nis'))) !== false) {
                                unset($data1[$key]);
                            }
                            // unset($data1[1]);
                            // var_dump($data1);
                            $temp = array_unique(array_column($data1, 'nis'));
                            $unique_arr = array_intersect_key($data1, $temp);

                            $dtunser['data'] = $unique_arr;
                            $status = false;
                        } else {
                            $new_abs = array(
                                'tgl' => $this->input->post('nis'),
                                'data' => array(
                                    array(
                                        'nis' => $this->input->post('nis'),
                                        'abs' => $this->input->post('absensi')
                                    )
                                )
                            );
                        }
                    }


                    // var_dump($dtunser);

                    $result[] = $dtunser;
                }
                $this->db->update('tbl_abs_oc', ['dt_kc' => serialize($result)], ['id_pelajaran' => $this->input->post('idkc')]);

                echo "<script>window.history.go(-1);location.reload();</script>";
                // var_dump($result);
                // die;
            }
            // var_dump($result[1]);
        } else {
            $new_abs1 = array(
                array(
                    'tgl' => $this->input->post('tgl'),
                    'data' => array(
                        array(
                            'nis' => $this->input->post('nis'),
                            'abs' => $this->input->post('absensi')
                        )
                    )
                )
            );
            $this->db->insert('tbl_abs_oc', ['id_pelajaran' => $this->input->post('idkc'), 'tgs_abs' => serialize($new_abs1)]);
            echo "<script>window.history.go(-1);location.reload();</script>";
        }
    }
}