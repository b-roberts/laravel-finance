@php
$datatable = [];
$datatable[]=array('Category','Value');
		foreach($this->categories as $category)
		{
			$datatable[] = array($category['name'],$category['value']);
		}
echo json_encode($datatable, JSON_NUMERIC_CHECK);
@endphp