<?php

namespace App\Shell;

use App\Shell\Task\DoSomethingTask;
use Cake\Console\Shell;

/**
 * Class AppShell
 * @package App\Shell
 * @property DoSomethingTask $ImportPlanComptable
 */
class AppShell extends Shell
{
  /**
   * @var array Tasks
   */
  public $tasks = ['DoSomething'];

  /**
   * Manage the available sub-commands along with their arguments and help
   *
   * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
   *
   * @return \Cake\Console\ConsoleOptionParser
   */
  public function getOptionParser()
  {
    $parser = parent::getOptionParser();
    $parser->addSubcommand('doSomething', [
      'help' => DoSomething::HELP,
      'parser' => $this->DoSomething->getOptionParser(),
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
    $this->helper('Table')->output([[self::class]]);
    $this->out($this->OptionParser->help());
    return true;
  }

}