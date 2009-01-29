<?php
require_once dirname(__FILE__).'/../test_helper.php';

class RuleIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        // you'll need to add your connection info, plus a publisher/filter/rule combo that you own
        // make sure that if the filter is created on a publisher you own, the namespace is "my"
        // if the publisher is public, make sure the namespace is "gnip"
        $this->gnip = new Services_Gnip("", "");
        $this->scope = "gnip";
        $this->publisher = ""; // string publisher name
        $this->filter = ""; //string filter name you have on above publisher
        $this->rule = new Services_Gnip_Rule("to", ""); //add rule for to
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
        $this->gnip->deleteRule($this->publisher, $this->filter, $this->newSingleRule, $this->scope);
        $this->gnip->deleteRule($this->publisher, $this->filter, $this->batchRule1, $this->scope);
        $this->gnip->deleteRule($this->publisher, $this->filter, $this->batchRule2, $this->scope);
        $this->gnip->deleteRule($this->publisher, $this->filter, $this->batchRule3, $this->scope);
    }
    
    function testRuleExists(){
        $this->assertEquals($this->gnip->ruleExists($this->publisher, $this->filter, $this->rule, $this->scope), 1);
        $this->assertEquals($this->gnip->ruleExists($this->publisher, $this->filter, $this->newSingleRule, $this->scope), 0);
    }

    function testAddSingleRule(){
        $status = $this->gnip->addBatchRules($this->publisher, $this->filter, $this->newSingleRule, $this->scope);
        $this->assertEquals($status, $this->expected_status);

        $rool = $this->gnip->getRule($this->publisher, $this->filter, $this->newSingleRule, $this->scope);
        $this->assertEquals($rool, $this->newSingleRule);
    }

    function testDeleteRule(){
        $status = $this->gnip->deleteRule($this->publisher, $this->filter, $this->newSingleRule, $this->scope);
        $this->assertEquals($status, $this->expected_status);
    }

    function testAddBatchRule(){
        $rulesArray = array($this->batchRule1, $this->batchRule2, $this->batchRule3);
        $status = $this->gnip->addBatchRules($this->publisher, $this->filter, $rulesArray, $this->scope);
        $this->assertEquals($status, $this->expected_status);

        $rool = $this->gnip->getRule($this->publisher, $this->filter, $this->batchRule1, $this->scope);
        $this->assertEquals($rool, $this->batchRule1);

        $rool = $this->gnip->getRule($this->publisher, $this->filter, $this->batchRule2, $this->scope);
        $this->assertEquals($rool, $this->batchRule2);

        $rool = $this->gnip->getRule($this->publisher, $this->filter, $this->batchRule3, $this->scope);
        $this->assertEquals($rool, $this->batchRule3);
    }
}
?>