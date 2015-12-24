# SteamGenerateMobileAuthPHP
Generate Steam Mobile Auth (2FA) Code using PHP.

# Installation

```
require_once './api/AuthFunctions.php'
```

# Usage
Instantiate a steam auth..

```php
$SteamAuth = new SteamAuth;
```

...generating 2FA Code:

```php
$SteamAuth->GenerateSteamGuardCode("Shared Secret Key");
```

# Proof
https://youtu.be/umoMBn_UTqg
