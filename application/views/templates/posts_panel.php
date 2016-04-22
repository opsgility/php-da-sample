<div class="post-panel-content">
<?php
if( isset($posts) && (count($posts) > 0) && !empty($posts) )
{
    foreach ($posts AS $post)
    {
        $title  =   str_replace(' ', '-', $post->postTitle);     // Replaces all spaces with hyphens.
        $title  =   preg_replace('/[^A-Za-z0-9\-]/', '', $title);   // Remove special characters...
        $title  =   preg_replace('/-+/', '-', strtolower($title));              // Replaces multiple hyphens with single one.
        
        $url = base_url().'community/view/'.$post->postId.'/'.$title; // make url using with discussion id and title
?>
	<div class="posts-list">
        <p>
            <span class="post_date"><?php echo date('j<\s\up>S</\s\up> F Y', strtotime($post->postDate)); ?></span>
            <?php if($post->noOfPostResponses) { ?>
            <span class="post_resp"><?php echo $post->noOfPostResponses;?> Replie<?php echo ($post->noOfPostResponses > 1) ? 's':'';?></span>
            <?php } ?>
        </p>            
        <p class="post_title"><a href="<?php echo $url; ?>"><?php echo $post->postTitle; ?></a></p>        
		<p class="post_content"><a href="<?php echo $url; ?>"><?php echo substr($post->postContent, 0, 60).'...'; ?></a></p>
	</div>	
<?php
    }
}// If condition finish about posts
?>
</div>