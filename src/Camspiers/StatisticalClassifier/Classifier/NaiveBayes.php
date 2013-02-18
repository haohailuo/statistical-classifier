<?php

namespace Camspiers\StatisticalClassifier\Classifier;

use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;
use Camspiers\StatisticalClassifier\Tokenizer\TokenizerInterface;
use Camspiers\StatisticalClassifier\Normalizer\NormalizerInterface;
use Camspiers\StatisticalClassifier\Transform;
use Camspiers\StatisticalClassifier\ClassificationRule;
use Camspiers\StatisticalClassifier\Index\IndexInterface;

class NaiveBayes extends GenericClassifier
{

    public function __construct(
        DataSourceInterface $source,
        IndexInterface $index,
        TokenizerInterface $tokenizer,
        NormalizerInterface $normalizer
    )
    {
        parent::__construct(
            $source,
            $index,
            new ClassificationRule\NaiveBayes(
                Transform\Weight::PARTITION_NAME
            ),
            $tokenizer,
            $normalizer,
            array(
                new Transform\DC(),
                new Transform\TCBD(
                    $tokenizer,
                    $normalizer
                ),
                new Transform\TF(
                    Transform\TCBD::PARTITION_NAME
                ),
                new Transform\TAC(
                    Transform\TCBD::PARTITION_NAME
                ),
                new Transform\IDF(
                    Transform\TF::PARTITION_NAME,
                    Transform\DC::PARTITION_NAME,
                    Transform\TAC::PARTITION_NAME
                ),
                new Transform\DL(
                    Transform\IDF::PARTITION_NAME
                ),
                new Transform\TBC(
                    Transform\TCBD::PARTITION_NAME
                ),
                new Transform\DocumentTokenSums(
                    Transform\DL::PARTITION_NAME
                ),
                new Transform\DocumentTokenCounts(
                    Transform\DL::PARTITION_NAME
                ),
                new Transform\Complement(
                    Transform\DL::PARTITION_NAME
                ),
                new Transform\Weight(
                    Transform\Complement::PARTITION_NAME
                ),
                // new Transform\WeightNormalization(
                //     Transform\Weight::PARTITION_NAME
                // ),
                new Transform\Prune(
                    array(
                        Transform\Weight::PARTITION_NAME
                    )
                )
            )
        );
    }
}
