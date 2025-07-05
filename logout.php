<?php
require_once 'config/Session.php';

Session::init();
Session::destroy();

header('Location: login.php');
exit();