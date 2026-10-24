$phpDir = "C:\Users\NCV\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
$extDir = Join-Path $phpDir "ext"
$iniFile = Join-Path $phpDir "php.ini"

Write-Host "Configuring php.ini..."
if (-not (Test-Path $iniFile)) {
    Copy-Item (Join-Path $phpDir "php.ini-development") $iniFile
}

# Download mongodb extension if not present or needs upgrade
$mongoDll = Join-Path $extDir "php_mongodb.dll"
Write-Host "Downloading MongoDB extension 1.21.12 for PHP 8.2..."
$zipPath = Join-Path $phpDir "php_mongodb.zip"
Invoke-WebRequest -Uri "https://windows.php.net/downloads/pecl/releases/mongodb/1.21.12/php_mongodb-1.21.12-8.2-ts-vs16-x64.zip" -OutFile $zipPath -UseBasicParsing
Expand-Archive -Path $zipPath -DestinationPath (Join-Path $phpDir "mongodb_temp") -Force
Copy-Item (Join-Path (Join-Path $phpDir "mongodb_temp") "php_mongodb.dll") $mongoDll -Force
Remove-Item (Join-Path $phpDir "mongodb_temp") -Recurse -Force
Remove-Item $zipPath -Force
Write-Host "MongoDB extension updated to 1.21.12 in $mongoDll"

# Read php.ini content and enable required extensions
$content = Get-Content $iniFile -Raw
$content = $content -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
$content = $content -replace ';extension=curl', 'extension=curl'
$content = $content -replace ';extension=mbstring', 'extension=mbstring'
$content = $content -replace ';extension=openssl', 'extension=openssl'
$content = $content -replace ';extension=fileinfo', 'extension=fileinfo'
$content = $content -replace ';extension=zip', 'extension=zip'
$content = $content -replace ';extension=pdo_pgsql', 'extension=pdo_pgsql'
$content = $content -replace ';extension=pgsql', 'extension=pgsql'

if ($content -notmatch 'extension=mongodb') {
    $content += "`nextension=mongodb`n"
}
Set-Content -Path $iniFile -Value $content -Encoding utf8
Write-Host "php.ini configured successfully."

# Download composer.phar
$composerPhar = "d:\ABISHEK\urban\composer.phar"
if (-not (Test-Path $composerPhar)) {
    Write-Host "Downloading composer.phar..."
    Invoke-WebRequest -Uri "https://getcomposer.org/composer.phar" -OutFile $composerPhar -UseBasicParsing
    Write-Host "Composer downloaded successfully."
}

# Create composer.bat in project directory
$composerBat = "d:\ABISHEK\urban\composer.bat"
Set-Content -Path $composerBat -Value "@echo off`r`n`"$phpDir\php.exe`" `"$composerPhar`" %*"
Write-Host "composer.bat created."

# Add to user path
$uPath = [Environment]::GetEnvironmentVariable('Path', 'User')
if ($uPath -notlike "*$phpDir*") {
    [Environment]::SetEnvironmentVariable('Path', "$phpDir;d:\ABISHEK\urban;$uPath", 'User')
}
Write-Host "Setup complete."
