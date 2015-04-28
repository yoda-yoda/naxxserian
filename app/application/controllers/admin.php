<?php

class Admin extends CI_Controller
/*
Class to hold admin helpers for CRUDL processes
C->reate, R->ead, U->pdate, D->elete, L->
*/
{
	private $data;

	function __construct()
	{
		parent::__construct();

		#default timezone
		date_default_timezone_set("Africa/Nairobi");

		#set default models here

		#default data
		$data = array(
				"title" => "Naxxserian Admin Panel"
			);
	}

	public function __load_view($data)
	{
		$this->load->view("inc/admin_template", $data);
	}

	public function add_user()
	/*
	Allows admin to add a new user / member
	@return boolean
	*/
	{

	}
}