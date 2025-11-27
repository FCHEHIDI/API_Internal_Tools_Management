"""Quick database connection test."""
import asyncio
import sys
from sqlalchemy.ext.asyncio import create_async_engine
from sqlalchemy import text

# Fix for Windows ProactorEventLoop issue with psycopg
if sys.platform == 'win32':
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())

DATABASE_URL = "postgresql+psycopg://dev:dev123@localhost:5432/internal_tools"

async def test_connection():
    """Test database connection and verify tables."""
    engine = create_async_engine(DATABASE_URL, echo=False)
    
    try:
        async with engine.connect() as conn:
            # Test basic connection
            result = await conn.execute(text("SELECT version()"))
            version = result.scalar()
            print(f"✓ Connected to: {version}\n")
            
            # Check if tables exist
            result = await conn.execute(text("""
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public'
                ORDER BY table_name
            """))
            tables = [row[0] for row in result.fetchall()]
            
            if tables:
                print(f"✓ Found {len(tables)} tables:")
                for table in tables:
                    print(f"  - {table}")
                
                # Count records in key tables
                print("\n✓ Record counts:")
                for table in ['categories', 'tools', 'users']:
                    if table in tables:
                        result = await conn.execute(text(f"SELECT COUNT(*) FROM {table}"))
                        count = result.scalar()
                        print(f"  - {table}: {count} records")
            else:
                print("⚠ No tables found. Database needs initialization.")
                
    except Exception as e:
        print(f"✗ Connection failed: {e}")
    finally:
        await engine.dispose()

if __name__ == "__main__":
    asyncio.run(test_connection())
