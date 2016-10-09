<?php

include_once './connect.php';

$sql       = "select * from calendar";
$statement = $link->prepare($sql);
$statement->execute();
$result = $statement->fetchALL(PDO::FETCH_ASSOC);

foreach ( $result as $key => $value )
{
    $data[] = [
        'id'     => $value['id'],
        'title'  => $value['title'],
        'start'  => date('Y-m-d H:i', $value['starttime']),
        'end'    => date('Y-m-d H:i', $value['endtime']),
        'allDay' => $value['allday'] ? true : false,
        'color'  => $value['color']
    ];
}

echo json_encode($data);
