<?php

$script = '
<html>
<table id="cronjobfiles">
    <thead>
    <tr>
        <td>Last File Written</td>
        <td>Date</td>
    </tr>
    </thead>
    <tbody>';
foreach($files AS $k => $v) {
    $script .= '<tr>';
    $script .= '<td> ' . $v["last_filename"] . '</td>';
    $script .= '<td> ';
    if ($v . match == 1)
        $script .= ' class="match" ';
    else
        $script .= ' class="nomatch" ';
    $script .= '> ' . $v["last_filedate"] . '</td>';
    $script .= '</tr>';
}
    $script .= '
    </tbody>
</table>
</html>';