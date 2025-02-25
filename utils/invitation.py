# utils/invitation.py

import os
import base64
import hashlib
from datetime import datetime, timedelta
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC

# Constants
SALT_SIZE = 16  # Size of the salt in bytes
ITERATIONS = 100000  # Number of iterations for PBKDF2
EXPIRATION_DAYS = 7  # Default expiration time for invitation links

def generate_invitation_link(base_url: str, secret_key: str) -> str:
    """
    Generate a secure invitation link.

    Args:
        base_url (str): The base URL of the application.
        secret_key (str): A secret key used to generate the link.

    Returns:
        str: A secure invitation link.
    """
    # Generate a random salt
    salt = os.urandom(SALT_SIZE)

    # Generate a unique token using PBKDF2
    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=32,
        salt=salt,
        iterations=ITERATIONS,
    )
    token = base64.urlsafe_b64encode(kdf.derive(secret_key.encode())).decode()

    # Combine the base URL, token, and expiration timestamp
    expiration_timestamp = (datetime.utcnow() + timedelta(days=EXPIRATION_DAYS)).timestamp()
    link = f"{base_url}/invite?token={token}&expires_at={int(expiration_timestamp)}"

    return link

def validate_invitation_link(link: str, secret_key: str) -> bool:
    """
    Validate a secure invitation link.

    Args:
        link (str): The invitation link to validate.
        secret_key (str): The secret key used to generate the link.

    Returns:
        bool: True if the link is valid and not expired, False otherwise.
    """
    try:
        # Parse the token and expiration timestamp from the link
        query_params = dict(param.split('=') for param in link.split('?')[1].split('&'))
        token = query_params.get('token')
        expires_at = int(query_params.get('expires_at'))

        # Check if the link has expired
        if datetime.utcnow().timestamp() > expires_at:
            return False

        # Recreate the token using the secret key and salt
        salt = base64.urlsafe_b64decode(token)[:SALT_SIZE]
        kdf = PBKDF2HMAC(
            algorithm=hashes.SHA256(),
            length=32,
            salt=salt,
            iterations=ITERATIONS,
        )
        expected_token = base64.urlsafe_b64encode(kdf.derive(secret_key.encode())).decode()

        # Validate the token
        return token == expected_token
    except Exception:
        return False

# Example usage (for testing purposes only)
if __name__ == "__main__":
    base_url = "https://cynox.app"
    secret_key = "supersecretkey"

    # Generate an invitation link
    invitation_link = generate_invitation_link(base_url, secret_key)
    print(f"Generated Invitation Link: {invitation_link}")

    # Validate the invitation link
    is_valid = validate_invitation_link(invitation_link, secret_key)
    print(f"Is the invitation link valid? {is_valid}")
