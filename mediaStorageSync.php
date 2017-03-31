<?php
require_once 'abstract.php';

class Webtise_Shell_MediaStorageSync extends Mage_Shell_Abstract
{
    /**
     * Storage systems ids
     */
    const files     = 0;
    const database  = 1;

    /**
     * Storage to sync to
     *
     * @var string
     */
    protected $_storage;

    /**
     * Connection type to use
     *
     * @var string
     */
    protected $_connection = 'default_setup';

    public function __construct() {
        parent::__construct();

        set_time_limit(0);

        if(!$this->getArg('storage')) {
            echo 'Please specify the storage you would like to sync to.';
            return;
        }

        switch ($this->getArg('storage')) {
            case 'file':
                $this->_storage = self::files;
                break;
            case 'files':
                $this->_storage = self::files;
                break;
            case 'db':
                $this->_storage = self::database;
                break;
            case 'database':
                $this->_storage = self::database;
                break;
            default:
                echo 'Please specify the storage you would like to sync to, `db` or `files`';
                return;
        }

        if($connection = $this->getArg('connection')) {
            $this->_connection = $connection;
        }
    }

    /**
     * Run - point of entry
     */
    public function run() {

        $flag = Mage::getSingleton('core/file_storage')->getSyncFlag();
        if ($flag && $flag->getState() == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING
            && $flag->getLastUpdate()
            && time() <= (strtotime($flag->getLastUpdate()) + Mage_Core_Model_File_Storage_Flag::FLAG_TTL)
        ) {
            echo 'Sync is already running. Please try again shortly.';
            return;
        }
        $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_RUNNING)->save();
        Mage::getSingleton('admin/session')->setSyncProcessStopWatch(false);

        $storage = array(
            'type'          => (int) $this->_storage,
            'connection'    => $this->_connection
        );

        try {
            Mage::getSingleton('core/file_storage')->synchronize($storage);
            echo 'Sync Successful';
        } catch (Exception $e) {
            Mage::logException($e);
            echo $e->getMessage().'@'.time();
            $flag->passError($e);
        }
        $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_FINISHED)->save();
    }

    /**
     * Usage Instructions
     *
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php mediaStorageSync.php -- [options]
  --storage <name>          Storage to sync to
  --connection <name>       Specify connection 'default_setup' is used if not passed
  help                      This help
USAGE;
    }

}


$shell = new Webtise_Shell_MediaStorageSync();
$shell->run();