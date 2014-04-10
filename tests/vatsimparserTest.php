<?php
require_once('../public/include/vatsimparser.php');

class VatsimParserTest extends PHPUnit_Framework_TestCase {

	public function testFailedServerListFetch() {
		$vp1 = new VatsimParser('testdata-vatsim-servers.txt');

		$data1 = @$vp1->fetchServerList(true);

		$vp2 = new VatsimParser('not_existing.txt');

		$data2 = @$vp2->fetchServerList(true);

		$this->assertEquals($data2, $data1);
	}

	public function testFailedDataFetch() {
		$vp1 = new VatsimParser('testdata-vatsim-servers.txt');

		$data1 = @$vp1->fetchData(true, 'testdata-vatsim.txt');

		$vp2 = new VatsimParser('not_existing.txt');

		$data2 = @$vp2->fetchData(true, 'not_existing.txt');

		$this->assertEquals($data2, $data1);
	}
}
?>