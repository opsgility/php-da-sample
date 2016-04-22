<!DOCTYPE html>
<html lang="en">
<head>    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title> INSTALL Digital Agency Web Site Sample </title>   
    <style>
        body{background: none;}
        #install {
            width: 1000px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        #status div{
            padding-bottom: 10px; 
        }
    </style>
</head>
<body>
<!-- [if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif] -->

<style>h1{font-size:20px}</style>
		
<!-- 2-column layout content area-->
<div class="row row-offcanvas row-offcanvas-right" id="install">
    <div class="col-xs-12 col-sm-8">
        <h1>Installation of Digital Agency Web Site</h1>
        <div class="container-fluid" id="status">
            <div>Database Exported successfully</div>
        </div>
        <br />
        <div class="container-fluid" id="working_status">Creating containers &hellip;</div>
        <br />
        <div id="ajaxBar"></div>
    </div>
</div><!--/row-->    

<script src="<?php echo STATIC_URL.'js/jquery.min.js';?>"></script>
<script>
var base_url = '<?php echo base_url(); ?>';

// jQuery with the document ready function on page load
$(function () {
    $("#ajaxBar").html($('<img/>').attr({ src: base_url + '/assets/images/ajax-loader-bar.gif', id: 'ajax-loader-bar' }));

    function runMyAjax(url){
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            success: function (response) {                
               // console.log('DATA', response);
                if(response.isInstallComplete){
                   //reload.href = base_url;
                   //console.log('inside if ');
                   var mainpage_link = '&nbsp;&nbsp; <a href="'+base_url+'"> click here</a>';
                   jQuery('#working_status').html(response.updatemsg + mainpage_link);
                   $("#ajaxBar").html('');
                }else if(!response.isInstallComplete){
                  // console.log('inside if else');
                    if(response.message == 'Twitter mining container created successfully.'){                        
                       // console.log('mahesh 1');
                        if($( "#status div:last" ).text() === response.message){
                            $( "#status div:last" ).text( response.message );
                        }else{
                            jQuery('#status').append('<div>'+response.message+'</div>');
                        }
                    }else{                    
                        jQuery('#status').append('<div>'+response.message+'</div>');
                    }
                   jQuery('#working_status').html(response.updatemsg);
                    runMyAjax(response.url);
                }
            },
            error: function (xhr, desc, err) {
               // console.log(desc);
               // console.log(err);
            }
        });
    }

    runMyAjax(base_url + 'startinstall/createcontainer');
});    
</script>
</body>
</html>