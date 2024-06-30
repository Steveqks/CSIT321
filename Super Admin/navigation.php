<?php
	$navigation =	"<div class='vertical-menu' style='border-right: 1px solid black; padding: 0px;'>
					<a href='superadmin_homepage.php'>" . $_SESSION['FirstName'] . " (" . $_SESSION['Role'] . ")</a>
					<a href='superadmin_ManageAccount.php'>Manage Account</a>";
					
	// first group link
	$navigation .=	"<a id='myButton1' class='myButton1' onClick='showLinks1()' > Manage Company ➕</a>
						<div id='linkContainer1'>
							<div class='show'>
								<a href='superadmin_manageCompany_create.php'> - Create Company </a>
								<a href='superadmin_manageCompany_view.php'> - View Company </a>
								<a href='superadmin_manageCAdmin_approve_unreg_user.php'> - Approve New Company (Create New Company & Company Admin) </a>
							</div>
						</div>";
					
	// second group link
	$navigation .=	"<a id='myButton2' class='myButton2' onClick='showLinks2()' > Manage Company Admin ➕ </a>
						<div id='linkContainer2'>
							<div class='show'>
								<a href='superadmin_manageCAdmin_create.php'> - Create Company Admin </a>
								<a href='superadmin_manageCAdmin_view_delete.php'> - View Company Admin</a>
							</div>
						</div> 
					
					<a href='Logout.php'>Logout</a>
					</div>";
	echo $navigation;
?>

<script>
	let show1 = false;
	let show2 = false;


	function showLinks1() {
		let myButton = document.getElementById("myButton1");
		let linkContainer = document.getElementById("linkContainer1");

		if (show1 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Manage Company ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage Company ➕';
			linkContainer.style.display = 'none';
		}
		show1 = !show1;
	}
	
	function showLinks2() {
		
		let myButton = document.getElementById("myButton2");
		let linkContainer = document.getElementById("linkContainer2");

		if (show2 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Manage Company Admin ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage Company Admin ➕';
			linkContainer.style.display = 'none';
		}
		show2 = !show2;
	}
	
</script>