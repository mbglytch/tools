<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Mailer;

use Cake\Mailer\Email as BaseEmail;

/**
 * CakePHP Email class.
 *
 * This class is used for sending Internet Message Format based
 * on the standard outlined in https://www.rfc-editor.org/rfc/rfc2822.txt
 *
 * ### Configuration
 *
 * Configuration for Email is managed by Email::config() and Email::configTransport().
 * Email::config() can be used to add or read a configuration profile for Email instances.
 * Once made configuration profiles can be used to re-use across various email messages your
 * application sends.
 */
class Email extends BaseEmail
{

    /**
     * The subject prefix
     *
     * @var string|null
     */
    protected $_subjectPrefix = '';

    /**
     * Apply the config to an instance
     *
     * @param string|array $config Configuration options.
     * @return void
     * @throws \InvalidArgumentException When using a configuration that doesn't exist.
     */
    protected function _applyConfig($config)
    {
        parent::_applyConfig($config);
        if (isset($config['subjectPrefix']) and !empty($config['subjectPrefix'])) {
            $this->setSubjectPrefix($config['subjectPrefix']);
        }
    }

    /**
     * Sets subject.
     *
     * @param string $subject Subject string.
     * @return $this
     */
    public function setSubject($subject)
    {
        if (!empty($this->_subjectPrefix)) {
            $subject = $this->_subjectPrefix . ' ' . $subject;
        }
        return parent::setSubject($subject);
    }

    /**
     * Sets subject prefix.
     *
     * @param string $subjectPrefix Subject prefix string.
     * @return $this
     */
    public function setSubjectPrefix($subjectPrefix)
    {
        $this->_subjectPrefix = (string)$subjectPrefix;
        return $this;
    }

}
