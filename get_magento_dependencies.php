<?php

$directory = '/var/www/magento2/app/code/Magento';

$modules = scandir('/var/www/magento2/app/code/Magento');

unset($modules[0], $modules[1]);

$generalCmd = <<< HEREDOC
grep -R 'Magento\\\' . | grep -v '@return' | sed -E 's/.*(Magento(\\\[a-zA-Z]*)+).*/\\1/g' \\
 | grep -v 'Interface' | sort | uniq -c | sort -r | sed -e 's/^[ \t]*//' | awk '{print "|" $1 "|" $2 "| |"}' | grep -v 'Magento\\\
HEREDOC;

$header = '||Occurrences count||Class||Comments||';

$resultFile = '/var/www/magento2/results/result.txt';

unlink($resultFile);

foreach ($modules as $module) {

    chdir($directory . '/' . $module);

    $cmd = $generalCmd . $module . "\\\'";

    $result = 'h3. ' . $module . " dependencies\n" ;
    $result .= $header . "\n";
    $result .= shell_exec($cmd) . "\n\n";

    file_put_contents($resultFile, $result, FILE_APPEND);
}
