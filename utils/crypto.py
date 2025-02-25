from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.padding import PKCS7
from cryptography.hazmat.backends import default_backend
import os
import base64

# Constants
SALT_SIZE = 16  # Size of the salt in bytes
KEY_SIZE = 32   # AES-256 requires a 32-byte key
IV_SIZE = 16    # AES block size
ITERATIONS = 100000  # Number of iterations for PBKDF2

def generate_key(password: str, salt: bytes) -> bytes:
    """
    Generate a symmetric encryption key from a password and salt using PBKDF2.
    
    Args:
        password (str): The password to derive the key from.
        salt (bytes): A random salt for key derivation.
    
    Returns:
        bytes: The derived encryption key.
    """
    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=KEY_SIZE,
        salt=salt,
        iterations=ITERATIONS,
        backend=default_backend()
    )
    return kdf.derive(password.encode())

def encrypt_message(message: str, password: str) -> str:
    """
    Encrypt a message using AES-256 in CBC mode with PKCS7 padding.
    
    Args:
        message (str): The plaintext message to encrypt.
        password (str): The password to derive the encryption key.
    
    Returns:
        str: The base64-encoded ciphertext, including the salt and IV.
    """
    # Generate a random salt and IV
    salt = os.urandom(SALT_SIZE)
    iv = os.urandom(IV_SIZE)
    
    # Derive the encryption key
    key = generate_key(password, salt)
    
    # Initialize the cipher
    cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
    encryptor = cipher.encryptor()
    
    # Pad the message and encrypt
    padder = PKCS7(algorithms.AES.block_size).padder()
    padded_message = padder.update(message.encode()) + padder.finalize()
    ciphertext = encryptor.update(padded_message) + encryptor.finalize()
    
    # Combine salt, IV, and ciphertext and encode as base64
    encrypted_data = base64.b64encode(salt + iv + ciphertext).decode()
    return encrypted_data

def decrypt_message(encrypted_data: str, password: str) -> str:
    """
    Decrypt a message encrypted with AES-256 in CBC mode with PKCS7 padding.
    
    Args:
        encrypted_data (str): The base64-encoded ciphertext, including the salt and IV.
        password (str): The password to derive the decryption key.
    
    Returns:
        str: The decrypted plaintext message.
    """
    # Decode the base64-encoded data
    encrypted_data_bytes = base64.b64decode(encrypted_data)
    
    # Extract the salt, IV, and ciphertext
    salt = encrypted_data_bytes[:SALT_SIZE]
    iv = encrypted_data_bytes[SALT_SIZE:SALT_SIZE + IV_SIZE]
    ciphertext = encrypted_data_bytes[SALT_SIZE + IV_SIZE:]
    
    # Derive the decryption key
    key = generate_key(password, salt)
    
    # Initialize the cipher
    cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
    decryptor = cipher.decryptor()
    
    # Decrypt and unpad the message
    padded_message = decryptor.update(ciphertext) + decryptor.finalize()
    unpadder = PKCS7(algorithms.AES.block_size).unpadder()
    message = unpadder.update(padded_message) + unpadder.finalize()
    
    return message.decode()

# Example usage (for testing purposes only)
if __name__ == "__main__":
    password = "securepassword"
    message = "This is a secret message."
    
    encrypted = encrypt_message(message, password)
    print(f"Encrypted: {encrypted}")
    
    decrypted = decrypt_message(encrypted, password)
    print(f"Decrypted: {decrypted}")
