<?php 
  
	/*
	Display all members belonging to Naxxserian Chama
	@__author = Denis Karanja
	@__date = 19th April, 2015 12:12 pm
	*/

	#get all members
	$all_members = $this->model_users->get_all_members();
	$logged_in_member = $this->session->all_userdata()["id_number"];	 


?>

<div class="container offset-top">
	<div>
		<h4 class="btn btn-success">All Members</h4>
		<h4 class="btn btn-danger">Loanees & Guarantors</h4>
		<h4 class="btn btn-info">Signatories</h4>
	</div>
	<hr class="hrDividerBetween">
	
	<table class="table table-responsive table-hover table-stripped well">
		<thead>
			<td class="bold">Name</td>
			<td class="bold">Phone Number</td>
		</thead>

	<?php
		foreach($all_members->result() as $key)
		{
			$f_name = $key->first_name;
			$surname = $key->surname;
			$m_name = $key->middle_name;
			$id_number = $key->id_number;
			$phone = $key->phone_number;

			$fullname = ucfirst($f_name).' '.ucfirst($m_name).' '.ucfirst($surname);

			if($id_number == $logged_in_member)
			{
				echo"
					<tr id='".$id_number."' class='pointer text text-success'>
						<td>".$fullname." (Me)</td>
						<td>".$phone."</td>
					</tr>
				";
			}
			else
			{
				echo"
					<tr id='".$id_number."' class='pointer'>
						<td>".$fullname."</td>
						<td>".$phone."</td>
					</tr>
				";
			}

		}

	 ?>

	</table>
</div>