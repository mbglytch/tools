<?php

namespace App\Shell\Task;

use App\Model\Entity\PlanCompte;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\EntityInterface;
use Cake\Filesystem\Folder;
use Cake\ORM\Query;
use League\Csv\Exception;
use League\Csv\Reader;
use Psr\Log\LogLevel;

/**
 * ImportPlanComptable shell task.
 *
 * Lit les fichiers csv des codes tvas, des journaux et des comptes.
 * Formatte les données et ajoute seulement les nouvelles entités.
 * Pas de mise à jour.
 *
 * @package App\Shell\Task
 * @property \App\Model\Table\PlanCodeTvasTable $PlanCodeTvas
 * @property \App\Model\Table\PlanJournauxTable $PlanJournaux
 * @property \App\Model\Table\PlanComptesTable $PlanComptes
 */
class DoSomethingTask extends Shell
{
  use InstanceConfigTrait;

  /**
   * Message d'aide
   */
  const HELP = 'Do something usefull';
  /**
   * @var array Default Configuration
   */
  protected $_defaultConfig = [];

  /**
   * initialize() method
   *
   * Charge la configuration et les modèles de données
   */
  public function initialize()
  {
    parent::initialize();
    $this->setConfig(Configure::read('DoSomething'));
  }

  /**
   * Manage the available sub-commands along with their arguments and help
   *
   * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
   * @return \Cake\Console\ConsoleOptionParser
   */
  public function getOptionParser()
  {
    $parser = parent::getOptionParser();

    $parser
      ->setDescription([
        '',
        'Do something',
        'very usefull'
      ]);

    return $parser;
  }

  /**
   * main() method.
   *
   * @return bool|int|null Success or error code.
   */
  public function main()
  {
    if ($this->param('verbose')) {
      $this->helper('Table')->output([[self::class]]);
    }
    $this->out('doing something...');
    return true;
  }
}
