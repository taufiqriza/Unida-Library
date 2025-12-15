---
description: How to sync large files (Shamela, Ebooks, Universitaria) to production VM via SSH/rsync
---

# Sync Large Files to Production VM

This workflow handles syncing large files that cannot be deployed via git:
- Shamela Database (~22 GB)
- Universitaria Collection (~5 GB) 
- Local Ebooks (~20 GB)
- Cover Images (~2 GB)

## Prerequisites

1. SSH access to production VM
2. rsync installed on both local and VM
3. Sufficient storage on VM (50+ GB recommended)

## Configuration

Set these environment variables or edit directly in commands:

```bash
# Production VM SSH details
export PROD_HOST="your-vm-ip-or-hostname"
export PROD_USER="your-ssh-username"
export PROD_PATH="/path/to/app/storage"
export SSH_KEY="~/.ssh/your-key"  # Optional if using SSH agent
```

## Sync Commands

### 1. Initial Full Sync (First Deployment)

// turbo-all
Run these commands in order:

```bash
# Step 1: Sync Shamela database files (largest - run first, can take hours)
rsync -avz --progress \
  -e "ssh -i $SSH_KEY" \
  storage/database/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/database/

# Step 2: Sync public storage (covers, photos, etc)
rsync -avz --progress \
  -e "ssh -i $SSH_KEY" \
  storage/app/public/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/app/public/
```

### 2. Incremental Sync (Updates Only)

For ongoing updates, rsync only transfers changed files:

```bash
# Sync all storage with delta transfer
rsync -avz --progress --delete \
  -e "ssh -i $SSH_KEY" \
  --exclude='*.log' \
  --exclude='framework/*' \
  --exclude='.gitignore' \
  storage/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/
```

### 3. Sync Specific Collections

#### Shamela Only
```bash
rsync -avz --progress \
  -e "ssh -i $SSH_KEY" \
  storage/database/shamela_content.db \
  storage/database/master.db \
  storage/database/cover.db \
  storage/database/book/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/database/
```

#### Universitaria Only (when files are added)
```bash
rsync -avz --progress \
  -e "ssh -i $SSH_KEY" \
  storage/app/public/universitaria/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/app/public/universitaria/
```

#### Ebooks Only
```bash
rsync -avz --progress \
  -e "ssh -i $SSH_KEY" \
  storage/app/public/ebooks/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/app/public/ebooks/
```

### 4. Dry Run (Preview Changes)

Always test with `--dry-run` first:

```bash
rsync -avz --progress --dry-run \
  -e "ssh -i $SSH_KEY" \
  storage/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/
```

## Database Import Strategy

For MySQL database sync (not files):

```bash
# Step 1: Export from local
mysqldump -u root perpustakaan > backup.sql

# Step 2: Transfer to VM
scp backup.sql $PROD_USER@$PROD_HOST:/tmp/

# Step 3: Import on VM (SSH into VM first)
ssh $PROD_USER@$PROD_HOST
mysql -u dbuser -p perpustakaan < /tmp/backup.sql
```

## Tips

1. **Use screen/tmux** for long transfers to avoid disconnection:
   ```bash
   screen -S sync
   # run rsync command
   # Ctrl+A, D to detach
   # screen -r sync to reattach
   ```

2. **Compress during transfer** (already enabled with `-z` flag)

3. **Bandwidth limit** if needed:
   ```bash
   rsync -avz --bwlimit=10000 ...  # 10 MB/s limit
   ```

4. **Resume interrupted transfers**:
   ```bash
   rsync -avz --progress --partial ...
   ```

## File Structure on VM

After sync, the structure should be:
```
/path/to/app/storage/
├── database/
│   ├── shamela_content.db    # 21GB - Shamela pages
│   ├── master.db             # 3MB - Shamela metadata
│   ├── cover.db              # 37MB - Shamela covers
│   └── book/                 # Individual book DBs
├── app/
│   └── public/
│       ├── covers/           # Book covers
│       ├── ebooks/           # Ebook PDF files
│       ├── universitaria/    # Manuscripts, Warta, etc
│       ├── members/          # Member photos
│       └── thesis-submissions/
└── logs/
```

## Verification

After sync, verify on VM:

```bash
# Check file sizes
ssh $PROD_USER@$PROD_HOST "du -sh $PROD_PATH/database/* $PROD_PATH/app/public/*"

# Verify Shamela DB is readable
ssh $PROD_USER@$PROD_HOST "sqlite3 $PROD_PATH/database/shamela_content.db 'SELECT COUNT(*) FROM pages;'"
```
