<?php
require_once('vendor/autoload.php');

if(isset($_POST['blog'])){
	
	$blog = $_POST['blog'];
	if ($blog) {
		$client = new Tumblr\API\Client('XXXXXXXXX', 'XXXXXXXXX');
	}
}
?>

<!doctype html>
<html>
	<head>
    	<meta charset="utf-8">
        <title>Blog Loader</title>
		<script>
			document.onkeydown=function(evt){
				var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
				if(keyCode == 13)
				{
					document.bloginput.submit();
				}
			}
		</script>
        <script src="lib/sorttable.js"></script>
        <link rel="stylesheet" type="text/css" href="lib/style.css">
	</head>
    
	<body class="blog-loader">
    	<?php include_once("../analyticstracking.php") ?>
        <script>ga('send', 'pageview');</script>
    
		<form name="bloginput" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
		<input type="text" name="blog" <?php if ($blog) {?>value="<?php echo $blog; ?>" <?php } ?> />.tumblr.com
        <input type="submit" value="Load" />
		</form>
		
        <?php if ($blog) { ?>
		<table class="sortable">
			<thead>
				<th>Date</th>
                <th>Post</th>
				<th>Tags</th>
				<th>Likes</th>
				<th>Reblogs</th>
				<th>Link</th>
			</thead>
			<tbody>
			<?php

			$index = 0;
			$total_posts = $client->getBlogPosts($blog.'.tumblr.com', array('limit' => 1))->total_posts;
			
			do {
				try {
					
					$posts = $client->getBlogPosts($blog.'.tumblr.com', array('offset' => $index, 'notes_info' => true))->posts;
					$index += count($posts);
					foreach($posts as $post) { 
					
						$likes = 0;
						$reblogs = 0;
						
						if (!empty($post->notes)) {
							foreach ($post->notes as $note) {
								switch ($note->type) {
									case "reblog":
										$reblogs++;
										break;
									case "like":
										$likes++;
										break;
								}
							}
						}
						
						?>
					<tr>
						<td><?php echo date('d/m/y', strtotime($post->date)); ?></td>
						<td><?php 
							echo '<h2>'.$post->title.'</h2>';
							
							switch ($post->type) {
								case "quote":
									echo $post->text;
									break;
								case "link":
									echo '<a href="'.$post->url.'" target="_blank">'.$post->excerpt.'</a><br />';
									echo $post->description.'<br />';
									if ($post->photos) {
										foreach ($post->photos as $photo) {
											echo '<img src="'.$photo->alt_sizes[3]->url.'" width="'.$photo->alt_sizes[3]->width.'" height="'.$photo->alt_sizes[3]->height.'" />';
											echo $photo->caption;
											//print_r($photo->alt_sizes);
										}
									}
									break;
								case "answer":
									echo '<a href="'.$post->asking_url.'" target="_blank">'.$post->asking_name.'</a><br />';
									echo $post->question.'<br />';
									echo $post->answer;
									break;
								case "video":
								case "audio":
									echo $post->caption.'<br />';
									echo end($post->player)->embed_code;
									break;
								case "photo":
									echo $post->caption.'<br />';
									foreach ($post->photos as $photo) {
										echo '<img src="'.$photo->alt_sizes[3]->url.'" width="'.$photo->alt_sizes[3]->width.'" height="'.$photo->alt_sizes[3]->height.'" />';
										echo $photo->caption;
										//print_r($photo->alt_sizes);
									}
									break;
								case "text":
								case "chat":
									echo $post->body;
									break;
							}
						?></td>
                    <td><?php 
						foreach ($post->tags as $tag) {
							echo '<a href="http://www.tumblr.com/tagged/'.$tag.'" target="_blank">'.$tag.'</a>&nbsp;&nbsp;'; 
						}?></td>
                    <td><?php echo $reblogs; ?></td>
                    <td><?php echo $likes; ?></td>
                    <td><a href="<?php echo $post->post_url; ?>" target="_blank">link</a></td>
                </tr>
            <?php }  
				} catch (Exception $e) { ?>
			<p>Is the blog name correct?</p>
				<?php } 
			} while ($index < $total_posts);?>
            </tbody>
		</table>
        <?php } ?>
		
	</body>
</html>
