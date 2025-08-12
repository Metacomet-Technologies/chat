#!/bin/bash

# Strict error handling
set -euo pipefail
IFS=$'\n\t'

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Track errors
ERRORS=()
WARNINGS=()

# Function to print colored output
print_status() {
    local color=$1
    local message=$2
    echo -e "${color}${message}${NC}"
}

# Function to check if command exists
command_exists() {
    command -v "$1" &> /dev/null
}

# Function to run command with error handling
run_step() {
    local step_name=$1
    local command=$2
    local allow_failure=${3:-false}
    
    echo -n "  → ${step_name}..."
    
    if output=$(eval "$command" 2>&1); then
        print_status "$GREEN" " ✓"
        if [[ -n "$output" ]] && [[ "$VERBOSE" == "true" ]]; then
            echo "$output" | sed 's/^/    /'
        fi
    else
        exit_code=$?
        if [[ "$allow_failure" == "true" ]]; then
            print_status "$YELLOW" " ⚠ (warning)"
            WARNINGS+=("${step_name}: ${output}")
        else
            print_status "$RED" " ✗"
            ERRORS+=("${step_name}: ${output}")
            if [[ "$CONTINUE_ON_ERROR" != "true" ]]; then
                echo -e "${RED}Error output:${NC}"
                echo "$output" | sed 's/^/    /'
                exit $exit_code
            fi
        fi
    fi
}

# Parse command line arguments
VERBOSE=false
CONTINUE_ON_ERROR=false
SKIP_DEPS_CHECK=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        -c|--continue)
            CONTINUE_ON_ERROR=true
            shift
            ;;
        -s|--skip-deps)
            SKIP_DEPS_CHECK=true
            shift
            ;;
        -h|--help)
            echo "Usage: $0 [OPTIONS]"
            echo "Options:"
            echo "  -v, --verbose       Show detailed output from each command"
            echo "  -c, --continue      Continue on errors (don't exit immediately)"
            echo "  -s, --skip-deps     Skip dependency checks"
            echo "  -h, --help          Show this help message"
            exit 0
            ;;
        *)
            echo "Unknown option: $1"
            echo "Use -h or --help for usage information"
            exit 1
            ;;
    esac
done

print_status "$BLUE" "🎨 Starting code formatting and linting..."
echo ""

# Check dependencies
if [[ "$SKIP_DEPS_CHECK" != "true" ]]; then
    print_status "$BLUE" "🔍 Checking dependencies..."
    
    missing_deps=()
    
    if ! command_exists php; then
        missing_deps+=("PHP")
    fi
    
    if ! command_exists composer; then
        missing_deps+=("Composer")
    fi
    
    if ! command_exists npm; then
        missing_deps+=("npm")
    fi
    
    if [[ ${#missing_deps[@]} -gt 0 ]]; then
        print_status "$RED" "❌ Missing dependencies: ${missing_deps[*]}"
        exit 1
    fi
    
    # Check if vendor directory exists
    if [[ ! -d "vendor" ]]; then
        print_status "$YELLOW" "⚠ vendor/ directory not found. Running composer install..."
        composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
    fi
    
    # Check if node_modules exists
    if [[ ! -d "node_modules" ]]; then
        print_status "$YELLOW" "⚠ node_modules/ directory not found. Running npm install..."
        npm install --silent
    fi
    
    print_status "$GREEN" "✓ All dependencies checked"
    echo ""
fi

# Create temporary backup of changed files (for safety)
BACKUP_DIR=".format-backup-$(date +%Y%m%d-%H%M%S)"
if command_exists git && git rev-parse --git-dir > /dev/null 2>&1; then
    if [[ -n $(git status --porcelain) ]]; then
        print_status "$YELLOW" "📦 Creating backup of uncommitted changes in $BACKUP_DIR"
        mkdir -p "$BACKUP_DIR"
        git diff > "$BACKUP_DIR/uncommitted.patch"
        git diff --cached > "$BACKUP_DIR/staged.patch"
    fi
fi

# Backend formatting and analysis
print_status "$BLUE" "📦 Running PHP/Laravel formatting and analysis..."

run_step "IDE Helper Generation" "php artisan ide-helper:generate"
run_step "IDE Helper Models" "php artisan ide-helper:models -RW"
run_step "PHPStan Analysis" "./vendor/bin/phpstan analyse --memory-limit=1G"
run_step "Duster Fix" "./vendor/bin/duster fix"

# Frontend formatting and linting
echo ""
print_status "$BLUE" "🎨 Running Frontend formatting and linting..."

run_step "Prettier formatting" "npm run format"
run_step "ESLint" "npm run lint"

# Type checking (optional but recommended)
if npm run | grep -q "types"; then
    echo ""
    print_status "$BLUE" "📝 Running TypeScript type checking..."
    run_step "TypeScript checking" "npm run types" true
fi

# Build production assets
echo ""
print_status "$BLUE" "🏗️ Building production assets..."
run_step "Building assets" "npm run build"

# Summary
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [[ ${#ERRORS[@]} -eq 0 ]] && [[ ${#WARNINGS[@]} -eq 0 ]]; then
    print_status "$GREEN" "✅ All formatting, linting, and build completed successfully!"
    
    # Clean up ALL backup directories if everything succeeded
    if ls .format-backup-* 1> /dev/null 2>&1; then
        print_status "$BLUE" "🧹 Cleaning up backup directories..."
        rm -rf .format-backup-*
    fi
else
    if [[ ${#WARNINGS[@]} -gt 0 ]]; then
        print_status "$YELLOW" "⚠ Completed with ${#WARNINGS[@]} warning(s):"
        for warning in "${WARNINGS[@]}"; do
            echo "  - $warning"
        done
    fi
    
    if [[ ${#ERRORS[@]} -gt 0 ]]; then
        print_status "$RED" "❌ Encountered ${#ERRORS[@]} error(s):"
        for error in "${ERRORS[@]}"; do
            echo "  - $error"
        done
        
        if [[ -d "$BACKUP_DIR" ]]; then
            print_status "$YELLOW" "💾 Backup saved in $BACKUP_DIR"
            echo "   Restore with: git apply $BACKUP_DIR/*.patch"
        fi
        
        exit 1
    fi
fi