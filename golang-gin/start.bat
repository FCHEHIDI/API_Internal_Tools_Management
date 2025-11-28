@echo off
REM Batch script to start the Go/Gin server

REM Navigate to the golang-gin directory
cd /d "%~dp0"

REM Set Go path
set PATH=%PATH%;C:\Program Files\Go\bin;%USERPROFILE%\go\bin

REM Start the server
echo Starting Go/Gin server...
go run main.go
