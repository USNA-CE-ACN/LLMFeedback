<?php
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vigenère Cipher Encryption</title>
</head>
<body>

<h2>Vigenère Cipher Encryption Form</h2>

<!-- HTML Form -->
<form method="post">
    <label for="plaintext">Enter text to encrypt:</label><br>
    <textarea id="plaintext" name="plaintext" rows="4" cols="50" required></textarea><br><br>
    <input type="submit" name="submit" value="Encrypt">
</form>

<?php
// PHP Logic for Encryption using Vigenère Cipher
if (isset($_POST['submit'])) {
    $plaintext = strtoupper($_POST['plaintext']); // Convert to uppercase to simplify
    $key = 'KEYWORD'; // Hard-coded key (in uppercase)

    echo "<h3>Original Text:</h3><p>" . htmlentities($plaintext) . "</p>";

    // Function to perform Vigenère Cipher encryption
    function vignereEncrypt($plaintext, $key) {
        $ciphertext = '';
        $keyLength = strlen($key);
        $keyIndex = 0;

        // Loop through each character of the plaintext
        for ($i = 0; $i < strlen($plaintext); $i++) {
            $char = $plaintext[$i];

            // Encrypt only alphabetic characters
            if (ctype_alpha($char)) {
                $plainCharIndex = ord($char) - 65; // Get the index in A-Z (0-25)
                $keyCharIndex = ord($key[$keyIndex % $keyLength]) - 65; // Get the key index

                // Perform Vigenère shift
                $cipherChar = chr((($plainCharIndex + $keyCharIndex) % 26) + 65); // Cipher character
                $ciphertext .= $cipherChar;

                // Move to the next character in the key
                $keyIndex++;
            } else {
                // Non-alphabetic characters are added as-is
                $ciphertext .= $char;
            }
        }

        return $ciphertext;
    }

    // Encrypt the plaintext
    $ciphertext = vignereEncrypt($plaintext, $key);

    echo "<h3>Encrypted Text:</h3><p>" . htmlentities($ciphertext) . "</p>";
}
?>

</body>
</html>