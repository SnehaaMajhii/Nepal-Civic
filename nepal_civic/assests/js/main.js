document.getElementById('getLoc').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('locInp').value = pos.coords.latitude + ", " + pos.coords.longitude;
        });
    } else {
        alert("Geolocation not supported");
    }
});