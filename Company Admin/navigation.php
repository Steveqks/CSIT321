<?php
	$navigation =	"<div class='vertical-menu' style='border-right: 1px solid black; padding: 0px;'>";
	$navigation .= "<a href='companyadmin_homepage.php'>Home</a>
					<a href='companyadmin_ManageAccount.php'>Manage Account</a>
					<a href='companyadmin_ManageUserAccounts_create.php'>Manage User Accounts > Create</a>
					<a href='companyadmin_ManageUserAccounts_view.php'>Manage User Accounts > View</a>
					<a href='companyadmin_specialisation_create.php'>Manage Specialisation > Create </a>
					<a href='companyadmin_specialisation_view_delete.php'>Manage Specialisation > View</a>
					<a href='companyadmin_teamManagement_create.php'>Manage Team > Create </a>
					<a href='companyadmin_teamManagement_view_delete.php'>Manage Team > View</a>
					<a href='companyadmin_ManageCalendar_create.php'>Manage Calendar > Create Entry</a>
					<a href='companyadmin_ManageCalendar_view.php'>Manage Calendar > View Entries</a>
					<a href='Logout.php'>Logout</a>
					</div>";
	echo $navigation;
?>