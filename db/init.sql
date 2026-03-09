-- =============================================
-- Football Pitch Reservation System – Schema
-- =============================================

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pitches (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    price_per_hour DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reservations (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    pitch_id INT REFERENCES pitches(id) ON DELETE CASCADE,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Prevent overlapping reservations on the same pitch/date
CREATE UNIQUE INDEX idx_no_overlap
    ON reservations (pitch_id, reservation_date, start_time);

-- =============================================
-- Seed data – 4 sample pitches
-- =============================================

INSERT INTO pitches (name, location, price_per_hour) VALUES
    ('Pitch A – Grand Stadium',  'Zone 1, Rabat',      150.00),
    ('Pitch B – City Arena',     'Hay Riad, Rabat',     120.00),
    ('Pitch C – Ocean Field',    'Corniche, Casablanca', 200.00),
    ('Pitch D – Atlas Ground',   'Guéliz, Marrakech',   180.00);
