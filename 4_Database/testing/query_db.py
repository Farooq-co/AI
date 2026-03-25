from sqlalchemy import create_engine, text

engine = create_engine('postgresql://neondb_owner:npg_HQP0rSdevT4m@ep-royal-math-a8356d8i-pooler.eastus2.azure.neon.tech/neondb?sslmode=require&channel_binding=require')

with engine.connect() as conn:
    result = conn.execute(text('SELECT * FROM alembic_version'))
    revisions = result.fetchall()
    print('Alembic revisions in DB:')
    for r in revisions:
        print(r)

    result = conn.execute(text('''
        SELECT column_name, data_type, is_nullable, column_default, character_maximum_length
        FROM information_schema.columns 
        WHERE table_name = 'todos' 
        ORDER BY ordinal_position
    '''))
    columns = result.fetchall()
    print('\\nTodos table columns:')
    for col in columns:
        print(col)
    
    # Check if table exists
    result = conn.execute(text("SELECT to_regclass('todos');"))
    table_exists = result.scalar()
    print('\\nTodos table exists:', bool(table_exists))
