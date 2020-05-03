<?php
if(!empty($_GET['color'])) {
    $color = '#' . $_GET['color'];
} else {
    $color = '#800000';
}

if(empty($_GET['view']) || !in_array($_GET['view'], array('berkeley', 'alameda'))) {
    echo 'Invalid or undefined "view" parameter';
} else {
?>

<head>
    <title>Berkeley High Jacket COVID-19 Tracker Embed</title>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans&family=PT+Serif&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
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
            <?php if($_GET['view'] == 'alameda' && empty($_GET['hide_source'])) { ?>
            <span><a href="https://data.acgov.org/">Source</a></span>
            <?php } ?>
            <?php if($_GET['view'] == 'berkeley' && empty($_GET['hide_source'])) { ?>
            <span><a href="https://data.cityofberkeley.info/">Source</a></span>
            <?php } ?>
        </div>
        <div class="logo">
            <a href="https://berkeleyhighjacket.com/?utm_source=embed&utm_medium=cpc&utm_campaign=coronavirus_data_embed"><img src="https://berkeleyhighjacket.com/wp-content/themes/crimson/images/masthead-dark.svg"></a>
        </div>
    </div>
</main>

<?php // Alameda County Data
if($_GET['view'] == 'alameda') {
    $ac_json = file_get_contents('https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_dates/FeatureServer/0/query?where=1%3D1&outFields=Date,AC_CumulDeaths,AC_CumulCases&returnGeometry=false&orderByFields=Date%20ASC&outSR=4326&f=json');
    $ac_data = json_decode($ac_json, 'true')['features'];

    foreach($ac_data as $ac_stat) {
        $ac_cases[] = $ac_stat['attributes']['AC_CumulCases'];
        $ac_deaths[] = $ac_stat['attributes']['AC_CumulDeaths'];
        $labels[] = date('M j', ($ac_stat['attributes']['Date'] / 1000));
    };

    $labels = "'" . implode("','", $labels) . "'";
    $ac_cases = implode(",", $ac_cases);
    $ac_deaths = implode(",", $ac_deaths);
}
?>

<?php // Berkeley Data
if($_GET['view'] == 'berkeley') {
    $json = file_get_contents('https://data.cityofberkeley.info/resource/xn6j-b766.json');
    $data = json_decode($json, 'true');

    $json_deaths = file_get_contents('https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_dates/FeatureServer/0/query?where=1%3D1&outFields=BkLHJ_CumulDeaths,Date&returnGeometry=false&orderByFields=Date%20ASC&outSR=4326&f=json');
    $death_data = json_decode($json_deaths, 'true')['features'];

    foreach($data as $stat) {
        $labels[] = date('M j', strtotime($stat['date']));
        $berkeley_cases[] = $stat['bklhj_cumulcases'];
    }

    foreach($death_data as $death_stat) {
        $berkeley_deaths[] = $death_stat['attributes']['BkLHJ_CumulDeaths'];
    }

    $berkeley_deaths = implode(",", $berkeley_deaths);

    $labels = "'" . implode("','", $labels) . "'";
    $berkeley_cases = implode(",", $berkeley_cases);
}
?>

<?php if($_GET['view'] == 'alameda') { ?>
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
                    fill: 'origin',
                }, {
                    data: [<?php echo $ac_deaths; ?>],
                    label: 'Alameda County Deaths',
                    borderColor: '#000000',
                    fill: 'origin',
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

<?php if($_GET['view'] == 'berkeley') { ?>
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
                    fill: 'origin',
                }, {
                    data: [<?php echo $berkeley_deaths; ?>],
                    label: 'Berkeley Deaths',
                    borderColor: '#000000',
                    fill: 'origin',
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
<?php }} ?>