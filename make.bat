where nmake.exe 2> NUL
if %ERRORLEVEL% GTR 0 call  "c:\Program Files (x86)\Microsoft Visual Studio 12.0\VC\vcvarsall.bat" amd64
@rem Allow python build to succeed:
@rem http://stackoverflow.com/questions/2817869/error-unable-to-find-vcvarsall-bat
SET VS90COMNTOOLS=%VS120COMNTOOLS%
nmake /nologo /s /f winbuild\Makefile %1
