@echo off
:: Ensure the script runs from the folder where it is located
cd /d "%~dp0"

echo Clearing previous login sessions...

:: 1. Clear Laravel Session ONLY (Forces Logout)
:: This removes active session files to log the user out before starting.
:: We use a loop to delete files in storage/framework/sessions except .gitignore
if exist "storage\framework\sessions" (
    pushd "storage\framework\sessions"
    for %%F in (*) do (
        :: /F = force delete, /Q = quiet mode
        if /I not "%%~nxF"==".gitignore" del /F /Q "%%F"
    )
    popd
)

echo Starting Server...

:: 2. Start Laravel (php artisan serve) in a named window
start "LaravelAppServer" cmd /k "php artisan serve"

:: 3. Start Vite (npm run dev) in a named window
start "LaravelAppVite" cmd /k "npm run dev"

:: 4. Wait 2 seconds for services to load
timeout /t 2 /nobreak >nul

:: 5. Open the browser
start http://localhost:8000