<?php

include_once('connect.php'); //连接数据库

$action = $_GET['action'];
$isend = '';

if ($action == 'add') { //增加
    $events = stripslashes(trim($_POST['event'])); //事件内容
    $events = mysql_real_escape_string(strip_tags($events), $link); //过滤HTML标签，并转义特殊字符

    $isallday = isset($_POST['isallday']) ? $_POST['isallday'] : ''; //是否是全天事件
    $isend = isset($_POST['isend']) ? $_POST['isend'] : ""; //是否有结束时间

    $startdate = trim($_POST['startdate']); //开始日期
    $enddate = trim($_POST['enddate']); //结束日期

    $s_time = $_POST['s_hour'] . ':' . $_POST['s_minute'] . ':00'; //开始时间
    $e_time = $_POST['e_hour'] . ':' . $_POST['e_minute'] . ':00'; //结束时间
    $endtime = 0;

    if ($isallday == 1 && $isend == 1) {
        $starttime = strtotime($startdate);
        $endtime = strtotime($enddate);
    } elseif ($isallday == 1 && $isend == "") {
        $starttime = strtotime($startdate);
    } elseif ($isallday == "" && $isend == 1) {
        $starttime = strtotime($startdate . ' ' . $s_time);
        $endtime = strtotime($enddate . ' ' . $e_time);
    } else {
        $starttime = strtotime($startdate . ' ' . $s_time);
    }

    $colors = array("#360", "#f30", "#06c");
    $key = array_rand($colors);
    $color = $colors[$key];

    $isallday = $isallday ? 1 : 0;
    $sql = "insert into `calendar` (`title`,`starttime`,`endtime`,`allday`,`color`) values ('$events','$starttime','$endtime','$isallday','$color')";
    $query = mysql_query($sql);
    if (mysql_insert_id() > 0) {
        echo '1';
    } else {
        echo '写入失败！';
    }
} elseif ($action == "edit") { //编辑
    $isend = '';
    $id = intval($_POST['id']);
    if ($id == 0) {
        echo '事件不存在！';
        exit;
    }
    $events = stripslashes(trim($_POST['event'])); //事件内容
    $events = mysql_real_escape_string(strip_tags($events), $link); //过滤HTML标签，并转义特殊字符

    $isallday = isset($_POST['isallday']) ? $_POST['isallday'] : ''; //是否是全天事件
    $isend = isset($_POST['isend']) ? $_POST['isend'] : ""; //是否有结束时间

    $startdate = trim($_POST['startdate']); //开始日期
    $enddate = trim($_POST['enddate']); //结束日期

    $s_time = $_POST['s_hour'] . ':' . $_POST['s_minute'] . ':00'; //开始时间
    $e_time = $_POST['e_hour'] . ':' . $_POST['e_minute'] . ':00'; //结束时间

    if ($isallday == 1 && $isend == 1) {
        $starttime = strtotime($startdate);
        $endtime = strtotime($enddate);
    } elseif ($isallday == 1 && $isend == "") {
        $starttime = strtotime($startdate);
        $endtime = 0;
    } elseif ($isallday == "" && $isend == 1) {
        $starttime = strtotime($startdate . ' ' . $s_time);
        $endtime = strtotime($enddate . ' ' . $e_time);
    } else {
        $starttime = strtotime($startdate . ' ' . $s_time);
        $endtime = 0;
    }

    $isallday = $isallday ? 1 : 0;
    mysql_query("update `calendar` set `title`='$events',`starttime`='$starttime',`endtime`='$endtime',`allday`='$isallday' where `id`='$id'");
    if (mysql_affected_rows() == 1) {
        echo '1';
    } else {
        echo '出错了！';
    }
} elseif ($action == "del") {
    $id = intval($_POST['id']);
    if ($id > 0) {
        mysql_query("delete from `calendar` where `id`='$id'");
        if (mysql_affected_rows() == 1) {
            echo '1';
        } else {
            echo '出错了！';
        }
    } else {
        echo '事件不存在！';
    }
} elseif ($action == "drag") {
    $id = $_POST['id'];
    $daydiff = (int) $_POST['daydiff'] * 24 * 60 * 60;
    $minudiff = (int) $_POST['minudiff'] * 60;
    $allday = $_POST['allday'];
    $query = mysql_query("select * from `calendar` where id='$id'");
    $row = mysql_fetch_array($query);
    //echo $allday;exit;
    if ($allday == "true") {
        if ($row['endtime'] == 0) {
            $sql = "update `calendar` set starttime=starttime+'$daydiff' where id='$id'";
        } else {
            $sql = "update `calendar` set starttime=starttime+'$daydiff',endtime=endtime+'$daydiff' where id='$id'";
        }
    } else {
        $difftime = $daydiff + $minudiff;
        if ($row['endtime'] == 0) {
            $sql = "update `calendar` set starttime=starttime+'$difftime' where id='$id'";
        } else {
            $sql = "update `calendar` set starttime=starttime+'$difftime',endtime=endtime+'$difftime' where id='$id'";
        }
    }
    $result = mysql_query($sql);
    if (mysql_affected_rows() == 1) {
        echo '1';
    } else {
        echo '出错了！';
    }
} elseif ($action == "resize") {
    $id = $_POST['id'];
    $daydiff = (int) $_POST['daydiff'] * 24 * 60 * 60;
    $minudiff = (int) $_POST['minudiff'] * 60;

    $query = mysql_query("select * from `calendar` where id='$id'");
    $row = mysql_fetch_array($query);
    //echo $allday;exit;
    $difftime = $daydiff + $minudiff;
    if ($row['endtime'] == 0) {
        $sql = "update `calendar` set endtime=starttime+'$difftime' where id='$id'";
    } else {
        $sql = "update `calendar` set endtime=endtime+'$difftime' where id='$id'";
    }

    $result = mysql_query($sql);
    if (mysql_affected_rows() == 1) {
        echo '1';
    } else {
        echo '出错了！';
    }
}
