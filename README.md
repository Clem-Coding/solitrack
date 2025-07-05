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

### Prerequisites :

- Docker and Docker Compose installed

- For Windows users:
  It is highly recommended to place the project inside WSL (Windows Subsystem for Linux) to avoid major slowdowns caused by filesystem performance.

### Step 1: Clone the Repository

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
