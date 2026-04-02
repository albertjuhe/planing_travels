#!/bin/bash

# WebSocket Implementation Verification Script
# Verifies that all WebSocket files are in place and configured correctly

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "\n${BLUE}╔════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  WebSocket Implementation Verification      ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════╝${NC}\n"

PASSED=0
FAILED=0

# Function to check file exists
check_file() {
    local FILE=$1
    local DESC=$2

    if [ -f "$SCRIPT_DIR/$FILE" ]; then
        echo -e "${GREEN}✓${NC} $DESC"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $DESC (missing)"
        ((FAILED++))
    fi
}

# Function to check directory exists
check_dir() {
    local DIR=$1
    local DESC=$2

    if [ -d "$SCRIPT_DIR/$DIR" ]; then
        echo -e "${GREEN}✓${NC} $DESC"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $DESC (missing)"
        ((FAILED++))
    fi
}

# Function to check content in file
check_content() {
    local FILE=$1
    local PATTERN=$2
    local DESC=$3

    if grep -q "$PATTERN" "$SCRIPT_DIR/$FILE" 2>/dev/null; then
        echo -e "${GREEN}✓${NC} $DESC"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $DESC (not found)"
        ((FAILED++))
    fi
}

echo -e "${YELLOW}1. Checking JavaScript Files${NC}"
check_file "public/js/websocket/websockets.js" "Client WebSocket script"

echo -e "\n${YELLOW}2. Checking CSS Files${NC}"
check_file "public/css/main.css" "Main CSS with WebSocket styles"
check_content "public/css/main.css" "st-online-badge" "Badge styling"
check_content "public/css/main.css" "websocket-messages" "Messages container styling"

echo -e "\n${YELLOW}3. Checking Templates${NC}"
check_file "src/UI/templates/travel/showTravel.html.twig" "Travel template"
check_content "src/UI/templates/travel/showTravel.html.twig" "websocket-messages" "Messages container in HTML"

echo -e "\n${YELLOW}4. Checking Server Files${NC}"
check_file "websocket-server.js" "WebSocket Node.js server"
check_file "start-websocket.sh" "Server startup script"
check_file "websocket-client-test.js" "WebSocket test client"

echo -e "\n${YELLOW}5. Checking Documentation${NC}"
check_file "WEBSOCKET.md" "Main WebSocket documentation"
check_file "WEBSOCKET_QUICK_START.md" "Quick start guide"
check_file "WEBSOCKET_SETUP.md" "Technical setup guide"
check_file "WEBSOCKET_IMPLEMENTATION.md" "Implementation summary"

echo -e "\n${YELLOW}6. Checking File Permissions${NC}"
if [ -x "start-websocket.sh" ]; then
    echo -e "${GREEN}✓${NC} start-websocket.sh is executable"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} start-websocket.sh is not executable"
    ((FAILED++))
fi

echo -e "\n${YELLOW}7. Checking Dependencies${NC}"
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v)
    echo -e "${GREEN}✓${NC} Node.js installed ($NODE_VERSION)"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Node.js not installed"
    ((FAILED++))
fi

if [ -d "node_modules/ws" ]; then
    echo -e "${GREEN}✓${NC} ws package installed"
    ((PASSED++))
else
    echo -e "${YELLOW}!${NC} ws package not installed (will install on first run)"
fi

echo -e "\n${YELLOW}8. Checking WebSocket Configuration${NC}"
check_content "public/js/websocket/websockets.js" "const WS_URL" "WebSocket URL configured"
check_content "websocket-server.js" "const PORT" "Server port configured"

echo -e "\n${BLUE}╔════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Verification Results                     ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════╝${NC}"

echo -e "\n${GREEN}Passed: $PASSED${NC}"
echo -e "${RED}Failed: $FAILED${NC}"

if [ $FAILED -eq 0 ]; then
    echo -e "\n${GREEN}✓ All checks passed! WebSocket implementation is complete.${NC}\n"
    echo -e "${YELLOW}Next steps:${NC}"
    echo -e "  1. Run: ${BLUE}./start-websocket.sh${NC}"
    echo -e "  2. Open: ${BLUE}http://localhost:8000/public/index.php/en/travel/toscana-italia-1${NC}"
    echo -e "  3. Check for green 'Connected' badge in top-right\n"
    exit 0
else
    echo -e "\n${RED}✗ Some checks failed. Please review the output above.${NC}\n"
    exit 1
fi

