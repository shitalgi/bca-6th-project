<?php
// distance_utils.php
// In distance_utils.php or algorithmImplement.php

function haversine_distance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // Earth radius in kilometers

    $dlat = deg2rad($lat2 - $lat1);
    $dlon = deg2rad($lon2 - $lon1);

    $a = sin($dlat / 2) * sin($dlat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earth_radius * $c;

    return $distance;
}

function sort_providers_by_distance($providers, $userLat, $userLon) {
    foreach ($providers as &$provider) {
        $provider['distance'] = haversine_distance($userLat, $userLon, $provider['latitude'], $provider['longitude']);
    }
    usort($providers, function($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });
    return $providers;
}

function get_service_providers($user_lat, $user_lon, $service_type) {
    // Database connection
    include 'database.php';

    // Fetch service providers' locations
    $provider_query = $conn->prepare("SELECT provider_id, name, latitude, longitude FROM service_providers WHERE status = 'approved' AND service_type = ?");
    $provider_query->bind_param("s", $service_type);
    $provider_query->execute();
    $provider_result = $provider_query->get_result();

    $providers = [];
    while ($row = $provider_result->fetch_assoc()) {
        $providers[$row['provider_id']] = [
            'name' => $row['name'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'distance' => haversine_distance($user_lat, $user_lon, $row['latitude'], $row['longitude'])
        ];
    }

    $conn->close();

    return $providers;
}

function dijkstra_algorithm($start, $graph) {
    $dist = [];
    $prev = [];
    $queue = [];

    foreach ($graph as $vertex => $edges) {
        $dist[$vertex] = INF;
        $prev[$vertex] = null;
        $queue[$vertex] = $dist[$vertex];
    }

    $dist[$start] = 0;
    $queue[$start] = 0;

    while (!empty($queue)) {
        asort($queue);
        $u = key($queue);
        unset($queue[$u]);

        foreach ($graph[$u] as $neighbor => $weight) {
            $alt = $dist[$u] + $weight;
            if ($alt < $dist[$neighbor]) {
                $dist[$neighbor] = $alt;
                $prev[$neighbor] = $u;
                $queue[$neighbor] = $alt;
            }
        }
    }

    return [$dist, $prev];
}

function find_nearest_service_provider($user_id, $service_type) {
    $conn = new mysqli('localhost', 'username', 'password', 'database');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE user_id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    $user_location = $user_result->fetch_assoc();

    $user_lat = $user_location['latitude'];
    $user_lon = $user_location['longitude'];

    $providers = get_service_providers($user_lat, $user_lon, $service_type);

    $graph = [];
    foreach ($providers as $provider_id => $provider) {
        $graph[$provider_id] = [];
        foreach ($providers as $other_provider_id => $other_provider) {
            if ($provider_id != $other_provider_id) {
                $distance = haversine_distance($provider['latitude'], $provider['longitude'], $other_provider['latitude'], $other_provider['longitude']);
                $graph[$provider_id][$other_provider_id] = $distance;
            }
        }
    }

    $graph[0] = [];
    foreach ($providers as $provider_id => $provider) {
        $graph[0][$provider_id] = $provider['distance'];
    }

    list($distances, $previous) = dijkstra_algorithm(0, $graph);

    $nearest_provider_id = array_search(min($distances), $distances);
    $nearest_provider = $providers[$nearest_provider_id];

    $conn->close();

    return $nearest_provider;
}

