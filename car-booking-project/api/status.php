<?php
$log = 'log.txt';

if (file_exists($log)) {
    $contents = file_get_contents($log);
    $lines = explode("\n\n", trim($contents));
    $latest = end($lines);

    echo "<pre>$latest</pre>";
} else {
    echo "No transaction log found.";
}
?>
