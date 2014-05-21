<!DOCTYPE HTML>
<html>
<head>
<title>25 Truganini</title>
<meta name="description" content="25 Truganini development" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./includes/style.css" media="screen" />
<link rel="stylesheet" href="./includes/flexslider.css" type="text/css" media="screen" />
<script src="./includes/js/modernizr.js"></script>
</head>
<body>
		<div class="logo">
			<img src="./includes/images/logo.png">
		</div>
	<div class="wrapper">
		<div class="w-slider">
			<div class="flexslider">
          <ul class="slides">
            <li>
                <img src="./includes/images/1.jpg" alt="" />
                </li>
                <li>
                <img src="./includes/images/2.jpg" alt="" />
                </li>
                <li>
                <img src="./includes/images/3.jpg" alt="" />
                </li>
                <li>
                <img src="./includes/images/4.jpg" alt="" />
                </li>
                <li>
                <img src="./includes/images/5.jpg" alt="" />
                </li>
          </ul>
        </div>
		</div>
		<div class="w-bottom">
			<div class="bottom-left">
				<p class="register-text">Register your interest</p>
				<div class="divider"></div>
				<?php include 'form.php' ?>
			</div>
			<div class="bottom-right">
				<p class="large-2-3">Large 2 & 3 Bedroom<br/>Apartments</p>
				<div class="divider"></div>
				<p class="james">James Cirelli</>
				<p>0401 570 180</p>
				<p style="text-transform: lowercase;">james@steller.com.au</p>
				<img src="./includes/images/steller-logo.jpg" alt="Steller logo">
			</div>
		</div>
	</div>
	  <!-- jQuery -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="./includes/js/jquery-1.7.min.js">\x3C/script>')</script>
  <!-- FlexSlider -->
  <script defer src="./includes/js/jquery.flexslider.js"></script>
  <script type="text/javascript">
    $(function(){
      SyntaxHighlighter.all();
    });
    $(window).load(function(){
      $('.flexslider').flexslider({
        animation: "slide",
        start: function(slider){
          $('body').removeClass('loading');
        }
      });
    });
  </script>
</body>
</html>