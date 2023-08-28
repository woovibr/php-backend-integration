# php-backend-integration

Pix integration example using the [OpenPix](https://openpix.com.br/) Platform. Check our documentation at [OpenPix Developers](https://developers.openpix.com.br/).

## Example API

This is a REST API designed to send donations. It encompasses the following endpoints:

- **POST `/webhook`:** Called when a webhook is triggered on the platform.
- **GET `/donation/{donationId}`:** Retrieve a donation by its ID.
- **POST `/donation`:** Create a new donation.

## Setup

Generate a [App ID](https://developers.openpix.com.br/docs/plugin/app-id) in your OpenPix Account.

Copy the `env.example.php` into `env.php` file and configure the AppID there.

## How to run

### Docker

Having [Docker compose](https://docs.docker.com/compose/install/) installed, configure the environment variables in the `env.php` file and run the `docker compose up` command.

### PHP

Having [Composer](https://getcomposer.org) and PHP `>=8.1.0` installed directly on your machine, execute the command `./start-server.sh`.

## How to access the API

By default, API runs at http://0.0.0.0:8080
