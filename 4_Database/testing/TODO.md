 TODO: Fix Todo Model Issues

## Steps:
1. [ ] Read dependent files: config/database.py, main.py (check SA version, usage).
2. [ ] Edit models/todo_model.py: Modernize to SA 2.0+, fix typo/syntax/default.
3. [ ] Generate Alembic migration: `alembic revision --autogenerate -m "fix created_at"`.
4. [ ] Upgrade DB: `alembic upgrade head`.
5. [ ] Test model in Python shell/app.
6. [ ] Mark complete.

Current progress: Plan approved, starting step 1.
