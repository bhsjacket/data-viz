<?php
date_default_timezone_set('America/Los_Angeles');
header('Content-Type: application/json');
if(empty($_GET['data'])) {
    echo 'Empty data parameter.';
    die;
} else {
    $dataset = $_GET['data'];
}

// BERKELEY TESTING
// https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=berkeley_testing
if($dataset == 'berkeley_testing') {
    if(!in_array(date('m-d-Y-A') . '.json', scandir('cache/berkeley_testing/'))) {
        $data = file_get_contents('https://data.cityofberkeley.info/resource/nw6x-9edb.json');
        print_r($data);
        file_put_contents('cache/berkeley_testing/' . date('m-d-Y-A') . '.json', $data);
        die;
    } else {
        $data = file_get_contents('cache/berkeley_testing/' . date('m-d-Y-A') . '.json');
        print_r($data);
        die;
    }
}

// BERKELEY NEW & TOTAL CASES
// https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=berkeley
if($dataset == 'berkeley') {
    if(!in_array(date('m-d-Y-A') . '.json', scandir('cache/berkeley/'))) {
        $data = file_get_contents('https://data.cityofberkeley.info/resource/xn6j-b766.json');
        print_r($data);
        file_put_contents('cache/berkeley/' . date('m-d-Y-A') . '.json', $data);
        die;
    } else {
        $data = file_get_contents('cache/berkeley/' . date('m-d-Y-A') . '.json');
        print_r($data);
        die;
    }
}

// BERKELEY DEATHS
// https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=berkeley_deaths
if($dataset == 'berkeley_deaths') {
    if(!in_array(date('m-d-Y-A') . '.json', scandir('cache/new_cases/'))) {
        $data = file_get_contents('https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_dates/FeatureServer/0/query?where=1%3D1&outFields=BkLHJ_CumulDeaths,Date&returnGeometry=false&orderByFields=Date%20ASC&outSR=4326&f=json');
        $data = json_decode($data, 'true')['features'];
        $data = json_encode($data);
        print_r($data);
        file_put_contents('cache/new_cases/' . date('m-d-Y-A') . '.json', $data);
        die;
    } else {
        $data = file_get_contents('cache/new_cases/' . date('m-d-Y-A') . '.json');
        print_r($data);
        die;
    }
}

// ALAMEDA COUNTY
// https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=alameda
if($dataset == 'alameda') {
    if(!in_array(date('m-d-Y-A') . '.json', scandir('cache/alameda/'))) {
        $data = file_get_contents('https://services3.arcgis.com/1iDJcsklY3l3KIjE/arcgis/rest/services/AC_dates/FeatureServer/0/query?where=1%3D1&outFields=Date,AC_Cases,AC_CumulCases,AC_Deaths,AC_CumulDeaths&returnGeometry=false&orderByFields=Date%20ASC&outSR=4326&f=json');
        $data = json_decode($data, 'true')['features'];
        $data = json_encode($data);
        print_r($data);
        file_put_contents('cache/alameda/' . date('m-d-Y-A') . '.json', $data);
        die;
    } else {
        $data = file_get_contents('cache/alameda/' . date('m-d-Y-A') . '.json');
        print_r($data);
        die;
    }
}


// BAY AREA
// https://jeromepaulos.com/bhsjacket/coronavirus/data.php?data=bay_area
if($dataset == 'bay_area') {
    if(!in_array(date('m-d-Y-A') . '.json', scandir('cache/counties'))) {
        $counties = array('alameda', 'contra_costa', 'marin', 'napa', 'san_francisco', 'san_mateo', 'santa_clara', 'solano', 'sonoma');
        $alameda = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Alameda');
        $alameda = json_decode($alameda, 'true');
        $contra_costa = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Contra%20Costa');
        $contra_costa = json_decode($contra_costa, 'true');
        $marin = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Marin');
        $marin = json_decode($marin, 'true');
        $napa = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Napa');
        $napa = json_decode($napa, 'true');
        $san_francisco = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=San%20Francisco');
        $san_francisco = json_decode($san_francisco, 'true');
        $san_mateo = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=San%20Mateo');
        $san_mateo = json_decode($san_mateo, 'true');
        $santa_clara = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Santa%20Clara');
        $santa_clara = json_decode($santa_clara, 'true');
        $solano = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Solano');
        $solano = json_decode($solano, 'true');
        $sonoma = file_get_contents('https://coronavirus-tracker-api.herokuapp.com/v2/locations?source=nyt&country_code=US&province=California&county=Sonoma');
        $sonoma = json_decode($sonoma, 'true');

        $data = array_combine($counties, array($alameda, $contra_costa, $marin, $napa, $san_francisco, $san_mateo, $santa_clara, $solano, $sonoma));

        $data['alameda'][] = array('population' => '1671000');
        $data['contra_costa'][] = array('population' => '1154000');
        $data['marin'][] = array('population' => '258826');
        $data['napa'][] = array('population' => '137744');
        $data['san_francisco'][] = array('population' => '883305');
        $data['san_mateo'][] = array('population' => '727206');
        $data['santa_clara'][] = array('population' => '1928000');
        $data['solano'][] = array('population' => '447643');
        $data['sonoma'][] = array('population' => '494336');

        $data['alameda'][] = array('income' => '102125');
        $data['contra_costa'][] = array('income' => '101618');
        $data['marin'][] = array('income' => '126373');
        $data['napa'][] = array('income' => '79637');
        $data['san_francisco'][] = array('income' => '112376');
        $data['san_mateo'][] = array('income' => '124425');
        $data['santa_clara'][] = array('income' => '126606');
        $data['solano'][] = array('income' => '84395');
        $data['sonoma'][] = array('income' => '81395');

        $data = json_encode($data);
        print_r($data);

        file_put_contents('cache/counties/' . date('m-d-Y-A') . '.json', $data);
        die;

    } else {

        $data = file_get_contents('cache/counties/' . date('m-d-Y-A') . '.json');
        print_r($data);
        die;

    }
}