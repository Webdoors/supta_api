<?php
header('Content-Type: application/json; charset=utf-8');
		$q1 = mysqli_query($con,"SELECT * FROM sectors");
		$r1 = mysqli_fetch_all($q1,MYSQLI_ASSOC);
		echo json_encode($r1);
?>