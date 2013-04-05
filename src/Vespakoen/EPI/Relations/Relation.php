<?php namespace Vespakoen\EPI\Relations;

class Relation {

	protected function countParents()
	{
		$lookup = $this;

		$i = 0;
		while( ! is_null($lookup))
		{
			$i++;
			$lookup = $lookup->parent;
		}

		return $i;
	}

	protected function getAliased($table)
	{
		return 'alias_'.$table;
	}

}