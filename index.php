<?php
require_once './app/core/Loader.php';

new JAPI\Loader;
call_user_func([new JAPI\Route , 'do']);

