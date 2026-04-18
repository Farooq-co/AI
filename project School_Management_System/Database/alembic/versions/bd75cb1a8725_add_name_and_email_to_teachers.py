"""add name and email to teachers

Revision ID: bd75cb1a8725
Revises: cc581a55f05c
Create Date: 2026-04-19 03:54:45.469924

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision: str = 'bd75cb1a8725'
down_revision: Union[str, Sequence[str], None] = 'cc581a55f05c'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    """Upgrade schema."""
    pass


def downgrade() -> None:
    """Downgrade schema."""
    pass
