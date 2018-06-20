# \App\Mailer\Email Class for CakePHP

## Installation

Copier cette classe dans src/Mailer/.

Dans config/app.php, rajoutez la clé de configuration `subjectPrefix` pour spécifier un prefix à tous les sujets des emails.

Enfin, ne pas oubliez de remplacer ceci :

```
use Cake\Mailer\Email;
```

par ceci :

```
use App\Mailer\Email;
```