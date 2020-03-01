<?php
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
</head>

<?php
$path = $_SERVER['REQUEST_URI'];

if ($path) {
	$path = preg_replace ("'^/(.*)$'","$1",$path);
}

$dbcon = new mysqli(DBSERVERNAME, DBUSER, DBPASS, DBNAME);

if ($dbcon->connect_error) {
	die("Connection failed");
}

$dbcon->set_charset("utf8");
?>

<body id="body" class="d-flex flex-column h-100">

<header id="header" class="navbar-dark bg-dark">
<div class="container">
<nav class="navbar navbar-expand-md">
<div id="mainmenu" class="navbar-collapse collapse"><ul class="navbar-nav mr-auto">
<li id="menu-item-home" class="nav-item active"><a class="nav-link" href="/">Home</a></li>
<li id="menu-item-admin" class="nav-item"><a class="nav-link" href="admin.php">Admin Panel</a></li>
</ul></div>
<button class="btn btn-secondary navbar-toggler float-right collapsed" type="button" data-toggle="collapse" data-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
</nav></div>
</header>
<main>

<?php
$id = 0;
$data = NULL;

if ($path) {
	$query = "SELECT * FROM ".DBTABLEPREFIX."assets WHERE `alias`='".$path."' AND `state`>0 LIMIT 1";
	$result = $dbcon->query($query);
	if (!$result->num_rows) {
		http_response_code(404);
		echo '<div class="container"><h1 class="text-center">404: Page Not Found</h1></div></main><footer class="footer navbar-dark bg-dark mt-auto py-3"><div class="container"><div><span>Copyright notice</span></div></div></footer></body></html>';
		die();
	} else {
		$data = $result->fetch_assoc();
		$id = $data['id'];
	}
} else {
	$query = "SELECT * FROM ".DBTABLEPREFIX."assets WHERE `state` > 0 ORDER BY `state` DESC LIMIT 9";
	$result = $dbcon->query($query);
}

?>

<section class="data">
<div class="container">
<?php if (!$path) : ?>
<?php if ($result->num_rows) : ?>
<?php $images = array(); ?>
<div class="row">
	<?php while ($row = mysqli_fetch_array($result)) : ?>
	<div class="col col-12 col-md-6 col-lg-4 asset-<?php echo $row['id']; ?>"><div class="card-wrapper"><a class="card" href="/<?php echo $row['alias']; ?>" >
		<?php $name = mb_substr(strip_tags($row['name']),0,75); ?>
		<?php $desc = mb_substr(strip_tags($row['desc']),0,150); ?>		
		<?php if ($row['image']) : ?>
		<?php $images[$row['id']] = $row['image']; ?>
		<span class="container"><span class="row justify-content-center"><span class="col-sm px-0"><span class="embed-responsive embed-responsive-16by9"><span class="card-image embed-responsive-item" alt="<?php echo $name; ?>" title="<?php echo $name; ?>"></span></span></span></span></span>
		<?php endif; // if ($row['image']) : ?>
		<span class="card-body">
		<span class="card-title"><?php echo $name; ?></span>
		<span class="card-desc"><?php echo $desc; ?></span>
		</span>
	</a></div></div>
	<?php endwhile; ?>
</div>
<?php endif; // if ($result->num_rows) ?>
<?php else : // if (!$path) ?>
	<?php if ($data) : ?>
<div class="text-center">
		<?php $name = strip_tags($data['name']); ?>
		<?php $desc = strip_tags($data['desc']); ?>
		<h2 class="page-title"><?php echo $name; ?></h2>
		<?php if ($data['image']) : ?>
		<div class="main-image-wrap"><img class="main-image" src="/images/<?php echo $data['image']; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" /></div>
		<?php endif; ?>
		<?php if ($desc) : ?>
		<div class="page-desc"><?php echo $desc; ?></div>
		<?php endif; ?>
</div>
	<?php endif; ?>
<?php endif; // if (!$path) ?>
</div>
</section>

<?php

if ($settings['commentsperpage']) {$commentsperpage = $settings['commentsperpage'];}
else {$commentsperpage = 10;}

