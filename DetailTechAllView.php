<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="Dashboard">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Business Monitor</title>

        <!-- Bootstrap core CSS -->
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <!--external css-->
        <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <!-- Custom styles for this template -->
        <link href="assets/css/style.css" rel="stylesheet">
        <link href="assets/css/style-responsive.css" rel="stylesheet">
        <link href="assets/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link id="bsdp-css" href="assets/css/bootstrap-datepicker3.min.css" rel="stylesheet">
        <link href="assets/css/manutan-style.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="assets/img/man.png"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <?php
        include_once('_commonFunctions.php');
        $bdd = connexion_base();

        $dateStart = (is_null($_GET['start'])) ? date('Y-m-d', strtotime(date('ymd') . ' - 1 months')) : $_GET['start'];
        $dateEnd = (is_null($_GET['end'])) ? date('Y-m-d') : $_GET['end'];

        $statdateWeek = getStatWeek($bdd, '2000-01-01', $dateEnd, ($_GET['BS'] != "Critical") ? $_GET['BS'] : str_replace('|', "'',''", $CRITICAL));
        $statdate = getStatDate($bdd, '2000-01-01', $dateEnd, ($_GET['BS'] != "Critical") ? $_GET['BS'] : str_replace('|', "'',''", $CRITICAL));
        $flows = getFlows($bdd, '2000-01-01', $dateEnd);
        $BONull = array_shift($flows);

//        var_dump($flows);

        $dateOnly = array();
        for ($k = 0; $k < sizeof($statdateWeek); $k++) {
            array_push($dateOnly, $statdateWeek[$k][Date]);
        }

        $staterror = array();
        for ($k = 0; $k < sizeof($statdateWeek); $k++) {
            array_push($staterror, array($statdateWeek[$k][Date], $statdateWeek[$k][Error]));
        }

        $statwarn = array();
        for ($k = 0; $k < sizeof($statdateWeek); $k++) {
            array_push($statwarn, array($statdateWeek[$k][Date], $statdateWeek[$k][Warning]));
        }

        $stattotal = array();
        for ($k = 0; $k < sizeof($statdateWeek); $k++) {
            array_push($stattotal, array($statdateWeek[$k][Date], $statdateWeek[$k][Total]));
        }

        $statsuccess = array();
        for ($k = 0; $k < sizeof($statdateWeek); $k++) {
            array_push($statsuccess, array($statdateWeek[$k][Date], $statdateWeek[$k][Success]));
        }
        $statduration = array();
        for ($k = 0; $k < sizeof($statdate); $k++) {
            array_push($statduration, array($statdate[$k][Date], $statdate[$k][Duration]));
        }

        $allflows = array();
        for ($k = 0; $k < sizeof($flows); $k++) {
            array_push($allflows, array($flows[$k][BusinessService], $flows[$k][Total]));
        }
