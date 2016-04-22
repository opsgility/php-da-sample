<?php
/**
 * Twitter widget with title
 */ 
?>
<?php $widget_info    =   $this->config->item('twitter_hashtag');?>
<div class="twitter-panel-title">
    <div class="twitter-panel-title-icon"></div>
    <div class="twitter-panel-title-caption">Twitter</div>    
</div>
<div class="clear_both twitter-panel-content"> 
    <div class="t_widgets">
        <div class="t_widgets_tabs">
            <ul class="nav nav-tabs" id="t_widget">
                <li class="active t_hashtag"><a href="#hashtag1"> <?php echo $widget_info[1]['key'];?></a></li>
                <li class="t_hashtag"><a href="#hashtag2"><?php echo $widget_info[2]['key'];?></a></li>
                <li class="t_hashtag"><a href="#hashtag3"><?php echo $widget_info[3]['key'];?></a></li>
            </ul>
        </div>

        <div id='content' class="tab-content">
            <div class="tab-pane active" id="hashtag1">
                <a class="twitter-timeline" href="<?php echo $widget_info['search_link'].htmlentities($widget_info[1]['key']);?>" data-chrome="noheader" data-widget-id="<?php echo $widget_info[1]['widget_id'];?>">Tweets about "<?php echo $widget_info[1]['key'];?>"</a>
            </div>
            <div class="tab-pane" id="hashtag2">
            <a class="twitter-timeline" href="<?php echo $widget_info['search_link'].htmlentities($widget_info[2]['key']);?>" data-chrome="noheader" data-widget-id="<?php echo $widget_info[2]['widget_id'];?>">Tweets about "<?php echo $widget_info[2]['key'];?>"</a>
            </div>
            <div class="tab-pane" id="hashtag3">
                <a class="twitter-timeline" href="<?php echo $widget_info['search_link'].htmlentities($widget_info[3]['key']);?>" data-chrome="noheader" data-widget-id="<?php echo $widget_info[3]['widget_id'];?>">Tweets about "<?php echo $widget_info[3]['key'];?>"</a>
            </div>
            <div class="tab-pane" id="settings"></div> 
        </div> 
    </div>
</div>