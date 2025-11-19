@echo off
echo Demarrage du serveur HTTP local...
echo.
powershell -ExecutionPolicy Bypass -File "%~dp0start-server.ps1"
pause

