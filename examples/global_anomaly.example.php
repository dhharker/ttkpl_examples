<?php

/**
 * Looks up temperature anomaly and outputs it to the console.
 */


/**
 * First define what time period we are interested in
 * Palaeotime objects contain a time in years "b.p." or "before present" where the "present" is
 * arbitrarily set at 1950AD.  
 */
$dateRecent = new palaeoTime("NOW");
$dateAncient = new palaeoTime(10000);


/**
 * Next we need to look up the global anomaly (°C hotter or colder than present) over time. The
 * temperatures class contains both a continuous time series of global anomalies and global annual
 * temperature models at 0bp ("pre-industrial control"), 6kbp ("mid holocene"), 21kbp (LGM) and is
 * capable of interpolating temperatures at essentially arbitrary precision in both time and space.
 * Pretty cool, huh? Luckily it's not too hard to use:
 */
$temps = new temperatures ();


/**
 * That's all the setup done, let's look up some anomalies and introduce the datum and scalar classes.
 * getGlobalMeanAnomalyAt needs to know what year to look at, so lets give it our dates from above.
 * It will return temporalDatum (i.e. a data point referenced in time) objects which in turn contain
 * scalar objects which contain the numeric result we're after as well as optionally units, symbols
 * and clusures to convert to other sorts of scalar.
 */
$results = array ();
$results['Recent']    = $temps->getGlobalMeanAnomalyAt ($dateRecent);
$results['Ancient']   = $temps->getGlobalMeanAnomalyAt ($dateAncient);


/**
 * Now we will iterate over our results above and print out a niceley formatted description which
 * is entirely generated from the metadata held within the objects.
 */
foreach ($results as $label => $temporalDatum) {
    printf("\tMean global temperature anomaly at %6.0f %s = %0.4f %s\n",
            $temporalDatum->palaeoTime->timeScalar->getValue(),
            $temporalDatum->palaeoTime->timeScalar->getUnitsLong (),
            $temporalDatum->value->getValue(),
            $temporalDatum->value->getUnitsLong()
            );
}


?>