-- Database Schema for TOPSIS E-Wallet Selection

-- Table: Criteria
CREATE TABLE IF NOT EXISTS criteria (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    weight DECIMAL(10,4) NOT NULL,
    type VARCHAR(10) NOT NULL CHECK(type IN ('benefit', 'cost')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: Alternatives (E-Wallets)
CREATE TABLE IF NOT EXISTS alternatives (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: Ratings (values for each alternative-criteria pair)
CREATE TABLE IF NOT EXISTS ratings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    alternative_id INTEGER NOT NULL,
    criteria_id INTEGER NOT NULL,
    value DECIMAL(10,4) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (alternative_id) REFERENCES alternatives(id) ON DELETE CASCADE,
    FOREIGN KEY (criteria_id) REFERENCES criteria(id) ON DELETE CASCADE,
    UNIQUE(alternative_id, criteria_id)
);

-- Table: Calculation Results
CREATE TABLE IF NOT EXISTS calculation_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    alternative_id INTEGER NOT NULL,
    positive_distance DECIMAL(10,6),
    negative_distance DECIMAL(10,6),
    preference_value DECIMAL(10,6),
    ranking INTEGER,
    calculation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (alternative_id) REFERENCES alternatives(id) ON DELETE CASCADE
);

-- Insert Default Criteria from the research paper
INSERT OR IGNORE INTO criteria (code, name, weight, type) VALUES
('C1', 'Kemudahan Penggunaan', 0.2567, 'benefit'),
('C2', 'Keamanan Transaksi', 0.1567, 'benefit'),
('C3', 'Kecepatan Proses Transaksi', 0.0900, 'benefit'),
('C4', 'Kelengkapan Fitur', 0.0400, 'benefit'),
('C5', 'Biaya Admin Transaksi', 0.4567, 'cost');

-- Insert Default Alternatives (E-Wallets) from the research paper
INSERT OR IGNORE INTO alternatives (code, name, description) VALUES
('A1', 'Dana', 'E-Wallet Dana - Digital Payment Solution'),
('A2', 'OVO', 'E-Wallet OVO - Lippo Group Digital Payment'),
('A3', 'GoPay', 'E-Wallet GoPay - Gojek Digital Payment'),
('A4', 'ShopeePay', 'E-Wallet ShopeePay - Shopee Digital Payment'),
('A5', 'LinkAja', 'E-Wallet LinkAja - BUMN Digital Payment'),
('A6', 'Flip', 'E-Wallet Flip - Transfer Gratis'),
('A7', 'Kantong Saya', 'E-Wallet Kantong Saya (My Pocket)'),
('A8', 'Dokumen', 'E-Wallet Dokumen'),
('A9', 'i.saku', 'E-Wallet i.saku (i.pocket)');

-- Insert Default Ratings from the research paper
-- Alternative A1: Dana (C1:4, C2:3, C3:3, C4:2, C5:2500)
INSERT OR IGNORE INTO ratings (alternative_id, criteria_id, value) VALUES
(1, 1, 4), (1, 2, 3), (1, 3, 3), (1, 4, 2), (1, 5, 2500),
-- Alternative A2: OVO (C1:3, C2:3, C3:2, C4:2, C5:3000)
(2, 1, 3), (2, 2, 3), (2, 3, 2), (2, 4, 2), (2, 5, 3000),
-- Alternative A3: GoPay (C1:3, C2:2, C3:3, C4:3, C5:2000)
(3, 1, 3), (3, 2, 2), (3, 3, 3), (3, 4, 3), (3, 5, 2000),
-- Alternative A4: ShopeePay (C1:3, C2:3, C3:3, C4:1, C5:5000)
(4, 1, 3), (4, 2, 3), (4, 3, 3), (4, 4, 1), (4, 5, 5000),
-- Alternative A5: LinkAja (C1:2, C2:2, C3:3, C4:2, C5:7000)
(5, 1, 2), (5, 2, 2), (5, 3, 3), (5, 4, 2), (5, 5, 7000),
-- Alternative A6: Flip (C1:3, C2:2, C3:2, C4:1, C5:4000)
(6, 1, 3), (6, 2, 2), (6, 3, 2), (6, 4, 1), (6, 5, 4000),
-- Alternative A7: My Pocket (C1:3, C2:2, C3:3, C4:2, C5:3500)
(7, 1, 3), (7, 2, 2), (7, 3, 3), (7, 4, 2), (7, 5, 3500),
-- Alternative A8: Dokumen (C1:2, C2:2, C3:3, C4:3, C5:2500)
(8, 1, 2), (8, 2, 2), (8, 3, 3), (8, 4, 3), (8, 5, 2500),
-- Alternative A9: i.pocket (C1:3, C2:3, C3:2, C4:2, C5:4500)
(9, 1, 3), (9, 2, 3), (9, 3, 2), (9, 4, 2), (9, 5, 4500);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_ratings_alternative ON ratings(alternative_id);
CREATE INDEX IF NOT EXISTS idx_ratings_criteria ON ratings(criteria_id);
CREATE INDEX IF NOT EXISTS idx_calculation_results_ranking ON calculation_results(ranking);