//        var_dump($allflows);
//        $lengthWeek = sizeof($statdateWeek);
//        $successWeek = $statdateWeek[$lengthWeek - 1][Success];
//        $successLastWeek = $statdateWeek[$lengthWeek - 2][Success];
//        $successGapWeek = $successWeek - $successLastWeek;
//        $successGapWeekPerc = round($successGapWeek * 100 / $successLastWeek, 0, PHP_ROUND_HALF_UP);
//
//        if ($successGapWeekPerc > 0) {
//            $trendArrow = '<img src="assets/img/arrow-304-512.png" alt="" height="120"/>';
//        } else {
//            
//        }
//
//        if ($successGapWeekPerc == 0) {
//            $trendArrow = '<img src="assets/img/arrow-21-512.png" alt="" height="120"/>';
//        } else if ($successGapWeekPerc > 0) {
//            $trendArrow = '<img src="assets/img/arrow-304-512.png" alt="" height="120"/>';
//        } else {
//            $trendArrow = '<img src="assets/img/arrow-326-512.png" alt="" height="120"/>';
//        }
        ?>

    </head>
    <body>
        <section id="container" >
            <?php
            include_once('header.php');
            ?>
            <!-- **********************************************************************************************************************************************************
            MAIN CONTENT
            *********************************************************************************************************************************************************** -->
            <!--main content start-->

            <section id="main-content">
                <section class="bm-wrapper">
                    <div class="row mt">
                        <div class="col-md-12 space">
                            <div class="content-panel">
                                <div>
                                    <input class="btn btn-primary btn-sm bm-position" type="button" value="Return" onclick="document.location.href = 'main.php';"/>
                                    <div id='switchDiv' class="switchRight">
                                        <span class="align rangePadding">Switch into %</span>
                                        <input id="switchToPercentage" type="checkbox" data-toggle="switch" />
                                    </div>
                                </div>
                                <div id="splinetime" style="width: 100%; height: 350px; margin: 0 auto"></div>
                            </div><!-- /content-panel -->
                            <div class="row mt ">
                                <!-- SERVER STATUS PANELS -->
                                <!--                                <div class="col-md-4 col-sm-4 mb">
                                                                    <div class="white-panel bm-pn">
                                                                        <div class="white-header">
                                                                            <h5>Trend</h5>
                                                                        </div>
                                                                        <div class="row vcenter">
                                                                            <div class='col-md-6 bm-trendnum'></div>
                                                                            <div class='col-md-6'></div>
                                                                        </div>
                                                                    </div><! --/grey-panel -->
                                <!--</div>--><!-- /col-md-4-->
                                <div class="col-md-12  mb">
                                    <div class="content-panel">
                                        <div id="columnchart" style="width: 100%; height: 350px; margin: 0 auto"></div>
                                    </div>
                                </div><!-- /col-md-4 -->
                            </div><!-- /row -->
                        </div><!-- /col-md-12 -->
                    </div><!-- /row -->
                </section><! --/wrapper -->

