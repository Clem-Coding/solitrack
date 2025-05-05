# Soli'Track - Internal Logistics Tracking Tool

Soli'Track is the internal tool I'm developing for my final Web Development diploma project. Built with Symfony, it is designed for Les Fourmis Soli’terre, a non-profit organization based in Plouasne (22), Brittany.

## About Les Fourmis Soli’terre

The organization is dedicated to solidarity and environmental protection in rural areas. They manage a recycled goods shop, giving objects a second life while fostering social connections among local residents. With the support of 35 volunteers, the shop is evolving into a community hub offering eco-friendly workshops and a café.

## Project Goals

Soli'Track aims to streamline the management of:

- Weighing incoming and outgoing items.

- Tracking sales.

- Organizing volunteer schedules.

- Providing data and statistics for the shop manager.

## How to Install Locally

Follow these steps to set up the Soli'Track project on your local machine:
Prerequisites

Before starting, ensure you have the following installed:

    PHP 8.0+: Soli'Track requires PHP 8.0 or higher.

    Composer: The PHP dependency manager.

    Symfony CLI (optional, but recommended for managing Symfony projects).

    MySQL or PostgreSQL: For database management. You can use XAMPP for a local MySQL server or any other database management system.

    Node.js and npm: Required for managing frontend dependencies (if applicable).

### Step 1: Clone the Repository

### Step 2: Install Backend Dependencies

Run the following command to install the PHP dependencies:

`composer install`

### Step 3: Set Up the Environment

Create a .env file from the example configuration file provided:

`cp .env.example .env`

Edit the .env file to match your local setup (e.g., database credentials, mailer configuration, etc.).

### Step 4: Create the Database

Once your environment is set up, create the database by running the following command:

`php bin/console doctrine:database:create`

Then, run the migrations to set up the database schema:

`php bin/console doctrine:migrations:migrate`

### Step 5: Create Admin and Volunteer Users

Manually create users with their roles since there are no fixtures. Use the following commands to create a user with ROLE_ADMIN and a user with ROLE_VOLUNTEER_PLUS. By default, users have limited access.

`php bin/console app:create-user email@example.com ROLE_ADMIN`
`php bin/console app:create-user volunteer@example.com ROLE_VOLUNTEER_PLUS`

`symfony server:start`
