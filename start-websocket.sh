#!/bin/bash

# WebSocket Server Setup Script
# This script sets up and starts the WebSocket server for the Travel Planner application

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$SCRIPT_DIR"
SERVER_FILE="$PROJECT_ROOT/websocket-server.js"
PACKAGE_JSON="$PROJECT_ROOT/ws-package.json"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  WebSocket Server Setup for Travel Planner  ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════╝${NC}\n"

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo -e "${RED}✗ Node.js is not installed${NC}"
    echo -e "${YELLOW}Please install Node.js from https://nodejs.org/${NC}"
    exit 1
fi

NODE_VERSION=$(node -v)
echo -e "${GREEN}✓ Node.js found: $NODE_VERSION${NC}\n"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo -e "${RED}✗ npm is not installed${NC}"
    exit 1
fi

NPM_VERSION=$(npm -v)
echo -e "${GREEN}✓ npm found: $NPM_VERSION${NC}\n"

# Create a separate package.json for the WebSocket server
if [ ! -f "$PACKAGE_JSON" ]; then
    echo -e "${YELLOW}Creating package.json for WebSocket server...${NC}"
    cat > "$PACKAGE_JSON" <<EOF
{
  "name": "travel-planner-websocket",
  "version": "1.0.0",
  "description": "WebSocket server for Travel Planner application",
  "main": "websocket-server.js",
  "scripts": {
    "start": "node websocket-server.js",
    "dev": "node websocket-server.js"
  },
  "keywords": [
    "websocket",
    "travel",
    "realtime"
  ],
  "author": "",
  "license": "MIT",
  "dependencies": {
    "ws": "^8.13.0"
  }
}
EOF
    echo -e "${GREEN}✓ Created ws-package.json${NC}\n"
fi

# Install dependencies if node_modules doesn't exist
if [ ! -d "$PROJECT_ROOT/node_modules" ]; then
    echo -e "${YELLOW}Installing dependencies (this may take a minute)...${NC}"
    cd "$PROJECT_ROOT"
    npm install --save ws 2>&1 | tail -n 5
    echo -e "${GREEN}✓ Dependencies installed${NC}\n"
else
    echo -e "${GREEN}✓ Dependencies already installed${NC}\n"
fi

# Check if the server file exists
if [ ! -f "$SERVER_FILE" ]; then
    echo -e "${RED}✗ WebSocket server file not found: $SERVER_FILE${NC}"
    exit 1
fi

echo -e "${GREEN}✓ WebSocket server file found${NC}\n"

# Start the server
echo -e "${BLUE}─────────────────────────────────────────${NC}"
echo -e "${YELLOW}Starting WebSocket server...${NC}"
echo -e "${BLUE}─────────────────────────────────────────${NC}\n"

cd "$PROJECT_ROOT"
node websocket-server.js

