<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tugas extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('M_forum', 'm_forum');
		$this->load->helper('text');

		if ($this->session->userdata('masuk') != TRUE) {
			$url = base_url('login');
			redirect($url);
		};
	}

	public function index()
	{
		ob_start('ob_gzhandler');

		$akses = $this->session->userdata('akses');

		if ($akses == 2) {
			$this->load->view('siswa/layout/v_header');
			$this->load->view('siswa/layout/v_navbar');
			$this->load->view('siswa/v_tugas');
		} else {
			$this->load->view('pengajar/layout/v_header');
			$this->load->view('pengajar/layout/v_navbar');
			$this->load->view('pengajar/v_tugas');
		}
	}

	private function validasi_tugas()
	{
		$data = array();
		$data['inputerror'] = array();
		$data['error'] = array();
		$data['status'] = true;

		if ($this->input->post('judul_materi') == '') {
			$data['inputerror'][] = 'judul_materi';
			$data['error'][] = 'Judul tugas harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[a-zA-Z0-9,. ]+$/', strtoupper($this->input->post('judul_materi')))) {
			$data['inputerror'][] = 'judul_materi';
			$data['error'][] = 'Judul tugas tidak valid';
			$data['status'] = false;
		}

		if ($this->input->post('isi_materi') == '') {
			$data['inputerror'][] = 'isi_materi';
			$data['error'][] = 'Isi tugas harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[a-zA-Z0-9,. ]+$/', strtoupper($this->input->post('isi_materi')))) {
			$data['inputerror'][] = 'isi_materi';
			$data['error'][] = 'Isi tugas tidak valid';
			$data['status'] = false;
		}

		if ($data['status'] === false) {
			echo json_encode($data);
			exit();
		}
	}

	public function tugas($id)
	{
		$akses = $this->session->userdata('akses');

		$data['forum'] = $this->m_forum->get_forum($id);
		$data['tugas'] = $this->db->get_where('tbl_materi_tugas', ['id_forum' => $id])->result_array();

		if ($akses == 2) {
			$this->load->view('siswa/layout/v_header');
			$this->load->view('siswa/layout/v_navbar');
			$this->load->view('siswa/v_tugas', $data);
		} else {
			$this->load->view('pengajar/layout/v_header');
			$this->load->view('pengajar/layout/v_navbar');
			$this->load->view('pengajar/v_tugas', $data);
		}
	}

	public function get_siswa($nis)
	{
		if ($nis != 'NULL') {
			$data = $this->m_forum->get_siswa($nis);
			echo json_encode($data);
			exit;
		}
	}

	function data_id($id)
	{
		$qry = $this->db->get_where('tbl_komen_tugas', ['id_forum' => $id])->result_array();
		echo json_encode($qry);
		exit();
	}

	function datafr_id($id)
	{
		$qry = $this->db->get_where('tbl_materi_tugas', ['id_forum' => $id])->result_array();
		echo json_encode($qry);
		exit();
	}

	public function edit_tugas($id)
	{
		$data = $this->db->get_where('tbl_materi_tugas', ['id' => $id])->row_array();

		echo json_encode($data);
		exit;
	}

	public function delete_tugas($id)
	{
		$this->db->delete('tbl_materi_tugas', ['id' => $id]);
		echo json_encode(['status' => true]);
		exit;
	}

	public function save_tugas()
	{
		$this->validasi_tugas();

		$akses = $this->session->userdata('akses');

		if ($akses == 3) {
			$kd_mapel = $this->input->post('kd_mapel');

			$data = array(
				'id_forum' => $kd_mapel,
				'judul_materi' => $this->input->post('judul_materi'),
				'jns_materi' => $this->input->post('tipe_forum'),
				'isi_materi' => $this->input->post('isi_materi')
			);

			$cek = $this->db->get_where('tbl_materi_tugas', ['id_forum' => $kd_mapel]);
			if ($cek->num_rows() > 0) {
				$count = $cek->num_rows() + 1;

				$data['pertemuan'] = $count;

				$this->db->insert('tbl_materi_tugas', $data);
			} else {
				$data['pertemuan'] = 1;
				$this->db->insert('tbl_forum', ['fr_id_pelajaran' => $kd_mapel]);

				$this->db->insert('tbl_materi_tugas', $data);
			}

			// $this->diskusi($kd_mapel);
			echo json_encode(['status' => true]);
			exit;
		}
	}

	public function update_tugas()
	{
		$this->validasi_tugas();

		$akses = $this->session->userdata('akses');

		if ($akses == 3) {
			$data = array(
				'judul_materi' => $this->input->post('judul_materi'),
				'jns_materi' => $this->input->post('tipe_forum'),
				'isi_materi' => $this->input->post('isi_materi')
			);

			$this->db->update('tbl_materi_tugas', $data, ['id' => $this->input->post('id_fm')]);

			// $this->diskusi($kd_mapel);
			echo json_encode(['status' => true]);
			exit;
		}
	}




	public function submit_main()
	{
		$komen = $this->input->post('komentar');
		$id = $this->input->post('id');

		if (!empty($_FILES['gambar']['name'])) {
			$config['upload_path'] = './assets/test/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
			$config['max_size'] = '1024'; // max_size in kb
			$config['file_name'] = $_FILES['gambar']['name'];
			$this->load->library('upload', $config);

			// File upload
			if ($this->upload->do_upload('gambar')) {
				// Get data about the file
				$uploadData = $this->upload->data();

				$img = file_get_contents('./assets/test/' . $uploadData['file_name']);

				// Encode the image string data into base64 
				// $data = base64_encode($img); 

				$base_64 = base64_encode($img);
				$this->db->insert('tbl_base64', ['text' => $base_64]);
				unlink('./assets/test/' . $uploadData['file_name']);
				// $baseendoce = base64_decode($base_64);

				// $data = file_get_contents($base_64);

				// fclose( $ifp ); 
				// echo '<img src="data:image/webp;base64,'.$base_64.'" />';
			}
		}

		if (!empty($komen)) {
			$data = array(
				'id_forum' => $this->input->post('id_forum'),
				'pertemuan' => $this->input->post('pertemuan'),
				'reply_to' => 0,
				'user_komen' => $this->input->post('user_komen'),
				'isi_komen' => $this->input->post('komentar')
			);

			$this->db->insert('tbl_komen_tugas', $data);

			$cek_log = $this->db->get_where('tbl_log_forum', ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum']]);
			if ($cek_log->num_rows() > 0) {
				$log = $cek_log->row_array();

				if ($log['log_tugas'] != '') {
					$isi_log = $log['log_tugas'] . '::' . $data['pertemuan'];
					$this->db->update('tbl_log_forum', ['log_tugas' => $isi_log], ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum']]);
				} else {
					$this->db->update('tbl_log_forum', ['log_tugas' => $data['pertemuan']], ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum']]);
				}
			} else {
				$this->db->insert('tbl_log_forum', ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum'], 'log_tugas' => $data['pertemuan']]);
			}

			$this->session->set_flashdata('page', $data['pertemuan']);
			$this->session->set_flashdata('mention', $id);

			redirect(site_url('tugas/' . $data['id_forum']));
		}
	}

	public function submit_komen()
	{
		$komen = $this->input->post('komentar');
		$id = $this->input->post('id');

		if (!empty($komen)) {
			$data = array(
				'id_forum' => $this->input->post('id_forum'),
				'pertemuan' => $this->input->post('pertemuan'),
				'reply_to' => $this->input->post('reply_to'),
				'mention' => $this->input->post('mention'),
				'user_komen' => $this->input->post('user_komen'),
				'isi_komen' => $this->input->post('komentar')
			);

			$this->db->insert('tbl_komen_tugas', $data);

			$cek_log = $this->db->get_where('tbl_log_forum', ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum']]);
			if ($cek_log->num_rows() > 0) {
				$log = $cek_log->row_array();

				if ($log['log_tugas'] != '') {
					$isi_log = $log['log_tugas'] . '::' . $data['pertemuan'];
					$this->db->update('tbl_log_forum', ['log_tugas' => $isi_log], ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum']]);
				} else {
					$this->db->update('tbl_log_forum', ['log_tugas' => $data['pertemuan']], ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum']]);
				}
			} else {
				$this->db->insert('tbl_log_forum', ['nisn_siswa' => $data['user_komen'], 'id_forum' => $data['id_forum'], 'log_tugas' => $data['pertemuan']]);
			}



			$this->session->set_flashdata('page', $data['pertemuan']);
			$this->session->set_flashdata('mention', $id);

			redirect(site_url('tugas/' . $data['id_forum']));
		}
	}

	public function delete_komen($id)
	{
		$data = $this->db->get_where('tbl_komen_tugas', ['id' => $id])->row_array();

		$this->session->set_flashdata('page', $data['pertemuan']);

		$this->db->delete('tbl_komen_tugas', ['id' => $id]);
		$this->db->delete('tbl_komen_tugas', ['reply_to' => $id]);

		echo json_encode([
			'msg' => 'Komentar berhasil dihapus!'
		]);
		exit;
	}

	public function delete_subkomen($id)
	{
		$data = $this->db->get_where('tbl_komen_tugas', ['id' => $id])->row_array();

		$this->session->set_flashdata('page', $data['pertemuan']);

		$this->db->delete('tbl_komen_tugas', ['id' => $id]);

		echo json_encode([
			'msg' => 'Komentar berhasil dihapus!'
		]);
		exit;
	}

	public function edit_komen($id)
	{
		# code...
	}
}