<?php

ini_set('memory_limit', '6G');

require __DIR__ . '/../vendor/autoload.php';

$container = new StatisticalClassifierServiceContainer;

use Camspiers\StatisticalClassifier\DataSource\Directory;

$cats = array(
    'alt.atheism',
    'comp.graphics',
    'rec.motorcycles',
    'sci.crypt'
);

$container->set(
    'data_source.data_source',
    new Directory(__DIR__ . '/../resources/20news-bydate/20news-bydate-train', $cats)
);

$nb = $container->get('classifier.naive_bayes');

$testSource = new Directory(__DIR__ . '/../resources/20news-bydate/20news-bydate-test', $cats);

$data = $testSource->getData();

$stats = array();
$fails = array();

foreach ($data as $category => $documents) {
    $stats[$category] = 0;
    foreach ($documents as $document) {
        if (($classifiedAs = $nb->classify($document)) == $category) {
            $stats[$category]++;
        } else {
            $fails[] = array($category, $classifiedAs, $document);
        }
    }
    echo $category, ': ', ($stats[$category] / count($documents)), PHP_EOL;
}

echo 'Failures:', PHP_EOL;
foreach ($fails as $fail) {
    echo "Classified document from '{$fail[0]}' as '{$fail[1]}'", PHP_EOL;
}
