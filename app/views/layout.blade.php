<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8"/>
		<title>Evernote All</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta name="description" content=""/>
		<meta name="author" content="Mauricio Giordano <mauricio.c.giordano@gmail.com>"/>

		<style>
			body {
			padding-top: 40px; /* 60px to make the container go all the way to the bottom of the topbar */
			}
		</style>
		<!-- CSS -->
		<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"/>
		<link href="{{ asset('css/my-bootstrap.css') }}" rel="stylesheet"/>
		<link href="{{ asset('css/bootstrap-responsive.min.css') }}" rel="stylesheet"/>
		<link href="{{ asset('css/template.css') }}" rel="stylesheet"/>
		<link href="{{ asset('css/template-responsive.css') }}" rel="stylesheet"/>
		<link href="{{ asset('css/pages.css') }}" rel="stylesheet"/>
		<link href="{{ asset('css/gchrome-scrollbar.css') }}" rel="stylesheet"/>
		<link rel="shortcut icon" href="favicon.ico">
		<script src="{{ asset('javascript/jquery-1.8.2.min.js') }}"></script>
	</head>
	<body>
		<div id="header">
			<div class="header1 navbar navbar-inverse dark-gray navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
						<a class="brand" href="">
							<i class="icon-tags icon-white logo-margin"></i>
							evernote all
						</a>
						<div class="nav-collapse collapse">
							<ul class="nav">
								@if($route == "home")
								<li class="active">
								@else
								<li>
								@endif
									<a href="home">
										<i class="icon-home icon-white"></i>
									</a>
								</li>
								<!-- IF LOGGED -->
								@if($route == "user-configuration")
								<li class="active">
								@else
								<li>
								@endif
									<a href="user-configuration">
										<i class="icon-wrench icon-white"></i>
									</a>
								</li>
								<!-- ENDIF -->
								@if($route == "help")
								<li class="active">
								@else
								<li>
								@endif
									<a href="help">
										<i class="icon-question-sign icon-white"></i>
									</a>
								</li>
							</ul>
							<div class="user-menu pull-right">
								<div class="user-login">
									@if($evernote->auth->Token == false)
									<a href="/login">
										<i class="icon-user icon-white"></i>
										Sign in with Evernote
									</a>
									@else
									<a href="">
										<i class="icon-user icon-white"></i>
										{{ $evernote->user->name }}
									</a>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div id="content">
				{{ $view }}
			</div>
		</div>
		<span id="u-id" guid=""></span>
		<script src="{{ asset('javascript/bootstrap.min.js') }}"></script>
		<script src="{{ asset('javascript/template.js') }}"></script>
	</body>
</html>