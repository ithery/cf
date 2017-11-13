<?php

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

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
		$this->visibility = carr::get($options, 'visibility', 'public');

		$this->build();
	}

	private function build() {
		$S3Client = new S3Client([
		    'credentials' => [
		        'key' => $this->key,
		        'secret' => $this->secret,
		    ],
		    'region' => $this->region,
		    'version' => $this->version,
		    'endpoint' => $this->endpoint,
		]);

		$adapter = new AwsS3Adapter($S3Client, $this->bucket);

		$options = [
			'visibility' => $this->visibility,
		];

		$filesystem = new Filesystem($adapter, $options);

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

	public function setBucket($bucket) {
		$this->bucket = $bucket;
		$this->build();

		return $this;
	}

	public function setVisibility($visibility) {
		$this->visibility = $visibility;
		$this->build();

		return $this;
	}

	public function upload($path, $fileContent, $filename, $options = []) {
		return $this->client->put($path . DS . $filename, $fileContent, $options);
	}

	public function copy($path, $newPath) {
		return $this->client->copy($path, $newPath);
	}

	public function delete($path) {
		return $this->client->delete($path);
	}

	public function listContent($directory) {
		return $this->client->listContents($directory);
	}
}