<?php

namespace App\Services;

class RoutingService
{
    /**
     * A lightweight global maritime waypoint graph to prevent crossing landmasses.
     */
    const WAYPOINTS = [
        // Mediterranean & Black Sea
        'suez' => [31.2, 32.3],
        'mediterranean_east' => [34.0, 27.0],
        'crete_west' => [35.0, 23.0],
        'crete_east' => [35.0, 27.0],
        'aegean_sea' => [37.0, 25.0],
        'dardanelles' => [40.1, 26.2],
        'marmara_sea' => [40.7, 27.5],
        'bosporus' => [41.1, 29.0],
        'black_sea' => [43.0, 34.0],
        'mediterranean_west' => [37.0, 5.0],
        'gibraltar' => [35.9, -5.5],
        'ligurian_sea' => [43.0, 9.0],

        // North Atlantic & Europe
        'portugal_coast' => [39.0, -10.0],
        'bay_of_biscay' => [46.0, -7.0],
        'english_channel' => [50.0, -1.5],
        'north_sea' => [55.0, 3.0],
        'german_bight' => [54.2, 8.0],
        'scheldt_mouth' => [51.5, 3.2],
        'skagerrak_strait' => [58.0, 9.0],
        'kattegat_strait' => [56.5, 11.5],
        'baltic_sea' => [55.0, 15.0],
        'mid_atlantic_north' => [35.0, -40.0],

        // North America (East Coast & Gulf & Caribbean)
        'canada_east_coast' => [45.0, -60.0],
        'us_east_coast' => [35.0, -74.0],
        'chesapeake_bay_entrance' => [37.0, -75.8],
        'florida_strait' => [24.0, -80.0],
        'bahamas_east' => [23.0, -70.0],
        'gulf_of_mexico' => [25.0, -88.0],
        'caribbean_sea_west' => [15.0, -75.0],
        'caribbean_sea_east' => [15.0, -62.0],
        'windward_passage' => [20.0, -73.8],
        'panama_canal_east' => [9.3, -79.9],

        // Central & South America (West & East)
        'panama_canal_west' => [8.9, -79.5],
        'costa_rica_west' => [9.0, -85.0],
        'mexico_south_west' => [15.0, -96.0],
        'mexico_west_coast' => [20.0, -106.0],
        'baja_california_south' => [22.5, -110.0],
        'us_west_coast_south' => [32.0, -118.0],
        'us_west_coast_mid' => [37.0, -123.0],
        'us_west_coast_north' => [45.0, -125.0],
        'hawaii' => [21.0, -157.0],
        'mid_pacific_north' => [30.0, -165.0],
        'mid_pacific_south' => [-20.0, -140.0],
        'ecuador_coast' => [-1.0, -82.0],
        'peru_coast' => [-10.0, -79.0],
        'chile_coast' => [-30.0, -73.0],
        'strait_of_magellan_west' => [-53.0, -75.0],
        'strait_of_magellan_east' => [-52.5, -68.0],
        'argentina_coast' => [-40.0, -60.0],
        'brazil_coast_south' => [-25.0, -45.0],
        'east_brazil' => [-5.0, -33.0],
        'north_brazil_coast' => [5.0, -45.0],

        // South Atlantic & West Africa
        'mid_atlantic_south' => [-10.0, -20.0],
        'senegal_coast' => [15.0, -18.0],
        'west_africa' => [5.0, -10.0],
        'angola_coast' => [-10.0, 10.0],
        'cape_of_good_hope' => [-34.5, 18.5],

        // Indian Ocean & East Africa
        'madagascar_west' => [-20.0, 40.0],
        'madagascar_east' => [-20.0, 50.0],
        'horn_of_africa' => [12.0, 51.0],
        'bab_el_mandeb' => [12.5, 43.5],
        'red_sea_mid' => [21.0, 38.0],
        'arabian_sea' => [15.0, 65.0],
        'persian_gulf' => [26.0, 52.0],
        'strait_of_hormuz' => [26.5, 56.5],
        'gulf_of_oman' => [24.0, 59.0],
        'indian_ocean_mid' => [-10.0, 75.0],
        'sri_lanka_south' => [5.5, 80.0],
        'bay_of_bengal' => [12.0, 88.0],

        // East Asia & SE Asia
        'yangtze_estuary' => [31.2, 122.5],
        'malacca_strait_north' => [6.0, 95.0],
        'malacca_strait_mid' => [3.0, 101.0],
        'malacca_strait_south' => [1.2, 103.5],
        'singapore_strait_east' => [1.3, 104.2],
        'natuna_sea' => [4.0, 108.0],
        'sunda_strait' => [-6.0, 105.5],
        'south_java_sea' => [-8.0, 115.0],
        'timor_sea' => [-10.0, 125.0],
        'arafura_sea' => [-10.0, 138.0],
        'torres_strait' => [-10.0, 142.0],
        'coral_sea' => [-15.0, 150.0],
        'tasman_sea' => [-40.0, 160.0],
        'australia_south' => [-38.0, 130.0],
        'australia_west' => [-30.0, 112.0],
        'south_china_sea' => [12.0, 113.0],
        'taiwan_strait' => [24.0, 119.5],
        'east_china_sea' => [30.0, 125.0],
        'japan_sea' => [38.0, 135.0],
        'luzon_strait' => [20.0, 121.0],
        'philippine_sea' => [15.0, 130.0],
        'tokyo_bay_approach' => [34.0, 140.0],
    ];

