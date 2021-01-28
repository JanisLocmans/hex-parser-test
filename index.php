<?php

require 'vendor/autoload.php';


$httpClient = new App\HttpRequest();


$params = [];

if(isset($_GET["key"])) {
    $params['key'] = $_GET["key"];
}

if(isset($_GET["offset"])) {
    $params['offset'] = $_GET["offset"];
}

$result = $httpClient->getRequest('https://mapon.com/integration', $params);

if( (int) $result['status'] = 200) {
    if(isset($result['response']->data[0])) {
        $service = new App\UnpackAVL($result['response']->data[0]);
    }
    $data = $service->retrieveData(); ?>

    <table style="width:400px">
        <thead>
            <tr>
                <th>Izveidošanas laiks</th>
                <th>IMEI</th>
                <th>GPS</th>
                <th>IO DATI</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data['avl_data'])) : ?>
                <?php foreach ($data['avl_data'] as $key => $avl_node) :  ?>


                        <tr>
                            <td> <?php echo date('Y-m-d H:i:s', $avl_node['timestamp']/1000); ?>
                            <td> <?php echo $key + 1 ?></td>
                            <td> <?php echo $avl_node['longitude']/10000000 ?>, <?php echo $avl_node['latitude']/10000000 ?></td>
                            <td>
                                <?php if(isset($avl_node['n_values'])) : ?>
                                    <?php foreach ($avl_node['n_values'] as $key => $n_value) :  ?>
                                        <?php echo 'IO - ' . $n_value['id'] . ' : ' . $n_value['value'] ?> <br>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </td>
                        </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>



<?php
} else {
    echo $result['response'];
}
?>

