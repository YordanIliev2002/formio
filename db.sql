CREATE DATABASE IF NOT EXISTS formio;
USE formio;

CREATE TABLE IF NOT EXISTS users(
    faculty_number VARCHAR(255) PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);