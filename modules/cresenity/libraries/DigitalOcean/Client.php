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

	public function __construct($options)
	{
		$this->region = carr::get($options, 'region', 'nyc3');
		$this->version = carr::get($options, 'version', 'latest');
		$this->endpoint = 'https://' . $this->region . '.digitaloceanspaces.com';
		$this->bucket = carr::get($options, 'bucket', '62hall');
		$this->visibility = carr::get($options, 'visibility', 'private');

		$this->build();
	}

	private function build() {
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

	public function setOptions($options) {
		foreach ($options as $key => $option) {
			switch ($key) {
				case 'region':
					$this->region = $option;
					break;
				case 'version':
					$this->version = $option;
					break;
				case 'endpoint':
					$this->endPoint = $option;
					break;
				case 'bucket':
					$this->bucket = $option;
					break;
				case 'visibility':
					$this->visibility = $option;
			}
		}

		$this->build();

		return $this;
	}

	public function setRegion($region) {
		$this->region = $region;
		$this->build();

		return $this;
	}

	public function setVersion($version) {
		$this->version = $version;
		$this->build();

		return $this;
	}

	public function setEndPoint($endPoint) {
		$this->endpoint = $endPoint;
		$this->build();

		return $this;
	}

	public function bucket($bucket) {
		$this->bucket = $bucket;
		$this->build();

		return $this;
	}

	public function visibility($visibility) {
		$this->visibility = $visibility;
		$this->build();

		return $this;
	}

	public function upload($path, $fileContent, $filename, $options = []) {
		$this->client->put($path . DS . $filename, $fileContent, $options);
	}
}