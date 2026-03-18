# Realtime Consultation Notifications

## Runtime choice

This repository now uses Laravel Reverb for the self-hosted WebSocket server.

Reverb is Laravel 12 compatible and speaks the same Pusher protocol that Laravel broadcasting and Echo expect.

## Installed packages

- `laravel/reverb`
- `pusher/pusher-php-server`
- Laravel broadcast driver: `pusher`
- Frontend client: `laravel-echo` + `pusher-js`
- Private per-user channel: `consultation.user.{userId}`

## Environment variables

Set these values in `.env`:

```dotenv
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=database

REVERB_APP_ID=astro-jyotish
REVERB_APP_KEY=astro-jyotish-key
REVERB_APP_SECRET=astro-jyotish-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER=reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_SERVER_PATH=
REVERB_SCALING_ENABLED=false
REVERB_ALLOWED_ORIGINS=app.example.com,www.app.example.com

PUSHER_APP_ID="${REVERB_APP_ID}"
PUSHER_APP_KEY="${REVERB_APP_KEY}"
PUSHER_APP_SECRET="${REVERB_APP_SECRET}"
PUSHER_HOST="${REVERB_HOST}"
PUSHER_PORT="${REVERB_PORT}"
PUSHER_SCHEME="${REVERB_SCHEME}"
PUSHER_APP_CLUSTER=mt1

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

VITE_PUSHER_APP_KEY="${REVERB_APP_KEY}"
VITE_PUSHER_HOST="${REVERB_HOST}"
VITE_PUSHER_PORT="${REVERB_PORT}"
VITE_PUSHER_SCHEME="${REVERB_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

The duplicated `PUSHER_*` values are intentional. Laravel's `pusher` broadcast driver still reads those keys, while Reverb uses `REVERB_*` for the server process.

Set `REVERB_ALLOWED_ORIGINS` to the exact browser origins that should be allowed to open socket connections. Do not use `*` in production.

Then rebuild frontend assets:

```bash
npm run build
```

## Broadcast flow

1. Astrologer marks the booking as `ready_to_start`.
2. `ConsultationStatusUpdated` broadcasts to `consultation.user.{userId}` through a private channel.
3. Customer browser receives the event through Echo.
4. Popup and browser notification appear.
5. When the customer joins, the booking moves to `live` and the next realtime event is broadcast.
6. If the socket drops, the frontend falls back to polling every 8 seconds.

## Queue worker

`ConsultationStatusUpdated` implements `ShouldBroadcast`, so you need a queue worker in production.

Example:

```bash
php artisan queue:work --queue=default --sleep=1 --tries=3 --timeout=120
```

Redis is optional but recommended for production queue throughput and cache consistency.

## Local development

The `composer dev` script now starts:

- `php artisan serve`
- `php artisan queue:listen --tries=1 --timeout=0`
- `php artisan pail --timeout=0`
- `php artisan reverb:start --host=0.0.0.0 --port=8080`
- `npm run dev`

You can still start Reverb manually:

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

## WebSocket server

Start the Reverb server with:

```bash
php artisan reverb:start
```

For zero-downtime deploys or config changes, restart it with:

```bash
php artisan reverb:restart
```

If you need horizontal scaling, enable `REVERB_SCALING_ENABLED=true` and back it with Redis.

## Frontend subscription example

```js
Echo.private('consultation.user.' + userId)
	.listen('ConsultationStatusUpdated', (event) => {
		console.log(event.bookingId, event.status, event.joinUrl);
	});
```

## Supervisor

See `deploy/supervisor/consultation-realtime.conf.example` for a production baseline.
