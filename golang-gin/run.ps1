#!/usr/bin/env pwsh
# Refresh PATH environment variable
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
$env:Path += ";$env:USERPROFILE\go\bin"

# Navigate to golang-gin directory
Set-Location -Path $PSScriptRoot

# Run the application
Write-Host "🚀 Starting Go/Gin server..."
go run main.go
