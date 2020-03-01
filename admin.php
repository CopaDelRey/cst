<?php
$chsetup = 0;
if ($_POST) {
	$file = file_get_contents('config.php');
	if (isset($_POST['premoderation'])) {$file = preg_replace('/(?m)(.*)^ *\'premoderation\' *=>.*$(.*)/','$1\'premoderation\' => '.$_POST['premoderation'].',$2',$file);}
	if (isset($_POST['commentsperpage'])) {$file = preg_replace('/(?m)(.*)^ *\'commentsperpage\' *=>.*$(.*)/','$1\'commentsperpage\' => '.$_POST['commentsperpage'].',$2',$file);}
	if (isset($_POST['commentsperpageadmin'])) {$file = preg_replace('/(?m)(.*)^ *\'commentsperpageadmin\' *=>.*$(.*)/','$1\'commentsperpageadmin\' => '.$_POST['commentsperpageadmin'].',$2',$file);}
	$chsetup = file_put_contents('config.php', $file);
}

if (file_exists('config.php')) {
	require_once('config.php' );
} else {
	die("Configuration file absent");
}
?>

<!DOCTYPE html>
<html lang="en-US" class="h-100">

<head>
<base href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">
<link rel='stylesheet' href='css/bootstrap.min.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/custom.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/customadmin.css' type='text/css' media='all' />
</head>

<?php
$urlquery = $_SERVER['QUERY_STRING'];
$urlparams = array();
// Mode 0 = dashboard, 1 = settings
$mode = 0;

if ($urlquery) {
	parse_str ($urlquery,$urlparams);
	if ($urlparams) {
		if (array_key_exists('page',$urlparams)) {
			if ($urlparams['page'] == 'settings') {
				$mode = 1;
			} //elseif ($urlparams['page'] == 'comments') {
			//	$mode = 2;
			//}
		}
	}
}

if ($mode == 0) {
	$dbcon = new mysqli(DBSERVERNAME, DBUSER, DBPASS, DBNAME);

	if ($dbcon->connect_error) {
		die("Connection failed");
	}

	$dbcon->set_charset("utf8");

	if ($settings['commentsperpageadmin']) {$commentsperpage = $settings['commentsperpageadmin'];}
	else {$commentsperpage = 10;}

	$query = "SELECT count(1) FROM ".DBTABLEPREFIX."comments";

	$num = $dbcon->query($query);
	$num = mysqli_fetch_array($num)[0];

	$totalpages = intdiv($num-1, $commentsperpage);

	$query = "SELECT t1.`id` as `id`, t1.`author` as author, t1.`text` as text, t1.`parent` as parent, t1.`state` as `state`, t1.`date` as `date`, t2.`alias` as link, t2.`name` as linktitle FROM ".DBTABLEPREFIX."comments t1 LEFT JOIN ".DBTABLEPREFIX."assets t2 ON t1.`parent`=t2.`id` ORDER BY `date` LIMIT ";
	if ($totalpages) {$query .= ($totalpages*$commentsperpage).", ";}
	$query .= $commentsperpage;

	$result = $dbcon->query($query);
}

?>

<body id="body" class="d-flex flex-column h-100">

<header id="header" class="navbar-dark bg-dark">
<div class="container">
<nav class="navbar navbar-expand-md">
<div id="mainmenu" class="navbar-collapse collapse"><ul class="navbar-nav mr-auto">
<li id="menu-item-home" class="nav-item"><a class="nav-link" href="/">Home</a></li>
<li id="menu-item-admin" class="nav-item active"><a class="nav-link" href="admin.php">Admin Panel</a></li>
</ul></div>
<button class="btn btn-secondary navbar-toggler float-right collapsed" type="button" data-toggle="collapse" data-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
</nav></div>
</header>
<main>
<section class="message">
<div class="container">
<?php if ($chsetup) : ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
<strong>Success!</strong> Your settings have been applied successfully.
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<?php endif; ?>
</div>
</section>
<section class="subnav">
<div class="container">
<ul class="nav nav-tabs">
	<li class="nav-item"><a class="nav-link<?php if (!$mode) {echo ' active';} ?>" href="/admin.php">Dashboard</a></li>
	<li class="nav-item"><a class="nav-link<?php if ($mode == 1) {echo ' active';} ?>" href="/admin.php?page=settings">Settings</a></li>
</ul>
</div>
</section>

