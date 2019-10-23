@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../visavi/cleanup/cleanup
php "%BIN_TARGET%" %*
