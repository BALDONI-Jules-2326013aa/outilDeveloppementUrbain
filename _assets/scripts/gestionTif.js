var map = L.map('map').setView([0, 0], 5);

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

document.getElementById('geotiff-file').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.readAsArrayBuffer(file);
    reader.onloadend = function() {
        var arrayBuffer = reader.result;
        parseGeoraster(arrayBuffer).then(georaster => {
            var layer = new GeoRasterLayer({
                georaster: georaster,
                opacity: 1,
                resolution: 256
            });
            layer.addTo(map);
            map.fitBounds(layer.getBounds());
        });
    };
});
