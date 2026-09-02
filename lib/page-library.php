<?php

$page_number = get_page_amount($table,$column,$amount);

$current_page = (isset($_GET["p"]) && $_GET["p"] > 0) && $_GET["p"] <= $page_number ? $_GET['p'] : 1;

