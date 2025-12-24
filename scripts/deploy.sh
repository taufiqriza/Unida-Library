#!/bin/bash
# =============================================================================
# Deploy to Production VM
# =============================================================================
# Usage: ./scripts/deploy.sh [full|code|db|sync]
#
# Options:
#   full  - Full deployment (code + database + large files)
#   code  - Code only (git pull + composer + npm)
#   db    - Sync main database only
#   sync  - Sync large files (Shamela, Universitaria)
# =============================================================================

set -e

# Configuration - EDIT THESE
PROD_HOST="${PROD_HOST:-your-vm-ip}"
PROD_USER="${PROD_USER:-your-username}"
PROD_PATH="${PROD_PATH:-/var/www/perpustakaan}"
SSH_KEY="${SSH_KEY:-~/.ssh/id_rsa}"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
echo_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
echo_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# Check SSH connection
check_ssh() {
    echo_info "Checking SSH connection..."
    if ! ssh -i "$SSH_KEY" -o ConnectTimeout=5 "$PROD_USER@$PROD_HOST" "echo 'connected'" > /dev/null 2>&1; then
        echo_error "Cannot connect to $PROD_HOST"
        exit 1
    fi
    echo_info "SSH connection OK"
}

# Deploy code via git
deploy_code() {
    echo_info "Deploying code..."
    ssh -i "$SSH_KEY" "$PROD_USER@$PROD_HOST" << EOF
cd $PROD_PATH
git pull origin main
composer install --optimize-autoloader --no-dev
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true
echo "Code deployment complete!"
EOF
}

# Sync main database
sync_database() {
    echo_info "Syncing main database..."
    
    # Backup remote first
    ssh -i "$SSH_KEY" "$PROD_USER@$PROD_HOST" \
        "cp $PROD_PATH/database/database.sqlite $PROD_PATH/database/database.sqlite.bak-\$(date +%Y%m%d%H%M%S)" || true
    
    # Copy local database
    rsync -avz --progress \
        -e "ssh -i $SSH_KEY" \
        database/database.sqlite \
        "$PROD_USER@$PROD_HOST:$PROD_PATH/database/"
    
    echo_info "Database sync complete!"
}

# Sync large files
sync_large_files() {
    echo_info "Syncing large files (this may take hours)..."
    echo_warn "Running in background mode. Use 'screen -r' to monitor."
    
    # Shamela databases
    echo_info "Syncing Shamela database..."
    rsync -avz --progress --partial \
        -e "ssh -i $SSH_KEY" \
        storage/database/ \
        "$PROD_USER@$PROD_HOST:$PROD_PATH/storage/database/"
    
    # Public storage (universitaria, covers, etc)
    echo_info "Syncing public storage..."
    rsync -avz --progress --partial \
        -e "ssh -i $SSH_KEY" \
        --exclude='*.log' \
        storage/app/public/ \
        "$PROD_USER@$PROD_HOST:$PROD_PATH/storage/app/public/"
    
    echo_info "Large files sync complete!"
}

# Main
main() {
    local mode="${1:-full}"
    
    echo "=========================================="
    echo " Perpustakaan Deployment Script"
    echo " Mode: $mode"
    echo " Target: $PROD_USER@$PROD_HOST"
    echo "=========================================="
    
    check_ssh
    
    case "$mode" in
        full)
            deploy_code
            sync_database
            echo_warn "Large files sync not included in 'full'. Run with 'sync' separately."
            ;;
        code)
            deploy_code
            ;;
        db)
            sync_database
            ;;
        sync)
            sync_large_files
            ;;
        *)
            echo "Usage: $0 [full|code|db|sync]"
            exit 1
            ;;
    esac
    
    echo_info "Deployment complete!"
}

main "$@"
