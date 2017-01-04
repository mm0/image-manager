<?php

namespace mm0\ImageManager\AWS;

use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Aws\S3\S3Client;
use Aws\Common\Exception\MultipartUploadException;
use mm0\ImageManager\SaveInterface;
use mm0\ImageManager\ConnectionInterface;
use mm0\ImageManager\Exceptions\BucketNotFoundException;

/**
 * Class Uploader
 * @package mm0\ImageManager\AWS
 */
class Uploader implements SaveInterface
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
     * @var S3Client
     */
    protected $client;
    /**
     * @var
     */
    protected $key;
    /**
     * @var int
     */
    protected $concurrency;

    protected $debug = false;

    /**
     * Upload constructor.
     * @param ConnectionInterface $connection
     * @param S3Client $client
     * @param int $concurrency
     */
    public function __construct(
        ConnectionInterface $connection,
        $bucket,
        $region,
        $concurrency = 10
    )
    {
        $this->connection = $connection;
        $this->concurrency = $concurrency;
        $this->bucket = $bucket;
        $this->region = $region;
        $this->concurrency = $concurrency;
        $this->client = S3Client::factory([
            "region" => $this->region
        ]);
        $this->testSave();
    }

    /**
     * @throws BucketNotFoundException
     */
    public function testSave()
    {
        if (!$this->client->doesBucketExist($this->bucket)) {
            throw new BucketNotFoundException(
                "S3 bucket (" . $this->bucket . ")  not found in region (" .
                $this->region . ")",
                0
            );
        }
    }

    /**
     * @param string $filename
     */
    public function saveFile($filename)
    {
        $transfer = UploadBuilder::newInstance()
            ->setClient($this->client)
            ->setSource($filename)
            ->setBucket($this->bucket)
            ->setKey($this->key)
            ->setOption('CacheControl', 'max-age=3600')
            ->setConcurrency($this->concurrency)
            ->build();
        try {
            $transfer->upload();
            return true;
        } catch (MultipartUploadException $e) {
            echo $e->getMessage() . "\n";
            $transfer->abort();
            return false;
        }
    }

    /**
     * @param string $filename
     */
    public function saveDirectory($filename)
    {
        $this->client->uploadDirectory(
            $filename,
            $this->bucket,
            $this->key,
            array(
                'concurrency' => $this->concurrency,
                'debug' => $this->debug
            ));
    }

    public function save($filename)
    {
        $this->saveDirectory($filename);
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}