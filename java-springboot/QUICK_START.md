# Java Spring Boot - Quick Setup Guide

Since Maven is not installed, here are alternative ways to run the application:

## Option 1: Using IntelliJ IDEA (Recommended)
1. Open IntelliJ IDEA
2. File → Open → Select `java-springboot` folder
3. Wait for dependencies to download (IntelliJ uses embedded Maven)
4. Right-click `InternalToolsApplication.java` → Run
5. Server starts on http://localhost:8080

## Option 2: Using VS Code with Java Extensions
1. Install "Extension Pack for Java" in VS Code
2. Open `java-springboot` folder
3. Press F5 or Run → Start Debugging
4. Select "Java" when prompted
5. Server starts on http://localhost:8080

## Option 3: Install Maven Manually
```powershell
# Download Maven from https://maven.apache.org/download.cgi
# Extract to C:\Program Files\Apache\Maven
# Add to PATH: C:\Program Files\Apache\Maven\bin
# Then run:
mvn spring-boot:run
```

## Option 4: Use Docker (If available)
```powershell
# Build and run in container
docker build -t internal-tools-java .
docker run -p 8080:8080 --network host internal-tools-java
```

## Testing Without Running
You can review the code structure and architecture without running:
- See `ARCHITECTURE_FLOW.md` for complete flow diagram
- See `README.md` for API documentation
- All code is fully documented with inline comments
