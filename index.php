<?php
//$site_id = '5a7f1b31d2e04c0001f454a3';
//$ch = curl_init('https://api.webflow.com/info');
//$ch = curl_init('https://api.webflow.com/sites/5a7f1b31d2e04c0001f454a3/collections');
//$ch = curl_init('https://api.webflow.com/collections/5a7f1b73d2e04c0001f4553b/items?limit=1');
//sites/:site_id/collections


$token = 'b12aeb9fad5cbaeab3fc3d60319f0d1ff41fc17e72517f78a65ac4080ab15e57';

function authorization($token)
{
    $ch = curl_init('https://api.webflow.com/info');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'accept-version: 1.0.0')
    );
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    return json_decode($html);
}

function getCollection($token)
{
    $ch = curl_init('https://api.webflow.com/collections/5a7f1b73d2e04c0001f4553b');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'accept-version: 1.0.0')
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    return json_decode($html);
}

function getItems($token)
{
    $ch = curl_init('https://api.webflow.com/collections/5a7f1b73d2e04c0001f4553b/items?limit=2');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'accept-version: 1.0.0')
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    return json_decode($html);
}

//authorization($token);

$collection = getCollection($token);
$collection_name = $collection->name;

foreach ($collection as $items) {

    if (is_array($items)) {
        $valid_name_array = inspector($items);
    }
}

function inspector($items)
{
    $array = [];
    foreach ($items as $item) {

        if ($item->type === 'ImageRef' || $item->type === 'PlainText' || $item->type === 'RichText') {
            $array[] = strtolower($item->name);
        }
    }
    return $array;
}

/*
echo '<pre>';
print_r( $valid_name_array );
echo '</pre><hr>';
*/

$items = getItems($token);

/*
echo '<pre>';
print_r( $items );
echo '</pre><hr>';
*/

$items_array = $items->items;

$fields_array = [];
$title_array = [];
//$result['collection_name'] = $collection_name;
$title_array = array('Collections', 'Item_id', 'Item_name');

foreach ($items_array as $item) {
    $field = [];
    $field[] = $collection_name;
    $field[] = $item->_id;
    $field[] = $item->name;


    foreach ($valid_name_array as $name) {
        $title_array[] = 'Field';

        if($name === 'igm') {
            $igm = $item->igm;
            $field[] = $igm->fileId;
            $field[] = $igm->url;
        }
        else {
            $field[] = $item->$name;
        }
    }
    //$fields_array[$item->name] = $field;
    $result[] = $field;
}

$result[] = $title_array;
$result = array_reverse($result);


//$result['items'] = $fields_array;
echo '<pre>';
print_r($result);
echo '</pre><hr>';
return $result;

//$result = json_encode($result);






