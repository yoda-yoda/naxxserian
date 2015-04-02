<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
/*
Authorised members function helpers only
*/
{
	private $data;

	function __construct()
	{
		parent::__construct();

		#load models on startup
		$this->load->model('model_users');
		$data = array(
				"title" => "Naxxserian Investment Enterprise"
			);

		!$this->session->userdata("is_logged_in") ? redirect("out/"): '';

	}

	public function _load_view($data){
		$this->load->view("inc/template", $data);
	}

	public function index()
	/*Entry point*/
	{
		$this->home();
		
	}


	public function home()
	{
		
		if($this->session->userdata('is_logged_in'))
		{
		
			$logged_in_member = $this->session->all_userdata()['id_number'];
			$user_data = $this->model_users->user_details("first_name", $logged_in_member);
			
			if($user_data)
			{
				$first_name =  ucfirst($this->array_to_single($user_data, "first_name"));

				/*all thru page content*/
				$data = array(
						"username" => $first_name,
						"auth" => "home",
						"title" => "Naxxserian &middot; Home"
					);


				$this->_load_view($data);
			}
		}
		else
		{
			$data = array(
					"out" => "login",
					"title" => "Naaxserian &middot; Home"
				);
			$this->_load_view($data);
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


	public function update_password($password)
	/*
	Update new user password to the database
	*/
	{
		if($this->session->userdata("is_logged_in"))
		{
			/*logged in members password reset. Use session vars*/
			$unique_id = $this->session->all_userdata()["id_number"];

			if($this->model_users->make_changes($password, $unique_id))
			{
				return true;
			}
			else return false;
		}
		else
		{
			/*non-logged in member. Use email address*/
		}
		
	}


	public function members()
	/**
	*Loged in members area
	*/
	{
		if($this->session->userdata('is_logged_in'))
		{
			$data = array(
					"auth" => "members",
					"title" => "Naxxserian &middot; Members",
					"password_feedback" => ""
				);
			
			$this->_load_view($data);
		}
		else
		{
			$data = array(
					"out" => "login",
					"title" => "Naxxserian &middot; Login"
				);
			
			$this->_load_view($data);

		}
	}

	public function logout()
	/*
	Logout a member
	*/
	{
		$this->session->sess_destroy();
		redirect('out/');
	}

	public function change_password()
	{
		if($this->session->userdata("is_logged_in"))
			/*logged in member*/
		{
			$this->load->library("form_validation");

			$config = array(
					array(
							"field" => "member_old_password",
							"label" => "<b>Old Password</b>",
							"rules" => "required|trim"
						),

					array(
							"field" => "member_new_password",
							"label" => "<b>New Password</b>",
							"rules" => "required|trim|min_length[6]"
						),

					array(
							"field" => "member_conf_new_password",
							"label" => "<b>Confirm New Password</b>",
							"rules" => "required|trim|matches[member_new_password]"
						)
				);

			$this->form_validation->set_rules($config);

			if($this->form_validation->run())
			{
				/*code here to update pass*/
				$new_password = $this->input->post("member_new_password");
				$old_password = $this->input->post("member_old_password");
				$unique_id = $this->session->all_userdata()["id_number"];

				/*check if old password is correct*/
				if($this->model_users->password_is_valid())
				{
					/*update password*/
					if ($this->update_password($new_password))
					{
						$success = "<h4 class='alert alert-success'> Password has been updated successfuly. </h4>";/*echo in members when password has been succsessfuly updated*/

						$data = array(
								"auth" => "members",
								"title" => "Naxxserian &middot; Members",
								"password_feedback" => $success
							);

						$this->_load_view($data);

					}
					else
					{
						$success = "<h4 class='alert alert-danger scroll-to-password-field pointer'> Something went wrong. Please click here to try again.</h4>";/*echo in members when password has been succsessfuly updated*/

						$data = array(
								"auth" => "members",
								"title" => "Naxxserian &middot; Members",
								"password_feedback" => $success
							);

						$this->_load_view($data);
					}
				}
				else
				{
					/*old password incorrect*/
					$success = "<h4 class='alert alert-danger scroll-to-password-field pointer'>Wrong old password :( Click here to edit again</h4>";
						/*echo in members when password has been succsessfuly updated*/

						$data = array(
								"auth" => "members",
								"title" => "Naxxserian &middot; Members",
								"password_feedback" => $success
							);

						$this->_load_view($data);
				}
				
			}
			else
			{
				$data = array(
						"auth" => "members",
						"title" => "Naxxserian &middot; Members",
						"password_feedback" => ""
					);
				$this->_load_view($data);
			}
		}
		else
		{
			/*public*/
			redirect("auth/members");
		}
		
	}/*end change_password*/

	public function upload()
	/*
	Upload user photo()
	*/
	{
		if($this->session->userdata("is_logged_in"))
		{
			$member_id = $this->session->all_userdata()["id_number"];
			$date = date("Y-m-d H:i:s");

			$filename = sha1($member_id.$date);

			$config = array(
					"upload_path" => "images/",
					"allowed_types" => "jpg|png|jpeg",
					"max_size" => "2048",
					"max_width" => "2048",
					"max_height" => "1538",
					"file_name" => $filename
				);

			$this->load->library("upload", $config);
			$iamge_field_name = "user_image";

			if(!$this->upload->do_upload($iamge_field_name))
			{
				$errors = "<h4 class='text text-danger'>".$this->upload->display_errors()."</h4>";

				$data = array(
						"auth" => "members",
						"title" => "Photo Uploader",
						"photo_feedback" => $errors
					);

				$this->_load_view($data);

			}
			else
			{

				/*previous photo*/
				$previous_photo = $this->model_users->get_details("photo", $member_id);
				/*delete the previous photo*/	
				if($previous_photo != "naxxserian.default.photo.naxxserian.png")
				{
					unlink("images/".$previous_photo);
				}

   			/*update DB(photo field*/
   			$filename = $this->upload->data()["file_name"];

				if($this->model_users->upload_photo($filename))
				{/*DB updated*/
					$success = "<h4 class='text text-success'>Photo has been updated successfuly</h4>";

					$data = array(
						"auth" => "members",
						"title" => "Photo Uploader",
						"photo_feedback" => $success
					);

					$this->_load_view($data);
				
				}
				else
				{/*DB not updated*/
					$error = "<h4 class='text text-danger'>Photo upload failed.</h4>";

					$data = array(
						"auth" => "members",
						"title" => "Photo Uploader",
						"photo_feedback" => $error
					);

					$this->_load_view($data);
				}

			}

		}
		else
		{
			redirect("out/");
		}
	}/*end upload*/




}/*end of Class*/