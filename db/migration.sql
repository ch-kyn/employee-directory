-- create database
-- use NOT EXISTS keyword to avoid attempting to duplicate existing database/schemas
CREATE DATABASE IF NOT EXISTS corax;

USE corax; -- select database

-- set up 'Admin' table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- set up 'Employees' table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    photo_path VARCHAR(255) DEFAULT NULL
);