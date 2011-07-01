<?php


/**
 * Example #7
 * What is thermal age and where can I get some?
 */

/**
 * Thermal age is a way of normalising time for temperature, it is measured in "10°C thermal years".
 * The age of a specimen in thermal years is the number of years at a constant 10°C it would have
 * taken to cause the same amount of degradation to take place. If you insist, you can think of
 * thermal age being "equivalent years deep in a cave in northern Germany".
 *
 * Thermal age is a powerful concept because it allows direct comparison between samples of different
 * ages, from different sites, time periods or parts of the world entirely. This aids in decision
 * making on funding provision, destructive sampling of valuable samples as well as helping highlight
 * contamination.
 *
 * Let's get cracking. Most of this example has been covered in previous installments, this is where
 * we start bringing it all together.
 *
 * In this example we'll model a burial that's been under ground for a few thousand years, then dug
 * up and kept in a box somewhere for a load more years (a habit archaeologists have).
 */

// First the basics
$temps = new temperatures ();
$depurination = new kinetics (126940, 17745329175.856213, "DNA depurination (bone)");

// Where's it?
$graveLocation = new latLon (51.15579196587064, -2.2412109375); // <-- middle of nowhere
// What's the weather like there?
$localisingCorrections = $temps->getPalaeoTemperatureCorrections ($graveLocation);

// What's it buried under?
$topSoil = new thermalLayer(scalarFactory::makeMetres(1.5), scalarFactory::makeThermalDiffusivity (0.1), "Hypothetical dry, peaty soil.");
$subSoil = new thermalLayer(scalarFactory::makeMetres(2.8), scalarFactory::makeThermalDiffusivity (0.18), "Hypothetical dry, clay soil.");
$grave = new burial();
$grave->addThermalLayer($topSoil);
$grave->addThermalLayer($subSoil);

// palaeoTimes always contain years "before 1950". sp 2011 is -61 years b.p. Yup. Feature, not bug ;)
$today = new palaeoTime ("NOW");
$excavation = new palaeoTime (-37);
$wayBackWhen = new palaeoTime (4000);

// We've defined everything we need to describe the burial, lets put it all together in a temporothermal
$timeUnderground = new temporothermal ();
$timeUnderground->setTempSource ($temps);
$timeUnderground->setLocalisingCorrections ($localisingCorrections);
// *Heads up, these are new! (pretty self-explanatory though I hope)
$timeUnderground->setBurial($grave);
$timeUnderground->setTimeRange($wayBackWhen, $excavation);

/**
 * Now for all that time spent in a box. We're not going to model the temperature in the garage or
 * whatever, we're just going to define a sine for the whole couple of decades because that could be
 * all the information we've got to work with!
 */
$climateInMyGarage = new sine ();
$climateInMyGarage->setGenericSine(scalarFactory::makeCentigradeAbs(9), 15, 30);

$timeInGarage = new temporothermal();
$timeInGarage->setConstantClimate($climateInMyGarage);
$timeInGarage->setTimeRange($excavation, $today);

/**
 * Up to this point there's not much new, however instead of crunching a load of stuff by hand as
 * we did in example 4, we'll get to know some of the shortcuts. At this point we've got two
 * temporothermals, so two spans of time in a known temperature. Let's mix them all up together:
 */
$thermalAge = new thermalAge ();
$thermalAge->setKinetics ($depurination);
$thermalAge->addTemporothermal ($timeInGarage);
$thermalAge->addTemporothermal ($timeUnderground);

// This method call is what kicks off all the little hamsters running in their little wheels
$thermalYearsScalar = $thermalAge->getThermalAge ();

// That's it - all done now!

printf ("The sample has an age of %d years but a thermal age of %d 10°C thermal years. Its effective temperature over this time was %0.2f°C",
        $thermalAge->getAge(),
        $thermalYearsScalar->getValue(),
        $thermalAge->getTeff()->getValue() + scalarFactory::kelvinOffset
        );

?>