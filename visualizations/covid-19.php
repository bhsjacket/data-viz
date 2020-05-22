<?php
/**
 * views=
 * berkeley - total berkeley cases + deaths
 * alameda - total alameda county cases + deaths
 * new_cases - total berkeley cases + new daily cases
 * alameda_bar - new daily alameda county cases + deaths
 * berkeley_testing - berkeley weekly tests and positive results
 * bay_area - all cases and deaths in the bay area, by county, adjusted for population, with income line
 * 
 * color= hex color code w/ #, 6 characters
 * 
 * hide_source=hidden
 * 
 * hide_logo=hidden
 * 
 * max=numer - only works for berkeley_testing, number < 100
 */

$view = $_GET['view'];

date_default_timezone_set('America/Los_Angeles');
if(!empty($_GET['color'])) {
    $color = '#' . $_GET['color'];
} else {
    $color = '#800000';
}

if(empty($view) || !in_array($view, array('berkeley', 'alameda', 'new_cases', 'alameda_bar', 'berkeley_testing', 'bay_area'))) {
    echo 'Invalid or undefined "view" parameter';
} else {
?>

<head>
    <title>Berkeley High Jacket COVID-19 Tracker Embed</title>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans&family=PT+Serif&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<style>
.chart-wrapper {
    max-width: 100%;
    height: 300px;
}

.credits  {
    width: 100%;
    display: flex;
    transition: opacity 0.2s;
    margin-top: 15px;
    align-items: center;
}

.data {
    padding-left: 10px;
    font-family: 'PT Serif', serif;
}

.data a {
    text-decoration: none;
    color: black;
    box-shadow: inset 0px -2px 0px 0px <?php echo $color; ?>;
    transition: all 0.2s;
}

.data a:hover {
    box-shadow: inset 0 -2px 0 0 <?php echo $color; ?>, inset 0 -1.3em 0 0 <?php echo $color; ?>40;
    transition: all .2s;
}

.logo {
    padding-right: 10px;
    text-align: right;
}

.logo img {
    opacity: 1;
    transition: opacity 0.2s;
    max-width: 200px;
}

.logo img:hover {
    filter: invert(8%) sepia(80%) saturate(5879%) hue-rotate(0deg) brightness(88%) contrast(110%)
}

.credits > div {
    width: 50%;
}
</style>

<main class="coronavirus-tracker">
    <div class="chart-wrapper">
        <canvas id="chart"></canvas>
    </div>
    <div class="credits">
        <div class="data">
            <?php if(empty($_GET['source'])) { ?>
            <?php if($view == 'alameda' || $view == 'alameda_bar') { ?>
            <span><a href="https://data.acgov.org/" target="_blank">AC</a></span>
            <?php } ?>
            <?php if($view == 'berkeley' || $view == 'new_cases' || $view == 'berkeley_testing') { ?>
            <span><a href="https://data.cityofberkeley.info/" target="_blank">CoB</a></span>
            <?php } ?>
            <?php if($view == 'bay_area') { ?>
            <span><a href="https://github.com/nytimes/covid-19-data" target="_blank">NYT</a></span>
            <?php } ?>
            <?php } elseif(!empty($_GET['embed'])) { ?>
            <span><i style="font-size:14px;padding-right:5px" class="fas fa-code"></i><a style="cursor:pointer" class="embed-link" target="_blank">Embed</a></span>
            <script>
                $('.embed-link').click(function(){
                    prompt("Note: This embed will not be available forever.", '<?php echo '<iframe style="border:none;width:100%;height:360px;" src="https://jeromepaulos.com/bhsjacket/coronavirus/covid-19.php?view=' . $view . '"></iframe>' ?>');
                });
            </script>
            <?php } ?>
        </div>
        <?php if(empty($_GET['hide_logo'])) { ?>
        <div class="logo">
            <a href="https://berkeleyhighjacket.com/?utm_source=embed&utm_medium=cpc&utm_campaign=coronavirus_data_embed" target="_blank"><img src="https://berkeleyhighjacket.com/wp-content/themes/crimson/images/masthead-dark.svg"></a>
        </div>
        <?php } ?>
    </div>
</main>

<?php // alameda & alameda_bar
if($view == 'alameda' || $view == 'alameda_bar') {

    $ac_data = json_decode(file_get_contents('https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=alameda'), 'true');

    foreach($ac_data as $ac_stat) {
        $ac_cases[] = $ac_stat['attributes']['AC_CumulCases'];
        $ac_deaths[] = $ac_stat['attributes']['AC_CumulDeaths'];
        $ac_new_cases[] = $ac_stat['attributes']['AC_Cases'];
        $ac_new_deaths[] = $ac_stat['attributes']['AC_Deaths'];
        $labels[] = date('M j', ($ac_stat['attributes']['Date'] / 1000));
    };

    $labels = "'" . implode("','", $labels) . "'";
    $ac_cases = implode(",", $ac_cases);
    $ac_deaths = implode(",", $ac_deaths);
    $ac_new_cases = implode(",", $ac_new_cases);
    $ac_new_deaths = implode(",", $ac_new_deaths);
}
?>

<?php // berkeley & new_cases
if($view == 'berkeley' || $view == 'new_cases') {

    $data = json_decode(file_get_contents('https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=berkeley'), 'true');
    $death_data = json_decode(file_get_contents('https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=berkeley_deaths'), 'true');

    foreach($data as $stat) {
        $labels[] = date('M j', strtotime($stat['date']));
        $berkeley_cases[] = $stat['bklhj_cumulcases'];
        $berkeley_new_cases[] = $stat['bklhj_newcases'];
    }

    foreach($death_data as $death_stat) {
        $berkeley_deaths[] = $death_stat['attributes']['BkLHJ_CumulDeaths'];
    }

    $berkeley_cases_adj = array_map(function($bc, $bnc){
        return $bc - $bnc;
    }, $berkeley_cases, $berkeley_new_cases);

    $berkeley_cases = implode(",", $berkeley_cases);
    $berkeley_deaths = implode(",", $berkeley_deaths);

    $labels = "'" . implode("','", $labels) . "'";
    $berkeley_cases_adj = implode(",", $berkeley_cases_adj);
    $berkeley_new_cases = implode(",", $berkeley_new_cases);
}
?>

<?php // berkeley_testing
if($view == 'berkeley_testing') {

    $data = json_decode(file_get_contents('http://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=berkeley_testing'), 'true');

    foreach($data as $stat) {
        $labels[] = DateTime::createFromFormat('F j, Y', html_entity_decode(str_replace("&nbsp;", " ", htmlentities($stat['weekstartdate'], null, 'utf-8'))))->format('M j') . ' to ' . DateTime::createFromFormat('F j, Y', html_entity_decode(str_replace("&nbsp;", " ", htmlentities($stat['weekenddate'], null, 'utf-8'))))->format('M j');
        $tests[] = $stat['totaltests'];
        $positive[] = $stat['positivetests'];
        $percent[] = ($stat['percentpositive'] * 100);
    }

    $labels = "'" . implode("','", $labels) . "'";
    $tests = implode(",", $tests);
    $positive = implode(",", $positive);
    $percent = implode(",", $percent);
}
?>

<?php // bay_area
if($view == 'bay_area') {

    $data = file_get_contents('http://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=bay_area');
    $data = json_decode($data, true);

    extract($data);

    foreach($data as $county) {
        $labels[] = $county['locations'][0]['county'];
    }

    foreach($data as $county) {
        $cases[] = ($county['latest']['confirmed'] / $county[0]['population']);
        $deaths[] = ($county['latest']['deaths'] / $county[0]['population']);
        $income[] = ($county[1]['income'] / 100000000);
    }

    echo "<script>console.log('used cached data');</script>";

    $labels = "'" . implode("','", $labels) . "'";
    $deaths = implode(",", $deaths);
    $cases = implode(",", $cases);
    $income = implode(",", $income);
}
?>

<?php /** JAVASCRIPT CHARTS */ ?>

<?php if($view == 'alameda') { ?>
<script>
    $(document).ready(function(){
        Chart.defaults.global.defaultFontFamily = "'PT Sans', serif";
        Chart.defaults.global.animation.duration = 0;
        var chart_id = $('#chart');
        var chart = new Chart(chart_id, {
            type: 'line',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [{
                    data: [<?php echo $ac_cases; ?>],
                    label: 'Alameda County Cases',
                    borderColor: '<?php echo $color; ?>',
                    fill: 'none',
                }, {
                    data: [<?php echo $ac_deaths; ?>],
                    label: 'Alameda County Deaths',
                    borderColor: '#000000',
                    fill: 'none',
                }]
            },
            options: {
                elements: {
                    point:{
                        radius: 0,
                    }
                },
                legend: {

                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    caretSize: 0,
                    backgroundColor: '#000000',
                    cornerRadius: 0,
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
<?php } ?>

<?php if($view == 'berkeley') { ?>
<script>
    $(document).ready(function(){
        Chart.defaults.global.defaultFontFamily = "'PT Sans', serif";
        Chart.defaults.global.animation.duration = 0;
        var chart_id = $('#chart');
        var chart = new Chart(chart_id, {
            type: 'line',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [{
                    data: [<?php echo $berkeley_cases; ?>],
                    label: 'Berkeley Cases',
                    borderColor: '<?php echo $color; ?>',
                    fill: 'none',
                }, {
                    data: [<?php echo $berkeley_deaths; ?>],
                    label: 'Berkeley Deaths',
                    borderColor: '#000000',
                    fill: 'none',
                }]
            },
            options: {
                elements: {
                    point:{
                        radius: 0,
                    }
                },
                legend: {

                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    caretSize: 0,
                    backgroundColor: '#000000',
                    cornerRadius: 0,
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
<?php } ?>

<?php if($view == 'new_cases') { ?>
<script>
    $(document).ready(function(){
        Chart.defaults.global.defaultFontFamily = "'PT Sans', serif";
        Chart.defaults.global.animation.duration = 0;
        var chart_id = $('#chart');
        var chart = new Chart(chart_id, {
            type: 'bar',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [{
                    data: [<?php echo $berkeley_new_cases; ?>],
                    label: 'New Berkeley Cases',
                    backgroundColor: '<?php echo $color; ?>',
                    fill: 'none',
                    order: 2,
                }, {
                    data: [<?php echo $berkeley_cases_adj; ?>],
                    label: 'Total Berkeley Cases',
                    backgroundColor: '#808080',
                    fill: 'none',
                }]
            },
            options: {
                elements: {
                    point:{
                        radius: 0,
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true,
                    }]
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    caretSize: 0,
                    backgroundColor: '#000000',
                    cornerRadius: 0,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            if(data.datasets[tooltipItem.datasetIndex].label == 'New Berkeley Cases') {
                                var label = data.datasets[tooltipItem.datasetIndex].label + ': ' + data['datasets'][0]['data'][tooltipItem['index']];
                            } else {
                                var label = data.datasets[tooltipItem.datasetIndex].label + ': ' + (data['datasets'][1]['data'][tooltipItem['index']] + data['datasets'][0]['data'][tooltipItem['index']]);
                            }
                            return label;
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
<?php } ?>

<?php if($view == 'alameda_bar') { ?>
<script>
    $(document).ready(function(){
        Chart.defaults.global.defaultFontFamily = "'PT Sans', serif";
        Chart.defaults.global.animation.duration = 0;
        var chart_id = $('#chart');
        var chart = new Chart(chart_id, {
            type: 'bar',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [{
                    data: [<?php echo $ac_new_deaths; ?>],
                    label: 'New Alameda County Deaths',
                    backgroundColor: '<?php echo $color; ?>',
                    fill: 'none',
                    order: 2,
                }, {
                    data: [<?php echo $ac_new_cases; ?>],
                    label: 'New Alameda County Cases',
                    backgroundColor: '#808080',
                    fill: 'none',
                }]
            },
            options: {
                elements: {
                    point:{
                        radius: 0,
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            precision: 0,
                        },
                    }]
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    caretSize: 0,
                    backgroundColor: '#000000',
                    cornerRadius: 0,
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
<?php } ?>

<?php if($view == 'berkeley_testing') { ?>
<script>
    $(document).ready(function(){
        Chart.defaults.global.defaultFontFamily = "'PT Sans', serif";
        Chart.defaults.global.animation.duration = 0;
        Chart.defaults.scale.gridLines.display = false;
        var chart_id = $('#chart');
        var chart = new Chart(chart_id, {
            type: 'line',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [{
                    data: [<?php echo $tests; ?>],
                    label: 'People Tested in Berkeley',
                    borderColor: '#808080',
                    fill: 'none',
                    yAxisID: 'tests',
                    order: 20,
                },{
                    data: [<?php echo $positive; ?>],
                    label: 'Positive Tests',
                    borderColor: '#000000',
                    fill: 'none',
                    yAxisID: 'tests',
                    order: 10
                },{
                    data: [<?php echo $percent; ?>],
                    label: 'Percent Positive',
                    borderColor: '<?php echo $color; ?>',
                    fill: 'none',
                    borderDash: [5, 3],
                    yAxisID: 'percent'
                }]
            },
            options: {
                elements: {
                    point:{
                        radius: 0,
                    }
                },
                scales: {
                    yAxes: [{
                        id: 'tests',
                        type: 'linear',
                        position: 'left',
                        ticks: {
                            precision: 0,
                        },
                    },{
                        id: 'percent',
                        type: 'linear',
                        position: 'right',
                        ticks: {
                            min: 0,
                            max: <?php if(!empty($_GET['max'])){echo $_GET['max'];}else{echo 'this.max';}; ?>,
                            callback: function(value){
                                return value + '%';
                            },
                        }
                    }]
                },
                tooltips: {
                    mode: 'index',
                    position: 'nearest',
                    intersect: false,
                    caretSize: 0,
                    backgroundColor: '#000000',
                    cornerRadius: 0,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            if(data.datasets[tooltipItem.datasetIndex].label == 'Percent Positive') {
                                return data['datasets'][2]['data'][tooltipItem['index']] + '% Positive';
                            } else if(data.datasets[tooltipItem.datasetIndex].label == 'People Tested in Berkeley') {
                                return data.datasets[tooltipItem.datasetIndex].label + ': ' + data['datasets'][0]['data'][tooltipItem['index']];
                            } else {
                                return data.datasets[tooltipItem.datasetIndex].label + ': ' + data['datasets'][1]['data'][tooltipItem['index']];
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
<?php } ?>

<?php if($view == 'bay_area') { ?>
<script>
    $(document).ready(function(){
        Chart.defaults.global.defaultFontFamily = "'PT Sans', serif";
        Chart.defaults.global.animation.duration = 0;
        var chart_id = $('#chart');
        var chart = new Chart(chart_id, {
            type: 'bar',
            data: {
                labels: [<?php echo $labels; ?>],
                datasets: [/* {
                    data: [<?php echo $income; ?>],
                    label: 'Average Income',
                    borderColor: '#808080',
                    fill: 'none',
                    type: 'line'
                }, */{
                    data: [<?php echo $cases; ?>],
                    label: 'Cases per capita',
                    backgroundColor: '#808080',
                    fill: 'none',
                }, {
                    data: [<?php echo $deaths; ?>],
                    label: 'Deaths per capita',
                    backgroundColor: '<?php echo $color; ?>',
                    fill: 'none',
                }]
            },
            options: {
                ticks: {
                    precision: 0,
                },
                elements: {
                    point:{
                        radius: 0,
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    caretSize: 0,
                    backgroundColor: '#000000',
                    cornerRadius: 0,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            if(data.datasets[tooltipItem.datasetIndex].label == 'Cases per capita'){
                                return data.datasets[tooltipItem.datasetIndex].label + ': ~' + Math.round(chart.data['datasets'][0]['data'][tooltipItem['index']] * 10000)/10000;
                            } else {
                                return data.datasets[tooltipItem.datasetIndex].label + ': ~' + Math.round(chart.data['datasets'][1]['data'][tooltipItem['index']] * 1000000)/1000000;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
<?php }} ?>