<?php

/**
 * Example #3
 * Estimates the local temperature in York for the last 50kyrs
 */

// The following is covered in previous examples
$dateRecent = new palaeoTime(100);
$dateAncient = new palaeoTime(50000);
$temps = new temperatures ();
$step = 250;

/**
 * Set lat and long
 */
$location = new latLon (53.946799933662604, -1.0580241680145264);

/**
 * We calculate the local temperature by performing a linear regression between the local modelled
 * temperature at 0, 6 and 21ka and the global anomaly to come up with something like "the temperature
 * at york is (a * [global anomaly] + b)°C. The numbers go from relative C(or K) to absolute, so the
 * values a and b can be quite wacky looking. We do the same for amplitude. (annual temperature
 * variation is +/- (a * [anomaly] + b)°C")
 * With these 2nd order polynomials as a correction layer above our high resolution global anomaly
 * data, we can interpolate the air temperature throughout the year at any place (away from the
 * poles, anyway) and any year. Accuracy is quite another matter, the errors could be huge, but at
 * least it's relatively consistent. You'll also be happy to hear that it's dead easy to accomplish:
 */
$localisingCorrections = $temps->getPalaeoTemperatureCorrections ($location);
/**
 * ...yup, that's literally it!
 * $localisingCorrections is now an array containing polynomial corrections which can be applied to
 * the global temperatures later on as needed. Note other corrections (burial, which we'll come on
 * to later, vegetation cover and arbitrarily extensible to correct for anything else you can describe
 * with an equation).
 */



// Set up plot and data ranges
$tempDir = examples_output_path(EXAMPLE_NAME);
$plot = new ttkplPlot("Global and local climate (" .
        $dateRecent->getYearsBp() .
        " b.p. to " .
        $dateAncient->getYearsBp() .
        ' b.p. at ' .
        $step .
        " year intervals)"
        );

$plot->labelAxes("Years b.p.", "°C")->setGrid('y')
        ->setData("Time-interpolated local annual max/min daily mean air temperature (°C)", 0, 'x1y1', 'filledcurves', '1:2:3')
        ->setData("Time-interpolated local annual mean daily mean air temperature (°C)", 3, 'x1y1', 'lines', '1:2')
        ->setData("Global mean anomaly (+/-°C of pre-industrial)",          1, 'x1y1', 'lines')
        ->setData("HADCM3M2 local max/mean/min daily mean air temperature (°C)",           2, 'x1y1', 'yerrorbars', '1:2:3:4');


/**
 * First of all, let's plot our "real" (i.e. raw data from PMIP2) modelled local temperatures so we
 * can compare them to the values interpolated over time via the polynomial resulting from the linear
 * regression. We've got 6 temperatures; the max and min annual temps at 3 points in the past.
 * TTKPL is designed to keep track of the source of inputs to calculations to make the report generation
 * easier and ensure quality. We could get these values out of the corrections themselves, but there
 * is a cleaner lookin' way ('whens' contains the filenames coresponding to the 3 different times,
 * they can be converted to palaeoTimes with the pmip::ptcToPalaeoTime method.)
 */

foreach ($temps->whens as $pmipTimeConst) {
    $mean = $temps->getLocalMeanTempAt ($location, $pmipTimeConst)->getScalar()->getValue();
    $amplitude = $temps->getLocalAmplitudeAt ($location, $pmipTimeConst)->getScalar()->getValue();
    $fluctuation = $amplitude / 2;
    $mean += scalarFactory::kelvinOffset;
    $min = $mean - $fluctuation;
    $max = $mean + $fluctuation;
    $yr = pmip::ptcToPalaeoTime($pmipTimeConst)->getYearsBp();
    echo "$yr, $mean, $min, $max\n";
    $plot->addDataAssoc(array (array ($yr, $mean, $min, $max)), 2);

}

/**
 * Right, let's take stock; we've got global anomaly and polynomial corrections which guesstimate
 * that anomaly value into a local mean and amplitude. What we need is an easy way to glue all this
 * stuff together. Introducing temporothermal ("time, temperature") class:
 */
$localClimate = new temporothermal();


/**
 * The temporothermal can contain a few different sorts of corrections, the "Localising" corrections
 * are the ones that go from global anomaly to local temperature and amplitude. It expects them in the
 * same format as we've got them already so let's get geolocal!
 */
$localClimate->setLocalisingCorrections($localisingCorrections);

/**
 * Now iterate through time and calculate all the values for our (rather cluttered) graph...
 * This isn't the quickest way to access these calculations but it demonstrates how they happen
 * "under the hood" while making all the intermediate values available for our graph.
 */
for ($yr = $dateRecent->getYearsBp(); $yr <= $dateAncient->getYearsBp(); $yr += $step) {
    $td = $temps->getGlobalMeanAnomalyAt (palaeoTime::_bootstrap ($yr));
    $plot->addData($yr, $td->value->getValue(), 1);
    /**
     * The getSineFromGlobal method causes the temporothermal to interpolate the annual local daily
     * temperatures and return them as sine parameters (mean and amplitude (max-min).
     */
    $localTemperatureSineThisYear = $localClimate->getSineFromGlobal($td->getValue());
    // Mean temperature stored here as a float in kelvin, scalar also available in sine->mean
    $mean = $localTemperatureSineThisYear->Ta + scalarFactory::kelvinOffset;
    $min = $mean - $localTemperatureSineThisYear->A0;
    $max = $mean + $localTemperatureSineThisYear->A0;
    //$plot->addData($yr, $mean, 1);
    $plot->addDataAssoc(array (array ($yr, $min, $max)), 0);
    $plot->addData($yr, $mean, 3);
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