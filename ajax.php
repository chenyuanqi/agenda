<?php

include_once('connect.php'); //连接数据库

$action = $_GET['action'];
$isend  = '';

if ( $action == 'add' )
{ //增加
    $events = stripslashes(trim($_POST['event'])); //事件内容
    $events = addslashes(strip_tags($events)); //过滤HTML标签，并转义特殊字符

    $isallday = isset($_POST['isallday']) ? $_POST['isallday'] : ''; //是否是全天事件
    $isend    = isset($_POST['isend']) ? $_POST['isend'] : ""; //是否有结束时间

    $startdate = trim($_POST['startdate']); //开始日期
    $enddate   = trim($_POST['enddate']); //结束日期

    $s_time  = $_POST['s_hour'].':'.$_POST['s_minute'].':00'; //开始时间
    $e_time  = $_POST['e_hour'].':'.$_POST['e_minute'].':00'; //结束时间
    $endtime = 0;

    if ( $isallday == 1 && $isend == 1 )
    {
        $starttime = strtotime($startdate);
        $endtime   = strtotime($enddate);
    }
    elseif ( $isallday == 1 && $isend == "" )
    {
        $starttime = strtotime($startdate);
    }
    elseif ( $isallday == "" && $isend == 1 )
    {
        $starttime = strtotime($startdate.' '.$s_time);
        $endtime   = strtotime($enddate.' '.$e_time);
    }
    else
    {
        $starttime = strtotime($startdate.' '.$s_time);
    }

    $colors = [
        "#360",
        "#f30",
        "#06c"
    ];
    $key    = array_rand($colors);
    $color  = $colors[ $key ];

    $isallday = $isallday ? 1 : 0;

    $sql       = "INSERT INTO `calendar` (`title`,`starttime`,`endtime`,`allday`,`color`) VALUES (:events, :starttime, :endtime, :isallday, :color)";
    $statement = $link->prepare($sql);
    $statement->bindParam(":events", $events, PDO::PARAM_STR);
    $statement->bindParam(":starttime", $starttime, PDO::PARAM_INT);
    $statement->bindParam(":endtime", $endtime, PDO::PARAM_INT);
    $statement->bindParam(":isallday", $isallday, PDO::PARAM_INT);
    $statement->bindParam(":color", $color, PDO::PARAM_STR);
    $statement->execute();

    if ( $link->lastInsertId() > 0 )
    {
        echo '1';
    }
    else
    {
        echo '写入失败！';
    }
}
elseif ( $action == "edit" )
{ //编辑
    $isend = '';
    $id    = intval($_POST['id']);
    if ( $id == 0 )
    {
        echo '事件不存在！';
        exit;
    }
    $events = stripslashes(trim($_POST['event'])); //事件内容
    $events = addslashes(strip_tags($events)); //过滤HTML标签，并转义特殊字符

    $isallday = isset($_POST['isallday']) ? $_POST['isallday'] : ''; //是否是全天事件
    $isend    = isset($_POST['isend']) ? $_POST['isend'] : ""; //是否有结束时间

    $startdate = trim($_POST['startdate']); //开始日期
    $enddate   = trim($_POST['enddate']); //结束日期

    $s_time = $_POST['s_hour'].':'.$_POST['s_minute'].':00'; //开始时间
    $e_time = $_POST['e_hour'].':'.$_POST['e_minute'].':00'; //结束时间

    if ( $isallday == 1 && $isend == 1 )
    {
        $starttime = strtotime($startdate);
        $endtime   = strtotime($enddate);
    }
    elseif ( $isallday == 1 && $isend == "" )
    {
        $starttime = strtotime($startdate);
        $endtime   = 0;
    }
    elseif ( $isallday == "" && $isend == 1 )
    {
        $starttime = strtotime($startdate.' '.$s_time);
        $endtime   = strtotime($enddate.' '.$e_time);
    }
    else
    {
        $starttime = strtotime($startdate.' '.$s_time);
        $endtime   = 0;
    }

    $isallday  = $isallday ? 1 : 0;
    $sql       = "UPDATE `calendar` SET `title`=:events, `starttime`=:starttime, `endtime`=:endtime, `allday`=:isallday, `color`=:color WHERE `id`=:id";
    $statement = $link->prepare($sql);
    $statement->bindParam(":events", $events, PDO::PARAM_STR);
    $statement->bindParam(":starttime", $starttime, PDO::PARAM_INT);
    $statement->bindParam(":endtime", $endtime, PDO::PARAM_INT);
    $statement->bindParam(":isallday", $isallday, PDO::PARAM_INT);
    $statement->bindParam(":color", $color, PDO::PARAM_STR);
    $statement->bindParam(":id", $id, PDO::PARAM_INT);
    $statement->execute();

    if ( $statement->rowCount() == 1 )
    {
        echo '1';
    }
    else
    {
        echo '出错了！';
    }
}
elseif ( $action == "del" )
{
    $id = intval($_POST['id']);
    if ( $id > 0 )
    {
        $sql       = "DELETE FROM `calendar` WHERE `id`=:id";
        $statement = $link->prepare($sql);

        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        if ( $statement->rowCount() == 1 )
        {
            echo '1';
        }
        else
        {
            echo '出错了！';
        }
    }
    else
    {
        echo '事件不存在！';
    }
}
elseif ( $action == "drag" )
{
    $id        = $_POST['id'];
    $daydiff   = (int)$_POST['daydiff'] * 24 * 60 * 60;
    $minudiff  = (int)$_POST['minudiff'] * 60;
    $allday    = $_POST['allday'];
    $sql       = "SELECT * FROM `calendar`  WHERE `id`=:id";
    $statement = $link->prepare($sql);
    $statement->bindParam(":id", $id, PDO::PARAM_INT);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    //echo $allday;exit;
    if ( $allday == "true" )
    {
        if ( $row['endtime'] == 0 )
        {
            $sql = "UPDATE `calendar` SET starttime=starttime+:daydiff WHERE id=:id";
        }
        else
        {
            $sql = "UPDATE `calendar` SET starttime=starttime+:daydiff, endtime=endtime+:daydiff WHERE id=:id";
        }
        $statement = $link->prepare($sql);
        $statement->bindParam(":daydiff", $daydiff, PDO::PARAM_STR);
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();
    }
    else
    {
        $difftime = $daydiff + $minudiff;
        if ( $row['endtime'] == 0 )
        {
            $sql = "UPDATE `calendar` SET starttime=starttime+:difftime WHERE id=:id";
        }
        else
        {
            $sql = "UPDATE `calendar` SET starttime=starttime+:difftime,endtime=endtime+:difftime WHERE id=:id";
        }
        $statement = $link->prepare($sql);
        $statement->bindParam(":difftime", $difftime, PDO::PARAM_STR);
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();
    }

    if ( $statement->rowCount() == 1 )
    {
        echo '1';
    }
    else
    {
        echo '出错了！';
    }
}
elseif ( $action == "resize" )
{
    $id       = $_POST['id'];
    $daydiff  = (int)$_POST['daydiff'] * 24 * 60 * 60;
    $minudiff = (int)$_POST['minudiff'] * 60;

    $sql       = "SELECT * FROM `calendar`  WHERE `id`=:id";
    $statement = $link->prepare($sql);
    $statement->bindParam(":id", $id, PDO::PARAM_INT);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    $difftime = $daydiff + $minudiff;

    if ( $row['endtime'] == 0 )
    {
        $sql = "UPDATE `calendar` SET endtime=starttime+:difftime WHERE id=:id";
    }
    else
    {
        $sql = "UPDATE `calendar` SET endtime=endtime+:difftime, endtime=endtime+:difftime WHERE id=:id";
    }
    $statement = $link->prepare($sql);
    $statement->bindParam(":difftime", $difftime, PDO::PARAM_STR);
    $statement->bindParam(":id", $id, PDO::PARAM_INT);
    $statement->execute();

    if ( $statement->rowCount() == 1 )
    {
        echo '1';
    }
    else
    {
        echo '出错了！';
    }
}
