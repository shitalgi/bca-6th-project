<?php
function dijkstra($graph, $source) {
    $distances = [];
    $previous = [];
    $queue = new SplPriorityQueue();

    foreach ($graph as $vertex => $neighbors) {
        $distances[$vertex] = INF;
        $previous[$vertex] = null;
        $queue->insert($vertex, INF);
    }

    $distances[$source] = 0;
    $queue->insert($source, 0);

    while (!$queue->isEmpty()) {
        $u = $queue->extract();

        foreach ($graph[$u] as $neighbor => $cost) {
            $alt = $distances[$u] + $cost;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $u;
                $queue->insert($neighbor, $alt);
            }
        }
    }

    return [$distances, $previous];
}
?>
