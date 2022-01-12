<?php
$current_dir = getcwd();
chdir('..'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'python'.DIRECTORY_SEPARATOR.'script');
if (PHP_OS == "Linux"){
    exec('venv/bin/python3 detect.py test1.jpg');
}else{
    exec('venv\Scripts\python detect.py test1.jpg 2>&1');
}
chdir($current_dir);
