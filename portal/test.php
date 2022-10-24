<?php
spl_autoload_register(
    function($class){
        require_once'../classes/'.$class.'.php';
    }
);
require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_pie.php');

//SMS::send('Assalamu alaykum','2348087159516','2348106413226');
$pie_image_path = 'bar_' . Token::create(5) . '.png';
Chart::pie_chart([5, 6, 7, 3, 5, 6], 'Chart Title', 200, 200, true, $pie_image_path);