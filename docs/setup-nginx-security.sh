#!/bin/bash
# Security Enhancement Script for Nginx
# Run with: sudo bash setup-nginx-security.sh

echo "=== Setting up Nginx Security ==="

# Create security snippet
cat > /etc/nginx/snippets/security.conf << 'EOF'
# =============================================================================
# NGINX SECURITY RULES - Perpustakaan UNIDA
# =============================================================================

# Block suspicious query strings (SQL Injection)
set $block_sql 0;
if ($query_string ~ "union.*select") { set $block_sql 1; }
if ($query_string ~ "concat.*\(") { set $block_sql 1; }
if ($query_string ~ "drop.*table") { set $block_sql 1; }
if ($block_sql = 1) { return 403; }

# Block XSS attempts
if ($query_string ~ "<script") { return 403; }
if ($query_string ~ "javascript:") { return 403; }

# Block gambling/judol keywords in URL
if ($request_uri ~* "(slot.?gacor|judi.?online|togel|sbobet|poker.?online|casino)") {
    return 403;
}

# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

# Hide server version
server_tokens off;
EOF

echo "✓ Created /etc/nginx/snippets/security.conf"

# Backup original config
cp /etc/nginx/sites-available/library.unida.gontor.ac.id /etc/nginx/sites-available/library.unida.gontor.ac.id.backup.$(date +%Y%m%d)

# Create new config with security
cat > /etc/nginx/sites-available/library.unida.gontor.ac.id << 'EOF'
server {
    server_name library.unida.gontor.ac.id;
    root /var/www/perpustakaan-app/current/public;
    index index.php;
    client_max_body_size 100M;

    # Include security rules
    include snippets/security.conf;

    # Block PHP in uploads
    location ~* /storage/.*\.php$ { deny all; }
    location ~* /uploads/.*\.php$ { deny all; }

    # Protect sensitive files
    location ~ /\.(env|git|htaccess|htpasswd) { deny all; }
    location ~ ^/(artisan|composer\.json|package\.json) { deny all; }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    listen 443 ssl;
    ssl_certificate /etc/letsencrypt/live/library.unida.gontor.ac.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/library.unida.gontor.ac.id/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}

server {
    listen 80;
    server_name library.unida.gontor.ac.id;
    return 301 https://$host$request_uri;
}
EOF

echo "✓ Updated Nginx config with security rules"

# Test config
nginx -t
if [ $? -eq 0 ]; then
    echo "✓ Nginx config test passed"
    systemctl reload nginx
    echo "✓ Nginx reloaded"
    echo ""
    echo "=== Security Setup Complete ==="
else
    echo "✗ Nginx config test failed! Restoring backup..."
    cp /etc/nginx/sites-available/library.unida.gontor.ac.id.backup.* /etc/nginx/sites-available/library.unida.gontor.ac.id
fi
