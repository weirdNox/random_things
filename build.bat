@echo off

mkdir "%~dp0/build"
pushd "%~dp0/build"

REM Set variables
set PLATFORM=amd64
set TOOLSET_LOCATION=C:\Program Files (x86)\Microsoft Visual Studio 14.0\VC
if exist "%~dp0\env.bat" call "%~dp0\env.bat"

REM Grab the compiler
call "%TOOLSET_LOCATION%\vcvarsall.bat" %PLATFORM%

REM Build
cl "%~f1" /nologo /FC /Gm- /GR- /EHsc /Od /W4 /wd4505 /wd4201 /wd4100 /MT /Zi /Ob1 /D_CRT_SECURE_NO_WARNINGS /DNDEBUG /link /incremental:no /opt:ref user32.lib gdi32.lib winmm.lib
del *.obj >NUL 2>NUL

popd
