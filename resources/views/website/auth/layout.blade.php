<!DOCTYPE html>
<html>
<head>
    
    @include('website._partials._head')	

    <style type="text/css">
    	html, body {
    		height: 100%;
    	}
    </style>

</head>
<body id="authPage">

	<div class="container">
	    @yield('content')
	</div>

</body>
</html>