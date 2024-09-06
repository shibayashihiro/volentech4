<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;
use App\Libraries\Googlemaps;
use CodeIgniter\Email\Email;
class Home extends Controller {

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
	protected $db; // Declare the db property
	public function __construct() {
		
		// parent::__construct();
		date_default_timezone_set('EST');
		$this->db = Database::connect();
	}

	public function index()
	{
		$data = [];
		
        // Fetch comments
        $data['comments'] = $this->db->table('tblcomments')->get()->getResultArray();

		// Fetch articles
        $data['articles'] = $this->db->table('tblarticles')->get()->getResultArray();

		// Fetch portfolios
        $portfolios = $this->db->table('tblportfolios')->orderBy('ID', 'desc')->limit(2)->get()->getResultArray();
		$portfolios_array = [];
        foreach ($portfolios as $portfolio) {
            // Fetch slides
            $portfolio['Slides'] = $this->db->table('tblslides')->where('PortfolioID', $portfolio['ID'])->get()->getResultArray();
            // Fetch descriptions
            $portfolio['Descriptions'] = $this->db->table('tblport_descriptions')->where('PortfolioID', $portfolio['ID'])->get()->getResultArray();
            $portfolios_array[] = $portfolio;
        }

        $data['portfolios'] = $portfolios_array;
		
        return view('fe/home', $data); // Correct view loading
    }

	public function hardware()
    {
        return view('fe/hardware');
    }

    public function embedded()
    {
        return view('fe/embedded');
    }

    public function software()
    {
        return view('fe/software');
    }

    public function aboutUs()
    {
        return view('fe/about-us');
    }


	public function projects() {
		$portfolios = $this->db->table('tblportfolios')->get()->getResultArray();

        $portfolios_array = [];
        foreach ($portfolios as $portfolio) {
            $portfolio['Descriptions'] = $this->db->table('tblport_descriptions')->where('PortfolioID', $portfolio['ID'])->get()->getResultArray();
            $portfolio['Slides'] = $this->db->table('tblslides')->where('PortfolioID', $portfolio['ID'])->get()->getResultArray();
            $portfolios_array[] = $portfolio;
        }

        $data['portfolios'] = $portfolios_array;

        return view('fe/projects', $data);
	}

	public function contactUs() {
		// Load Google Maps library
        // Assuming you have the library set up correctly
        $googleMap = new Googlemaps();

        $config = [
            'center' => '37.3384194733874, -122.01663868654052',
            'zoom' => 17,
        ];

        // Initialize the Google Map
        $googleMap->initialize($config);

        $marker = [
            'position' => "37.3384194733874, -122.01663868654052",
            'title' => "Londonderry Office",
            'label' => "Londonderry Office",
            'infowindow_content' => "Phone Number : (123)456-7890<br>Address : 824 Londonderry Drive Sunnyvale, CA 94087<br>Email : yugansh@volentech.com"
        ];

        // Add the marker
        $googleMap->add_marker($marker);

        // Create the map data
        $data['map'] = $googleMap->create_map();

        $data['questions'] = $this->db->table('tblfaqs')->get()->getResultArray();

        return view('fe/contact-us', $data);
	}
	// public function contactUs() {
    //     // Configuration for Google Maps
    //     $config = [
    //         'center' => '37.3384194733874, -122.01663868654052',
    //         'zoom' => 17,
    //     ];

    //     // Prepare the marker information
    //     $marker = [
    //         'position' => "37.3384194733874, -122.01663868654052",
    //         'title' => "Londonderry Office",
    //         'label' => "Londonderry Office",
    //         'infowindow_content' => "Phone Number : (123)456-7890<br>Address : 824 Londonderry Drive Sunnyvale, CA 94087<br>Email : yugansh@volentech.com"
    //     ];

    //     // Pass the configuration and marker to the view
    //     $data['mapConfig'] = $config;
    //     $data['marker'] = $marker;

    //     // Fetch FAQs from the database
    //     $data['questions'] = $this->db->table('tblfaqs')->get()->getResultArray();

    //     return view('fe/contact-us', $data);
    // }
	public function sendEmail()
    {
        // Load the email library
        $email = new Email();

        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $emailAddress = $this->request->getPost('email');
        $message = $this->request->getPost('message');
        $subject = $this->request->getPost('subject');

        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => 'smtp.googlemail.com',
            'SMTPPort' => 465,
            'SMTPUser' => 'yugansh@volentech.com',
            'SMTPPass' => 'Yuggu1992!',
            'mailType' => 'html',
            'charset' => 'iso-8859-1'
        ];

        $email->initialize($config);
        $email->setFrom('yugansh@volentech.com', 'Contact Us');
        $email->setTo('yugansh@volentech.com');
        $email->setSubject('Contact Us');

        $content = "Name: <strong>{$name}</strong><br>Phone: <strong>{$phone}</strong><br>Email: <strong>{$emailAddress}</strong><br>Subject: <strong>{$subject}</strong><br>Message:<br><strong>{$message}</strong>";
        $email->setMessage($content);

        if ($email->send()) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => $email->printDebugger()]);
        }
    }
}