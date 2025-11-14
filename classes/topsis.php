<?php
/**
 * TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)
 * Implementation Class
 */

class TOPSIS {
    private $db;
    private $alternatives = [];
    private $criteria = [];
    private $decisionMatrix = [];
    private $normalizedMatrix = [];
    private $weightedNormalizedMatrix = [];
    private $idealPositive = [];
    private $idealNegative = [];
    private $distances = [];
    private $preferences = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Load data from database
     */
    public function loadData() {
        // Load criteria
        $stmt = $this->db->query("SELECT * FROM criteria ORDER BY code");
        $this->criteria = $stmt->fetchAll();

        if (empty($this->criteria)) {
            throw new Exception("No criteria found. Please add criteria first.");
        }

        // Load alternatives
        $stmt = $this->db->query("SELECT * FROM alternatives ORDER BY code");
        $this->alternatives = $stmt->fetchAll();

        if (empty($this->alternatives)) {
            throw new Exception("No alternatives found. Please add alternatives first.");
        }

        // Load decision matrix (ratings)
        foreach ($this->alternatives as $alt) {
            $this->decisionMatrix[$alt['id']] = [];
            foreach ($this->criteria as $crit) {
                $stmt = $this->db->query(
                    "SELECT value FROM ratings WHERE alternative_id = ? AND criteria_id = ?",
                    [$alt['id'], $crit['id']]
                );
                $rating = $stmt->fetch();
                $this->decisionMatrix[$alt['id']][$crit['id']] = $rating ? floatval($rating['value']) : 0;
            }
        }

        // Validate that all alternatives have ratings for all criteria
        foreach ($this->alternatives as $alt) {
            if (count($this->decisionMatrix[$alt['id']]) !== count($this->criteria)) {
                throw new Exception("Alternative {$alt['name']} is missing ratings for some criteria.");
            }
        }
    }

    /**
     * Step 1: Normalize the decision matrix
     */
    private function normalizeMatrix() {
        // Calculate the sum of squares for each criterion
        $sumSquares = [];
        foreach ($this->criteria as $crit) {
            $sumSquares[$crit['id']] = 0;
            foreach ($this->alternatives as $alt) {
                $value = $this->decisionMatrix[$alt['id']][$crit['id']];
                $sumSquares[$crit['id']] += pow($value, 2);
            }
            $sumSquares[$crit['id']] = sqrt($sumSquares[$crit['id']]);
        }

        // Normalize each value
        foreach ($this->alternatives as $alt) {
            $this->normalizedMatrix[$alt['id']] = [];
            foreach ($this->criteria as $crit) {
                $value = $this->decisionMatrix[$alt['id']][$crit['id']];
                $normalized = $sumSquares[$crit['id']] != 0
                    ? $value / $sumSquares[$crit['id']]
                    : 0;
                $this->normalizedMatrix[$alt['id']][$crit['id']] = $normalized;
            }
        }
    }

    /**
     * Step 2: Calculate weighted normalized decision matrix
     */
    private function calculateWeightedMatrix() {
        foreach ($this->alternatives as $alt) {
            $this->weightedNormalizedMatrix[$alt['id']] = [];
            foreach ($this->criteria as $crit) {
                $normalized = $this->normalizedMatrix[$alt['id']][$crit['id']];
                $weight = floatval($crit['weight']);
                $this->weightedNormalizedMatrix[$alt['id']][$crit['id']] = $normalized * $weight;
            }
        }
    }

    /**
     * Step 3: Determine ideal positive and negative solutions
     */
    private function determineIdealSolutions() {
        foreach ($this->criteria as $crit) {
            $values = [];
            foreach ($this->alternatives as $alt) {
                $values[] = $this->weightedNormalizedMatrix[$alt['id']][$crit['id']];
            }

            // For benefit criteria: max is positive ideal, min is negative ideal
            // For cost criteria: min is positive ideal, max is negative ideal
            if ($crit['type'] === 'benefit') {
                $this->idealPositive[$crit['id']] = max($values);
                $this->idealNegative[$crit['id']] = min($values);
            } else {
                $this->idealPositive[$crit['id']] = min($values);
                $this->idealNegative[$crit['id']] = max($values);
            }
        }
    }

