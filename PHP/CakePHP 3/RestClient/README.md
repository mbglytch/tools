# RestClient Class for CakePHP

## Installation

Copier cette classe dans src/Client/.

Vous pouvez maintenant créer votre propre client qui héritera de cette classe.

Voici un exemple d'implémentation avec un client MailChimp :

```
<?php

namespace App\Client;

use Cake\Core\Configure;

class MailChimpClient extends RestClient
{
    /**
     * MailChimpClient constructor.
     *
     * @param array $config The config
     */
    public function __construct($config = array())
    {
        $config += [
            'debug' => Configure::read('debug'),
            'auth' => ['username' => 'USERNAME', 'password' => Configure::read('MailChimp.apiKey')],
            'url' => sprintf("https://%s.api.mailchimp.com/%s/",
                Configure::read('MailChimp.dc'),
                Configure::read('MailChimp.version')
            )
        ];
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function _connect($url = null, $method = 'get', $options = null)
    {
        $connect = parent::_connect($url, $method, $options);
        return $connect ? json_decode($connect) : $connect;
    }
    
    /**
     * Hash an email with md5
     *
     * @param $email The email to hash
     * @return string The hashed email
     */
    public function hashEmail($email)
    {
        return hash('md5', strtolower($email));
    }

    /**
     * Get the users' members list endpoint
     *
     * @return string The endpoint
     */
    public function getEndpointUsers()
    {
        return sprintf("lists/%s/members", Configure::read('MailChimp.lists.users'));
    }

}

```

Et un exemple d'utilisation :

```
/**
 * @var MailChimpClient
 */
protected $client;
```
```
$this->client = new MailChimpClient();
```
```
return $this->client->post($this->client->getEndpointUsers(), json_encode($data));
```