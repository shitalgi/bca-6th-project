<?php
// distance_utils.php

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

function sort_providers_by_distance($providers, $user_lat, $user_lon) {
    foreach ($providers as &$provider) {
        $provider['distance'] = haversine_distance(
            $user_lat,
            $user_lon,
            $provider['latitude'],
            $provider['longitude']
        );
    }
    usort($providers, function($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    return $providers;
}
?>
