<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Course extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('masuk') != TRUE) {
            $url = base_url('login');
            redirect($url);
        };

		$this->load->model('M_course', 'm_course');
	}

	public function index()
	{
		ob_start('ob_gzhandler');

		$akses = $this->session->userdata('akses');

		$data['course'] = $this->m_course->get_course();
		$data['kelas'] = $this->db->select('b.kelas_nama , b.kelas_id ,b.kls_jadwal')->from('tbl_siswa a')->join('tbl_kelas b', 'a.siswa_kelas_id = b.kelas_id', 'left')->where(['a.siswa_nis' => $this->session->userdata('user')])->get()->row_array();
		// $data['jadwal'] = $this->db->get('tbl_kelas', limit, offset);
		

		$test = array(
			array(
				'hari' => 'senin',
				'data' => array(
					array(
						'mapel' => 'Matematika',
						'tipe' => 'Kc'
					),
					array(
						'mapel' => 'Prakarya/TIK',
						'tipe' => 'Kc'
					),
					array(
						'mapel' => 'Ilmu Pengetahuan Sosial',
						'tipe' => 'Vc'
					)
				)
			), array(
				'hari' => 'selasa',
				'data' => array(
					array(
						'mapel' => 'bahasa indonesia',
						'tipe' => ''
					), array(
						'mapel' => 'Matematika',
						'tipe' => 'vc'
					)
				)
			), array(
				'hari' => 'rabu',
				'data' => array(
					array(
						'mapel' => 'bahasa indonesia',
						'tipe' => 'kc'
					), array(
						'mapel' => 'Matematika',
						'tipe' => 'vc'
					)
				)
			), array(
				'hari' => 'kamis',
				'data' => array(
					array(
						'mapel' => 'bahasa indonesia',
						'tipe' => 'kc'
					), array(
						'mapel' => 'Matematika',
						'tipe' => 'vc'
					)
				)
			), array(
				'hari' => 'jumat',
				'data' => array(
					array(
						'mapel' => 'bahasa indonesia',
						'tipe' => 'kc'
					), array(
						'mapel' => 'Matematika',
						'tipe' => 'vc'
					)
				)
			)
		);

// 		var_dump($test[0]);
		// echo serialize($test);
// 		die;

		if ($akses == 2) {
			$this->load->view('siswa/layout/v_header');
			$this->load->view('siswa/layout/v_navbar');
			$this->load->view('siswa/v_course', $data);
			$this->load->view('siswa/layout/v_footer');
		} else {
			$this->load->view('pengajar/layout/v_header');
			$this->load->view('pengajar/layout/v_navbar');
			$this->load->view('pengajar/v_course', $data);
			$this->load->view('pengajar/layout/v_footer');
		}
	}

	function view($id)
	{
		// $data = $this->db->get_where('tbl_pelajaran', ['id_pelajaran' => $id])->row_array();
		$data = $this->db->select('*')->from('tbl_pelajaran a')
			->join('tbl_mapel b', 'a.kd_mapel = b.kd_mapel', 'inner')
			->join('tbl_kelas c', 'a.id_kelas = c.kelas_id', 'inner')
			->where(['a.id_pelajaran' => $id])->get()->row_array();

		echo json_encode($data);
		exit();
	}

	function update_oc()
	{
		// var_dump($_POST); die;	
		// $validasi[] = $this->input->post('link_oc');
		// $validasi[] = $this->input->post('jdl_kelas');
		// $validasi[] = $this->input->post('start_on');



		$sql = $this->db->get_where('tbl_abs_oc', ['id_pelajaran' => $this->input->post('id')])->row_array();
		// var_dump($sql); die;

		$data = array(
			'link_oc' => $this->input->post('link_oc'),
			'tgl_oc' => $this->input->post('jdl_kelas'),
			'time_start' => $this->input->post('start_on'),
			'time_end' => $this->input->post('end_on'),
			'aktifkan' => $this->input->post('opsi_kls')
		);

		//validasi kiriman kosong
		foreach ($data as $key => $value) {
			if ($value === '') {
				echo json_encode(['msg' => 'eror']); //jika ada yang kosong
				exit();
			}
		}
		//end of validasi
		// var_dump($data->result_array());
		// if ($data['link_oc'] == "" || $data['tgl_oc'] == "" || $data['time_start'] == "" || $data['time_end'] == "" || $data['aktifkan'] == "") {
		// 	echo json_encode(['msg' => 'eror']);
		// 	exit();
		// }
		$new_abs1 = array(
			array(
				'tgl' => $this->input->post('jdl_kelas'),
				'data' => array(
					array(
						'nis' => 'null',
						'absensi' => 'null'
					)
				)
			)
		);

		if ($sql['dt_oc'] == null) {

			// belum ada data di tbl_abs_oc

			$this->db->insert('tbl_abs_oc', ['id_pelajaran' => $this->input->post('id')]);

			$this->db->update('tbl_abs_oc', ['dt_oc' => serialize($new_abs1)], ['id_pelajaran' => $this->input->post('id')]);

			$this->db->update('tbl_pelajaran', $data, ['id_pelajaran' => $this->input->post('id')]);
			echo json_encode(['msg' => 'Berhasil']);
			exit();
			// end of belum ada data di tbl_abs_oc
		}

		//sudah ada data
		if ($sql['dt_oc'] != null) {

			$dtunser = unserialize($sql['dt_oc']);

			foreach ($dtunser as $datuns) {
				if ($datuns['tgl'] == $this->input->post('jdl_kelas')) {
					// update hanya tbl pelajaran
					$this->db->update('tbl_pelajaran', $data, ['id_pelajaran' => $this->input->post('id')]);
					echo json_encode(['msg' => 'Berhasil']);
					exit();
				}
			}

			$dtfix = array_merge($dtunser, $new_abs1);
			// var_dump($dtfix);
			$this->db->update('tbl_abs_oc', ['dt_oc' => serialize($dtfix)], ['id_pelajaran' => $this->input->post('id')]);
			$this->db->update('tbl_pelajaran', $data, ['id_pelajaran' => $this->input->post('id')]);
			echo json_encode(['msg' => 'Berhasil']);
			exit();
		}

		//end of data lama

	}
}
