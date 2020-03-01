<?php
if (file_exists('config.php')) {
	require_once('config.php' );
} else {
	die("Configuration file absent");
}

if ($_POST) {
	if (!empty($_POST['action'])) {

		// Detect comment per page count from the site settings
		if ($settings['commentsperpage']) {$commentsperpage = $settings['commentsperpage'];}
		else {$commentsperpage = 10;}


		if ($_POST['action'] == 'newcomment') {
			if (!empty($_POST['key'])) {

				if ($_POST['name'] && $_POST['comment']) {
					$dbcon = new mysqli(DBSERVERNAME, DBUSER, DBPASS, DBNAME);
					if ($dbcon->connect_error) {
						die("Connection failed");
					}
					$dbcon->set_charset("utf8");

					$name = urldecode($_POST['name']);
					$comment = urldecode($_POST['comment']);
					$name = preg_replace ("/'/","\\'",$name);
					$comment = preg_replace ("/'/","\\'",$comment);

					$reload = 1;
					if (array_key_exists('premoderation',$settings)) {$reload = $state = !$settings['premoderation'];}
					else {$reload = $state = 0;}
					if (!$state) {$state = '0';}

					if ($_POST['parent']) {
						$query = "INSERT INTO ".DBTABLEPREFIX."comments (`author`,`text`,`parent`,`state`,`date`) VALUES ('".$name."','".$comment."',".$_POST['parent'].",".$state.",NOW())";
					} else {
						$query = "INSERT INTO ".DBTABLEPREFIX."comments (`author`,`text`,`parent`,`state`,`date`) VALUES ('".$name."','".$comment."',0,".$state.",NOW())";
					}
					$result = $dbcon->query($query);
					header('Content-Type: application/json');
					if ($result) {
					if ($reload) {
						//if ($_POST['parent']) {
						//	$query = "SELECT count(1) FROM ".DBTABLEPREFIX."comments WHERE `parent`=".$_POST['parent']." AND `state`>0";
						//} else {
						//	$query = "SELECT count(1) FROM ".DBTABLEPREFIX."comments t1 LEFT JOIN ".DBTABLEPREFIX."assets t2 ON t1.`parent`=t2.`id` WHERE t1.`state`>0 ORDER BY t1.`state` DESC, t1.`date`";
						//}
						//$num = $dbcon->query($query);
						//$num = mysqli_fetch_array($num)[0];

						//if ($num > $commentsperpage) {
						//	$page = intdiv($num-1,$commentsperpage) + 1;
						//} else {
							$page = 1;
						//}

						// Load last page
						if ($_POST['parent']) {
							$result = getcomments ($page, $commentsperpage, $_POST['parent'], 1, 0);
							//$query = "SELECT * FROM ".DBTABLEPREFIX."comments WHERE `parent`=".$_POST['parent']." AND `state`>0 LIMIT 10";
						} else {
							$result = getcomments ($page, $commentsperpage, 0, 1, '`state` DESC');
							//$query = "SELECT t1.`author` as author, t1.`text` as text, t1.`parent` as parent, t1.`date` as `date`, t2.`alias` as link, t2.`name` as linktitle FROM ".DBTABLEPREFIX."comments t1 LEFT JOIN ".DBTABLEPREFIX."assets t2 ON t1.`parent`=t2.`id` WHERE t1.`state`>0 ORDER BY t1.`state` DESC, t1.`date` LIMIT 10";
						}
						//$result = $dbcon->query($query);
						if ($result->num_rows) {
							$newcomments = array();
							while($row = $result->fetch_assoc()) {
								$newcomments[] = $row;
							}
						}
						//$newcomments['total'] = mysqli_fetch_array($num)[0];
						echo json_encode ($newcomments);
					} else {
						echo json_encode(1);
					}
					}
					die();
				}
			}
		} elseif ($_POST['action'] == 'getpage') {
			if ($page = $_POST['page']) {
				if ($_POST['parent']) {$result = getcomments($page,$commentsperpage,$_POST['parent'],1,0);}
				else {$result = getcomments($page,$commentsperpage,0,1,"`state` DESC");}
				
				if ($result->num_rows) {
					$newdata = array();
					while($row = $result->fetch_assoc()) {
						$newdata[] = $row;
					}
				}
				header('Content-Type: application/json');
				echo json_encode ($newdata);
			}
			die();
		} elseif ($_POST['action'] == 'getcommentsadmin') {
			if ($page = $_POST['page']) {
				if ($settings['commentsperpageadmin']) {$commentsperpage = $settings['commentsperpageadmin'];}
				else {$commentsperpage = 10;}
				$result = getcomments($page,$commentsperpage,0,0,"`date`");
				if ($result->num_rows) {
					$newdata = array();
					while($row = $result->fetch_assoc()) {
						$newdata[] = $row;
					}
				}
				header('Content-Type: application/json');
				echo json_encode ($newdata);
			}
			die();
		} elseif ($_POST['action'] == 'unpublishcomment') {
			if ($id = $_POST['id']) {
				$dbcon = new mysqli(DBSERVERNAME, DBUSER, DBPASS, DBNAME);
				if ($dbcon->connect_error) {
					die("Connection failed");
				}
				$dbcon->set_charset("utf8");
				$query = "UPDATE ".DBTABLEPREFIX."comments SET `state`=0 WHERE `id`=".$id;
				$result = $dbcon->query($query);
				if ($result) {
					echo $id;
				}
			}
			die();			
		} elseif ($_POST['action'] == 'publishcomment') {
			if ($id = $_POST['id']) {
				$dbcon = new mysqli(DBSERVERNAME, DBUSER, DBPASS, DBNAME);
				if ($dbcon->connect_error) {
					die("Connection failed");
				}
				$dbcon->set_charset("utf8");
				$query = "UPDATE ".DBTABLEPREFIX."comments SET `state`=1 WHERE `id`=".$id;
				$result = $dbcon->query($query);
				if ($result) {
					echo $id;
				}
			}
			die();			
		}
	}
}


function getcomments ($pagenum, $perpage, $parent, $state, $order) {
	$dbcon = new mysqli(DBSERVERNAME, DBUSER, DBPASS, DBNAME);
	if ($dbcon->connect_error) {
		die("Connection failed");
	}
	$dbcon->set_charset("utf8");

	if ($parent) {
		$query = "SELECT * FROM ".DBTABLEPREFIX."comments WHERE `parent`=".$parent." AND ";
		if ($state) {$query .= "`state`>0 ";}
		if ($order) {$query .= "ORDER BY ".$order;}
		$query .= " LIMIT ".($perpage*($pagenum-1)).", ".$perpage;
	} else {
		$query = "SELECT t1.`id` as `id`,t1.`author` as author, t1.`text` as text, t1.`parent` as parent, t1.`state` as `state`, t1.`date` as `date`, t2.`alias` as link, t2.`name` as linktitle FROM ".DBTABLEPREFIX."comments t1 LEFT JOIN ".DBTABLEPREFIX."assets t2 ON t1.`parent`=t2.`id` ";
		if ($state) {$query .= "WHERE t1.`state`>0 ";}
		if ($order) {$query .= "ORDER BY ".$order;}
		$query .= " LIMIT ".($perpage*($pagenum-1)).", ".$perpage;
	}

	return $dbcon->query($query);	
}

?>
