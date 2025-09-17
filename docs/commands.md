# Symfony Commands Cheat Sheet

### Step 1: Access the PHP Container

```bash
docker exec -it php_solitrack bash
```

## 1. Create an Entity

```bash
php bin/console make:entity
```

## 2. Create a Migration

```bash
php bin/console make:migration
```

## 3. Apply the Migration

```bash
php bin/console doctrine:migrations:migrate
```

## 4. Create a FormType

with name followed by "Type" example: "TaskType"

```bash
php bin/console make:form
```
