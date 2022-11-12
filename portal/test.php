<?php
spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_pie.php');

//SMS::send('Assalamu alaykum wo rohmatullah wa barakattuh','08087159516,08106413226');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div id="2021/2022/A029">Hello</div>
    <button onclick="addWorld()">Click</button>
    <div>
        <div>Congratulations! You have been offered admission to <em>Basic 5, Hikmah International School</em></div>
        <div>Kindly click on this link <a class="text-primary" onclick="getPage('http://localhost/hikmah/portal/dashboard.php?page=admission/admission_status.php')" href="#"> My Response to your offer</a> to respond to our offer.
    </div>
</body>
<script>
    function addWorld() {
        document.getElementById('2021/2022/A029').innerHTML = 'Hello World';
    }
</script>

</html>