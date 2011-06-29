<?php

/**
 * Draws a graph of global mean temperature anomaly over the last 120000 years at 50 year intervals.
 */

// The following is covered in global_anomaly.example.php
$dateRecent = new palaeoTime("NOW");
$dateAncient = new palaeoTime(120000);
$temps = new temperatures ();

// Plot every $step'th year
$step = 50;

/**
 * TTKPL contains some features to make reports and particularly graphs easier to generate. Graphs
 * are made with GNUPlot and a third party interface to it. Lets set up a new plot.
 */
$tempDir = examples_output_path(EXAMPLE_NAME); // (The GNUPlot lib reads this via global :-o
$plot = new ttkplPlot("Global mean temperature anomaly from " . 
        $dateRecent->getYearsBp() .
        " b.p. to " . 
        $dateAncient->getYearsBp() .
        ' b.p. at ' .
        $step .
        " year intervals."
        );

$plot->labelAxes("Years b.p.", "°K")->setGrid('x')->setData("Global Mean Anomaly");


/**
 * Set up to step in $step year intervals (e.g. sample every 100th year) and iterate from our late to
 * early date. We also want to graph these data, so lets add them directly to the graph.
 */
for ($yr = $dateRecent->getYearsBp(); $yr <= $dateAncient->getYearsBp(); $yr += $step) {
    $td = $temps->getGlobalMeanAnomalyAt (palaeoTime::_bootstrap ($yr));
    $plot->addData($td->palaeoTime->timeScalar->getValue(), $td->value->getValue());
}


/**
 * And there you have it. Hopefully it worked!
 *
 * Tip: When you draw graphs with higher sampling densities than the underlying dataset (e.g. every
 * 10 years), the way the temperatures are transparently interpolated between points can be seen
 * clearly by changing the style of the data series to 'linespoints' above, e.g.:
 * $plot->...->setData("Global Mean Anomaly", 0, 'x1y1', 'linespoints');
 *
 */

$f = $tempDir . 'global_mean_temperature_anomaly.png';
if ($plot->plot($f))
    echo "Wrote graph to $f\n";


?>