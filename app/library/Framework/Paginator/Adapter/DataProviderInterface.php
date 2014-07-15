<?php

namespace Framework\Paginator\Adapter;

interface DataProviderInterface {

	public function getCount();

	public function getItemsWithOffsetAndLimit($offset = 0, $limit = 0);
}