    const EDGES = [
        // Mediterranean & Black Sea Connections
        ['black_sea', 'bosporus'],
        ['bosporus', 'marmara_sea'],
        ['marmara_sea', 'dardanelles'],
        ['dardanelles', 'aegean_sea'],
        ['aegean_sea', 'crete_west'],
        ['crete_west', 'mediterranean_west'],
        ['crete_west', 'suez'],
        ['mediterranean_west', 'gibraltar'],
        ['mediterranean_west', 'suez'],
        ['mediterranean_west', 'ligurian_sea'],

        // North Atlantic & Europe Connections
        ['gibraltar', 'portugal_coast'],
        ['gibraltar', 'mid_atlantic_north'],
        ['portugal_coast', 'bay_of_biscay'],
        ['bay_of_biscay', 'english_channel'],
        ['english_channel', 'north_sea'],
        ['english_channel', 'mid_atlantic_north'],
        ['north_sea', 'skagerrak_strait'],
        ['north_sea', 'german_bight'],
        ['north_sea', 'scheldt_mouth'],
        ['german_bight', 'skagerrak_strait'],
        ['skagerrak_strait', 'kattegat_strait'],
        ['kattegat_strait', 'baltic_sea'],

        // North America (East & Caribbean & Gulf)
        ['canada_east_coast', 'us_east_coast'],
        ['us_east_coast', 'bahamas_east'],
        ['us_east_coast', 'chesapeake_bay_entrance'],
        ['us_east_coast', 'florida_strait'],
        ['florida_strait', 'gulf_of_mexico'],
        ['florida_strait', 'caribbean_sea_west'],
        ['bahamas_east', 'mid_atlantic_north'],
        ['bahamas_east', 'florida_strait'],
        ['caribbean_sea_west', 'gulf_of_mexico'],
        ['caribbean_sea_west', 'windward_passage'],
        ['windward_passage', 'bahamas_east'],
        ['caribbean_sea_west', 'caribbean_sea_east'],
        ['caribbean_sea_west', 'panama_canal_east'],

        // Panama Canal Crossing
        ['panama_canal_east', 'panama_canal_west'],

        // Americas West Coast / Pacific
        ['panama_canal_west', 'costa_rica_west'],
        ['costa_rica_west', 'mexico_south_west'],
        ['mexico_south_west', 'mexico_west_coast'],
        ['mexico_west_coast', 'baja_california_south'],
        ['baja_california_south', 'us_west_coast_south'],
        ['us_west_coast_south', 'us_west_coast_mid'],
        ['us_west_coast_mid', 'us_west_coast_north'],
        ['us_west_coast_north', 'hawaii'],
        ['us_west_coast_mid', 'hawaii'],
        ['us_west_coast_south', 'hawaii'],
        ['hawaii', 'mid_pacific_north'],

        // South America Coastal Ring
        ['panama_canal_west', 'ecuador_coast'],
        ['ecuador_coast', 'peru_coast'],
        ['peru_coast', 'chile_coast'],
        ['chile_coast', 'strait_of_magellan_west'],
        ['strait_of_magellan_west', 'strait_of_magellan_east'],
        ['strait_of_magellan_east', 'argentina_coast'],
        ['argentina_coast', 'brazil_coast_south'],
        ['brazil_coast_south', 'east_brazil'],
        ['east_brazil', 'north_brazil_coast'],
        ['north_brazil_coast', 'caribbean_sea_east'],

        // Mid/South Atlantic & Africa
        ['mid_atlantic_north', 'mid_atlantic_south'],
        ['east_brazil', 'mid_atlantic_south'],
        ['gibraltar', 'senegal_coast'],
        ['senegal_coast', 'west_africa'],
        ['west_africa', 'angola_coast'],
        ['angola_coast', 'cape_of_good_hope'],
        ['mid_atlantic_south', 'cape_of_good_hope'],

        // Indian Ocean & Suez / East Africa
        ['suez', 'red_sea_mid'],
        ['red_sea_mid', 'bab_el_mandeb'],
        ['bab_el_mandeb', 'horn_of_africa'],
        ['bab_el_mandeb', 'arabian_sea'],
        ['horn_of_africa', 'cape_of_good_hope'],
        ['horn_of_africa', 'madagascar_east'],
        ['cape_of_good_hope', 'madagascar_west'],
        ['madagascar_west', 'horn_of_africa'],
        ['cape_of_good_hope', 'madagascar_east'],
        ['madagascar_east', 'indian_ocean_mid'],
        ['indian_ocean_mid', 'sri_lanka_south'],
        ['arabian_sea', 'strait_of_hormuz'],
        ['strait_of_hormuz', 'persian_gulf'],
        ['arabian_sea', 'sri_lanka_south'],
        ['sri_lanka_south', 'bay_of_bengal'],

        // SE Asia & East Asia
        ['sri_lanka_south', 'malacca_strait_north'],
        ['bay_of_bengal', 'malacca_strait_north'],
        ['malacca_strait_north', 'malacca_strait_mid'],
        ['malacca_strait_mid', 'malacca_strait_south'],
        ['malacca_strait_south', 'singapore_strait_east'],
        ['singapore_strait_east', 'natuna_sea'],
        ['natuna_sea', 'south_china_sea'],
        ['south_china_sea', 'taiwan_strait'],
        ['south_china_sea', 'luzon_strait'],
        ['taiwan_strait', 'east_china_sea'],
        ['luzon_strait', 'east_china_sea'],
        ['luzon_strait', 'philippine_sea'],
        ['east_china_sea', 'japan_sea'],
        ['east_china_sea', 'yangtze_estuary'],
        ['east_china_sea', 'tokyo_bay_approach'],
        ['philippine_sea', 'tokyo_bay_approach'],
        ['philippine_sea', 'coral_sea'],

        // Oceania & Australia
        ['singapore_strait_east', 'sunda_strait'],
        ['sunda_strait', 'south_java_sea'],
        ['south_java_sea', 'timor_sea'],
        ['timor_sea', 'arafura_sea'],
        ['arafura_sea', 'torres_strait'],
        ['torres_strait', 'coral_sea'],
        ['coral_sea', 'tasman_sea'],
        ['tasman_sea', 'australia_south'],
        ['australia_south', 'australia_west'],
        ['australia_west', 'sunda_strait'],
        ['australia_west', 'cape_of_good_hope'],
        ['australia_south', 'mid_pacific_south'],
    ];

