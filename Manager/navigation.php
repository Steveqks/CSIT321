<script>
    let show1 = true;
	let show2 = true;
	let show3 = true;


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
			myButton.innerHTML = 'Task Management ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Task Management ➕';
			linkContainer.style.display = 'none';
		}
		show2 = !show2;
	}
	
	function showLinks3() {
		
		let myButton = document.getElementById("myButton3");
		let linkContainer = document.getElementById("linkContainer3");

		if (show3 == false) {
			myButton.classList.add('expanded');
			myButton.innerHTML = 'Project Management ➖';
			linkContainer.style.display = 'block';
		} else {
			myButton.classList.remove('expanded');
			myButton.innerHTML = 'Project Management ➕';
			linkContainer.style.display = 'none';
		}
		show3 = !show3;
	}
</script>

<div class="navBar">
    <nav>
        <ul>
            <li><a href="Manager_viewTasks.php"><?php echo "$firstName, Staff(Manager)"?></a></li>

            <li><a id='myButton1' class='myButton1' onClick='showLinks1()'>Manage Account ➕</a>
                <div id='linkContainer1'>
					<div class='show'>
                        <a href='Manager_createUserAccount.php'> - Create User Account </a>
                        <a href='Manager_viewAccount.php'> - View Account Details </a>
                    </div>
				</div>
            </li>

            <li><a id='myButton2' class='myButton2' onClick='showLinks2()'>Task Management ➕</a>
                <div id='linkContainer2'>
					<div class='show'>
                        <a href='Manager_viewTasks.php'> - View Tasks </a>
                        <a href='Manager_addTask.php'> - Add Task </a>
                    </div>
				</div>
            </li>

            <li><a href="Manager_leaveHistory.php">Leave Management</a></li>

            <li><a href="Manager_attendanceTracking.php">Time/Attendance Tracking</a></li>

            <li><a href="Manager_viewNewsFeed.php">News Feed Management</a></li>
            <li><a id='myButton3' class='myButton3' onClick='showLinks3()'>Project Management ➕</a>
                <div id='linkContainer3'>
					<div class='show'>
                        <a href='Manager_viewProjectList.php'> - View Projects </a>
                        <a href='Manager_addProject.php'> - Add Project </a>
                    </div>
				</div>
            </li>
            <li><a href="Logout.php">Logout</a></li>
        </ul>
    </nav>
</div>