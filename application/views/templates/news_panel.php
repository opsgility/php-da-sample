<div class="news-panel-content">
<?php foreach ($items AS $item){ ?>	
	<div class="news-list">
        <?php $datetime   =   new DateTime($item->pubDate); ?>        
        <p><span class="news_date"><?php echo date_format($datetime, 'j<\s\up>S</\s\up> F Y'); ?></span> <span><a class="news_link" href="<?php echo $item->link; ?>" <?php $sourceUrl = parse_url($item->link); echo (isset($sourceUrl['host'])) ? 'target="_blank"' : ''; ?>>Source</a></span></p>
        <p class="news_text"><?php echo substr($item->description, 0, 60).'...'; ?></p>
	</div>	
<?php } ?> 
</div>    