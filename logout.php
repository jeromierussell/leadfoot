<?php

session_start();
session_unregister("USERNAME");
session_destroy();
require("config.php");
header("Location: " . $config_basedir);

?>