    /**
     * Calculates distance between two coordinates in kilometers using Haversine formula
     */
    private function haversineDistance(array $coord1, array $coord2): float
    {
        $earthRadius = 6371; // km
        
        $lat1 = deg2rad($coord1[0]);
        $lon1 = deg2rad($coord1[1]);
        $lat2 = deg2rad($coord2[0]);
        $lon2 = deg2rad($coord2[1]);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Build the adjacency list graph including dynamic origin and destination nodes
     */
    private function buildGraph(array $origin, array $destination): array
    {
        $graph = [];
        
        foreach (self::WAYPOINTS as $name => $coord) {
            $graph[$name] = [];
        }
        $graph['origin'] = [];
        $graph['destination'] = [];

        // Add standard edges
        foreach (self::EDGES as $edge) {
            $dist = $this->haversineDistance(self::WAYPOINTS[$edge[0]], self::WAYPOINTS[$edge[1]]);
            $graph[$edge[0]][$edge[1]] = $dist;
            $graph[$edge[1]][$edge[0]] = $dist;
        }

        // Connect origin to the 2 closest waypoints
        $originDistances = [];
        foreach (self::WAYPOINTS as $name => $coord) {
            $originDistances[$name] = $this->haversineDistance($origin, $coord);
        }
        asort($originDistances);
        $closestOrigins = array_slice($originDistances, 0, 1, true);
        foreach ($closestOrigins as $name => $dist) {
            $graph['origin'][$name] = $dist;
            $graph[$name]['origin'] = $dist;
        }

        // Connect destination to the 2 closest waypoints
        $destDistances = [];
        foreach (self::WAYPOINTS as $name => $coord) {
            $destDistances[$name] = $this->haversineDistance($destination, $coord);
        }
        asort($destDistances);
        $closestDests = array_slice($destDistances, 0, 1, true);
        foreach ($closestDests as $name => $dist) {
            $graph['destination'][$name] = $dist;
            $graph[$name]['destination'] = $dist;
        }

        $directDist = $this->haversineDistance($origin, $destination);
        if ($directDist < 1000) { // Reduced to 1000 to be safer against land cuts
            $graph['origin']['destination'] = $directDist;
            $graph['destination']['origin'] = $directDist;
        }

        return $graph;
    }

    /**
     * Dijkstra's Algorithm implementation
     */
    private function dijkstra(array $graph, string $source, string $target): array
    {
        $distances = [];
        $previous = [];
        $unvisited = [];

        foreach ($graph as $vertex => $edges) {
            $distances[$vertex] = INF;
            $previous[$vertex] = null;
            $unvisited[$vertex] = true;
        }

        $distances[$source] = 0;

        while (count($unvisited) > 0) {
            // Get node with smallest distance
            $minDist = INF;
            $u = null;
            foreach ($unvisited as $vertex => $val) {
                if ($distances[$vertex] <= $minDist) {
                    $minDist = $distances[$vertex];
                    $u = $vertex;
                }
            }

            if ($u === null || $u === $target) {
                break;
            }

            unset($unvisited[$u]);

            foreach ($graph[$u] as $neighbor => $weight) {
                if (isset($unvisited[$neighbor])) {
                    $alt = $distances[$u] + $weight;
                    if ($alt < $distances[$neighbor]) {
                        $distances[$neighbor] = $alt;
                        $previous[$neighbor] = $u;
                    }
                }
            }
        }

        // Reconstruct path
        $path = [];
        $u = $target;
        if ($previous[$u] !== null || $u === $source) {
            while ($u !== null) {
                array_unshift($path, $u);
                $u = $previous[$u];
            }
        }

        return $path;
    }

    /**
     * Main entry point to get the route geometry
     */
    public function findRoute(array $originCoord, array $destCoord): array
    {
        $graph = $this->buildGraph($originCoord, $destCoord);
        $pathNodes = $this->dijkstra($graph, 'origin', 'destination');

        $geometry = [];
        foreach ($pathNodes as $node) {
            if ($node === 'origin') {
                $geometry[] = $originCoord;
            } elseif ($node === 'destination') {
                $geometry[] = $destCoord;
            } else {
                $geometry[] = self::WAYPOINTS[$node];
            }
        }

        // Add some noise/curves to the path to make it look realistic (optional but nice)
        return $geometry;
    }
}
