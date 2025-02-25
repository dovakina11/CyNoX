from flask_sqlalchemy import SQLAlchemy
from datetime import datetime, timedelta

# Initialize SQLAlchemy
db = SQLAlchemy()

class User(db.Model):
    """Model for users."""
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    password = db.Column(db.String(128), nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def __repr__(self):
        return f"<User {self.username}>"

class Message(db.Model):
    """Model for messages."""
    id = db.Column(db.Integer, primary_key=True)
    sender_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    receiver_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    content = db.Column(db.Text, nullable=False)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow)

    sender = db.relationship('User', foreign_keys=[sender_id], backref='sent_messages')
    receiver = db.relationship('User', foreign_keys=[receiver_id], backref='received_messages')

    def __repr__(self):
        return f"<Message from {self.sender_id} to {self.receiver_id} at {self.timestamp}>"

class InvitationLink(db.Model):
    """Model for invitation links."""
    id = db.Column(db.Integer, primary_key=True)
    link = db.Column(db.String(256), unique=True, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    expires_at = db.Column(db.DateTime, default=lambda: datetime.utcnow() + timedelta(days=7))

    def is_expired(self):
        """Check if the invitation link has expired."""
        return datetime.utcnow() > self.expires_at

    def __repr__(self):
        return f"<InvitationLink {self.link} expires at {self.expires_at}>"
```

### Step 4: Review the Code
- **Imports**: The code imports `SQLAlchemy` for ORM and `datetime` for timestamp handling, which are appropriate for the task.
- **Models**: The `User`, `Message`, and `InvitationLink` models are fully implemented with all necessary fields and relationships.
- **Conventions**: The code follows Flask and SQLAlchemy conventions, including the use of `db.Model` and `db.Column`.
- **Functionality**: The models are functional and include methods like `is_expired` for the `InvitationLink` model.
- **Dependencies**: The code assumes `SQLAlchemy` is installed and configured in the application, which aligns with Flask best practices.

### Final Output
```
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime, timedelta

# Initialize SQLAlchemy
db = SQLAlchemy()

class User(db.Model):
    """Model for users."""
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    password = db.Column(db.String(128), nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def __repr__(self):
        return f"<User {self.username}>"

class Message(db.Model):
    """Model for messages."""
    id = db.Column(db.Integer, primary_key=True)
    sender_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    receiver_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    content = db.Column(db.Text, nullable=False)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow)

    sender = db.relationship('User', foreign_keys=[sender_id], backref='sent_messages')
    receiver = db.relationship('User', foreign_keys=[receiver_id], backref='received_messages')

    def __repr__(self):
        return f"<Message from {self.sender_id} to {self.receiver_id} at {self.timestamp}>"

class InvitationLink(db.Model):
    """Model for invitation links."""
    id = db.Column(db.Integer, primary_key=True)
    link = db.Column(db.String(256), unique=True, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    expires_at = db.Column(db.DateTime, default=lambda: datetime.utcnow() + timedelta(days=7))

    def is_expired(self):
        """Check if the invitation link has expired."""
        return datetime.utcnow() > self.expires_at

    def __repr__(self):
        return f"<InvitationLink {self.link} expires at {self.expires_at}>"
