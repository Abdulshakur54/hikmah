<?php
class Chart
{
    private  $graph;

    private function initializeGraph(int $width, int $height)
    {
        $graph = new Graph($width, $height, 'auto');
        $graph->SetScale('intint', 0, 100);
        $graph->img->SetMargin(40, 10, 10, 40);
        $graph->yaxis->title->SetFont(FF_VERDANA, FS_NORMAL, 8);
        $graph->yaxis->title->SetColor('blue');

        $graph->xaxis->SetColor('black');
        $graph->yaxis->SetColor('black');
        $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
        $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
        $this->graph = $graph;
    }
    // @param $data is an array of number arrays
    public function bar_chart(int $width, int $height, array $data, string $xTitle, string $yTitle, array $labels, $save = false, $file_path = '')
    {
        $len = count($data);
        for ($i = 0; $i < $len; $i++) {
            ${'dat' . $i} = $data[$i];
        }
        $this->initializeGraph($width, $height);
        $this->graph->xaxis->SetTitle($xTitle, 'center');
        $this->graph->yaxis->SetTitle($yTitle, 'center');
        $this->graph->xaxis->SetTickLabels($labels);
        // $this->graph->xaxis->SetLabelAngle(70);
        $barPlots = [];
        for ($i = 0; $i < $len; $i++) {
            ${'barPlot' . $i} =  new BarPlot(${'dat' . $i});
            $barPlots[] = ${'barPlot' . $i};
        }
        $gbarplot = new GroupBarPlot($barPlots);
        $gbarplot->SetWidth(0.6);
        $this->graph->Add($gbarplot);
        if ($save) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $this->graph->Stroke($file_path);
        } else {
            $this->graph->Stroke();
        }
    }

    public static function pie_chart(array $data,string $title,int $width,int $height, array $labels,$save = false, $file_path = '') :void{
        $graph = new PieGraph($width, $height);
        $graph->legend->SetPos(0.93,0.67,'center','bottom');
        $graph->legend->SetColumns(1);
        $graph->legend->SetHColMargin(25);
        $graph->legend->SetVColMargin(25);
        $graph->legend->SetFont(FF_VERDANA, FS_BOLD, 9);
        $p1 = new PiePlot3D($data);
        $p1->SetLegends($labels);
        $p1->value->SetFont(FF_VERDANA,FS_BOLD,11);
        $p1->SetCenter(0.4, 0.5);
        $p1->SetLabelPos(0.65);
        $p1->ExplodeAll(7);
        $graph->Add($p1);
        if ($save) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $graph->Stroke($file_path);
        } else {
            $graph->Stroke();
        }
    }
}
