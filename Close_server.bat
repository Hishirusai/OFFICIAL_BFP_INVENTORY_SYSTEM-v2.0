@echo off
echo Stopping Server...

:: 1. Kill the specific windows we opened (by title)
taskkill /FI "WINDOWTITLE eq LaravelAppServer" /T /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq LaravelAppVite" /T /F >nul 2>&1

:: 2. Kill any remaining PHP or Node processes to free up ports
taskkill /IM php.exe /F >nul 2>&1
taskkill /IM node.exe /F >nul 2>&1

echo All servers stopped.
pause