-- Simple schema for a student apartment-finding platform
-- PostgreSQL-compatible

-- Users (students, landlords, admins)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(320) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(200),
    role VARCHAR(20) NOT NULL DEFAULT 'student' CHECK (role IN ('student','landlord','admin')),
    phone VARCHAR(30),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Listings (apartments)
CREATE TABLE listings (
    id SERIAL PRIMARY KEY,
    owner_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price_per_month NUMERIC(10,2) NOT NULL CHECK (price_per_month >= 0),
    bedrooms SMALLINT CHECK (bedrooms >= 0),
    bathrooms SMALLINT CHECK (bathrooms >= 0),
    sqft INT CHECK (sqft >= 0),
    available_from DATE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    -- Address fields
    street VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    latitude DOUBLE PRECISION,
    longitude DOUBLE PRECISION,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Quick indexes for search filtering
CREATE INDEX idx_listings_price ON listings(price_per_month);
CREATE INDEX idx_listings_location ON listings(latitude, longitude);
CREATE INDEX idx_listings_city ON listings(city);

-- Amenities master list
CREATE TABLE amenities (
    id SERIAL PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL
);

-- Mapping table for many-to-many between listings and amenities
CREATE TABLE listing_amenities (
    listing_id INT NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
    amenity_id INT NOT NULL REFERENCES amenities(id) ON DELETE CASCADE,
    PRIMARY KEY (listing_id, amenity_id)
);

-- Images for listings
CREATE TABLE listing_images (
    id SERIAL PRIMARY KEY,
    listing_id INT NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
    url TEXT NOT NULL,
    caption VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

CREATE INDEX idx_listing_images_primary ON listing_images(listing_id, is_primary);

-- Favorites / saved listings by users
CREATE TABLE favorites (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    listing_id INT NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    PRIMARY KEY (user_id, listing_id)
);

-- Inquiries / messages related to a listing
CREATE TABLE inquiries (
    id SERIAL PRIMARY KEY,
    listing_id INT REFERENCES listings(id) ON DELETE SET NULL,
    from_user_id INT NOT NULL REFERENCES users(id) ON DELETE SET NULL,
    to_user_id INT REFERENCES users(id) ON DELETE SET NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'open' CHECK (status IN ('open','closed','archived')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Reviews left by users about listings or landlords
CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    listing_id INT REFERENCES listings(id) ON DELETE SET NULL,
    reviewer_id INT NOT NULL REFERENCES users(id) ON DELETE SET NULL,
    rating SMALLINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    comment TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Short-term bookings/reservations (optional)
CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    listing_id INT NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
    renter_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','confirmed','cancelled','completed')),
    total_price NUMERIC(10,2) CHECK (total_price >= 0),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    CHECK (end_date >= start_date)
);

-- Trigger function to update updated_at timestamps (Postgres)
CREATE OR REPLACE FUNCTION touch_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER users_updated_at BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION touch_updated_at();

CREATE TRIGGER listings_updated_at BEFORE UPDATE ON listings
FOR EACH ROW EXECUTE FUNCTION touch_updated_at();

-- Example seed amenities (optional)
INSERT INTO amenities (slug, name) VALUES
  ('wifi','Wi-Fi'),
  ('laundry','On-site Laundry'),
  ('parking','Parking'),
  ('furnished','Furnished')
ON CONFLICT DO NOTHING;