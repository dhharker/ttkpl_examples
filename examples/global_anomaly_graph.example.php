<?php

/**
 * Draws a graph of global mean temperature anomaly over the last 10000 years at 100 year intervals.
 */

// The following is covered in global_anomaly.example.php
$dateRecent = new palaeoTime("NOW");
$dateAncient = new palaeoTime(10000);
$temps = new temperatures ();


/**
 * TTKPL contains some features to make reports and particularly graphs easier to generate. Graphs
 * are made with GNUPlot and a third party interface to it.
 */


/**
 * Set up to step in 100 year intervals (i.e. sample every 100th year) and iterate from our late to
 * early date. We also want to graph these data, so lets add them directly to the graph.
 */
$step = 100;
for ($yr = $dateRecent->getYearsBp(); $yr <= $dateAncient->getYearsBp(); $yr += $step) {
    $anomalies[$yr] = $temps->getGlobalMeanAnomalyAt ($value);
}

foreach ($results as $label => $temporalDatum) {
    printf("\tMean global temperature anomaly at %6.0f %s = %0.4f %s\n",
            $temporalDatum->palaeoTime->timeScalar->getValue(),
            $temporalDatum->palaeoTime->timeScalar->getUnitsLong (),
            $temporalDatum->value->getValue(),
            $temporalDatum->value->getUnitsLong()
            );
}


?>