<?php if( isset($topUsers) && (count($topUsers) > 0) && !empty($topUsers) ) { ?>
<div class="top-users-list">
    <strong>Top 5 Users</strong>
<?php foreach ($topUsers AS $user){ ?>
    <ul>
        <li class="user_name"><?php echo $user->name; ?></li>
        <li class="user_posts"><?php echo $user->noOfPosts; ?> Post<?php echo ($user->noOfPosts > 1) ? 's':''; ?></li>
    </ul>
<?php } /* End foreach */ ?>    
</div>
<?php  } /* If condition finish about topUsers */ ?>
