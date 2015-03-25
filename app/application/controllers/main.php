<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	private $data;

	function __construct(){
		parent::__construct();

		#load views and models on startup
		$this->load->view('inc/template');
		$this->load->model('model_users');
	}

	public function index()
	{
		$this->home();
		
	}

	public function home()
	{
		
		if($this->session->userdata('is_logged_in'))
		{
			$this->load->view("home");
			$logged_in_member = $this->session->all_userdata()['id_number'];
			$user_data = $this->model_users->user_details("first_name", $logged_in_member);
			
			if($user_data)
			{
				$first_name =  ucfirst($this->array_to_single($user_data, "first_name"));
			}
		}
		else
		{
			$this->load->view("home");
		}
		

	}

	private function array_to_single($array, $column)
	/*
	Array to  single value
	$params -> array(object array from db), string(column name)
	$return single value(int/string)
	*/
	{
		foreach($array->result() as $key)
		{
			$value = $key->$column;
		}
		return $value;
	}

	/*Login form*/
	public function login(){
		
		!$this->session->userdata('is_logged_in') ? $this->load->view('login'): redirect('main/members');
	}

	public function signup(){
		!$this->session->userdata('is_logged_in') ? $this->load->view('signup'): redirect('main/members');
	}


	/*
	*Validate log ins
	*/
	public function login_validation(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('id_number', 'ID number', 'required|trim|xss_clean|callback_validate_credentials');
		$this->form_validation->set_rules('password', 'Password', 'required|md5|trim');

		if ($this->form_validation->run()){
			//create session for the user
			$data = array(
					'id_number' => $this->input->post('id_number'),
					'is_logged_in' => 1
				);
			$this->session->set_userdata($data);
			redirect('main/members');
		} 
		else
		{
			$this->load->view('login');
		}
	}

	/*Validate sign ups*/
	public function signup_validation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('id_number', 'ID number', 'required|trim|xss_clean|is_unique[members.id_number]');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		$this->form_validation->set_rules('conf_password', 'Confirm password', 'required|trim|matches[password]');

		if ($this->form_validation->run())
		{
			echo"Pass :)";
		}
		else
		{
			echo "<span class='alert alert-error'>Couldn't sign you up.</span>";
			$this->load->view('signup');
		}
	}

	public function reset_password()
	{	
		#load view
		$this->load->view("reset_password");
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email_address', 'Email address', 'required|trim|xss_clean|is_email');

		if($this->form_validation->run())
		{
			echo"<span class='alert alert-success'>Is a valid email</span> ";
		}


	}

	//callback function to validate usernames and passwords
	public function validate_credentials(){

		if ($this->model_users->can_log_in()){
			return true;
		}
		else
		{
			$this->form_validation->set_message('validate_credentials', 'Incorrect login credentials!');
			return false;
		}
	}

	/**
	*Loged in members area
	*/
	public function members(){
		if($this->session->userdata('is_logged_in'))
		{
			$this->load->view('members');
		}
		else
		{
			$this->load->view('login');
		}
	}

	//logout member.
	public function logout(){
		$this->session->sess_destroy();
		redirect('main/home');
	}


}

