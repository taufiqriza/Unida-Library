# Cloudflare Security Setup untuk Perpustakaan UNIDA

## 1. DNS Setup
- Pastikan domain sudah di-proxy melalui Cloudflare (orange cloud)
- Aktifkan "Always Use HTTPS"

## 2. WAF Rules (Security > WAF)

### Rule 1: Block Gambling Keywords
```
(http.request.uri contains "slot" and http.request.uri contains "gacor") or
(http.request.uri contains "judi") or
(http.request.uri contains "togel") or
(http.request.uri contains "sbobet") or
(http.request.uri contains "poker" and http.request.uri contains "online") or
(http.request.uri contains "casino")
```
Action: **Block**

### Rule 2: Block SQL Injection
```
(http.request.uri.query contains "union" and http.request.uri.query contains "select") or
(http.request.uri.query contains "drop" and http.request.uri.query contains "table") or
(http.request.uri.query contains "insert" and http.request.uri.query contains "into")
```
Action: **Block**

### Rule 3: Block Suspicious User Agents
```
(http.user_agent contains "sqlmap") or
(http.user_agent contains "nikto") or
(http.user_agent contains "nmap") or
(http.user_agent contains "masscan") or
(http.user_agent contains "zgrab")
```
Action: **Block**

### Rule 4: Protect Admin Panel
```
(http.request.uri.path contains "/admin") and
not (ip.src in {103.195.19.0/24 180.72.81.0/24})
```
Action: **Managed Challenge** (atau Block untuk lebih ketat)

## 3. Rate Limiting (Security > WAF > Rate limiting rules)

### Rule 1: General Rate Limit
- If: URI Path contains "/"
- Rate: 100 requests per 1 minute
- Action: Block for 1 hour

### Rule 2: Login Rate Limit
- If: URI Path contains "/login" OR "/register"
- Rate: 10 requests per 1 minute
- Action: Block for 1 hour

### Rule 3: API Rate Limit
- If: URI Path starts with "/api"
- Rate: 60 requests per 1 minute
- Action: Block for 10 minutes

## 4. Bot Fight Mode (Security > Bots)
- Enable "Bot Fight Mode"
- Enable "Block AI Scrapers and Crawlers" (optional)

## 5. Security Level (Security > Settings)
- Set to "High" atau "I'm Under Attack" jika sedang diserang

## 6. Challenge Passage (Security > Settings)
- Set to 30 minutes

## 7. Browser Integrity Check
- Enable "Browser Integrity Check"

## 8. Hotlink Protection (Scrape Shield)
- Enable untuk mencegah hotlinking gambar

## 9. Email Address Obfuscation
- Enable untuk menyembunyikan email dari scraper

## 10. Page Rules (Optional)

### Cache Admin Panel
```
URL: *perpustakaan.unida.gontor.ac.id/admin/*
Setting: Cache Level = Bypass
```

### Force HTTPS
```
URL: *perpustakaan.unida.gontor.ac.id/*
Setting: Always Use HTTPS = On
```

## 11. Firewall Events Monitoring
- Pantau Security > Events secara berkala
- Set up email notification untuk blocked requests

## 12. Country Block (Optional)
Jika ingin memblokir negara tertentu:
```
(ip.geoip.country in {"CN" "RU" "KP"})
```
Action: **Managed Challenge** atau **Block**

---

## Catatan Penting:
1. Setelah setup, monitor Firewall Events untuk false positives
2. Whitelist IP kantor/kampus jika perlu
3. Backup rules sebelum membuat perubahan
4. Test dengan browser incognito setelah setup
