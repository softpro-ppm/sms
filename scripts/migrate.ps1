# Run Laravel migrations when `php` is not on PATH (Windows).
# Usage: powershell -ExecutionPolicy Bypass -File scripts/migrate.ps1

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $root

function Find-PhpExe {
    $candidates = @()
    foreach ($dir in @(
            (Join-Path $env:ProgramFiles "PHP"),
            (Join-Path "${env:ProgramFiles(x86)}" "PHP"),
            (Join-Path $env:LOCALAPPDATA "Programs\Php")
        )) {
        if (Test-Path $dir) {
            $candidates += Get-ChildItem -Path $dir -Filter "php.exe" -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
        }
    }
    $wingetRoot = Join-Path $env:LOCALAPPDATA "Microsoft\WinGet\Packages"
    if (Test-Path $wingetRoot) {
        $candidates += Get-ChildItem -Path $wingetRoot -Filter "php.exe" -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
    }
    foreach ($c in $candidates) {
        if ($c -and (Test-Path $c)) { return $c }
    }
    return $null
}

$php = $null
try {
    $cmd = Get-Command php -ErrorAction Stop
    $php = $cmd.Source
} catch {
    $php = Find-PhpExe
}

if (-not $php) {
    Write-Host "PHP not found. Install with:" -ForegroundColor Yellow
    Write-Host '  winget install --id PHP.PHP.8.3 --accept-package-agreements --accept-source-agreements'
    Write-Host "Then close and reopen this terminal (or add PHP to your user PATH)."
    exit 1
}

Write-Host "Using: $php" -ForegroundColor Cyan
& $php artisan migrate @args
