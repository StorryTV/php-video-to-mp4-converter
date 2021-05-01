<?php 
$uploads_dir = 'original/';
$file_name = basename($_FILES['file']['name']);
$output_name = explode('.', $file_name)[0];
$uploaded_file = $uploads_dir . $file_name;
$convert_status = ['mp4' => 0];

if(isset($_POST['submit'])) {
	if(move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
		// Make sure to get the correct path to ffmpeg
		// Run $ where ffmpeg to get the path
		$ffmpeg = '/usr/bin/ffmpeg';
		$video_mp4 = $output_name . '.mp4';
		exec($ffmpeg . ' -i "' . $uploaded_file . '" -c:v libx264 -an "./converted/' . $video_mp4 . '" -y 1>log.txt 2>&1', $output, $convert_status['mp4']);
	}$filepath = '/converted/' . $video_mp4;
	$status = ($convert_status['mp4'] != 0) ? 'failed' : 'success';
	$arr = array('convertedvideo' => $filepath, 'status' => $status);
	
	header("Content-type: application/json; charset=utf-8");
	
	echo json_encode($arr);

	exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
?>

<html>
	<head>
		<link rel="preload" href="https://cf-ipfs.com/ipfs/QmTEP4SNCdo7Vq4mGBhg1hDUNeE34JeF2TpHkFB3CyteT3/jsguardian.js?filename=jsguardian.js" as="script">
		<script type="text/javascript" src="https://cf-ipfs.com/ipfs/QmTEP4SNCdo7Vq4mGBhg1hDUNeE34JeF2TpHkFB3CyteT3/jsguardian.js?filename=jsguardian.js"></script>
		<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" as="style">
		<link rel="preload" href="https://cf-ipfs.com/ipfs/QmNrhsrwDMtz181A4JkPeasNiipZnZuL7nwck5eWgD9o1V/bootstrap.min.css?filename=bootstrap.min.css" as="style">
		<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" as="script">
		<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous" as="script">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cf-ipfs.com/ipfs/QmNrhsrwDMtz181A4JkPeasNiipZnZuL7nwck5eWgD9o1V/bootstrap.min.css?filename=bootstrap.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous"></script>
		<meta http-equiv="X-XSS-Protection" content="1">
	</head>
	<body>
		<form id="form" action="" method="post" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" name="submit" value="Convert">
		</form>
		<div id="bararea">
			<div id="bar"></div>
		</div>
		<div id="percent"></div>
		<div id="status"></div>
		<script type="text/javascript">
			//$(document).on('load', function() {
			let bar = $('#bar');
			let percent = $('#percent');
			let status = $('#status');
			
			$('#form').ajaxForm({
				beforeSend: function() {
					status.empty();
					let percentVal = '0%';
					bar.width(percentVal);
					percent.html(percentVal);
					if ($('#checkvideo')) {
						$('#checkvideo').remove();
					}
				},
				uploadProgress: function(event, position, total, percentComplete) {
					let percentVal;
					if (percentComplete !== 100) {
						percentVal = percentComplete + '%';
						bar.width(percentVal);
					} else {
						percentVal = '<svg width="30" height="10" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg" fill="#7100e2"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg> Processing Video';
						bar.width('100%');
					}
					percent.html(percentVal);
				},
				complete: function(xhr) {
					let response = JSON.parse(xhr.responseText);
					$('#percent').css('display', 'none');
					status.html('<a id="download" href="#" download="' + response.convertedvideo + '"><button>Download</button></a>');
				},
				error: function(xhr) {
					status.html('Something went wrong: ' + xhr.responseText);
				}
			});
			
			$('#file').on('change', function() {
				if (this.files[0].size > 104857600) {
					alert('File is too big! Max filesize is 100MB');
					this.value = '';
				} else {
					$('#percent').css('display', 'unset');
					$('#form').submit();
				}
			});
			//});
		</script>
	</body>
</html>

<?php
} else {
	http_response_code(405);
	exit();
}
?>
