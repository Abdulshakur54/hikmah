<?php
spl_autoload_register(
    function($class){
        require_once'../classes/'.$class.'.php';
    }
);
require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_pie.php');

SMS::send('Assalamu alaykum wo rohmatullah wa barakattuh','08087159516,08106413226');