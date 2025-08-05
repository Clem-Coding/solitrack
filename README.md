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
