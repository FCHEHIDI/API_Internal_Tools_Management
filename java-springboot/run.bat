@echo off
echo Java Spring Boot - Manual Run Script
echo =======================================

cd /d "%~dp0"

echo.
echo Step 1: Compiling Java sources...
javac -d target/classes -cp "lib/*" src/main/java/com/techcorp/internaltools/*.java src/main/java/com/techcorp/internaltools/**/*.java 2>compile-errors.txt

if %errorlevel% neq 0 (
    echo Compilation failed! Check compile-errors.txt
    type compile-errors.txt
    pause
    exit /b 1
)

echo.
echo Step 2: Running Spring Boot application...
java -cp "target/classes;lib/*" com.techcorp.internaltools.InternalToolsApplication

pause
