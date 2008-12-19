<?php
require_once dirname(__FILE__).'/../test_helper.php';

class RuleIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
		// you'll need to add your connection info, plus a publisher/filter/rule combo that you own
        $this->gnip = new Services_Gnip("", "");
		$this->publisher = "";
		$this->filter = "";
		$this->rule = new Services_Gnip_Rule("", "");
		// end editable section
		
		// you do not need to edit these below
		$this->newSingleRule = new Services_Gnip_Rule("actor", uniqid('actor'));
		$this->batchRule1 = new Services_Gnip_Rule("actor", uniqid('actorb'));
		$this->batchRule2 = new Services_Gnip_Rule("actor", uniqid('actorb'));
		$this->batchRule3 = new Services_Gnip_Rule("actor", uniqid('actorb'));
		$this->expected_status = "<result>Success</result>";
    }
    
    public function tearDown()
    {
        $this->gnip->deleteRule($this->publisher, $this->filter, $this->newSingleRule);
		$this->gnip->deleteRule($this->publisher, $this->filter, $this->batchRule1);
		$this->gnip->deleteRule($this->publisher, $this->filter, $this->batchRule2);
		$this->gnip->deleteRule($this->publisher, $this->filter, $this->batchRule3);
    }
    
    function testRuleExists(){
		$this->assertEquals($this->gnip->ruleExists($this->publisher, $this->filter, $this->rule), 1);
		$this->assertEquals($this->gnip->ruleExists($this->publisher, $this->filter, $this->newSingleRule), 0);
	}
	
	function testAddSingleRule(){
		$status = $this->gnip->addBatchRules($this->publisher, $this->filter, $this->newSingleRule);
		$this->assertEquals($status, $this->expected_status);
		
		$rool = $this->gnip->getRule($this->publisher, $this->filter, $this->newSingleRule);
		$this->assertEquals($rool, $this->newSingleRule);
	}
	
	function testDeleteRule(){
		$status = $this->gnip->deleteRule($this->publisher, $this->filter, $this->newSingleRule);
		$this->assertEquals($status, $this->expected_status);
	}

	function testAddBatchRule(){
		$rulesArray = array($this->batchRule1, $this->batchRule2, $this->batchRule3);
		$status = $this->gnip->addBatchRules($this->publisher, $this->filter, $rulesArray);
		$this->assertEquals($status, $this->expected_status);
		
		$rool = $this->gnip->getRule($this->publisher, $this->filter, $this->batchRule1);
		$this->assertEquals($rool, $this->batchRule1);
		
		$rool = $this->gnip->getRule($this->publisher, $this->filter, $this->batchRule2);
		$this->assertEquals($rool, $this->batchRule2);
		
		$rool = $this->gnip->getRule($this->publisher, $this->filter, $this->batchRule3);
		$this->assertEquals($rool, $this->batchRule3);
	}
}
?>