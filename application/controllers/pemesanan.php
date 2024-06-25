<?php

class Pemesanan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_pesanan');
		$this->load->helper('url');
		$this->load->library('form_validation');

		if ($this->session->userdata('role_id') != '2') {
			$this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Anda Belum Login!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>');
			redirect('auth/login');
		}
	}

	public function index()
	{
		$data['user'] = $this->model_pesanan->tampil_data()->result();
		$this->load->view('templates/header');
		$this->load->view('pemesanan', $data);
		$this->load->view('templates/footer');
	}

	public function tambah_aksi()
	{
		$conf = [['field' => 'id_jam', 'label' => 'Jam', 'rules' => 'trim|required']];
		$this->form_validation->set_rules($conf);
		$this->form_validation->set_message('required', '%s Harus Dipilih.');

		if ($this->form_validation->run() === FALSE) {
			echo validation_errors();
		} else {
			// Cek jika ada booking dengan tanggal, jam, dan lapangan yang sama
			$cekinputan = $this->model_pesanan->dTgl(
				$this->input->post('tgl_jadwal'),
				$this->input->post('id_jam'),
				$this->input->post('id_lapang')
			);

			if ($cekinputan->num_rows() > 0) {
				// Pesan kesalahan jika booking sudah ada
				$this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Silahkan pilih tanggal atau jam lain karena sudah di booking!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>');
				redirect('pemesanan');
			} else {
				// Memasukkan data booking ke database
				$data = [
					'nama'       => $this->input->post('nama'),
					'id_jam'     => $this->input->post('id_jam'),
					'tgl_jadwal' => $this->input->post('tgl_jadwal'),
					'id_lapang'  => $this->input->post('id_lapang'),
					'notelp'     => $this->input->post('notelp')
				];
				$this->model_pesanan->isiJadwal($data);
				redirect('proses');
			}
		}
	}
}
