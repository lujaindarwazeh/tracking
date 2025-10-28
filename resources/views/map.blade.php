<!DOCTYPE html>
<html>
<head>
    <title>Way Polygon Viewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 90vh; width: 100%; }
        button {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Draw Line and View Polygon</h2>
    <div id="map"></div>
    <button onclick="sendPoints()">Generate Polygon</button>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([32.22, 35.25], 13); // Center on Nablus

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data © OpenStreetMap contributors'
        }).addTo(map);

        let points = [];
        let polyline;

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            points.push([lat, lng]);

            console.log("Point added:", { lat, lng });

            if (polyline) map.removeLayer(polyline);
            polyline = L.polyline(points, { color: 'blue' }).addTo(map);
        });

        async function sendPoints() {
            const wayId = 1; // Change this to your actual way ID
            const formatted = points.map(p => ({ x: p[1], y: p[0] }));
            console.log("Formatted points:", formatted);

            try {
                const postRes = await fetch(`/api/ways/${wayId}/points`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ points: formatted })
                });
                console.log("Points POST status:", postRes.status);

                const genRes = await fetch(`/api/ways/${wayId}/generate-polygon`, { method: 'POST' });
                console.log("Polygon generation status:", genRes.status);

                const getRes = await fetch(`/api/ways/${wayId}/getgeneratePolygon`);
                console.log("Polygon fetch status:", getRes.status);

                const text = await getRes.text();
                console.log("Raw polygon response:", text);

                const data = JSON.parse(text);
                const raw = data.polygon;
                console.log("Polygon string:", raw);

                const coords = raw
                    .replace("POLYGON((", "")
                    .replace("))", "")
                    .split(", ")
                    .map(pair => {
                        const [lng, lat] = pair.split(" ");
                        return [parseFloat(lat), parseFloat(lng)];
                    });

                console.log("Parsed coordinates:", coords);

                const polygonLayer = L.polygon(coords, { color: 'red' }).addTo(map);
                map.fitBounds(polygonLayer.getBounds());
            } catch (err) {
                console.error("Error during polygon process:", err);
                alert("Something went wrong: " + err.message);
            }
        }
    </script>
</body>
</html>
