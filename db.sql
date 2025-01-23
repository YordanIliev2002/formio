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
