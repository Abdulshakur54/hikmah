<?php // content="text/plain; charset=utf-8"
// $Id: groupbarex1.php,v 1.2 2002/07/11 23:27:28 aditus Exp $
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_bar.php');
$datay1=array(35,160,0,0,0,0);
$datay2=array(35,190,190,190,190,190);
$datay3=array(20,70,70,140,230,260);
 
$graph = new Graph(450,200,'auto');    
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->img->SetMargin(40,30,40,40);
$graph->xaxis->SetTickLabels($gDateLocale->GetShortMonth());
 
$graph->xaxis->title->Set('Year 2002');
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
$graph->title->Set('Group bar plot');
$graph->title->SetFont(FF_FONT1,FS_BOLD);
 
$bplot1 = new BarPlot($datay1);
$bplot2 = new BarPlot($datay2);
$bplot3 = new BarPlot($datay3);
 
$bplot1->SetFillColor("orange");
$bplot2->SetFillColor("blue");
$bplot3->SetFillColor("red");
 

$gbarplot = new GroupBarPlot(array($bplot1,$bplot2,$bplot3));
$gbarplot->SetWidth(0.6);
$graph->Add($gbarplot);
 if(file_exists('barcharts/barchart.png')){
unlink('barcharts/barchart.png');
 }
$graph->Stroke('barcharts/barchart.png');
?>
