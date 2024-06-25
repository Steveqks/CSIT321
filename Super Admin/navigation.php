<?php
	$navigation =	"<div class='vertical-menu' style='border-right: 1px solid black; padding: 0px;'>";
	$navigation .= "<a href='superadmin_homepage.php'>Home</a>
					<a href='superadmin_ManageAccount.php'>Manage Account</a>
					<a href='superadmin_manageCompany_create.php'>Manage Company > Create Company </a>
					<a href='superadmin_manageCompany_view.php'>Manage Company > View Company </a>
					<a href='superadmin_manageCAdmin_approve_unreg_user.php'>Approve New Company (Create New Company & Company Admin)</a>
					<a href='superadmin_manageCAdmin_create.php'>Manage Company Admin > Create Company Admin</a>
					<a href='superadmin_manageCAdmin_view_delete.php'>Manage Company Admin > View Company Admin</a>
					<a href='Logout.php'>Logout</a>
					</div>";
	echo $navigation;
?>