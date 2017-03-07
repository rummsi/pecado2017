<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JObject.
 * Generated by PHPUnit on 2009-09-24 at 17:15:16.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Object
 * @since       11.1
 */
class JObjectTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JObject
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->o = new JObject;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 *
	 * @see     PHPUnit_Framework_TestCase::tearDown()
	 * @since   3.6
	 */
	protected function tearDown()
	{
		unset($this->o);
		parent::tearDown();
	}

	/**
	 * Tests the object constructor.
	 *
	 * @group    JObject
	 * @covers    JObject::__construct
	 * @return void
	 */
	public function test__construct()
	{
		$this->object = new JObject(array('property1' => 'value1', 'property2' => 5));
		$this->assertThat(
			$this->object->get('property1'),
			$this->equalTo('value1')
		);
	}

	/**
	 * Tests setting the default for a property of the object.
	 *
	 * @group    JObject
	 * @covers    JObject::def
	 * @return void
	 */
	public function testDef()
	{
		$this->o->def("check");
		$this->assertEquals(null, $this->o->def("check"));
		$this->o->def("check", "paint");
		$this->o->def("check", "forced");
		$this->assertEquals("paint", $this->o->def("check"));
		$this->assertNotEquals("forced", $this->o->def("check"));
	}

	/**
	 * Tests getting a property of the object.
	 *
	 * @group    JObject
	 * @covers    JObject::get
	 * @return void
	 */
	public function testGet()
	{
		$this->o->goo = 'car';
		$this->assertEquals('car', $this->o->get('goo', 'fudge'));
		$this->assertEquals('fudge', $this->o->get('foo', 'fudge'));
		$this->assertNotEquals(null, $this->o->get('foo', 'fudge'));
		$this->assertNull($this->o->get('boo'));
	}

	/**
	 * Tests getting the properties of the object.
	 *
	 * @group    JObject
	 * @covers    JObject::getProperties
	 * @return void
	 */
	public function testGetProperties()
	{
		$this->object = new JObject(
			array(
				'_privateproperty1' => 'valuep1',
				'property1' => 'value1',
				'property2' => 5
			)
		);
		$this->assertEquals(
			array(
				'_errors' => array(),
				'_privateproperty1' => 'valuep1',
				'property1' => 'value1',
				'property2' => 5
			),
			$this->object->getProperties(false),
			'Should get all properties, including private ones'
		);
		$this->assertEquals(
			array(
				'property1' => 'value1',
				'property2' => 5
			),
			$this->object->getProperties(),
			'Should get all public properties'
		);
	}

	/**
	 * Tests getting a single error.
	 *
	 * @group    JObject
	 * @covers    JObject::getError
	 * @return void
	 */
	public function testGetError()
	{
		$this->o->setError(1234);
		$this->o->setError('Second Test Error');
		$this->o->setError('Third Test Error');
		$this->assertEquals(
			1234,
			$this->o->getError(0, false),
			'Should return the test error as number'
		);
		$this->assertEquals(
			'Second Test Error',
			$this->o->getError(1),
			'Should return the second test error'
		);
		$this->assertEquals(
			'Third Test Error',
			$this->o->getError(),
			'Should return the third test error'
		);
		$this->assertFalse(
			$this->o->getError(20),
			'Should return false, since the error does not exist'
		);

		$exception = new Exception('error');
		$this->o->setError($exception);
		$this->assertThat(
			$this->o->getError(3, true),
			$this->equalTo((string) $exception)
		);
	}

	/**
	 * Tests getting the array of errors.
	 *
	 * @group    JObject
	 * @covers    JObject::getErrors
	 * @return void
	 */
	public function testGetErrors()
	{
		$errors = array(1234, 'Second Test Error', 'Third Test Error');

		foreach ($errors as $error)
		{
			$this->o->setError($error);
		}
		$this->assertAttributeEquals(
			$this->o->getErrors(),
			'_errors',
			$this->o
		);
		$this->assertEquals(
			$errors,
			$this->o->getErrors(),
			'Should return every error set'
		);
	}

	/**
	 * Tests setting a property.
	 *
	 * @group    JObject
	 * @covers    JObject::set
	 * @return void
	 */
	public function testSet()
	{
		$this->assertEquals(null, $this->o->set("foo", "imintheair"));
		$this->assertEquals("imintheair", $this->o->set("foo", "nojibberjabber"));
		$this->assertEquals("nojibberjabber", $this->o->foo);
	}

	/**
	 * Tests setting multiple properties.
	 *
	 * @group    JObject
	 * @covers    JObject::setProperties
	 * @return void
	 */
	public function testSetProperties()
	{
		$a = array("foo" => "ghost", "knife" => "stewie");
		$f = "foo";
		$this->assertEquals(true, $this->o->setProperties($a));
		$this->assertEquals(false, $this->o->setProperties($f));
		$this->assertEquals("ghost", $this->o->foo);
		$this->assertEquals("stewie", $this->o->knife);
	}

	/**
	 * Tests setting an error.
	 *
	 * @group    JObject
	 * @covers    JObject::setError
	 * @return void
	 */
	public function testSetError()
	{
		$this->o->setError('A Test Error');
		$this->assertAttributeEquals(
			array('A Test Error'),
			'_errors',
			$this->o
		);
	}
}
