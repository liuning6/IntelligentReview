<?php
header('Content-Type: image/jpeg');
echo file_get_contents($_REQUEST['p']);