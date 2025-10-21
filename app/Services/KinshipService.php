<?php

namespace App\Services;

use App\Models\Person;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KinshipService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    /**
     * Retrieve the relational path between two people using a recursive CTE.
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

        $results = $this->db->select(<<<SQL
            WITH RECURSIVE kinship_path AS (
                SELECT
                    r.person_id_a AS current_person_id,
                    r.person_id_b AS related_person_id,
                    r.id AS relationship_id,
                    CAST(r.person_id_a AS CHAR(1024)) AS visited,
                    1 AS depth,
                    JSON_ARRAY(JSON_OBJECT('person_id', r.person_id_a, 'via_relationship_id', NULL),
                               JSON_OBJECT('person_id', r.person_id_b, 'via_relationship_id', r.id)) AS path
                FROM relationships r
                WHERE r.family_id = :family_id
                  AND r.person_id_a = :start_id

                UNION ALL

                SELECT
                    CASE WHEN kp.related_person_id = r.person_id_a THEN r.person_id_b ELSE r.person_id_a END AS current_person_id,
                    CASE WHEN kp.related_person_id = r.person_id_a THEN r.person_id_a ELSE r.person_id_b END AS related_person_id,
                    r.id AS relationship_id,
                    CONCAT(kp.visited, ',', kp.related_person_id) AS visited,
                    kp.depth + 1 AS depth,
                    JSON_ARRAY_APPEND(
                        kp.path,
                        '$',
                        JSON_OBJECT(
                            'person_id', CASE WHEN kp.related_person_id = r.person_id_a THEN r.person_id_b ELSE r.person_id_a END,
                            'via_relationship_id', r.id
                        )
                    ) AS path
                FROM kinship_path kp
                JOIN relationships r ON r.family_id = :family_id
                    AND (r.person_id_a = kp.related_person_id OR r.person_id_b = kp.related_person_id)
                WHERE FIND_IN_SET(CASE WHEN kp.related_person_id = r.person_id_a THEN r.person_id_b ELSE r.person_id_a END, kp.visited) = 0
                  AND kp.depth < 8
            )
            SELECT kp.path
            FROM kinship_path kp
            WHERE JSON_EXTRACT(kp.path, '$[last].person_id') = :end_id
            ORDER BY kp.depth ASC
            LIMIT 1
        SQL, [
            'family_id' => $familyId,
            'start_id' => $from->getKey(),
            'end_id' => $to->getKey(),
        ]);

        if (empty($results)) {
            return [];
        }

        /** @var string $json */
        $json = $results[0]->path ?? '[]';

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
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
