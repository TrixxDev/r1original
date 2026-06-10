# Merge server baseline from GitHub with local master, then push. No server access.
$ErrorActionPreference = "Stop"
Set-Location "E:\r1riepasclone"
git fetch origin
git branch server-baseline origin/master
git checkout master
git merge server-baseline --allow-unrelated-histories -m "Merge server production baseline with local development updates."
git push origin master
Write-Host "Local merged and pushed to origin/master."
