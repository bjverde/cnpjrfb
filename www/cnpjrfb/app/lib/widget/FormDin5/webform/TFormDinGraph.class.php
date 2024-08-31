<?php
class TFormDinGraph
{

    public static function gerarBarChart(array $data,$width,$height,string $title,string $ytitle,string $xtitle){
        $pieChart = new THtmlRenderer('app/resources/google_bar_chart.html');
        $pieChart->enableSection('main',['data'  => json_encode($data)
                                        ,'width' => $width
                                        ,'height'=> $height
                                        ,'title' => $title
                                        ,'ytitle'=> $ytitle
                                        ,'xtitle'=> $xtitle
                                        ,'uniqid'=> uniqid()
                                        ]);
        return $pieChart;
    }

    public static function gerarPieChart(array $data,$width,$height,string $title,string $ytitle,string $xtitle)
    {   
        $pieChart = new THtmlRenderer('app/resources/google_pie_chart.html');
        $pieChart->enableSection('main',['data'  => json_encode($data)
                                        ,'width' => $width
                                        ,'height'=> $height
                                        ,'title' => $title
                                        ,'ytitle'=> $ytitle
                                        ,'xtitle'=> $xtitle
                                        ,'uniqid'=> uniqid()
                                        ]);
        return $pieChart;
    }
    
    public static function showInfoBox($title,$icon,$background,$valeu)
    {   
        $infoBox = new THtmlRenderer('app/resources/info-box.html');
        $infoBox->enableSection('main', ['title'      => $title
                                        ,'icon'       => $icon
                                        ,'background' => $background
                                        ,'value'      => $valeu
                                        ]);
        return $infoBox;
    }
    
}