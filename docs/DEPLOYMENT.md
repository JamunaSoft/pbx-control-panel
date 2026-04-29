# HTTPS Setup with Let's Encrypt

This guide covers setting up HTTPS for the PBX Control Panel using Let's Encrypt certificates on Ubuntu 22.04 with Nginx.

## Prerequisites

- A registered domain name pointing to your server's IP
- Ubuntu 22.04 LTS server
- Nginx installed and configured
- Ports 80 and 443 open in firewall

## Step 1: Install Certbot

```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
```

## Step 2: Obtain SSL Certificate

Replace `yourdomain.com` with your actual domain:

```bash
sudo certbot --nginx -d yourdomain.com
```

Certbot will:
- Verify domain ownership via HTTP challenge
- Automatically obtain and install the certificate
- Configure Nginx for HTTPS
- Set up automatic certificate renewal

## Step 3: Verify Nginx Configuration

Certbot modifies your Nginx server block automatically. Verify that it includes:

```nginx
listen 443 ssl http2;
listen [::]:443 ssl http2;

ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
```

## Step 4: Test Auto-Renewal

Let's Encrypt certificates are valid for 90 days. Test renewal:

```bash
sudo certbot renew --dry-run
```

Certbot automatically installs a systemd timer for renewal.

## Step 5: Force HTTPS Redirect

The application includes `App\Http\Middleware\ForceHttps` that automatically redirects HTTP to HTTPS in production. Ensure your Nginx config also redirects:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

## Optional: Generate Self-Signed Certificate for Development

For local development, you can create a self-signed certificate:

```bash
sudo apt install ssl-cert
sudo make-ssl-cert generate-default-snakeoil --force-overwrite
sudo cp /etc/ssl/certs/ssl-cert-snakeoil.pem /etc/ssl/certs/pbx-control-panel.crt
sudo cp /etc/ssl/private/ssl-cert-snakeoil.key /etc/ssl/private/pbx-control-panel.key
```

Update your Nginx dev config to use these files and add `listen 443 ssl;`. Then add the certificate to your system's trusted store to avoid browser warnings.

## Additional Security Headers

Consider adding these headers in Nginx for enhanced security:

```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header X-XSS-Protection "1; mode=block" always;
```

## Troubleshooting

- **"Too many redirects"**: Ensure `APP_URL` in `.env` uses `https://` in production.
- **Certificate not renewing**: Check Certbot timer: `systemctl list-timers | grep certbot`
- **Mixed content warnings**: Ensure all assets use `https://` or protocol-relative URLs.
