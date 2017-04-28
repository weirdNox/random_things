<?php
require_once 'Mysql.php';

$mysql = new Mysql();

$mysql->verifyNonActivated();