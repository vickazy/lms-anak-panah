<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Keuangan_siswa extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('masuk') != TRUE) {
			$url = base_url('login');
			redirect($url);
		};
	}


	function index()
	{
		$this->load->view('admin/v_keuangan_siswa');
	}
}
