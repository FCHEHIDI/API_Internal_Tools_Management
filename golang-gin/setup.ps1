#!/usr/bin/env pwsh
# Refresh PATH environment variable
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
$env:Path += ";$env:USERPROFILE\go\bin"

# Navigate to golang-gin directory
Set-Location -Path $PSScriptRoot

Write-Host "Tidying up Go modules..."
go mod tidy

Write-Host "Downloading all dependencies..."
go mod download

Write-Host "Dependencies ready! You can now run: go run main.go"
