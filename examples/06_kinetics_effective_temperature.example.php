<?php


/**
 * Example #6
 * In the last example we saw how the rate of reaction is related to temperature. Because this
 * relationship is not linear, we cannot take the average rate of reaction over a period of varying
 * temperature by calculating the rate of reaction at the mean temperature. Instead we sample the
 * rate of reaction and take the mean of these rates, optionally converting this back into a
 * temperature. This temperature is known as the "effective temperature". We'll use DNA depurination
 * as our reaction (covered in the previous example).
 */
$depurination = new kinetics (
        scalarFactory::makeKilojoulesPerMole(126940),
        scalarFactory::makeSeconds(17745329175.856213),
        "DNA depurination (bone)"
        );


/**
 * Let's compare a constant temperature with a sine modelled temperature over a year, and verify the
 * former while investigating the latter by converting back from a mean rate to effective temperature.
 *
 * First, a variable climate of 10±6°C over the year
 */
$varClimate = new sine();
$varClimate->setGenericSine(
        scalarFactory::makeCentigradeAbs(10),
        scalarFactory::makeKelvinAnomaly(12),
        scalarFactory::makeDays(0)
        );


/**
 * And lets say it's a constant 10°C in our temperature controlled store room or whatever...
 */
$storage = scalarFactory::makeCentigradeAbs(10);


/**
 * Now to find out how much the DNA decays in a year!
 */
for ($day = 1; $day <= 365; $day++) {
    $varRate = $depurination->getRate($varClimate->getValue($day));
    $storeRate = $depurination->getRate($storage);
}


$tempDir = examples_output_path(EXAMPLE_NAME);
$fn = $tempDir . "rate_of_reaction_temperature.png";
$plot->plot($fn);

?>