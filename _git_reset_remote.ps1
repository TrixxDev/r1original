# Reset GitHub master to empty baseline (local repo untouched)
$ErrorActionPreference = "Stop"
$tmp = Join-Path $env:TEMP "r1-empty-push"
if (Test-Path $tmp) { Remove-Item $tmp -Recurse -Force }
New-Item -ItemType Directory -Path $tmp | Out-Null
Set-Location $tmp
git init -q
git -c user.name="TrixxDev" -c user.email="TrixxDev@users.noreply.github.com" commit --allow-empty -q -m "Reset repository for server baseline"
git branch -M master
git remote add origin https://github.com/TrixxDev/r1original.git
git push -f origin master
Set-Location "E:\r1riepasclone"
Write-Host "Remote master reset to empty commit."
