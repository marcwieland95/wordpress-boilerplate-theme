# Bootstrap WP Theme

Theme powered by WordPress

# Setup

We're using Herd for the local environment.

```
make install
```

We're using DBngin to manage the local database. It's a GUI for MySQL.
DB-Name: XYZ -> Sample for local is in .env.example

# Development

- `make watch` Compile files and start the local server
- `make build` Compile a deployable build

### Database
- `make db_dump` Create a database dump

### Coding Standards

We're using Laravel Pint to keep the code clean and consistent.

```
make coding-standard
```

And to automatically fix the issues:

```
make coding-standard-fix
```

## Deployment

Gitlab CI/CD is set up to deploy to the production server (cyon). It needs to be triggered manually in the interface.
