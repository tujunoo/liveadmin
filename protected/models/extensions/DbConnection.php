<?php

Yii::import('application.models.extensions.DbCommand');

/**
 * Extends CDbConnection for read/write splitting.
 */
class DbConnection extends CDbConnection
{

    public $forceMaster = false;

    public $slave = array();

    /**
     * Override, use DbCommand for splitting.
     */
    public function createCommand($query = null)
    {
        if (is_string($query) && DbCommand::isOkFromSlaves($query)) {
            return new DbCommand($this->getActiveSlave(), $query);
        }

        $this->setActive(true);
        return new DbCommand($this, $query);
    }

    /**
     * Try to get an alive slave db connection.
     */
    public function getActiveSlave()
    {
        static $activeSlave = null;

        if ($activeSlave === null) {
            if (!$this->isSlaveAlive()) {
                Yii::trace('Slaves is gone', 'app.extensions.DbConnection');
                return null;
            }

            $config = $this->slave;
            if (!isset($config['class'])) {
                $config['class'] = 'CDbConnection';
            }
            $config['autoConnect'] = false;
            try {
                $slave = Yii::createComponent($config);
                if ($slave) {
                    $slave->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $slave->setActive(true);
                    $activeSlave = $slave;
                }
            } catch (Exception $e) {
                Yii::log("Slave database connection failed!\n\tConnection string:" . $config['connectionString'], 'warning');
            }
        }
        return $activeSlave;
    }

    /**
     * Check if slave is alive, in 0.5 second
     *
     * @return boolean
     */
    protected function isSlaveAlive()
    {
        static $isAlive = null;

        if ($isAlive === null) {
            try {
                $config = $this->slave;

                if (preg_match('/host=([a-z\d\.]+)/', $config['connectionString'], $matches)) {
                    $host = $matches[1];
                } else {
                    throw new Exception('Incorrect connection string.');
                }

                if (preg_match('/port=(\d+)/', $config['connectionString'], $matches)) {
                    $port = $matches[1];
                } else {
                    throw new Exception('Incorrect connection string.');
                }

                $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                socket_set_nonblock($socket);
                if (!@socket_connect($socket, $host, $port)) {
                    $r = $w = array($socket);
                    if (socket_select($r, $w, $e, 5) < 1) {
                        throw new Exception('Cannot connect to slave');
                    }
                    $isAlive = empty($r);
                }
            } catch (Exception $e) {
                $isAlive = false;
                Yii::trace($e->getMessage(), 'app.extensions.DbConnection');
            }
        }
        return $isAlive;
    }

    /**
     * Switch to master before beginning a transaction.
     */
    public function beginTransaction()
    {
        $this->forceMaster = true;

        return parent::beginTransaction();
    }
}
