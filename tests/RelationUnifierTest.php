<?php

use Vespakoen\Epi\Helpers\RelationUnifier;

class RelationUnifierTest extends EpiTests {

	public function setup()
	{
		parent::setup();

		$app = $this->app;
		$this->relationUnifier = new RelationUnifier($app);
	}

	public function testCanUnifyRelation()
	{
	}

}
