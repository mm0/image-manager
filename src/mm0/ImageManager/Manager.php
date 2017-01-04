<?php

namespace mm0\ImageManager;


use mm0\ImageManager\AWS\Uploader;
use mm0\ImageManager\FileTypes\AbstractFileType;
use mm0\ImageManager\Configuration\S3;
use mm0\ImageManager\Configuration\Filesystem;

class Manager extends BaseClass
{
    /**
     * @var SaveInterface
     */
    protected $save_interface;
    /**
     * @var Configuration\Filesystem
     */
    protected $filesystem_configuration;
    /**
     * @var Configuration\S3
     */
    protected $s3_configuration;

    /**
     * @var ConnectionInterface
     */
    protected $connection;
    /**
     * @var AbstractFileType[]
     */
    protected $valid_filetypes = array();

    /**
     * @var ActionInterface[]
     */
    protected $post_actions = array();

    /**
     * @var int
     * @default unlimited
     */
    protected $file_count_limit = 0;

    /**
     * @return Configuration\Filesystem
     */
    public function getFilesystemConfiguration()
    {
        return $this->filesystem_configuration;
    }

    /**
     * @param Configuration\Filesystem $filesystem_configuration
     */
    public function setFilesystemConfiguration(Filesystem $filesystem_configuration)
    {
        $this->filesystem_configuration = $filesystem_configuration;
    }

    /**
     * @return Configuration\S3
     */
    public function getS3Configuration()
    {
        return $this->s3_configuration;
    }

    /**
     * @param Configuration\S3 $s3_configuration
     */
    public function setS3Configuration(S3 $s3_configuration)
    {
        $this->s3_configuration = $s3_configuration;
    }

    /**
     * @return \mm0\ImageManager\FileTypes\AbstractFileType[]
     */
    public function getValidFiletypes()
    {
        return $this->valid_filetypes;
    }

    /**
     * @param \mm0\ImageManager\FileTypes\AbstractFileType $filetype
     */
    public function addValidFiletype(AbstractFileType $filetype)
    {
        if (!in_array($filetype, $this->valid_filetypes)) {
            $this->valid_filetypes[] = $filetype;
        }
    }

    /**
     * @param \mm0\ImageManager\FileTypes\AbstractFileType[] $filetypes
     */
    public function addValidFiletypes(array $filetypes)
    {
        foreach ($filetypes as $filetype) {
            if ($filetype instanceof AbstractFileType) {
                $this->addValidFiletype($filetype);
            }
        }
    }

    /**
     * @return int
     */
    public function getFileCountLimit()
    {
        return $this->file_count_limit;
    }

    /**
     * @param int $file_count_limit
     */
    public function setFileCountLimit($file_count_limit)
    {
        $this->file_count_limit = $file_count_limit;
    }

    private function initialize()
    {
        $this->setSaveInterface(new Uploader(
            $this->getConnection(),
            $this->getS3Configuration()->getBucket(),
            $this->getS3Configuration()->getRegion(),
            10
        ));
    }

    public function run()
    {
        $this->initialize();
        // use Connection to load (limited) information from Filesystem Configuration Destinations
        $conf = $this->getFilesystemConfiguration();
        $destinations = $conf->getDestinations();
        $limit = $this->getFileCountLimit();

        foreach ($destinations as $destination) {
            if ($this->validateDestination($destination->getPath())) {
                $iteratorType = $destination->returnIterator();
                $this->getConnection()->setIteratorType($iteratorType);
                $iterator = $this->getConnection()->getIterator($destination->getPath());
//                Log::logInfo($iterator);
                // LimitIterator Wrapper
                if ($limit > 0) {
                    $iterator = new \LimitIterator($iterator, 0, $limit);
                }
                iterator_apply(
                    $iterator,
                    array($this, 'iteratorCallback'),
                    array($iterator)
                );
            }
        }
    }

    private function iteratorCallback(\Iterator $iterator)
    {
        $item = $iterator->current();
//        Log::logInfo($item);

        $pathname = $item->getPathname();
        $filename = $item->getFilename();
        if (in_array($filename, array(".", ".."))) return true;

        if ($this->isValidFileType($pathname)) {
            if ($this->processFile($pathname)) {
                $this->processPostActionsForPathname($pathname);
            }
        }
        return true;
    }

    private function processPostActionsForPathname($pathname)
    {
        Log::logInfo("Processing Post Actions for: " . $pathname);
        foreach ($this->getPostActions() as $action) {
            $action::resourceAction($this->getConnection(),$pathname);
        }
    }

    private function isValidFileType($pathname)
    {
        $valid_types = $this->getValidFiletypes();
        $resource = $this->getConnection()->getFileResource($pathname);
        foreach ($valid_types as $type) {
            Log::logInfo("Checking Validity of File: " . $pathname);

            if ($type::isValidFile($pathname, $resource)) {
                Log::logNotice("Valid File: " . $pathname . " Type: " . get_class($type));
                return true;
            }
        }
        $this->getConnection()->closeFileResource($resource);
        return false;
    }

    /**
     * Verify destination exists and is file or directory
     */
    private function validateDestination($destination)
    {
        return $this->getConnection()->file_exists($destination);
    }

    /**
     * Verify destination exists and is file or directory
     */
    private function isDirectory($destination)
    {
        return $this->getConnection()->is_dir($destination);
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SaveInterface
     */
    public function getSaveInterface()
    {
        return $this->save_interface;
    }

    /**
     * @param SaveInterface $save_interface
     */
    public function setSaveInterface(SaveInterface $save_interface)
    {
        $this->save_interface = $save_interface;
    }

    private function processFile($pathname)
    {
        Log::logInfo("processing file: $pathname");
        //TODO: upload file
        $key = pathinfo($pathname);
        $key = $key['basename'];
        $this->getSaveInterface()->setKey($key);
        $result = $this->getSaveInterface()->saveFile($pathname);
        Log::logNotice("Upload for $key successful: " . var_export($result, true));
        return true; //success
    }

    /**
     * @return ActionInterface[]
     */
    public function &getPostActions()
    {
        return $this->post_actions;
    }

    /**
     * @param ActionInterface[] $post_actions
     */
    public function setPostActions(array $post_actions)
    {
        foreach ($post_actions as $action) {
            if ($action instanceof ActionInterface) {
                $this->addPostAction($action);
            }
        }
    }

    /**
     * @param ActionInterface $action
     */
    public function addPostAction(ActionInterface $action)
    {
        $this->getPostActions()[] = $action;
    }

}