# RestClient Class for CakePHP

## Installation

Copier cette classe dans src/Client/.

Vous pouvez maintenant créer votre propre client qui héritera de cette classe.

Vous pouvez consulter un exemple d'implémentation avec la classe `MailChimpClient.php`.

```
$client = new MailChimpClient();
$client->post($client->getEndpointUsers(), json_encode($data));
```