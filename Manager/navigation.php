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
			myButton.innerHTML = 'Manage Account ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Manage Account ➕';
			linkContainer.style.display = 'none';
		}
		show1 = !show1;
	}
	
	function showLinks2() {
		
		let myButton = document.getElementById("myButton2");
		let linkContainer = document.getElementById("linkContainer2");

		if (show2 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Project Management ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Project Management ➕';
			linkContainer.style.display = 'none';
		}
		show2 = !show2;
	}
	
	function showLinks3() {
		
		let myButton = document.getElementById("myButton3");
		let linkContainer = document.getElementById("linkContainer3");

		if (show3 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Task Management ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Task Management ➕';
			linkContainer.style.display = 'none';
		}
		show3 = !show3;
	}
	
	function showLinks4() {
		
		let myButton = document.getElementById("myButton4");
		let linkContainer = document.getElementById("linkContainer4");

		if (show4 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Leave a Review! ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Leave a Review ➕';
			linkContainer.style.display = 'none';
		}
		show4 = !show4;
	}
</script>

<div class="vertical-menu">

	<a href="Manager_viewTasksList.php"><?php echo "$firstName, Staff(Manager)"?></a>

    <a id='myButton1' class='myButton1' onClick='showLinks1()'>Manage Account ➕</a>
	<div id='linkContainer1'>
		<div class='show'>
            <a href='Manager_createUserAccount.php'> - Create User Account </a>
            <a href='Manager_viewAccount.php'> - View Account Details </a>
        </div>
	</div>

	<a id='myButton2' class='myButton2' onClick='showLinks2()'>Project Management ➕</a>
	<div id='linkContainer2'>
		<div class='show'>
			<a href='Manager_viewProjectList.php'> - View Projects </a>
			<a href='Manager_addProject.php'> - Add Project </a>
		</div>
	</div>
    
	<a id='myButton3' class='myButton3' onClick='showLinks3()'>Task Management ➕</a>
    <div id='linkContainer3'>
		<div class='show'>
			<a href='Manager_viewTasksList.php'> - View Tasks </a>
            <a href='Manager_addTask.php'> - Add Task </a>
        </div>
	</div>

    <a href="Manager_viewLeaveHistory.php">Leave Management</a>

	<a href="Manager_viewNewsFeed.php">News Feed Management</a>
    
	<a id='myButton4' class='myButton4' onClick='showLinks4()'>Leave a Review! ➕</a>
    <div id='linkContainer4'>
		<div class='show'>
			<a href='Manager_SubmitReview.php'> - Submit a Review </a>
            <a href='Manager_EditReview.php'> - Edit a Review </a>
        </div>
	</div>

    <a href="Logout.php">Logout</a>

</div>