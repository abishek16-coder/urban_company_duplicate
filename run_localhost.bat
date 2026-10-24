@echo off
title Service Connect - Local Server Runner
echo ========================================================
echo Starting Service Connect on Localhost
echo ========================================================

:: 1. Database Connection Check
findstr /C:"DB_DRIVER=postgres" "%~dp0.env" >nul 2>&1
if "%ERRORLEVEL%"=="0" (
    echo [OK] Using Neon Cloud PostgreSQL as Database.
) else (
    if not exist "%~dp0data\db" mkdir "%~dp0data\db"
    tasklist /FI "IMAGENAME eq mongod.exe" 2>NUL | find /I /N "mongod.exe">NUL
    if "%ERRORLEVEL%"=="0" (
        echo [OK] MongoDB is already running on port 27017.
    ) else (
        echo [STARTING] MongoDB Server...
        start "MongoDB Server" /min "%~dp0mongodb_srv\mongodb-win32-x86_64-windows-5.0.28\bin\mongod.exe" --dbpath "%~dp0data\db" --bind_ip 127.0.0.1 --port 27017
        timeout /t 2 /nobreak >nul
    )
)

:: 3. Seed database if needed or start web server
echo [OK] Opening browser at http://localhost:8000
start http://localhost:8000

echo [STARTING] PHP Built-in Server on http://localhost:8000...
"C:\Users\NCV\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" -S localhost:8000 -t "%~dp0public" "%~dp0public\index.php"
pause