    /**
     * Step 4: Calculate separation measures (distances)
     */
    private function calculateDistances() {
        foreach ($this->alternatives as $alt) {
            $positiveSum = 0;
            $negativeSum = 0;

            foreach ($this->criteria as $crit) {
                $value = $this->weightedNormalizedMatrix[$alt['id']][$crit['id']];

                // Distance from positive ideal
                $positiveSum += pow($value - $this->idealPositive[$crit['id']], 2);

                // Distance from negative ideal
                $negativeSum += pow($value - $this->idealNegative[$crit['id']], 2);
            }

            $this->distances[$alt['id']] = [
                'positive' => sqrt($positiveSum),
                'negative' => sqrt($negativeSum)
            ];
        }
    }

    /**
     * Step 5: Calculate relative closeness to ideal solution (preference value)
     */
    private function calculatePreferences() {
        foreach ($this->alternatives as $alt) {
            $dPositive = $this->distances[$alt['id']]['positive'];
            $dNegative = $this->distances[$alt['id']]['negative'];

            // Avoid division by zero
            $denominator = $dPositive + $dNegative;
            $preference = $denominator != 0 ? $dNegative / $denominator : 0;

            $this->preferences[$alt['id']] = $preference;
        }

        // Sort by preference value (descending)
        arsort($this->preferences);
    }

    /**
     * Execute TOPSIS calculation
     */
    public function calculate() {
        try {
            $this->loadData();
            $this->normalizeMatrix();
            $this->calculateWeightedMatrix();
            $this->determineIdealSolutions();
            $this->calculateDistances();
            $this->calculatePreferences();

            return $this->saveResults();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Save calculation results to database
     */
    private function saveResults() {
        try {
            $this->db->beginTransaction();

            // Clear previous results
            $this->db->query("DELETE FROM calculation_results");

            // Insert new results
            $ranking = 1;
            foreach ($this->preferences as $altId => $preference) {
                $this->db->query(
                    "INSERT INTO calculation_results
                    (alternative_id, positive_distance, negative_distance, preference_value, ranking)
                    VALUES (?, ?, ?, ?, ?)",
                    [
                        $altId,
                        $this->distances[$altId]['positive'],
                        $this->distances[$altId]['negative'],
                        $preference,
                        $ranking++
                    ]
                );
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get calculation results
     */
    public function getResults() {
        $stmt = $this->db->query(
            "SELECT cr.*, a.code, a.name, a.description
            FROM calculation_results cr
            JOIN alternatives a ON cr.alternative_id = a.id
            ORDER BY cr.ranking ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Get detailed calculation steps for display
     */
    public function getCalculationDetails() {
        return [
            'alternatives' => $this->alternatives,
            'criteria' => $this->criteria,
            'decision_matrix' => $this->decisionMatrix,
            'normalized_matrix' => $this->normalizedMatrix,
            'weighted_matrix' => $this->weightedNormalizedMatrix,
            'ideal_positive' => $this->idealPositive,
            'ideal_negative' => $this->idealNegative,
            'distances' => $this->distances,
            'preferences' => $this->preferences
        ];
    }

    /**
     * Export results to array
     */
    public function exportResults() {
        $results = [];
        $ranking = 1;

        foreach ($this->preferences as $altId => $preference) {
            $alt = array_filter($this->alternatives, function($a) use ($altId) {
                return $a['id'] == $altId;
            });
            $alt = reset($alt);

            $results[] = [
                'ranking' => $ranking++,
                'code' => $alt['code'],
                'name' => $alt['name'],
                'positive_distance' => $this->distances[$altId]['positive'],
                'negative_distance' => $this->distances[$altId]['negative'],
                'preference_value' => $preference
            ];
        }

        return $results;
    }
}
