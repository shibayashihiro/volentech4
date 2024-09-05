<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;
class ManageHome extends Controller {

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

	// Services
    public function faqs()
    {
        $data['questions'] = $this->db->table('tblfaqs')->get()->getResultArray();
        return view('be/home/faqs', $data);
    }
	
	// Comments
    public function comments()
    {
        $data['comments'] = $this->db->table('tblcomments')->get()->getResultArray();
        return view('be/home/comments', $data);
    }
	
	// Articles
    public function articles()
    {
        $data['articles'] = $this->db->table('tblarticles')->get()->getResultArray();
        return view('be/home/articles', $data);
    }

    // Portfolios
    public function portfolios()
    {
        $data['portfolios'] = $this->db->table('tblportfolios')->get()->getResultArray();
        return view('be/home/portfolios', $data);
    }


	public function portfolio_edit($ID)
    {
        if ($ID == 0) {
            $data = [
                'Title' => '',
                'Date' => '',
                'Location' => '',
                'ID' => 0,
                'descriptions' => [],
                'Images' => []
            ];
        } else {
            $data = $this->db->table('tblportfolios')->where('ID', $ID)->get()->getRowArray();
            $data['descriptions'] = $this->db->table('tblport_descriptions')->where('PortfolioID', $ID)->get()->getResultArray();
            $data['Images'] = $this->db->table('tblslides')->where('PortfolioID', $ID)->get()->getResultArray();
        }

        return view('be/home/portfolio_edit', $data);
    }

	/*
	*	CRUD APIS
	*/


	/*
	*	Questions
	*/
	// CRUD APIs for FAQs
    public function getQuestion()
    {
        $ID = $this->request->getPost('ID');
        $data = $this->db->table('tblfaqs')->where('ID', $ID)->get()->getRowArray();

        if ($data == null) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON([
            'success' => true,
            'Question' => $data['Question'],
            'Answer' => $data['Answer']
        ]);
    }
	public function addQuestion()
    {
        $data = $this->request->getPost();
        $result = $this->db->table('tblfaqs')->insert($data);

        if (!$result) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON(['success' => true, 'inserted_id' => $result]);
    }
	public function updateQuestion()
    {
        $ID = $this->request->getPost('ID');
        $data = $this->request->getPost();

        $this->db->table('tblfaqs')->where('ID', $ID)->update($data);
        return $this->response->setJSON(['success' => true]);
    }

    public function deleteQuestion()
    {
        $ID = $this->request->getPost('ID');
        $this->db->table('tblfaqs')->where('ID', $ID)->delete();
        return $this->response->setJSON(['success' => true]);
    }
	/*
	*	Comments
	*/
	public function getComment()
    {
        $ID = $this->request->getPost('ID');
        $data = $this->db->table('tblcomments')->where('ID', $ID)->get()->getRowArray();

        if ($data == null) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON(['success' => true, 'comment' => $data]);
    }	
	public function addComment()
    {
        $data = $this->request->getPost();
        $result = $this->db->table('tblcomments')->insert($data);

        if (!$result) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON(['success' => true, 'inserted_id' => $result]);
    }

	public function updateComment()
    {
        $ID = $this->request->getPost('ID');
        $data = $this->request->getPost();

        $this->db->table('tblcomments')->where('ID', $ID)->update($data);
        return $this->response->setJSON(['success' => true]);
    }
	public function deleteComment()
    {
        $ID = $this->request->getPost('ID');
        $this->db->table('tblcomments')->where('ID', $ID)->delete();
        return $this->response->setJSON(['success' => true]);
    }
	/*
	*	Articles
	*/

	public function addArticle() {
		$resource_data = $this->request->getPost();
		$attach = '';

		if ($this->request->getFile('resource')->isValid() && !$this->request->getFile('resource')->hasMoved()) {
			$file = $this->request->getFile('resource');
			
			// Set the upload path and file name
			$uploadPath = 'assets/images/articles/';
			$fileName = 'article_' . time() . '.' . $file->getExtension();
	
			// Move the uploaded file to the desired location
			if ($file->move($uploadPath, $fileName)) {
				$attach = $fileName;
			} else {
				return $this->response->setJSON(['message' => $file->getErrorString(), 'success' => false]);
			}
		}

        $resource_data['Image'] = $attach;

		$result = $this->db->table('tblarticles')->insert($resource_data);
		if(!$result){
			return $this->response->setJSON(['success' => false]);
		}
		return $this->response->setJSON(['success' => true]);		
	}

