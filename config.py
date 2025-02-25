# config.py

import os

class Config:
    """Base configuration class."""
    SECRET_KEY = os.environ.get('SECRET_KEY', 'your_secret_key_here')  # Replace with a secure key in production
    DEBUG = False
    TESTING = False
    DATABASE_URI = os.environ.get('DATABASE_URI', 'sqlite:///cynox.db')  # Default to SQLite database

class DevelopmentConfig(Config):
    """Development configuration."""
    DEBUG = True

class TestingConfig(Config):
    """Testing configuration."""
    TESTING = True
    DATABASE_URI = 'sqlite:///test_cynox.db'

class ProductionConfig(Config):
    """Production configuration."""
    SECRET_KEY = os.environ.get('SECRET_KEY')  # Ensure this is set in the environment
    DATABASE_URI = os.environ.get('DATABASE_URI', 'sqlite:///cynox.db')

# Dictionary to map environment names to configuration classes
configurations = {
    'development': DevelopmentConfig,
    'testing': TestingConfig,
    'production': ProductionConfig
}
