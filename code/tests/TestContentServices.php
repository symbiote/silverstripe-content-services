<?php

/**
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class TestContentServices extends SapphireTest {
	/**
	 * @var ContentService
	 */
	protected $contentService;
	
	protected $testDir;
	
	public function setUp() {
		parent::setUp();
		$dir = Director::baseFolder().'/testcontent';
		if (file_exists($dir)) {
			Filesystem::removeFolder($dir);
		}

		$this->contentService = new ContentService();
		
		// set some injector properties
		$injector = Injector::inst();
		
		$injector->load(array(
			'TestContentReader' => array(
				'class'	=> 'FileContentReader',
				'type' => 'prototype',
				'properties'	=> array(
					'basePath'	=> $dir,
				)
			),
			'TestContentWriter' => array(
				'class'	=> 'FileContentWriter',
				'type' => 'prototype',
				'properties'	=> array(
					'basePath'	=> $dir,
				)
			)
		));
		
		$this->contentService->setStores(array(
			'File' => array(
				'ContentReader'		=> 'TestContentReader',
				'ContentWriter'		=> 'TestContentWriter',
			)
		));

		$injector->registerService($this->contentService);

		$this->testDir = $dir;
		
	}

	public function testContentWriter() {
		$writer = $this->contentService->getWriterFor();
		$this->assertTrue($writer instanceof FileContentWriter);

		try {
			$reader = $writer->getReader();
		} catch (Exception $e) {
			$this->assertTrue(strpos($e->getMessage(), 'Null content identifier') !== false);
		}
	}

	/**
	 *
	 * @return FileContentWriter 
	 */
	protected function writeDummy() {
		$writer = $this->contentService->getWriterFor();
		$this->assertTrue($writer instanceof FileContentWriter);
		$writer->write(new RawContentReader("dummy content"), 'dummy.txt');
		
		return $writer;
	}

	public function testWriteContent() {
		$writer = $this->writeDummy();
		$id = $writer->getContentId();
		
		$this->assertNotNull($id);
		$this->assertTrue(strpos($id, 'dummy.txt') > 0);
		$this->assertTrue(strpos($id, 'File:') === 0);
	}
	
	public function testWriteFileContent() {
		$writer = $this->contentService->getWriterFor();
		$this->assertTrue($writer instanceof FileContentWriter);
		
		$writer->write(new FileContentReader(__FILE__), 'test_file.php');
		$id = $writer->getContentId();

		$this->assertNotNull($id);
		$this->assertTrue(strpos($id, 'test_file.php') > 0);
		$this->assertTrue(strpos($id, 'File:') === 0);
	}
	
	public function testReadContent() {
		$writer = $this->writeDummy();
		$id = $writer->getContentId();

		$reader = $this->contentService->getReader($id);
		$this->assertNotNull($reader);
		$this->assertTrue($reader instanceof FileContentReader);
		
		$text = $reader->read();
		$this->assertEquals('dummy content', $text);
	}
	
	public function testListFolder() {
		if (file_exists($this->testDir)) {
			Filesystem::removeFolder($this->testDir);
		}
		
		mkdir($this->testDir);
		file_put_contents($this->testDir.'/testfile.txt', 'dummy_data');
		
		$reader = $this->contentService->getReader('File:||' . $this->testDir);
		
		$list = $reader->getList();
		
		$this->assertEquals(1, count($list));
		
		$file = $list[0];
		
		$writer = $file->getWriter();
		
		$writer->write('new contents');
		
		$this->assertEquals('new contents', file_get_contents($this->testDir.'/testfile.txt'));
	}
}
