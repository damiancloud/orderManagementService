## Getting Started

To start the project, follow these steps:

1. Run the following command to build the Docker container and install dependencies:

```bash
make start
```

2. Start the Docker container:

```bash
make up
```

Access the container's console:

```bash
make console
```

3. Run migration
In container console:
```bash
root@1860ba9e5f53:/var/www# $ php bin/console doctrine:migrations:migrate
```