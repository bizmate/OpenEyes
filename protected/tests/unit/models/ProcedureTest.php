<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class ProcedureTest extends CDbTestCase {

                       public $model;
                       public $fixtures = array(
                                                'procedures' => 'Procedure',
                       );

                       public function setUp() {
                                              parent::setUp();
                                              $this->model = new Procedure;
                       }

                       public function dataProvider_ProcedureSearch() {
                                              return array(
                                                                       array('Foo', array('Foobar Procedure')),
                                                                       array('Foobar', array('Foobar Procedure')),
                                                                       array('Fo', array('Foobar Procedure')),
                                                                       array('UB', array('Foobar Procedure')),
                                                                       array('Bla', array('Foobar Procedure')),
                                                                       array('wstfgl', array('Foobar Procedure')),
                                                                       array('barfoo', array('Foobar Procedure')),
                                                                       array('Test', array('Test Procedure')),
                                                                       array('Test Pro', array('Test Procedure')),
                                                                       array('Te', array('Test Procedure')),
                                                                       array('TP', array('Test Procedure')),
                                                                       array('leh', array('Test Procedure')),
																	   array('Pro', array('Foobar Procedure', 'Test Procedure')),
                                              );
                       }

                       /**
                        * @covers Procedure::attributeLabels
                        * @todo   Implement testAttributeLabels().
                        */
                       public function testAttributeLabels() {
                                              $expected = array(
                                                                       'id' => 'ID',
                                                                       'term' => 'Term',
                                                                       'short_format' => 'Short Format',
                                                                       'default_duration' => 'Default Duration',
                                              );

                                              $this->assertEquals($expected, $this->model->attributeLabels());
                       }

                       /**
                        * @covers Procedure::search
                        * @todo   Implement testSearch().
                        */
                       public function testSearch() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @dataProvider dataProvider_ProcedureSearch
                        */
                       public function testGetList_ValidTerms_ReturnsValidResults($term, $data) {
                                              $results = Procedure::getList($term);
                                              $this->assertEquals($data, $results);
                       }

                       public function testGetList_InvalidTerm_ReturnsEmptyResults() {
                                              $results = Procedure::getList('Qux');
                                              $this->assertEquals(array(), $results);
                       }

					   public function testGetList_RestrictBooked()
					   {
						   $this->assertEquals(
							   array('Foobar Procedure'),
							   Procedure::getList('Proc', 'booked')
						   );
					   }

					   public function testGetList_RestrictUnbooked()
					   {
						   $this->assertEquals(
							   array('Test Procedure'),
							   Procedure::getList('Proc', 'unbooked')
						   );
					   }

                       /**
                        * @covers Procedure::getListBySubspecialty
                        * @todo   Implement testGetListBySubspecialty().
                        */
                       public function testGetListBySubspecialty() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

}
