#!/bin/bash
#
# Kubuku API Test Script (Correct Endpoints)
# Based on KubukuService.php implementation
#

API_KEY="N2NhZjVlMjJlYTNlYjgxNzVhYjUxODQyOWM4NTg5YTQ6NjYwNw=="
BASE_URL="https://kubuku.id/api/wl"

echo "=== Kubuku API Test ==="
echo "Running from: $(hostname)"
echo ""

# 1. Total Content
echo "1. GET /totalContent"
curl -sL -H "Authorization:$API_KEY" -H "Accept: application/json" "$BASE_URL/totalContent"
echo ""
echo ""

# 2. Get all content page 1
echo "2. GET /content/all/1"
RESPONSE=$(curl -sL -H "Authorization:$API_KEY" -H "Accept: application/json" "$BASE_URL/content/all/1")
echo "Response (first 800 chars):"
echo "$RESPONSE" | head -c 800
echo "..."
echo ""

# 3. Get categories
echo "3. GET /category/all"
curl -sL -H "Authorization:$API_KEY" -H "Accept: application/json" "$BASE_URL/category/all" | head -c 500
echo "..."
echo ""

# 4. Get new content
echo "4. GET /content/new"
curl -sL -H "Authorization:$API_KEY" -H "Accept: application/json" "$BASE_URL/content/new" | head -c 500
echo "..."
echo ""

# 5. Search test
echo "5. GET /content/search/islam/1"
curl -sL -H "Authorization:$API_KEY" -H "Accept: application/json" "$BASE_URL/content/search/islam/1" | head -c 500
echo "..."
echo ""

echo "=== Done ==="
