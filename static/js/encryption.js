/* static/js/encryption.js */

/**
 * Handles client-side encryption and decryption for the CyNoX application.
 * Utilizes the Web Crypto API for secure cryptographic operations.
 */

// Constants
const SALT_SIZE = 16; // Size of the salt in bytes
const ITERATIONS = 100000; // Number of iterations for PBKDF2
const KEY_LENGTH = 256; // Key length in bits
const IV_SIZE = 12; // Size of the IV for AES-GCM in bytes

/**
 * Generates a cryptographic key from a password and salt using PBKDF2.
 *
 * @param {string} password - The password to derive the key from.
 * @param {Uint8Array} salt - A random salt for key derivation.
 * @returns {Promise<CryptoKey>} - The derived cryptographic key.
 */
async function generateKey(password, salt) {
    const passwordKey = await window.crypto.subtle.importKey(
        "raw",
        new TextEncoder().encode(password),
        "PBKDF2",
        false,
        ["deriveKey"]
    );

    return window.crypto.subtle.deriveKey(
        {
            name: "PBKDF2",
            salt: salt,
            iterations: ITERATIONS,
            hash: "SHA-256"
        },
        passwordKey,
        {
            name: "AES-GCM",
            length: KEY_LENGTH
        },
        false,
        ["encrypt", "decrypt"]
    );
}

/**
 * Encrypts a plaintext message using AES-GCM.
 *
 * @param {string} message - The plaintext message to encrypt.
 * @param {string} password - The password to derive the encryption key.
 * @returns {Promise<string>} - The base64-encoded ciphertext, including the salt and IV.
 */
async function encryptMessage(message, password) {
    const salt = window.crypto.getRandomValues(new Uint8Array(SALT_SIZE));
    const iv = window.crypto.getRandomValues(new Uint8Array(IV_SIZE));
    const key = await generateKey(password, salt);

    const encryptedData = await window.crypto.subtle.encrypt(
        {
            name: "AES-GCM",
            iv: iv
        },
        key,
        new TextEncoder().encode(message)
    );

    // Combine salt, IV, and ciphertext into a single array
    const combinedData = new Uint8Array(salt.length + iv.length + encryptedData.byteLength);
    combinedData.set(salt);
    combinedData.set(iv, salt.length);
    combinedData.set(new Uint8Array(encryptedData), salt.length + iv.length);

    // Encode the combined data as base64
    return btoa(String.fromCharCode(...combinedData));
}

/**
 * Decrypts a ciphertext message using AES-GCM.
 *
 * @param {string} encryptedMessage - The base64-encoded ciphertext, including the salt and IV.
 * @param {string} password - The password to derive the decryption key.
 * @returns {Promise<string>} - The decrypted plaintext message.
 */
async function decryptMessage(encryptedMessage, password) {
    const combinedData = Uint8Array.from(atob(encryptedMessage), c => c.charCodeAt(0));

    // Extract the salt, IV, and ciphertext
    const salt = combinedData.slice(0, SALT_SIZE);
    const iv = combinedData.slice(SALT_SIZE, SALT_SIZE + IV_SIZE);
    const ciphertext = combinedData.slice(SALT_SIZE + IV_SIZE);

    const key = await generateKey(password, salt);

    const decryptedData = await window.crypto.subtle.decrypt(
        {
            name: "AES-GCM",
            iv: iv
        },
        key,
        ciphertext
    );

    return new TextDecoder().decode(decryptedData);
}

// Example usage (for testing purposes only)
(async () => {
    const password = "securepassword";
    const message = "This is a secret message.";

    console.log("Original Message:", message);

    const encrypted = await encryptMessage(message, password);
    console.log("Encrypted Message:", encrypted);

    const decrypted = await decryptMessage(encrypted, password);
    console.log("Decrypted Message:", decrypted);
})();
