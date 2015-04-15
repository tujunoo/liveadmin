<?php

/**
 * Extends CDbCommand for read/write splitting.
 */
class DbCommand extends CDbCommand
{

    /**
     * Use slaves if it is ok.
     */
    public function getConnection()
    {
        $sql = $this->getText();

        if (self::isOkFromSlaves($sql)) {
            //if (YII_DEBUG) echo '<!-- slaves: ', $sql, ' -->';
            Yii::trace('Read from slaves: ' . $sql, 'app.extensions.DbCommand');
            return Yii::app()->db->getActiveSlave();
        }

        //if (YII_DEBUG) echo '<!-- master: ', $sql, ' -->';
        Yii::trace('Read/write with master: ' . $sql, 'app.extensions.DbCommand');
        return parent::getConnection();
    }

    /**
     * Check if it is ok from slaves.
     */
    public static function isOkFromSlaves($sql)
    {
        $db = Yii::app()->db;

        if ($db->forceMaster || $db->getCurrentTransaction()) {
            return false;
        }

        if (!preg_match('/^[\W]*(SELECT|SHOW|DESCRIBE|PRAGMA)\s/i', $sql)) {
            return false;
        }

        if (!$db->getActiveSlave()) {
            return false;
        }

        return true;
    }
}
