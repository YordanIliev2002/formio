CREATE DATABASE IF NOT EXISTS formio;
USE formio;

CREATE TABLE IF NOT EXISTS users(
    faculty_number VARCHAR(255) PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS forms (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    author_fn VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    form_definition JSON NOT NULL,
    FOREIGN KEY (author_fn) REFERENCES users(faculty_number)
);

CREATE TABLE IF NOT EXISTS responses (
    form_id VARCHAR(36) NOT NULL,
    author_fn VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response JSON NOT NULL,
    PRIMARY KEY (form_id, author_fn),
    FOREIGN KEY (form_id) REFERENCES forms(id),
    FOREIGN KEY (author_fn) REFERENCES users(faculty_number)
);

CREATE TABLE IF NOT EXISTS invites(
    form_id VARCHAR(36) NOT NULL,
    faculty_number VARCHAR(36) NOT NULL,
    did_submit BOOLEAN,
    PRIMARY KEY (form_id, faculty_number),
    FOREIGN KEY (form_id) REFERENCES forms(id),
    FOREIGN KEY (faculty_number) REFERENCES users(faculty_number)
);