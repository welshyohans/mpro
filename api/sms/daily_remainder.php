<?php

include_once '../../model/SMS.php';



$quotes = ['Discipline is choosing between what you want now and what you want most.', "Social media won’t build your dream—your effort will. Get back to your grind.","Small consistent steps beat random bursts of effort. Keep showing up","The less you scroll, the more you soar. Use your time, don’t lose it.","We are what we repeatedly do. Excellence, then, is not an act, but a habit","Nothing in this world can take the place of persistence. Talent will not: nothing is more common than unsuccessful men with talent.","Discipline is the bridge between goals and accomplishment.","Do something today that your future self will thank you for.","The best marketing is a customer who can’t stop talking about you. Serve them like royalty.","Every wasted minute is a deal someone else just closed. Grind now—rest later.","Don’t just dream about it. Schedule it. Build it. Sell it. Live it.","Selling isn’t convincing—it’s solving. Solve better than anyone, and you’ll outsell everyone.","Your product doesn’t need to be perfect. Your message does. Make people feel something—then they’ll buy.","Consistency isn't sexy—but it's what makes legends. Stay in motion, no matter what.","See every task, even the small ones, as a step toward greatness. Reframe 'work' as 'progress'.","I’m not here to be average; I’m here to be the best.","Success is the sum of small efforts, repeated day in and day out."];
$randomQuote = $quotes[array_rand($quotes)];

//echo $randomQuote;

$t= '+251943090921';
$m= 'it is amazing...!';
$sms = new SMS();
$sms->sendSms($t,$randomQuote);


?>