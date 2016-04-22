    <footer class="footer">
        <div class="row" id="footer-row">
            <div class="col-sm-12">
                <div class="col-sm-4">
                    <span>Contact Us</span>
                    <ul>
                        <li>One Microsoft Way</li>
                        <li>Redmond, WA 98052-6399</li>
                        <li>123-456-7890</li>                       
                    </ul>
                </div>
                <div class="col-sm-4">
                    <span>Quick Links</span>
                    <ul>
                        <li><a href="<?php echo base_url(); ?>" title="Home">Home</a></li>
                        <li><a href="<?php echo base_url(); ?>community" title="Community">Community</a></li>
                        <li><a href="<?php echo base_url(); ?>product" title="Product">Product</a></li>
                    </ul>
                </div>
                <div class="col-sm-4">&nbsp;</div>                
            </div>                
        </div>
        <div class="row"><div class="col-sm-12 copyright"><span>2014 &copy Contoso All Rights Reserved</span></div></div>
    </footer>
</div><!--/.container-->

<!-- add javascripts -->
<script>
    var base_url = '<?php echo base_url(); ?>';
</script>    
    <script src="<?php echo STATIC_URL.'js/dawjs.min.js';?>"></script>
    <!--
    <script src="<?php //echo STATIC_URL.'js/jquery.min.js';?>"></script>
    <script src="<?php //echo STATIC_URL.'js/jquery.validate.js';?>"></script>
    <script src="<?php //echo STATIC_URL.'js/bootstrap.min.js';?>"></script>
    <script src="<?php //echo STATIC_URL.'js/nhpup_1.1.js';?>"></script>
    <script src="<?php //echo STATIC_URL.'js/custom.js';?>"></script>
    -->
<script>
    window.twttr = (function (d, s, id) {
        var t, js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return; js = d.createElement(s); js.id = id;
        js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
        return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
    }(document, "script", "twitter-wjs"));
</script>
</body>
</html>