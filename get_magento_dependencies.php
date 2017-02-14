<?php

$options = getopt('d:', ['directory:']);

if (!empty($options['d'])) {
    $directory = $options['d'];
} elseif (!empty($options['directory'])) {
    $directory = $options['directory'];
} else {
    throw new Exception('Directory parameter is not specified!');
}

$modules = scandir($directory);

unset($modules[0], $modules[1]);

$generalCmd = <<< HEREDOC
grep -R 'Magento\\\' . | grep -v '@return' | sed -E 's/.*(Magento(\\\[a-zA-Z]*)+).*/\\1/g' \\
 | grep -v 'Interface' | sort | uniq -c | sort -r | sed -e 's/^[ \t]*//' | awk '{print "|" $1 "|" $2 "| |"}' | grep -v 'Magento\\\
HEREDOC;

$header = '||Occurrences count||Class||Comments||';

foreach ($modules as $module) {

    $fullPathDir = $directory . '/' . $module;

    if (!is_dir($fullPathDir)) {
        continue;
    }

    chdir($fullPathDir);

    $cmd = $generalCmd . $module . "\\\'";

    $result = 'h3. ' . $module . " dependencies\n" ;
    $result .= $header . "\n";
    $result .= shell_exec($cmd) . "\n\n";

    echo $result;
}

