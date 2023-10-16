<!doctype html>
<html class="no-js" lang="fr">
    <head> 
        <title>Système de Gestion Integré des votes | {{ config('app.name') }}</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        @include('layouts.layouts-css')
    </head>

    <body class="home-gradient-bg">
		<div class="container ">
			
			@include('include.top-header')
	    	<div class="banner-text m-4 d-relative">
	    		<img height="50" class="d-absolute left-0"  src="{{asset('/img/p1.png')}}">
	    		<img height="300" class="d-absolute"  src="{{asset('/img/s1-2.png')}}">
	    		<img height="50" class="d-absolute right-0"  src="{{asset('/img/s2-2.png')}}">
	    		<marquee>Système de Gestion Integré des votes</marquee>
	    	</div>

	    	<div class="radmin-bannner text-center">
	    		<img  src="{{asset('/img/radmin.jpg')}}">
	    	</div>
		</div>
		<footer class="footer card bg-orange mt-1">
		    <div class="card-body template-demo text-center">
                <a href="" class="btn social-btn text-white btn-google"><i class="ik ik-globe"></i></a>
                <a href="" class="btn social-btn text-white btn-facebook "><i class="fab fa-github"></i></a>
                <a href="" class="btn social-btn text-white btn-twitter"><i class="fab fa-twitter"></i></a>
                <a href="" class="btn social-btn text-white btn-linkedin"><i class="fab fa-linkedin"></i></a>
            </div>
		    <div class="w-100 clearfix">
		        <span class="text-center text-sm-left d-md-inline-block text-white">
		            V{{config('app.version')}} {{ __('Copyright © '.date("Y"))}} - {{config('app.name')}}
		        </span>
		        <span class="float-none float-sm-right mt-1 mt-sm-0 text-center text-white">
		            {{ __('Developed by')}}
		            <a class="text-dark" target="_blank" href="https://www.sonecafrica.com">Sonec Africa</a>
		            </a>
		            with <i class="fa fa-heart text-danger"></i>
		        </span>
		    </div>
		</footer>
        @include('layouts.layouts-js')
    </body>
</html>