<!--                <section class="piechart-bm-wrapper">
                    <div class="row mt">
                        <div class="col-md-12">
                            <div class="piechart-bm-content-panel">
                                <div id="donutchart" style="width: 100%; height: 345px; margin: 0 auto"></div>
                            </div> /content-panel 
                        </div> /col-md-12 

                    </div> /row 
                    <div class="footer">© 2016 Manutan . All rights reserved</div>
                </section><! --/wrapper -->
                <div class="footer">© 2016 Manutan . All rights reserved</div>
            </section>

            <!-- /MAIN CONTENT -->
            <!--main content end-->

        </section>

        <!-- js placed at the end of the document so the pages load faster -->
        <script src="assets/js/jquery.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
        <script src="assets/js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="assets/js/bootstrap-switch.js"></script>
        <script src="assets/js/jquery.tagsinput.js"></script>
        <script src="assets/js/form-component.js"></script>  

        <!--calendar datepicker-->
        <script src="assets/js/bootstrap-datepicker.js"></script>
        <script src="assets/js/calendar/calendar.js"></script>

        <!--datatables bootstrap-->
        <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/dataTables.bootstrap.min.js"></script>

        <!--<script src="assets/js/common-scripts.js"></script>-->

        <!--highcharts js-->
        <script src="assets/js/highchart/highstock.js"></script>
        <script src="assets/js/highchart/highcharts.js" type="text/javascript"></script>
        <script src="assets/js/highchart/exporting.js" type="text/javascript"></script>

        <script type="text/javascript" src="js/bootstrap-dropdown.js"></script>

        <script>
                                        $(document).ready(function() {
                                            function round(number) {
                                                return Number(Math.round(number + 'e' + 2) + 'e-' + 2);
                                            }

                                            //data field php to js
                                            var statError = <?php echo json_encode($staterror) ?>;
                                            var statWarning = <?php echo json_encode($statwarn) ?>;
                                            var statTotal = <?php echo json_encode($stattotal) ?>;
                                            var statSuccess = <?php echo json_encode($statsuccess) ?>;
                                            var statDuration = <?php echo json_encode($statduration) ?>;
                                            var flows = <?php echo json_encode($allflows) ?>;
                                            var arrayDate = [];
                                            var arrayDateDuration = [];
                                            var arrayCntError = [];
                                            for (var i = 0; i < statError.length; i++) {
                                                arrayCntError.push(statError[i][1]);
                                            }

                                            flows = flows.sort(function(a, b)
                                            {
                                                return b[1] - a[1];
                                            });
                                            var arrayCntWarning = [];
                                            for (var i = 0; i < statWarning.length; i++) {
                                                arrayCntWarning.push(statWarning[i][1]);
                                            }

                                            var arrayCntTotal = [];
                                            for (var i = 0; i < statTotal.length; i++) {
                                                arrayDate.push(statTotal[i][0].split("-"));
                                                arrayCntTotal.push(statTotal[i][1]);
                                            }

                                            var arrayCntSuccess = [];
                                            for (var i = 0; i < statSuccess.length; i++) {
                                                arrayCntSuccess.push(statSuccess[i][1]);
                                            }

                                            var arrayCntDuration = [];
                                            for (var i = 0; i < statDuration.length; i++) {
                                                arrayDateDuration.push(statDuration[i][0].split("-"));
                                                arrayCntDuration.push(statDuration[i][1]);
                                            }

                                            var totalMsg = 0;
                                            $.each(arrayCntTotal, function() {
                                                totalMsg += parseInt(this);
                                            });
                                            var successPercentage = [];
                                            var cntTotal = 0;
                                            for (var i = 0; i < arrayCntSuccess.length; i++) {
                                                if (arrayCntTotal[i] == 0) {
                                                    cntTotal = 1;
                                                } else {
                                                    cntTotal = arrayCntTotal[i];
                                                }
                                                successPercentage.push(round(arrayCntSuccess[i] / cntTotal * 100));
                                            }

                                            var errorPercentage = [];
                                            cntTotal = 0;
                                            for (var i = 0; i < arrayCntError.length; i++) {
                                                if (arrayCntTotal[i] == 0) {
                                                    cntTotal = 1;
                                                } else {
                                                    cntTotal = arrayCntTotal[i];
                                                }
                                                errorPercentage.push(round(arrayCntError[i] / cntTotal * 100));
                                            }

                                            var warningPercentage = [];
                                            cntTotal = 0;
                                            for (var i = 0; i < arrayCntWarning.length; i++) {
                                                if (arrayCntTotal[i] == 0) {
                                                    cntTotal = 1;
                                                } else {
                                                    cntTotal = arrayCntTotal[i];
                                                }
                                                warningPercentage.push(round(arrayCntWarning[i] / cntTotal * 100));
                                            }

                                            //Options initialization for splinetime
                                            options = {
                                                tooltip: {
                                                    formatter: function() {
                                                        var s = Highcharts.dateFormat('%b %e, %Y', this.x);
                                                        $.each(this.points, function(i, point) {
                                                            s += '<br/><span style="color:' + point.series.color + '">\u25CF</span>' + point.series.name + ': ' + '<b>' + point.y + '</b>';
                                                        });
                                                        return s;
                                                    }
                                                },
                                                navigator: {
                                                    height: 25
                                                },
                                                rangeSelector: {
                                                    allButtonsEnabled: true,
                                                    buttons: [{
                                                            type: 'week',
                                                            count: 1,
                                                            text: 'Week',
                                                        }, {
                                                            type: 'month',
                                                            count: 1,
                                                            text: 'Month',
                                                        }, {
                                                            type: 'all',
                                                            text: 'All',
                                                        }],
                                                    buttonTheme: {
                                                        width: 60
                                                    },
                                                    selected: 2
                                                },
                                                legend: {
                                                    enabled: true
                                                },
                                                scrollbar: {
                                                    enabled: false
                                                },
                                                title: {text: "FO010"},
                                                xAxis: {
                                                    tickInterval: 7 * 24 * 36e5, // one week
                                                    labels: {
                                                        format: '{value:Week %W <br> %b \%y}'
                                                    }
                                                },
                                                yAxis: {
                                                    title: {
                                                        text: 'Message'
                                                    },
                                                    min: 0

                                                },
                                                plotOptions: {
                                                    series: {
                                                        marker: {
                                                            enabled: true,
                                                            symbol: 'circle',
                                                            radius: 5
                                                        }
                                                    }
                                                },
                                                series: [
                                                ]
                                            };
                                            //Options initialization for splinetime
                                            Highcharts.dateFormats = {
                                                W: function(timestamp) {
                                                    var date = new Date(timestamp),
                                                            day = date.getUTCDay() === 0 ? 7 : date.getUTCDay(),
                                                            dayNumber;
                                                    date.setDate(date.getUTCDate() + 4 - day);
                                                    dayNumber = Math.floor((date.getTime() - new Date(date.getUTCFullYear(), 0, 1, -6)) / 86400000);
                                                    return 1 + Math.floor(dayNumber / 7);
                                                }
                                            };
                                            //Error serie building into JSON
                                            var rowError = '';
                                            var rowWarning = '';
                                            var rowSuccess = '';
                                            var rowTotal = '';
                                            var rowErrorPerc = '';
                                            var rowWarningPerc = '';
                                            var rowSuccessPerc = '';
                                            var rowDuration = '';
                                            for (var k = 0; k < arrayDate.length - 1; k++) {
                                                rowError = rowError.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + arrayCntError[k] + '],');
                                                rowWarning = rowWarning.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + arrayCntWarning[k] + '],');
                                                rowSuccess = rowSuccess.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + arrayCntSuccess[k] + '],');
                                                rowTotal = rowTotal.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + arrayCntTotal[k] + '],');
                                                rowErrorPerc = rowErrorPerc.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + errorPercentage[k] + '],');
                                                rowWarningPerc = rowWarningPerc.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + warningPercentage[k] + '],');
                                                rowSuccessPerc = rowSuccessPerc.concat('[' + Date.UTC(arrayDate[k][0], arrayDate[k][1] - 1, arrayDate[k][2]) + ',' + successPercentage[k] + '],');
                                                rowDuration = rowDuration.concat('[' + Date.UTC(arrayDateDuration[k][0], arrayDateDuration[k][1] - 1, arrayDateDuration[k][2]) + ',' + arrayCntDuration[k] + '],');
                                            }
                                            rowError = rowError.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + arrayCntError[arrayDate.length - 1] + ']');
                                            rowWarning = rowWarning.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + arrayCntWarning[arrayDate.length - 1] + ']');
                                            rowSuccess = rowSuccess.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + arrayCntSuccess[arrayDate.length - 1] + ']');
                                            rowTotal = rowTotal.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + arrayCntTotal[arrayDate.length - 1] + ']');
                                            rowErrorPerc = rowErrorPerc.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + errorPercentage[arrayDate.length - 1] + ']');
                                            rowWarningPerc = rowWarningPerc.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + warningPercentage[arrayDate.length - 1] + ']');
                                            rowSuccessPerc = rowSuccessPerc.concat('[' + Date.UTC(arrayDate[arrayDate.length - 1][0], arrayDate[arrayDate.length - 1][1] - 1, arrayDate[arrayDate.length - 1][2]) + ',' + successPercentage[arrayDate.length - 1] + ']');
                                            rowDuration = rowDuration.concat('[' + Date.UTC(arrayDateDuration[arrayDateDuration.length - 1][0], arrayDateDuration[arrayDateDuration.length - 1][1] - 1, arrayDateDuration[arrayDateDuration.length - 1][2]) + ',' + arrayCntDuration[arrayDateDuration.length - 1] + ']');
                                            var lineError = '{"name":"Error","visible": false,"color": "#FA1A01","data":[' + rowError + ']}';
                                            var lineWarning = '{"name":"Warning","visible": false, "color": "#FEC60C","data":[' + rowWarning + ']}';
                                            var lineSuccess = '{"name":"Success","color": "#A9D86E","data":[' + rowSuccess + ']}';
                                            var lineTotal = '{"name":"Total","color": "#01B0F0","id": "primary","data":[' + rowTotal + ']}';
                                            var lineErrorPerc = '{"name":"Error (%)","true": false,"color": "#FA1A01","data":[' + rowErrorPerc + ']}';
                                            var lineWarningPerc = '{"name":"Warning (%)","visible": true,"color": "#FEC60C","data":[' + rowWarningPerc + ']}';
                                            var lineSuccessPerc = '{"name":"Success (%)","visible": true,"color": "#A9D86E","data":[' + rowSuccessPerc + ']}';
                                            var lineDuration = '{"name":"Duration","color": "#01B0F0","data":[' + rowDuration + ']}';
                                            serieError = JSON.parse(lineError);
                                            serieWarning = JSON.parse(lineWarning);
                                            serieSuccess = JSON.parse(lineSuccess);
                                            serieTotal = JSON.parse(lineTotal);
                                            serieErrorPerc = JSON.parse(lineErrorPerc);
                                            serieWarningPerc = JSON.parse(lineWarningPerc);
                                            serieSuccessPerc = JSON.parse(lineSuccessPerc);
                                            serieDuration = JSON.parse(lineDuration);
                                            options.series.push(serieError);
                                            options.series.push(serieWarning);
                                            options.series.push(serieSuccess);
                                            options.series.push(serieTotal);
                                            options.title.text = <?php echo json_encode($_GET['BS'] . " - " . $_GET['BO'] . " (AVG)"); ?>;
                                            $('#splinetime').highcharts('StockChart', options);
