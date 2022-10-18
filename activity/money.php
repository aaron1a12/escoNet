<?php

class customPage extends page {
    public $title = 'Recent Activity';
    
    public $jsDataArray;
    
    function init()
    {
        $query = "SELECT funds,time FROM esco_money_stats WHERE user=2";
        $queryResult = mysqli_query($this->link, $query);


        $this->jsDataArray = '';

        $extraDay = 0;
        while ($row = mysqli_fetch_row($queryResult)) {
            
            $extraDay = $extraDay+1000000;
            
            $date = date('Y-m-d', strtotime($row[1])+$extraDay);
            $this->jsDataArray .= '{ "date": "'.$date.'", "value": '.$row[0].' },';
        }
    }
    
    function head(){
    ?>
<script src="http://media.esco.net/global/amcharts/amcharts.js" type="text/javascript"></script>
<script src="http://media.esco.net/global/amcharts/serial.js" type="text/javascript"></script>   
            
<script type="text/javascript">
    var chart;
    var graph;

    var chartData = [
        <?php echo $this->jsDataArray;?>
    ];


    AmCharts.ready(function () {
        // SERIAL CHART
        chart = new AmCharts.AmSerialChart();
        chart.pathToImages = "../amcharts/images/";
        chart.dataProvider = chartData;
        chart.marginLeft = 10;
        chart.categoryField = "date";
        chart.dataDateFormat = "YYYY-MM-DD";

        // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
        //chart.addListener("dataUpdated", zoomChart);

        // AXES
        // category
        var categoryAxis = chart.categoryAxis;
        categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true


        // AARON CHANGE HERE FOR DATES!!! minPeriod can be DD, MM, or YYYY		
        categoryAxis.minPeriod = "DD"; // our data is yearly, so we set minPeriod to YYYY


        categoryAxis.dashLength = 3;
        categoryAxis.minorGridEnabled = true;
        categoryAxis.minorGridAlpha = 0.1;

        // value
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.axisAlpha = 0;
        valueAxis.inside = true;
        valueAxis.dashLength = 3;
        chart.addValueAxis(valueAxis);

        // GRAPH
        graph = new AmCharts.AmGraph();
        graph.type = "line"; // this line makes the graph smoothed line.
        graph.lineColor = "#00aa22";
        graph.negativeLineColor = "#637bb6"; // this line makes the graph to change color when it drops below 0
        //graph.bullet = "round";
        //graph.bulletSize = 8;
        //graph.bulletBorderColor = "#FFFFFF";
        //graph.bulletBorderAlpha = 1;
        //graph.bulletBorderThickness = 2;
        graph.lineThickness = 1;
        graph.valueField = "value";
        graph.balloonText = "[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>";
        chart.addGraph(graph);

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorAlpha = 0;
        chartCursor.cursorPosition = "mouse";
        chartCursor.categoryBalloonDateFormat = "YYYY";
        chart.addChartCursor(chartCursor);

        // SCROLLBAR
        var chartScrollbar = new AmCharts.ChartScrollbar();
        chart.addChartScrollbar(chartScrollbar);

        chart.creditsPosition = "bottom-right";

        // WRITE
        chart.write("chartdiv");
    });

    // this method is called when chart is first inited as we listen for "dataUpdated" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
        //chart.zoomToDates(new Date(1972, 0), new Date(1984, 0));
    }
</script>            
            <?php
    }
    
    function content() {
?>
<div class="widget">
    <h1>Money Stats</h1>
    <div id="chartdiv" style="width: 100%; height: 500px;"></div><hr>
</div>
<?php
    }
}

new customPage();