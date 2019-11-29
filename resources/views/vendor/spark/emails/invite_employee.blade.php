<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1></h1>
<p> {{preg_replace('#^https?://#', '', \URL::to('/'))}} Admin invited you to create a staff account
    at {{preg_replace('#^https?://#', '', \URL::to('/'))}}.</p>
<p>If you don’t need a staff account at {{preg_replace('#^https?://#', '', \URL::to('/'))}}, you can decline this
    invitation <a href="">decline this invitation.</a>.</p>


<a href="{{env('APP_URL')}}/register">Create account</a>

</body>
</html>
