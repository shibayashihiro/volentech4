<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class ManageLogin extends Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	protected $db;
	public function __construct() {
		
		// parent::__construct();
		date_default_timezone_set('EST');
		$this->db = Database::connect();
	}

	public function index() {
        return view('be/login/index'); // Directly return the view
    }
	
	public function login() {
		$username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
		// Query the database directly
        $builder = $this->db->table('tbluser');
        $result = $builder->select('ID')
                          ->where('Name', $username)
                          ->where('Password', $password)
                          ->limit(1)
                          ->get()
                          ->getRowArray();
		if ($result == null) {
			return redirect()->to('admin'); // Use redirect()->to() in CI4
		}

		session()->set('userId', $result['ID']); // Use session() helper

		return redirect()->to('admin/faqs');	
	}

	public function logout() {
        session()->remove('userId'); // Remove userId from session

        return redirect()->to('admin'); // Use redirect()->to() in CI4
    }
}