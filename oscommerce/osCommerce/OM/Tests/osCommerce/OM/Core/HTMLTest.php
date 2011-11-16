<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Registry;

  class HTMLTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
    }

    protected function tearDown() {
    }

    public function testOutput() {
      $this->assertEquals('test&quot;string', HTML::output(' test"string '));
    }

    public function testOutputWithAmpersand() {
      $this->assertEquals('test&quot;string &amp;', HTML::output(' test"string & ', array('&' => '&amp;', '"' => '&quot;')));
    }

    public function testOutputProtected() {
      $this->assertEquals('&lt;a href=&quot;test&quot;&gt;test&amp;string&lt;/a&gt;', HTML::outputProtected(' <a href="test">test&string</a> '));
    }

    public function testSanitize() {
      $this->assertEquals('test _test_', HTML::sanitize(' test      <test> '));
    }

    public function testLink() {
      $this->assertEquals('<a href="http://www.oscommerce.com" target="_blank">osCommerce</a>', HTML::link('http://www.oscommerce.com', 'osCommerce', 'target="_blank"'));
    }

    public function testImageWithWidthAndHeight() {
      $this->assertEquals('<img src="http://www.oscommerce.com/images/oscommerce.gif" border="0" alt="osCommerce" title="osCommerce" width="211" height="60" id="logo" />', HTML::image('http://www.oscommerce.com/images/oscommerce.gif', 'osCommerce', 211, 60, 'id="logo"'));
    }

    public function testImageWithoutWidthAndHeight() {
      $this->assertEquals('<img src="http://www.oscommerce.com/images/oscommerce.gif" border="0" alt="osCommerce" title="osCommerce" id="logo" />', HTML::image('http://www.oscommerce.com/images/oscommerce.gif', 'osCommerce', '', '', 'id="logo"'));
    }

    public function testIcon() {
      $this->assertEquals('<img src="public/sites/Shop/templates/oscom/images/icons/16x16/info.png" border="0" alt="Info" title="Info" id="iconInfo" />', HTML::icon('info.png', 'Info', '16x16', 'id="iconInfo"'));
    }

    public function testIconRaw() {
      $this->assertEquals('public/sites/Shop/templates/oscom/images/icons/16x16/info.png', HTML::iconRaw('info.png', '16x16'));
    }

    public function testSubmitImage() {
      $this->assertEquals('<input type="image" src="public/sites/Shop/templates/oscom/images/icons/16x16/edit.png" title="Edit" id="editSubmitImage" />', HTML::submitImage(HTML::iconRaw('edit.png'), 'Edit', 'id="editSubmitImage"'));
    }

    public function testButtonSubmit() {
      $this->assertEquals('<button id="button1" type="submit">Submit</button><script type="text/javascript">$("#button1").button({icons:{primary:"ui-icon-tick"}});</script>', HTML::button(array('title' => 'Submit', 'icon' => 'tick')));
    }

    public function testButtonReset() {
      $this->assertEquals('<button id="button2" type="submit">Reset</button><script type="text/javascript">$("#button2").button();</script>', HTML::button(array('title' => 'Reset')));
    }

    public function testButtonButton() {
      $this->assertEquals('<button id="button3" type="button" onclick="window.open(\'http://www.oscommerce.com\');">osCommerce</button><script type="text/javascript">$("#button3").button({icons:{secondary:"ui-icon-tick"}}).addClass("ui-priority-secondary");</script>', HTML::button(array('href' => 'http://www.oscommerce.com', 'newwindow' => true, 'title' => 'osCommerce', 'icon' => 'tick', 'iconpos' => 'right', 'priority' => 'secondary')));
    }

    public function testInputField() {
      $this->assertEquals('<input type="text" name="site" value="osCommerce" id="ifName" />', HTML::inputField('site', 'osCommerce', 'id="ifName"'));
    }

    public function testPasswordField() {
      $this->assertEquals('<input type="password" name="password" id="pfPassword" />', HTML::passwordField('password', 'id="pfPassword"'));
    }

    public function testTextareaField() {
      $this->assertEquals('<textarea name="description" cols="6" rows="65" id="taDescription">Description</textarea>', HTML::textareaField('description', 'Description', 6, 65, 'id="taDescription"'));
    }

    public function testSelectMenu() {
      $list_array = array(array('id' => 'one', 'text' => 'First'),
                          array('id' => 'two', 'text' => 'Second'),
                          array('id' => 'three', 'text' => 'Third'));

      $this->assertEquals('<select name="list" id="sList"><option value="one">First</option><option value="two" selected="selected">Second</option><option value="three">Third</option></select>', HTML::selectMenu('list', $list_array, 'two', 'id="sList"'));
    }

    public function testCheckboxField() {
      $list_array = array(array('id' => 'one', 'text' => 'First'),
                          array('id' => 'two', 'text' => 'Second'),
                          array('id' => 'three', 'text' => 'Third'));

      $this->assertEquals('<input type="checkbox" name="selection" id="selection_1" value="one" /><label for="selection1" class="fieldLabel">&nbsp;First</label>&nbsp;&nbsp;<input type="checkbox" name="selection" id="selection_2" value="two" checked="checked" /><label for="selection2" class="fieldLabel">&nbsp;Second</label>&nbsp;&nbsp;<input type="checkbox" name="selection" id="selection_3" value="three" /><label for="selection3" class="fieldLabel">&nbsp;Third</label>', HTML::checkboxField('selection', $list_array, 'two'));
    }

    public function testRadioField() {
      $list_array = array(array('id' => 'one', 'text' => 'First'),
                          array('id' => 'two', 'text' => 'Second'),
                          array('id' => 'three', 'text' => 'Third'));

      $this->assertEquals('<input type="radio" name="selection" id="selection_1" value="one" checked="checked" /><label for="selection1" class="fieldLabel">&nbsp;First</label>&nbsp;&nbsp;<input type="radio" name="selection" id="selection_2" value="two" /><label for="selection2" class="fieldLabel">&nbsp;Second</label>&nbsp;&nbsp;<input type="radio" name="selection" id="selection_3" value="three" /><label for="selection3" class="fieldLabel">&nbsp;Third</label>', HTML::radioField('selection', $list_array, 'one'));
    }

    public function testHiddenField() {
      $this->assertEquals('<input type="hidden" name="action" value="confirm" id="hfAction" />', HTML::hiddenField('action', 'confirm', 'id="hfAction"'));
    }

    public function testHiddenSessionIDField() {
      $OSCOM_Session = Registry::get('Session');

      if ( $OSCOM_Session->hasStarted() && (strlen(SID) > 0) ) {
        $this->assertEquals('<input type="hidden" name="' . $OSCOM_Session->getName() . '" value="' . $OSCOM_Session->getID() . '" />', HTML::hiddenSessionIDField());
      } else {
        $this->assertEquals('', HTML::hiddenSessionIDField());
      }
    }

    public function testLabel() {
      $this->assertEquals('<label for="firstname">First Name</label>', HTML::label('First Name', 'firstname'));
    }

    public function testDateSelectMenu() {
      $this->assertEquals('<select name="date_days" id="date_days"><option value="1" selected="selected">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option></select><select name="date_months" id="date_months" onchange="updateDatePullDownMenu(this.form, \'date\');"><option value="1" selected="selected">January</option><option value="2">February</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">August</option><option value="9">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select><select name="date_years" id="date_years" onchange="updateDatePullDownMenu(this.form, \'date\');"><option value="2011" selected="selected">2011</option><option value="2012">2012</option></select>', HTML::dateSelectMenu('date', array('year' => '2011', 'month' => '1', 'date' => '1')));
    }

    public function testTimeZoneSelectMenu() {
      $result = array();

      foreach ( DateTime::getTimeZones() as $zone => $zones_array ) {
        foreach ( $zones_array as $key => $value ) {
          $result[] = array('id' => $key,
                        'text' => $value,
                        'group' => $zone);
        }
      }

      $this->assertEquals(HTML::selectMenu('timezone', $result, 'Europe/Berlin'), HTML::timeZoneSelectMenu('timezone', 'Europe/Berlin'));
    }
  }
?>
