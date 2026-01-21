<?php
// road.php
include 'includes/db.php'; // Included to fetch dynamic departments count if needed
$page_title = "Roads and Infrastructure";
include 'includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
    <h1 style="text-align: center; margin-bottom: 40px;">Roads and Infrastructure</h1>
    <div class="features" style="justify-content: center;">
        
        <div class="feature-card">
            <a href="water.php"><h3>Local Road Network</h3></a>
            <p>Biratnagar’s local road network forms the backbone of daily urban mobility, connecting residential areas, markets, schools, hospitals, and industrial zones. While major highways handle intercity and regional traffic, local roads provide access to every ward and neighborhood within the city, supporting both pedestrians and motorized transport.<br>
            <h4>1. Road Layout and Structure</h4>
            Biratnagar’s local roads are generally grid-like in the city center, with main arterial streets branching into smaller lanes and alleys that connect residential clusters and commercial areas. <br>The road hierarchy includes:<br>
            <ul>
                <li>Main Road South – connecting Traffic Chowk to residential wards.</li>
                <li>Buddha Marg – linking central markets and government offices.</li>
                <li>Radha Raman Marg – serving industrial and educational zones.</li>
                <li>Rangeli Road (inner stretches) – facilitating east–west urban mobility.</li>
            </ul>
            <br>Smaller local streets and lanes often remain narrow, with one-way or shared traffic flow for motorcycles, bicycles, and pedestrians.</p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>