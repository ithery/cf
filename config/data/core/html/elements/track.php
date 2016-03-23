<?php
return array (
	"description"=>"Defines text tracks for media elements (<video> and <audio>)",
	"html5"=>true,
	"html5_support"=>true,
	"attr"=>array(
		"default"=>array(
			"html5"=>true,
			"html5_support"=>true,
			"description"=>"Specifies that the track is to be enabled if the user's preferences do not indicate that another track would be more appropriate"
		),
		"kind"=>array(
			"html5"=>true,
			"html5_support"=>true,
			"description"=>"Specifies the kind of text track"
		),
		"label"=>array(
			"html5"=>true,
			"html5_support"=>true,
			"description"=>"Specifies the title of the text track"
		),
		"src"=>array(
			"html5"=>true,
			"html5_support"=>true,
			"description"=>"Required. Specifies the URL of the track file"
		),
		"srclang"=>array(
			"html5"=>true,
			"html5_support"=>true,
			"description"=>"Specifies the language of the track text data (required if kind=\"subtitles\")"
			
		),
	),
);	
		