	public function getArticle()
    {
        $ID = $this->request->getPost('ID');
        $data = $this->db->table('tblarticles')->where('ID', $ID)->get()->getRowArray();

        if ($data == null) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

	public function updateArticle() {
		$ID = $this->request->getPost('ID');
        $resource_data = $this->request->getPost();

		$resource = $this->db->table('tblarticles')
        ->select('Image')
        ->where('ID', $ID)
        ->get()
        ->getRowArray();

		if ($resource == null) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'No such resource'
			]);
		}
				
		// Check if a file has been uploaded
		if ($this->request->getFile('resource')->isValid() && !$this->request->getFile('resource')->hasMoved()) {
			$file = $this->request->getFile('resource');
			$date = time();
			
			// Set up upload configuration
			$uploadPath = 'assets/images/articles/';
			$fileName = 'article_' . time() . '.' . $file->getExtension();
			
	
			// Load the file uploader
			if ($file->move($uploadPath, $fileName)) {
				$attach = $fileName;
			} else {
				return $this->response->setJSON(['message' => $file->getErrorString(), 'success' => false]);
			}
	
			// If you need to delete the old image
			$oldImage = $this->db->table('tblarticles')->select('Image')->where('ID', $ID)->get()->getRowArray();
			if ($oldImage && file_exists($uploadPath . $oldImage['Image'])) {
				unlink($uploadPath . $oldImage['Image']);
			}
	
			// Prepare the new image data
			$resource_data['Image'] = $attach;
		}
	
		// Update the database
		$this->db->table('tblarticles')->where('ID', $ID)->update($resource_data);
	
		return $this->response->setJSON(['success' => true]);
	}

	public function deleteArticle() {

		$ID = $this->request->getPost('ID');
		$resource = $this->db->table('tblarticles')->where('ID', $ID)->get()->getRowArray();
		if ($resource == null) {
			return $this->response->setJSON(['success' => false]);
		}

		if (!empty($resource['Image']) && file_exists('assets/images/articles/'.$resource['Image'])) {
			unlink('assets/images/articles/'.$resource['Image']);
		}

		$this->db->table('tblarticles')->where('ID', $ID)->delete();
		$deletedCnt = $this->db->affectedRows();

		if ($deletedCnt === 0) {
			return $this->response->setJSON(['success' => false]);
		} else {
			return $this->response->setJSON(['success' => true]);
		}
	}

	// Portfolio

	public function deletePortfolio() {
		$ID = $this->request->getPost('ID');
		$slides = $this->db->table('tblslides')->where('PortfolioID', $ID)->get()->getRowArray();
		
		if ($slides == null) {
			return $this->response->setJSON(['success' => false]);
		}

		foreach ($slides as $slide) {
			if (!empty($slide['Attach']) && file_exists('assets/images/slides/'.$slide['Attach'])) {
				unlink('assets/images/slides/'.$slide['Attach']);
			}
		}

		
		$this->db->table('tblslides')->where('PortfolioID', $ID)->delete();

		$this->db->table('tblportfolios')->where('ID', $ID)->delete();

		$deletedCnt = $this->db->affected_rows();

		if ($deletedCnt === 0) {
			return $this->response->setJSON(['success' => false]);
		} else {
			return $this->response->setJSON(['success' => true]);
		}	
	}

	public function updatePortfolio() {
		$ID = $this->request->getPost('ID');

        $data = [
            'Title' => $this->request->getPost('Title'),
			'Date' => $this->request->getPost('Date'),
			'Location' => $this->request->getPost('Location')
		];
		$returnedID = 0;

		if ($ID == '0') {			
			$returnedID = $this->db->table('tblportfolios')->insert($data);
		}
		else {
			$this->db->table('tblportfolios')->where('ID', $ID)->update($data);
			$returnedID = $ID;
		}

		if ($returnedID == 0)
			return $this->response->setJSON(['success' => false]);
		else 		
			return $this->response->setJSON([
				'success' => true,
				'ID' => $returnedID
			]);
	}

	// Slides

	public function addSlide() {		

		$attach = '';

		$slideData = $this->request->getPost();
		if ($this->request->getFile('resource')->isValid() && !$this->request->getFile('resource')->hasMoved()) {
			$file = $this->request->getFile('resource');
			
			// Set the upload path and file name
			$uploadPath = 'assets/images/slides/';
			$fileName = 'slide' . time() . '.' . $file->getExtension();
	
			// Move the uploaded file to the desired location
			if ($file->move($uploadPath, $fileName)) {
				$attach = $fileName;
			} else {
				return $this->response->setJSON(['message' => $file->getErrorString(), 'success' => false]);
			}
		}

        $slideData['Attach'] = $attach;

		$Id = $this->db->table('tblslides')->insert($slideData);

		if ($Id == 0)
			return $this->response->setJSON(['success' => false]);
		else 		
			return $this->response->setJSON(['success' => true]);
	}

	public function deleteSlide() {
		$ID = $this->request->getPost('ID');

		$resource = $this->db->table('tblslides')->where('ID', $ID)->get()->getRowArray();
		if ($resource == null) {
			return $this->response->setJSON(['success' => false]);
		}

		if (!empty($resource['Attach']) && file_exists('assets/images/slides/'.$resource['Attach'])) {
			unlink('assets/images/slides/'.$resource['Attach']);
		}

		$this->db->table('tblslides')->where('ID', $ID)->delete();		
		$deletedCnt = $this->db->affected_rows();

		if ($deletedCnt == 0) {
			return $this->response->setJSON(['success' => false]);
		}
		else {
			return $this->response->setJSON(['success' => true]);
		}
	}

	public function getSlide() {
		$ID = $this->request->getPost('ID');

		$data = $this->db->table('tblslides')->where('ID', $ID)->get()->getRowArray();

		if ($data == null) {
			return $this->response->setJSON(['success' => false]);
		}
		return $this->response->setJSON([
			'success' => true,
			'data' => $data
		]);
	}

	public function updateSlide() {
		$ID = $this->request->getPost('ID');
		$resource_data = $this->request->getPost();
		$resource = $this->db->table('tblslides')
        ->select('Attach')
        ->where('ID', $ID)
        ->get()
        ->getRowArray();

		if ($resource == null) {
			$this->response->setJSON([
					'success' => false,
					'message' => 'No such resource'
				]);
		}
		// Check if a file has been uploaded
		if ($this->request->getFile('resource')->isValid() && !$this->request->getFile('resource')->hasMoved()) {
			$file = $this->request->getFile('resource');
			
			// Set up upload configuration
			$uploadPath = 'assets/images/slides/';
			$fileName = 'slide' . time() . '.' . $file->getExtension();
			
	
			// Load the file uploader
			if ($file->move($uploadPath, $fileName)) {
				$attach = $fileName;
			} else {
				return $this->response->setJSON(['message' => $file->getErrorString(), 'success' => false]);
			}
	
			// If you need to delete the old image
			$oldImage = $this->db->table('tblslides')->select('Attach')->where('ID', $ID)->get()->getRowArray();
			if ($oldImage && file_exists($uploadPath . $oldImage['Attach'])) {
				unlink($uploadPath . $oldImage['Attach']);
			}
	
			// Prepare the new image data
			$resource_data['Attach'] = $attach;
		}
	
		// Update the database
		$this->db->table('tblslides')->where('ID', $ID)->update($resource_data);
	
		return $this->response->setJSON(['success' => true]);
	}

	/*
	*	Portfolio Description
	*/
	public function getPortfolioDescription() {
		$ID = $this->request->getPost('ID');

		$data = $this->db->table('tblport_descriptions')->where('ID', $ID)->get()->getRowArray();

		if ($data == null) {
			return $this->response->setJSON(['success' => false]);
		}

		return $this->response->setJSON([
				'success' => true,
				'description' => $data
			]);
	}

	public function addPortfolioDescription() {
		$data = $this->request->getPost();

		$id = $this->db->table('tblport_descriptions')->insert($data);

		if ($id == false) {
			return $this->response->setJSON(['success' => false]);
		}

		return $this->response->setJSON([
				'success' => true,
				'inserted_id' => $id
		]);
	}

	public function updatePortfolioDescription() {
		$ID = $this->request->getPost('ID');
		$data = $this->request->getPost();

		$this->db->table('tblport_descriptions')->where('ID', $ID)->update($data);

		return $this->response->setJSON(['success' => true]);
	}

	public function deletePortfolioDescription() {
		$ID = $this->request->getPost('ID');

		$this->db->where('ID', $ID);
		$this->db->table('tblport_descriptions')->where('ID', $ID)->delete();
		return $this->response->setJSON(['success' => true]);
	}

	// Add Log
    public function addLog()
    {
        $logData = [
            'IP' => $this->request->getIPAddress(),
            'Lat' => $this->request->getPost('Lat'),
            'Lng' => $this->request->getPost('Lng'),
            'Page' => $this->request->getPost("Page"),
            'Date' => date('Y-m-d H:i:s'),
            'Address' => $this->request->getPost('Address')
        ];

        $this->db->table('tbllog')->insert($logData);
    }
}
