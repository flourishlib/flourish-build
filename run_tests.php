<?php
$hosts = array(
    'will@vm-centos.wbond.net',
    'will@vm-debian.wbond.net',
    'will@vm-fedora.wbond.net',
    'will@vm-freebsd.wbond.net',
    'will@vm-netbsd.wbond.net',
    'will@vm-openbsd.wbond.net',
    'will@vm-opensolaris.wbond.net',
    'will@vm-opensuse.wbond.net',
    'will@osx.wbond.net',
    'will@vm-server2008.wbond.net',
    'will@vm-ubuntu.wbond.net',
    'will@vm-xp.wbond.net'
);

function pretty_json_encode($data) {
    $json = json_encode($data);
    $json = str_replace('[', "[\n\t", $json);
    $json = str_replace('{', "{\n\t", $json);
    $json = str_replace(',', ",\n\t", $json);
    $json = str_replace(']', "\n]", $json);
    $json = str_replace('}', "\n}", $json);
    return str_replace("[\n\t\n]", "[]", $json);
}

function exec_out($command) {
    exec($command, $output, $return_val);
    return array($return_val, join("\n", $output));
}

// Save the current branches so we can return there afterwards
$classes_branch = `git --work-tree=../classes --git-dir=../classes/.git symbolic-ref -q HEAD`;
$classes_branch = trim(str_replace('refs/heads/', '', $classes_branch));
`git --work-tree=../classes --git-dir=../classes/.git fetch --all`;

$tests_branch = `git --work-tree=../tests --git-dir=../tests/.git symbolic-ref -q HEAD`;
$tests_branch = trim(str_replace('refs/heads/', '', $tests_branch));
`git --work-tree=../tests --git-dir=../tests/.git fetch --all`;

$json = file_get_contents('../tests-results/todo.json');
$todo = json_decode($json, TRUE);

chdir('../tests');

$hashes_to_remove = array();
foreach ($todo as $classes_hash => $tests_hash) {
    if (!$classes_hash) {
        echo "[ERROR] Invalid classes sha1: " . $classes_hash ."\n";
        continue;
    }
    if (!$tests_hash) {
        echo "[ERROR] Invalid tests sha1: " . $classes_hash ."\n";
        continue;
    }

    // Checkout the appropriate versions to use for testing
    list($return_code, $_) = exec_out("git --work-tree=../classes --git-dir=../classes/.git checkout -q $classes_hash");
    if ($_) {
        echo "[ERROR] Error checking out classes sha1 " . $classes_hash ."\n";
        continue;
    }

    list($return_code, $_) = exec_out("git checkout -q $tests_hash");
    if ($_) {
        echo "[ERROR] Error checking out tests sha1 " . $classes_hash ."\n";
        continue;
    }

    $hashes_to_remove[] = $classes_hash;

    $test_results_summary = array();
    foreach ($hosts as $host) {
        $test_results_json = `bash remote_tests.sh $host .json`;
        $test_results = json_decode($test_results_json, TRUE);
        $test_results_summary[$host] = $test_results;

        file_put_contents("../tests-results/results/$classes_hash.json", json_encode($test_results_summary));
    }

    // Update the index of executed tests
    $json = file_get_contents('../tests-results/index.json');
    $index = json_decode($json, TRUE);
    if ($index[0] != $classes_hash) {
        array_unshift($index, $classes_hash);
        $json = pretty_json_encode($index);
        file_put_contents('../tests-results/index.json', $json);
    }
}

// Remove the processed hashes
$json = file_get_contents('../tests-results/todo.json');
$todo = json_decode($json, TRUE);
foreach ($hashes_to_remove as $hash) {
    unset($todo[$hash]);
}
$json = pretty_json_encode($todo);
file_put_contents('../tests-results/todo.json', $json);

chdir('../build');

// Return to the original location
$_ = `git --work-tree=../classes --git-dir=../classes/.git checkout -q $classes_branch`;
$_ = `git --work-tree=../tests --git-dir=../tests/.git checkout -q $tests_branch`;