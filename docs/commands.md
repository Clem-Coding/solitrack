# Symfony Commands Cheat Sheet

## Commands need top be run inside the PHP container

### Step 1: Access the PHP Container

```bash
docker exec -it php_solitrack bash
```

> **Note:** Type `exit` to leave the container.

### 1. Create an Entity

```bash
php bin/console make:entity
```

### 2. Create a Migration

```bash
php bin/console make:migration
```

### 3. Apply the Migration

```bash
php bin/console doctrine:migrations:migrate
```

**Note:** Type `exit` to leave the container.

## Commands That Can Be Run Locally

### 1. Create a Controller

```bash
php bin/console make:controller
```

### 2. Create a FormType

with name followed by "Type" example: "TaskType"

```bash
php bin/console make:form
```