if ($id) {
	$query = "SELECT * FROM ".DBTABLEPREFIX."comments WHERE `state`>0 AND `parent`=".$id." LIMIT ".$commentsperpage;
} else {
	$query = "SELECT t1.`author` as author, t1.`text` as text, t1.`parent` as parent, t1.`date` as `date`, t2.`alias` as link, t2.`name` as linktitle FROM ".DBTABLEPREFIX."comments t1 LEFT JOIN ".DBTABLEPREFIX."assets t2 ON t1.`parent`=t2.`id` WHERE t1.`state`>0 ORDER BY t1.`state` DESC LIMIT ".$commentsperpage;
}

$result = $dbcon->query($query);

if ($id) {
	$query = "SELECT count(1) FROM ".DBTABLEPREFIX."comments WHERE `state`>0 AND `parent`=".$id;
} else {
	$query = "SELECT count(1) FROM ".DBTABLEPREFIX."comments t1 LEFT JOIN ".DBTABLEPREFIX."assets t2 ON t1.`parent`=t2.`id` WHERE t1.`state`>0";
}

$num = $dbcon->query($query);
$num = mysqli_fetch_array($num)[0];
?>

<section class="comments">
<div class="container">
<h3>Comments</h3>

<?php if ($result->num_rows) : ?>
<div class="row">
	<?php while($row = $result->fetch_assoc()) : ?>
	<div class="comment col col-12 mb-2"><div class="wrap bg-light shadow-sm rounded">
		<?php if ($path) : ?>
		<div><span class="comment-author"><?php echo $row['author']; ?></span> </div>
		<?php else : ?>
			<?php if ($row['link']) : ?>
		<div><span class="comment-author"><?php echo $row['author']; ?></span> <em>commented on</em> <a href="/<?php echo $row['link']; ?>"><?php echo $row['linktitle']; ?></a></div>
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
	<li id="nav-item-prev" class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
	<li id="nav-item-first" class="page-item page-item-1 disabled"><a class="page-link" href="#" data-page="1">1</a></li>
		<?php for ($i=2; $i*$commentsperpage+1 <= $num; $i++) { ?>
	<li class="page-item page-item-<?php echo $i; ?>"><a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
		<?php } ?>
	<li id="nav-item-last" class="page-item page-item-<?php echo $i; ?>"><a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
	<li id="nav-item-next" class="page-item"><a class="page-link" href="#" data-page="2">Next</a></li>
</ul></nav>
	<?php endif; ?>	
<?php else : ?>
<div class="row"><div class="col-12 nocomments">No comments yet</div></div>
<?php endif; // if ($result->num_rows) ?>

<div class="new-comment">
<h3>Leave Your Comment</h3>

<form id="new-comment-form" method="post" enctype="multipart/form-data"<?php if ($id) {echo ' data-id="'.$id.'"';} ?> >
	<div class="form-group">
		<label for="input-name">Your Name</label>
		<input type="text" class="form-control" id="input-name" aria-describedby="input-name-help">
		<div id="input-name-invalid-notice" class="invalid-feedback">Please fill this field</div>
		<small id="input-name-help" class="form-text text-muted">Introduce yourself</small>
	</div>
	<div class="form-group">
		<label for="input-text">Your Comment</label>
		<textarea class="form-control" id="input-text" rows="5"></textarea>
		<div id="input-text-invalid-notice" class="invalid-feedback">Please fill this field</div>
		<small id="input-text-help" class="form-text text-muted">Tell us what you think</small>
	</div>
	<button id="submit-comment-btn" type="submit" class="btn btn-primary">Submit</button>
</form>
</div>

</section>

</main>
<footer class="footer navbar-dark bg-dark mt-auto py-3">
<div class="container"><div><span>Copyright notice</span></div></div>
</footer>

<?php if (!$path) : ?>
<style>
<?php foreach ($images as $id=>$image) : ?>
<?php if ($image) : ?>
.asset-<?php echo $id; ?> .card-image{background-image:url("/images/<?php echo $image; ?>")}
<?php endif; ?>
<?php endforeach; ?>
</style>
<?php endif; ?>

<script type='text/javascript' src='js/jquery-3.4.1.min.js'></script>
<script type='text/javascript' src='js/bootstrap.bundle.min.js'></script>
<script type='text/javascript' src='js/custom.js'></script>
</body>
</html>