<section class="data">
<div class="container">
<?php switch ($mode) : ?>
<?php case 0 : ?>
	<h2 class="page-title">Dashboard</h2>
	<h3 class="page-title">Manage Comments</h3>
	<?php if ($result->num_rows) : ?>
	<div class="row">
		<?php if ($totalpages++) : ?>
		<div class="pagination-results">Page <?php echo $totalpages; ?> of <?php echo $totalpages; ?></div>
		<?php endif; ?>
		<?php while($row = $result->fetch_assoc()) : ?>
		<?php if ($row['state']) : ?>
		<div class="comment comment-<?php echo $row['id']; ?> col col-12 mb-2"><div class="wrap bg-light shadow-sm rounded"><div class="buttons"><btn class="btn btn-warning btn-unpublish" data-id="<?php echo $row['id']; ?>">Unpublish</btn></div>
		<?php else : ?>
		<div class="comment comment-<?php echo $row['id']; ?> col col-12 mb-2"><div class="wrap alert-warning shadow-sm rounded"><div class="buttons"><btn class="btn btn-success btn-publish" data-id="<?php echo $row['id']; ?>">Publish</btn></div>
			<div class="marker-unpublished mb-1"><em>Unpublished</em></div>
		<?php endif; ?>
			<?php if ($path) : ?>
			<div><span class="comment-author"><?php echo $row['author']; ?></span> </div>
			<?php else : ?>
				<?php if ($row['link']) : ?>
			<div><span class="comment-author"><?php echo $row['author']; ?></span> <em>commented on</em> <a href="/<?php echo $row['link']; ?>" target="_blank"><?php echo $row['linktitle']; ?></a></div>
				<?php else : ?>
			<div><span class="comment-author"><?php echo $row['author']; ?></span> <em>in frontpage</em></div>
				<?php endif; ?>
			<?php endif; ?>
			<div class="comment-body"><?php echo strip_tags($row['text']); ?></div>
		</div></div>
		<?php endwhile; ?>
	</div>
		<?php if ($num > $commentsperpage) : ?> 
	<nav class="mt-3" aria-label="Comment pagination"><ul class="pagination justify-content-center">
		<li id="nav-item-prev" class="page-item"><a class="page-link" href="#" data-page="<?php echo $totalpages-1; ?>">Previous</a></li>
		<li id="nav-item-first" class="page-item page-item-1"><a class="page-link" href="#" data-page="1">1</a></li>
			<?php for ($i=2; $i*$commentsperpage+1 <= $num; $i++) { ?>
		<li class="page-item page-item-<?php echo $i; ?>"><a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
			<?php } ?>
		<li id="nav-item-last" class="page-item disabled page-item-<?php echo $i; ?>"><a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
		<li id="nav-item-next" class="page-item disabled"><a class="page-link" href="#" data-page="2">Next</a></li>
	</ul></nav>
		<?php endif; ?>	
	<?php else : ?>
	<div class="row"><div class="col-12 nocomments">No comments yet</div></div>
	<?php endif; // if ($result->num_rows) ?>
<?php break; // case 0 ?>

<?php case 1 : ?>
	<h2 class="page-title mb-4">Settings</h2>
	<form method="post">
		<div class="form-group mb-5">
			<label for="field-premoderation" class="float-left pr-3">Pre-moderate comments</label>
			<select id="field-premoderation" name="premoderation" class="form-control float-left" aria-describedby="field-premoderation-help" default="1" >
				<option value="0" <?php if (!$settings['premoderation']) {echo 'selected';} ?>>No</option>
				<option value="1" <?php if ($settings['premoderation'] == 1) {echo 'selected';} ?>>Yes</option>
			</select>
			<small id="field-premoderation-help" class="form-text clear">'Yes' (recommended) to keep the comments unpublished until admin's reviewal. 'No' to publish the comments in the frontend right away.</small>
		</div>
		<div class="form-group mb-5">
			<label for="field-commentsperpage" class="float-left pr-3">Comments per page (frontend)</label>
			<select id="field-commentsperpage" name="commentsperpage" class="form-control float-left" aria-describedby="field-commentsperpage-help" default="10" >
				<option value="5" <?php if ($settings['commentsperpage'] == 5) {echo 'selected';} ?>>5</option>
				<option value="10" <?php if ($settings['commentsperpage'] == 10) {echo 'selected';} ?>>10</option>
				<option value="15" <?php if ($settings['commentsperpage'] == 15) {echo 'selected';} ?>>15</option>
				<option value="20" <?php if ($settings['commentsperpage'] == 20) {echo 'selected';} ?>>20</option>
			</select>
			<small id="field-commentsperpage-help" class="form-text clear">Maximum number of comments per page to be displayed in the frontend.</small>
		</div>
		<div class="form-group mb-5">
			<label for="field-commentsperpageadmin" class="float-left pr-3">Comments per page (admin panel)</label>
			<select id="field-commentsperpageadmin" name="commentsperpageadmin" class="form-control float-left" aria-describedby="field-commentsperpageadmin-help" default="10" >
				<option value="5" <?php if ($settings['commentsperpageadmin'] == 5) {echo 'selected';} ?>>5</option>
				<option value="10" <?php if ($settings['commentsperpageadmin'] == 10) {echo 'selected';} ?>>10</option>
				<option value="15" <?php if ($settings['commentsperpageadmin'] == 15) {echo 'selected';} ?>>15</option>
				<option value="20" <?php if ($settings['commentsperpageadmin'] == 20) {echo 'selected';} ?>>20</option>
			</select>
			<small id="field-commentsperpageadmin-help" class="form-text clear">Maximum number of comments per page to be displayed in the admin panel.</small>
		</div>
		<button id="submit-btn" type="submit" class="btn btn-primary">Apply Settings</button>
	</form>
<?php break; // case 1 ?>

<?php endswitch; ?>


</div>
</section>

</main>
<footer class="footer navbar-dark bg-dark mt-auto py-3">
<div class="container"><div><span>Copyright notice</span></div></div>
</footer>


<script type='text/javascript' src='js/jquery-3.4.1.min.js'></script>
<script type='text/javascript' src='js/bootstrap.bundle.min.js'></script>
<script type='text/javascript' src='js/customadmin.js'></script>
</body>
</html>
