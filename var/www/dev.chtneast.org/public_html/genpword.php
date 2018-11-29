<?php

//$randomBytes = bin2hex(random_bytes(8));
$randomBytes = 'Cats4Loki';
//$randomBytes = "CHTN2587";
$options = [
    'cost' => 12,
];
$pword =  password_hash($randomBytes, PASSWORD_BCRYPT, $options);

echo $randomBytes . " :: " . $pword;
echo "<p>";

if (password_verify($randomBytes, $pword)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}

