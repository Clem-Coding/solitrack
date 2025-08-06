# Soli'Track - Internal Logistics Tracking Tool

Soli'Track is an internal tool developed with Symfony for Les Fourmis Soli’Terre, a non-profit organization based in Plouasne (22), Brittany.

## About Les Fourmis Soli’terre

This organization is committed to environmental protection and solidarity in rural areas. They operate a solidarity recycling shop that gives second life to used items while fostering social ties among local residents. Supported by 35 volunteers, the shop is evolving into a community hub offering eco-friendly workshops and a café.

## Project Goals

Soli'Track aims to streamline the management of:

- Weighing incoming and outgoing items.

- Tracking sales.

- Organizing volunteer schedules.

- Providing data and statistics for the shop manager.

## How to Install Locally

### Prerequisites :

- Docker and Docker Compose installed

- ⚠️ For Windows users:
  It is highly recommended to place the project inside WSL (Windows Subsystem for Linux) to avoid major slowdowns caused by filesystem performance.

### Step 1: Clone the Repository

### Step 2: Copy environment example file.

```bash
cp .env.example .env
```

Open the .env file and update the DATABASE_URL with your personal settings.

### Step 2: Starting the project

```bash
docker-compose up -d
```

### Step 3: Install dependencies

```bash
docker exec -it php composer install
```

### Step 3: Apply migrations
```bash
docker exec -it php_solitrack php bin/console doctrine:migrations:migrate
```

### Step 4: Load fixtures
```bash
docker exec -it php_solitrack php bin/console doctrine:fixtures:load
```

### Step 5: Create users

You have two options to test the app:

1. **Register a new user:**  
  Go to [`http://localhost:8080/register`](http://localhost:8080/register) and create a user account.  
  > **Note:** You’ll then need to manually edit the user’s role in the database (e.g. set ["ROLE_ADMIN"]) if you want full access.

2. **Use predefined test users:**  

To work locally, you can use the following test accounts (password: `password` for all):

| Email                      | Role(s)              | Access                                                             |
|----------------------------|----------------------|--------------------------------------------------------------------|
| admin@solitrack.fr         | ROLE_ADMIN           | Full access to all features                                        |
| benevole-plus@solitrack.fr | ROLE_VOLUNTEER_PLUS  | Entries, sales, statistics, and cash management pages              |
| user@solitrack.fr          | *(none)*             | “My Account” and “My Schedule” pages                               |



### Access URLs

- Web app: [`http://localhost:8080/`](http://localhost:8080/)
- phpMyAdmin (database management): [`http://localhost:8081/`](http://localhost:8081/)
- Mail interface (Maildev): [`http://localhost:8025/`](http://localhost:8025/)

### Consuming mails (Symfony Messenger)

To process async messages in the queue:

```bash
docker exec -it php php bin/console messenger:consume async
```

### Notes

- No built-in SASS compiler included
- Use Live Sass Compiler (VSCode) or any other SASS compilation tool depending on your setup
