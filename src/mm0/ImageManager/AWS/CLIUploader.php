<?php

namespace mm0\ImageManager\AWS;

use mm0\ImageManager\Log;
use mm0\ImageManager\SaveInterface;
use mm0\ImageManager\ConnectionInterface;
use mm0\ImageManager\Exceptions\CLINotFoundException;
use mm0\ImageManager\Exceptions\BucketNotFoundException;

/**
 * Class Uploader
 * @package mm0\ImageManager\AWS
 */
class CLIUploader implements SaveInterface
{

    /**
     * @var ConnectionInterface
     */
    protected $connection;
    /**
     * @var
     */
    protected $bucket;
    /**
     * @var
     */
    protected $region;
    /**
     * @var
     */
    protected $source;
    /**
     * @var
     */
    protected $key;
    /**
     * @var
     */
    protected $remove_file_after_upload;
    /**
     * @var int
     */
    protected $concurrency;
    /**
     * @var string
     */
    protected $binary = "aws";

    /**
     * Upload constructor.
     * @param $connection
     * @param $bucket
     * @param $key
     * @param $region
     * @param bool $remove_file_after_upload
     * @param int $concurrency
     */
    public function __construct(
        ConnectionInterface $connection,
        $bucket,
        $region,
        $concurrency = 10
    ) {
        $this->connection = $connection;
        $this->bucket = $bucket;
        $this->region = $region;
        $this->concurrency = $concurrency;
        $this->testSave();
        $this->verify();
    }

    /**
     * @throws BucketNotFoundException
     * @throws CLINotFoundException
     */
    public function testSave()
    {
        $command = "which " . $this->binary;
        $response = $this->connection->executeCommand($command);
        if (strlen($response->stdout()) == 0 || preg_match("/not found/i", $response->stdout())) {
            throw new CLINotFoundException(
                $this->binary . " CLI not installed.",
                0
            );
        }
        /*
         * TODO: Check that credentials work
         */
        $command = $this->binary .
            " --region " . $this->region .
            " s3 ls " . $this->bucket ." 2>&1 | grep -c 'AllAccessDisabled\|NoSuchBucket'" ;
        $response = $this->connection->executeCommand($command);
        if (intval($response->stdout()) == 1) {
            throw new BucketNotFoundException(
                "S3 bucket (" . $this->bucket . ")  not found in region (" . $this->region . ")",
                0
            );
        }

    }

    /**
     * @param string $filename
     */
    public function save($filename)
    {
        # upload compressed file to s3
        $command = $this->binary .
            " s3 sync $filename s3://" .
            $this->bucket .
            "/" .
            $this->key;
        Log::logDebug($command);
        $response = $this->connection->executeCommand(
            $command
        );
        Log::logDebug($response->stdout());
        Log::logError($response->stderr());

    }

    public function saveFile($filename){
        $this->save($filename);
    }
    public function saveDirectory($filename){
        $this->save($filename);
    }

    /**
     *
     */
    public function verify()
    {

    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}