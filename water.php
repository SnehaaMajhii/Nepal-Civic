<?php
// water.php
include 'includes/db.php'; // Included to fetch dynamic departments count if needed
$page_title = "Water Supply";
include 'includes/header.php';
?>
<div class="container" style="padding-top: 40px;">
    <h1 style="text-align: center; margin-bottom: 40px;">Water Supply</h1>

    <div class="features" style="justify-content: center;">
        
        <div class="feature-card">
            <a href="water.php"><h3>Water Route for Biratnagar</h3></a>
            <p>Biratnagar, the industrial and administrative hub of Koshi Province in Nepal, relies on a structured water supply system managed primarily by the Nepal Water Supply Corporation (NWSC) and supported by federal water infrastructure development agencies. The city’s water supply route consists of deep groundwater sources, pumping facilities, treatment infrastructure, storage reservoirs, and an interconnected distribution network serving households and institutions across the municipality.<br>
            <h4>1. Water Sources</h4>
            The principal sources of Biratnagar’s water supply are deep tube wells drilled into the groundwater aquifer. These wells draw water from depths where the quality and quantity are relatively reliable compared to shallow sources. Biratnagar currently has several production units located across key points in the city:    
            <ul>
                <li>Devkota Chowk – Wells feed a 500 m³ overhead tank serving the core urban area.</li>
                <li>Tinpaini – Two wells fill a 450 m³ overhead tank supplying the northern sectors.</li>
                <li>Rani – A well and receiver tank serve southern parts of the city.</li>
                <li>Munalpath – Three wells with a 450 m³ overhead tank serve eastern and central zones.</li>
            </ul>
            <br>In addition, a few wells at Kanchanbari, BFM, and Pichara directly feed the distribution network without intermediate storage tanks. </p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>