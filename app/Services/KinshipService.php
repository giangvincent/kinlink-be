<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Relationship;

class KinshipService
{
    /**
     * Retrieve the relational path between two people using an in-memory breadth-first search.
     *
     * @return array<int, array{person_id:int, via_relationship_id:int|null}>
     */
    public function relationshipPath(Person $from, Person $to): array
    {
        if ($from->is($to)) {
            return [
                [
                    'person_id' => $from->getKey(),
                    'via_relationship_id' => null,
                ],
            ];
        }

        $familyId = $from->family_id;

        $relationships = Relationship::query()
            ->where('family_id', $familyId)
            ->get(['id', 'person_id_a', 'person_id_b']);

        $adjacency = [];
        foreach ($relationships as $relationship) {
            $adjacency[$relationship->person_id_a][] = [$relationship->person_id_b, $relationship->id];
            $adjacency[$relationship->person_id_b][] = [$relationship->person_id_a, $relationship->id];
        }

        $queue = new \SplQueue();
        $queue->enqueue($from->getKey());

        $visited = [
            $from->getKey() => ['prev' => null, 'relationship_id' => null],
        ];

        while (! $queue->isEmpty()) {
            $current = $queue->dequeue();

            if ($current === $to->getKey()) {
                break;
            }

            foreach ($adjacency[$current] ?? [] as [$neighbor, $relationshipId]) {
                if (isset($visited[$neighbor])) {
                    continue;
                }

                $visited[$neighbor] = [
                    'prev' => $current,
                    'relationship_id' => $relationshipId,
                ];

                $queue->enqueue($neighbor);
            }
        }

        if (! isset($visited[$to->getKey()])) {
            return [];
        }

        $path = [];
        $current = $to->getKey();

        while ($current !== null) {
            $path[] = [
                'person_id' => $current,
                'via_relationship_id' => $visited[$current]['relationship_id'] ?? null,
            ];

            $current = $visited[$current]['prev'] ?? null;
        }

        return array_reverse($path);
    }

    /**
     * Provides a human readable relationship label from the path length.
     */
    public function relationshipLabel(array $path): ?string
    {
        $length = count($path);

        return match (true) {
            $length <= 1 => 'Self',
            $length === 2 => 'Direct relation',
            $length === 3 => 'Close relation',
            $length === 4 => 'Extended relation',
            default => 'Distant relation',
        };
    }
}
