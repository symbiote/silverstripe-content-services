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
	
	public function setUp() {
		parent::setUp();
		$dir = Director::baseFolder().'/testcontent';
		if (file_exists($dir)) {
			Filesystem::removeFolder($dir);
		}

		FileContentWriter::$base_path = 'testcontent';
		$this->contentService = new ContentService();
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
}
