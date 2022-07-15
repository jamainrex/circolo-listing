<label>Country: </label>
<?php
	$drop_down = '<select class="country-filter" id="country-filter">';
	$drop_down .= '<option value="all">All</option>'; 
		  foreach ( $countries as $key => $country ) {
			  $drop_down .= '<option value="' . $key . '"';
			  if ( $key === $selectedCountry ) { //$key === $selected
				  $drop_down .= ' selected="selected"';
			  }
			  $drop_down .= '>' . $country . '</option>';
		  }
		  $drop_down .= '</select>';

    echo $drop_down;
    