//                                          console.debug(successPercentage)
//                                          console.debug(statSuccess);
//                                            console.debug($('#splinetime > div:nth-child(1) > input:nth-child(1)').val());

                                            switchToNumber();
                                            function switchToPerc() {
                                                var chart = $('#splinetime').highcharts();
                                                for (var i = chart.series.length - 1; i > -1; i--) {
                                                    chart.series[i].remove(false);
                                                }
                                                chart.addSeries(serieErrorPerc, false);
                                                chart.addSeries(serieWarningPerc, false);
                                                chart.addSeries(serieSuccessPerc, false);
                                                chart.redraw();
                                            }
                                            function switchToNumber() {
                                                var chart = $('#splinetime').highcharts();
                                                for (var i = chart.series.length - 1; i > -1; i--) {
                                                    chart.series[i].remove(false);
                                                }
                                                try {
                                                    chart.addSeries(serieError, false);
                                                } finally {
                                                    chart.addSeries(serieWarning, false);
                                                    chart.addSeries(serieSuccess, false);
                                                    chart.addSeries(serieTotal, false);
                                                    chart.redraw();
                                                }
                                            }

                                            $("#switchToPercentage").change(function() {

                                                if ($(this).is(':checked')) {
                                                    switchToPerc();
                                                } else {
                                                    switchToNumber();
                                                }
                                            });
                                            if ($("div.switch > div").attr("class").contains("switch-on")) {
                                                switchToPerc();
                                            }


                                            optionsRepartion = {
                                                chart: {
                                                    type: 'column',
                                                    zoomType: 'y'
                                                },
                                                title: {
                                                    text: 'Distribution of all messages'
                                                },
                                                xAxis: {
                                                    type: 'category',
                                                    labels: {
                                                        rotation: -45,
                                                        style: {
                                                            fontSize: '13px',
                                                            fontFamily: 'Verdana, sans-serif'
                                                        }
                                                    }
                                                },
                                                yAxis: {
                                                    min: 0,
                                                    title: {
                                                        text: 'Message'
                                                    }
                                                },
                                                legend: {
                                                    enabled: false
                                                },
                                                tooltip: {
                                                    pointFormat: 'Total: <b>{point.y}</b>'
                                                },
                                                plotOptions: {
                                                    pie: {
                                                        allowPointSelect: true,
                                                        cursor: 'pointer',
                                                        size: '100%',
                                                        dataLabels: {
                                                            enabled: true,
                                                            format: '<b>{point.name}</b>: {point.percentage:.2f} %  ( {point.y} )',
                                                            style: {
                                                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                                            }
                                                        }
                                                    }
                                                },
                                                series: []
                                            };
                                            var datas = "";
                                            for (var k = 0; k < flows.length - 1; k++) {
                                                datas += '{"name":"' + flows[k][0] + '", "y":' + flows[k][1] + '},';
                                            }
                                            console.debug(datas.length);
                                            datas = datas.substring(datas.length - 1, 1);
                                            console.debug(datas);
                                            var totalPerFlow = '{"data":[{' + datas + ']}';

                                            serieTotalPerFlow = JSON.parse(totalPerFlow);
                                            optionsRepartion.series.push(serieTotalPerFlow);
                                            $('#columnchart').highcharts(optionsRepartion);
                                        });</script>

        <script src="assets/js/googleAnalytics.js" type="text/javascript"></script>
    </body>
</html>