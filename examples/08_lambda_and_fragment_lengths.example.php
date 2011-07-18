<?php


/**
 * λ        = k                             × t
 * lambda   = effective rate of reaction    × time spent at effective rate
 * Probability of (b)reakage or (s)urvival of a DNA molecule of (l)ength given λ
 * "Ps(l) = Probability of a DNA molecule of (l)ength base pairs not being broken by depurination"
 * Pb(l)    = λ^(l-1)
 * Ps(l)    = 1 - λ^(l-1)
 */

function Pb ($length, $lambda) {
    $pb = ($length - 1) * $lambda;
    return $pb;
}
function Ps ($length, $lambda) {
    return pow (1 - $lambda, $length - 1);
}

$gi = 1;

$plot = new ttkplPlot("Effect of λ on fragment length distribution");
$plot->labelAxes("DNA Fragment Length", "Relative Probability of survival through not-being-depurinated")
        ->setGrid(array ('x','y'))
        ->setLog(array ('x'))
        ->setData ("Mean Fragment Lengths", 1, 'x1y1', 'points');

$λ = .5;
do {
    $mfl = round ((1/$λ)+1);
    $plot->setData ("λ = $λ (mfl=$mfl)", ++$gi)
         ->addData ($mfl, Ps ($mfl, $λ), 1);

    for ($l = 2; $l <= $mfl * 3; $l += 1    )
        $plot->addData ($l, Ps ($l, $λ), $gi);
    $λ *= 0.5;
} while ($λ > 1E-4);



$tempDir = examples_output_path(EXAMPLE_NAME);
$fn = $tempDir . "lambdas_fragment_lengths.png";
$plot->plot($fn);