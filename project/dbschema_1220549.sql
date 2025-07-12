-- -----------------------------------------------------
-- Table 1: users
-- -----------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('customer', 'owner', 'manager') NOT NULL,
    national_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(20),
    dob DATE,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(20),
    telephone VARCHAR(20),
    photo VARCHAR(255),
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    bank_name VARCHAR(100),
    bank_branch VARCHAR(100),
    account_number VARCHAR(50)
);

-- -----------------------------------------------------
-- Table 2: flats
-- -----------------------------------------------------
CREATE TABLE flats (
    flat_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    reference_number VARCHAR(6) UNIQUE,
    location VARCHAR(100),
    address TEXT,
    rent_cost DECIMAL(10,2),
    available_from DATE,
    available_to DATE,
    bedrooms INT,
    bathrooms INT,
    size FLOAT,
    rent_conditions TEXT,
    has_heating BOOLEAN,
    has_ac BOOLEAN,
    has_access_control BOOLEAN,
    has_parking BOOLEAN,
    has_backyard ENUM('none', 'individual', 'shared'),
    has_playground BOOLEAN,
    has_storage BOOLEAN,
    status ENUM('pending', 'approved', 'rented') DEFAULT 'pending',
    approved_by INT,
    approved_date DATETIME,
    FOREIGN KEY (owner_id) REFERENCES users(user_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id)
);

-- -----------------------------------------------------
-- Table 3: flat_photos
-- -----------------------------------------------------
CREATE TABLE flat_photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    image_path VARCHAR(255),
    caption VARCHAR(100),
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id)
);

-- -----------------------------------------------------
-- Table 4: flat_marketing
-- -----------------------------------------------------
CREATE TABLE flat_marketing (
    marketing_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    title VARCHAR(100),
    description TEXT,
    link VARCHAR(255),
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id)
);

-- -----------------------------------------------------
-- Table 5: preview_slots
-- -----------------------------------------------------
CREATE TABLE preview_slots (
    slot_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    day DATE,
    time TIME,
    phone VARCHAR(20),
    is_booked BOOLEAN DEFAULT FALSE,
    booked_by INT,
    status ENUM('available', 'booked', 'expired') DEFAULT 'available',
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (booked_by) REFERENCES users(user_id)
);

-- -----------------------------------------------------
-- Table 6: messages
-- -----------------------------------------------------
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    body TEXT,
    date_sent DATETIME,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
);

-- -----------------------------------------------------
-- Table 7: rentals
-- -----------------------------------------------------
CREATE TABLE rentals (
    rental_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    customer_id INT NOT NULL,
    start_date DATE,
    end_date DATE,
    total_cost DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'past') DEFAULT 'pending',
    payment_confirmed BOOLEAN DEFAULT FALSE,
    created_at DATETIME,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (customer_id) REFERENCES users(user_id)
);

-- -----------------------------------------------------
-- Table 8: payments
-- -----------------------------------------------------
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    rental_id INT NOT NULL,
    card_number CHAR(9),
    card_holder_name VARCHAR(100),
    card_expiry CHAR(5),
    paid_at DATETIME,
    FOREIGN KEY (rental_id) REFERENCES rentals(rental_id)
);

-- -----------------------------------------------------
-- Table 9: shopping_basket
-- -----------------------------------------------------
CREATE TABLE shopping_basket (
    basket_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    flat_id INT NOT NULL,
    rental_id INT,
    created_at DATETIME,
    FOREIGN KEY (customer_id) REFERENCES users(user_id),
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (rental_id) REFERENCES rentals(rental_id)
);

-- -----------------------------------------------------
-- Table 10: sort_preferences
-- -----------------------------------------------------
CREATE TABLE sort_preferences (
    pref_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    table_name VARCHAR(50),
    `column` VARCHAR(50),
    `direction` ENUM('asc', 'desc'),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
