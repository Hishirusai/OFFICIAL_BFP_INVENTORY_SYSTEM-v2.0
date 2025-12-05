@echo off
echo Starting Laravel Development Server...
echo.

REM Start PHP Artisan Serve in a new window
start "Laravel Server" cmd /k "php artisan serve"

REM Wait a moment for Laravel to start
timeout /t 2 /nobreak >nul

REM Start NPM Dev Server in a new window
echo Starting Vite Development Server...
start "Vite Server" cmd /k "npm run dev"

REM Wait a few seconds for both servers to be ready
echo.
echo Waiting for servers to start...
timeout /t 5 /nobreak >nul

REM Open Chrome to the Laravel application
echo Opening Chrome...
start chrome http://localhost:8000

echo.
echo Development servers are running!
echo Laravel: http://localhost:8000
echo Vite: http://localhost:5173 (or check the Vite window)
echo.
echo Press any key to exit this window (servers will continue running)...
pause >nul

