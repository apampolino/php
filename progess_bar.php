<?php

function loadProgressBar($current, $total, $max_progress = 50) {

    $percentage = number_format((float) ($current / $total) * 100, 2);

    $progress_ctr = floor($max_progress * ($percentage / 100));

    $progress_bar = floor($max_progress * ($percentage / 100)) < 1 ? str_repeat("=", 1) . str_repeat(" ", $max_progress - 1) : str_repeat("=", $progress_ctr) . str_repeat(" ", $max_progress - $progress_ctr);

    $random = rand($current, $total);

    if ($current == $total) {

        echo "Completed " . $progress_bar . " " . $percentage . "%\r\n";

    } else {

        if (($random % 2) == 0) {

            echo "Processing " . $progress_bar . " " . $percentage . "%\r";
        }
    }

    sleep(1);
}

$total = 120;
$max_progress = 50;

for ($i = 1; $i <= $total; $i++) {

    loadProgressBar($i, $total, 50);
}
