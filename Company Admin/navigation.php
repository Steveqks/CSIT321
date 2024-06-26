
<?php
	$navigation =	"<div  class='vertical-menu' style='border-right: 1px solid black; padding: 0px;'>
					<a href='companyadmin_homepage.php'>Home</a>
					<a href='companyadmin_ManageAccount.php'>Manage Account</a>";
					
	// first group link
	$navigation .=	"<a id='myButton1' class='myButton1' onClick='showLinks1()' > Manage User Accounts ➕</a>
					<div id='linkContainer1'>
						<div class='show'>
							<a href='companyadmin_ManageUserAccounts_create.php'> - Create User Accounts </a>
							<a href='companyadmin_ManageUserAccounts_view.php'> - View User Accounts </a>
						</div>
					</div>";
					
	// second group link
	$navigation .=	"<a id='myButton2' class='myButton2' onClick='showLinks2()' > Manage Specialisation ➕ </a>
					<div id='linkContainer2'>
						<div class='show'>
							<a href='companyadmin_specialisation_create.php'> - Create Specialisation </a>
							<a href='companyadmin_specialisation_view_delete.php'> - View Specialisation</a>
						</div>
					</div>";
					
	// third group link
	$navigation .=	"<a id='myButton3' class='myButton3' onClick='showLinks3()' > Manage Team ➕ </a>
					<div id='linkContainer3'>
						<div class='show'>
							<a href='companyadmin_teamManagement_create.php'> - Create Team </a>
							<a href='companyadmin_teamManagement_view_delete.php'> - View Team </a>
						</div>
					</div>";
					
	// fourth group link
	$navigation .=	"<a id='myButton4' class='myButton4' onClick='showLinks4()' > Manage Calendar ➕ </a>
					<div id='linkContainer4'>
						<div class='show'>
							<a href='companyadmin_ManageCalendar_create.php'> - Create Entry </a>
							<a href='companyadmin_ManageCalendar_view.php'> - View Entries </a>
						</div>
					</div>
					

					<a href='Logout.php'>Logout</a>
					</div>";
	echo $navigation;
?>

<script>
	let show1 = false;
	let show2 = false;
	let show3 = false;
	let show4 = false;

	function showLinks1() {
		let myButton = document.getElementById("myButton1");
		let linkContainer = document.getElementById("linkContainer1");

		if (show1 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Manage User Accounts ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage User Accounts ➕';
			linkContainer.style.display = 'none';
		}
		show1 = !show1;
	}
	
	function showLinks2() {
		
		let myButton = document.getElementById("myButton2");
		let linkContainer = document.getElementById("linkContainer2");

		if (show2 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Manage Specialisation ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage Specialisation ➕';
			linkContainer.style.display = 'none';
		}
		show2 = !show2;
	}
	
	function showLinks3() {
		
		let myButton = document.getElementById("myButton3");
		let linkContainer = document.getElementById("linkContainer3");

		if (show3 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Manage Team ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage Team ➕';
			linkContainer.style.display = 'none';
		}
		show3 = !show3;
	}
	
	function showLinks4() {
		
		let myButton = document.getElementById("myButton4");
		let linkContainer = document.getElementById("linkContainer4");

		if (show4 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Manage Calendar ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage Calendar ➕';
			linkContainer.style.display = 'none';
		}
		show4 = !show4;
	}
</script>