<?php
function clean_input($data)
{
    if ($data === null) return '';
    $data = trim((string)$data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
