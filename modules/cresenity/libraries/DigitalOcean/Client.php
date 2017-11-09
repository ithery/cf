<?php

class DigitalOcean_Client
{
	private $key = 'TNYA2K7WT6IU2GCCVOED';
	private $secret = 'zfxsdLTOpMmLZndI1lJ4K/I+cFITVqS85c5YT7jPkC4';
	private $client;
	private $region;
	private $version;
	private $endpoint;
	private $bucket;
	private $visibility;

	function __construct($options)
	{
		$this->region = carr::get($options, 'region', 'nyc3');
		$this->version = carr::get($options, 'version', 'latest');
		$this->endpoint = 'https://' . $this->region . '.digitaloceanspaces.com';
		$this->bucket = carr::get($options, 'bucket', '62hall');
		$this->visibility = carr::get($options, 'visibility', 'private');

		$awsS3Client = new Aws_S3_S3Client([
		    'credentials' => [
		        'key' => $this->key,
		        'secret' => $this->secret,
		    ],
		    'region' => $this->region,
		    'version' => $this->version,
		    'endpoint' => $this->endpoint,
		]);

		$adapter = new League_Flysystem_AwsS3v3_AwsS3Adapter($awsS3Client, $this->bucket);
		$filesystem = new League_Flysystem_Filesystem($adapter, [
		    'visibility' => $this->visibility,
		]);

		$this->client = $filesystem;
	}
}