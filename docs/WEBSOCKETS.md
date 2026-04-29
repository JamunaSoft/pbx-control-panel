# WebSocket Server Setup (Soketi)

## What is Soketi?

Soketi is a **self-hosted, Pusher-compatible WebSocket server**. It provides real-time broadcasting for Laravel Echo without monthly fees.

- ✅ 100% free (no per-message or per-connection charges)
- ✅ Pusher protocol compatible (works with existing Echo code)
- ✅ Scales to thousands of connections
- ✅ Docker-ready (single command to start)

---

## Quick Start

### Option A: Docker Compose (Recommended — all-in-one)

Start the full stack including Soketi WebSocket server:

```bash
docker-compose up -d soketi
```

This starts Soketi on:
- WebSocket: `ws://127.0.0.1:6001`
- Metrics: `http://127.0.0.1:9601`

Stop:
```bash
docker-compose stop soketi
```

### Option B: Docker Run (single container)

```bash
docker run -d \
  --name soketi \
  -p 6001:6001 \
  -p 9601:9601 \
  -e SOKETI_DEFAULT_APP_ID=local \
  -e SOKETI_DEFAULT_APP_KEY=local \
  -e SOKETI_DEFAULT_APP_SECRET=local \
  quay.io/soketi/soketi:latest
```

### Option C: Local npm (without Docker)

If you have `npm` access:

```bash
npx @soketi/soketi start
```

Or install globally (may need sudo):
```bash
sudo npm install -g @soketi/soketi
soketi start
```

---

## Verify It's Running

Check WebSocket connection in browser console when viewing the dashboard:

```javascript
window.Echo.connector.pusher.connection.bind('connected', () => {
  console.log('✅ Connected to Soketi WebSocket server');
});
```

Or test via `wscat`:
```bash
npm install -g wscat
wscat -c ws://127.0.0.1:6001/app/local
```

You should see: `{"event":"pusher:connection_established","data":{"socket_id":"..."}}`

---

## Configuration Reference

**.env** (already configured in this project):

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_APP_HOST=127.0.0.1
PUSHER_APP_PORT=6001
PUSHER_APP_SCHEME=http
```

**Echo Client** (`resources/js/bootstrap.js`):

Already configured to use these environment variables via Vite.

---

## Metrics Dashboard

Soketi exposes Prometheus metrics at: `http://127.0.0.1:9601/metrics`

Use Grafana or `curl` to monitor:
```bash
curl http://127.0.0.1:9601/metrics
```

---

## Production Deployment

For production, Soketi should run behind a reverse proxy (NGINX) with TLS:

```nginx
map $http_upgrade $connection_upgrade {
    default upgrade;
    '' close;
}

server {
    listen 6001;
    server_name ws.yourdomain.com;

    location /app/ {
        proxy_pass http://soketi:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
```

Then update `.env`:
```env
PUSHER_APP_HOST=ws.yourdomain.com
PUSHER_APP_PORT=443
PUSHER_APP_SCHEME=https
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Connection refused | Ensure Soketi is running: `docker ps` |
| CORS errors | Set `SOKETI_ALLOWED_ORIGINS=*` env var in container |
| Events not received | Verify private channel auth works (check `routes/channels.php`) |
| Port 6001 already in use | Stop conflicting service or change `PUSHER_APP_PORT` |

---

## Cost Comparison

| Service | Monthly Cost | Notes |
|---------|--------------|-------|
| **Pusher (hosted)** | $49–$299 | Pay per message + connection |
| **Soketi (self-hosted)** | $0 (just VPS) | Free forever; you manage the server |
| **Laravel WebSockets** | $0 (just VPS) | PHP-based, heavier than Soketi |

---

**Bottom line:** Soketi gives you the same Pusher API at zero cost. Ideal for PBX control panel where you want unlimited real-time events without ongoing fees.
