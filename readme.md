# Miduner Framework

Miduner is a PHP web application framework designed to provide expressive syntax and practical tooling for building modern applications.  
The framework leverages PHP 7.4+ features such as type hints, typed properties, and arrow functions.

## Requirements

- PHP `>= 7.4.10`
- Composer

## Installation

Create a new project with Composer:

```bash
composer create-project danganh97/miduner:dev-master your-project-folder
```

## Quick Start

From your project directory:

```bash
cp .env.example .env
php hustle key:generate
php hustle config:cache
php hustle serve
```

Run the server with a custom host and port:

```bash
php hustle serve --host=192.168.1.1 --port=1997
```

Use `--open` to launch the application in your browser automatically.

## CLI Commands

List all available commands:

```bash
php hustle list
```

### Code Generation

```bash
php hustle make:command {CommandName}
php hustle make:controller {ControllerName}
php hustle make:model {ModelName}
php hustle make:request {RequestName}
php hustle make:migration --table={TableName}
```

### Application Setup

```bash
php hustle config:cache
php hustle key:generate
php hustle jwt:install
```

After generating or updating keys, refresh cached configuration:

```bash
php hustle config:cache
```

### Database Operations

```bash
php hustle migrate
php hustle migrate:rollback
php hustle db:seed
```

### Query Execution

```bash
php hustle exec:query --query="select * from users"
php hustle exec:query --query="select * from users" --test=true
```

### Routing and Interactive Tools

```bash
php hustle route:list
php hustle route:list --format=json
php hustle route:list --format=array
php hustle live:code
```

For command-specific help:

```bash
php hustle serve --help
```

## Task Scheduling

Add this entry to your crontab:

```cron
* * * * * cd miduner && php hustle schedule:run >> /dev/null 2>&1
```

Example schedule configuration in `App\Console\Kernel`:

```php
<?php

namespace App\Console;

use App\Console\Commands\ExampleCommand;
use Midun\Console\Kernel as ConsoleKernel;
use Midun\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    /**
     * List of commands.
     *
     * @var array $commands
     */
    protected array $commands = [
        ExampleCommand::class,
    ];

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(ExampleCommand::class)->daily();
        $schedule->command(ExampleCommand::class)->weekly();
        $schedule->command(ExampleCommand::class)->monthly();
        $schedule->command(ExampleCommand::class)->yearly();
        $schedule->command(ExampleCommand::class)->dailyAt('13:30');
        $schedule->command(ExampleCommand::class)->cron('* * * * *');

        $schedule->command(ExampleCommand::class)
            ->everyMinute()
            ->output(storage_path('logs/schedule.log'))
            ->cli('/usr/bin/php');
    }
}
```

## Docker Setup

If PHP is not installed locally, you can run Miduner with Docker:

```bash
docker build ./docker
docker-compose up -d
```

Or build and start in one command:

```bash
docker-compose up --build -d
```

Then add the following entry to your `/etc/hosts` file:

```text
127.0.0.1 miduner.local
```

## Documentation

Documentation is in progress at: [https://miduner.com/docs](https://miduner.com/docs)

## Contributing

Thank you for considering a contribution to Miduner Framework.

To enable development mode:

```bash
php hustle development:enable
```

## Security

If you discover a security vulnerability, please contact:

- **Dang Anh**
- Email: `danganh.dev@gmail.com`
- Facebook: [https://facebook.com/underspected](https://facebook.com/underspected)

## License

Miduner Framework is open-source software licensed under the [MIT License](http://opensource.org/licenses/MIT).
