<?php

// Make sure to get the correct path to ffmpeg
// Run "whereis ffmpeg" in your terminal to get the correct path (the first of the results is usually the correct one)
$ffmpeg = '/usr/bin/ffmpeg';
// Choose how many threads per ffmpeg conversion you want to use (leave it at: '1', for free cores and more parallel conversions).
// Set to: 'MAX', for max cpu usage and faster conversions, but no parallel conversions.
$ffmpeg_threads = '1';
//Default IPFS below can be changed to your own IPFS node
$ipfsapi = array(
	'protocol' => 'https',
	'host' => 'ipfs.infura.io',
	'port' => '5001',
);

if(isset($_POST['upload_form'])) {
	if (!isset($_FILES['file'])) {
		$arr = array(
			'convertedvideo' => 'There is no file',
			'convertingstatus' => 'failed',
		);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($arr);
		exit();
	}
	$_filepath = $_FILES['file']['tmp_name'];
	$_fileSize = filesize($_filepath);
	$_fileinfo = finfo_open(FILEINFO_MIME_TYPE);
	$_filetype = finfo_file($_fileinfo, $_filepath);
	if ($_fileSize === 0) { // Check if file is empty
		$arr = array(
			'convertedvideo' => 'The file is empty',
			'convertingstatus' => 'failed',
		);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($arr);
		exit();
	}
	if ($_fileSize > 104857600) { // Check if file is bigger than 100MB
		$arr = array(
			'convertedvideo' => 'The file is too large',
			'convertingstatus' => 'failed',
		);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($arr);
		exit();
	}
	if (substr($_filetype, 0, 5 ) !== 'video') { // Check if it is really a video
		$arr = array(
			'convertedvideo' => 'File not allowed.',
			'convertingstatus' => 'failed',
		);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($arr);
		exit();
	}
	$uploads_dir = 'original/';
	$file_name = basename($_FILES['file']['name']);
	$output_name = substr($file_name, 0 , (strrpos($file_name, '.')));
	$uploaded_file = $uploads_dir . $file_name;
	$convert_status = ['mp4' => 0];
	if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
		$video_mp4 = $output_name . '.mp4';
		$filepath = '/converted/' . $video_mp4;
		$arr = array(
			'convertingstatus' => 'converting',
		);
		$status_arr = json_encode($arr);
		file_put_contents('./converted/' . $video_mp4 . '.json', $status_arr);
		if ($ffmpeg_threads === 'MAX') {
			$ffmpeg_threads = '';
		} else {
			$ffmpeg_threads = '1';
		}
		if (strpos($threads, '.') !== false) {
			$ffmpeg_threads = explode('.', $ffmpeg_threads)[0];
		}
		$ffmpeg_threads = ' -threads ' . $ffmpeg_threads;
		exec($ffmpeg . ' -i "' . $uploaded_file . '" -preset slow -c:v libx264 -c:a copy' . $ffmpeg_threads . ' "./converted/' . $video_mp4 . '" -y 1>log.txt 2>&1', $output, $convert_status['mp4']);
		$arr1 = array(
			'convertedvideo' => $filepath,
			'convertingstatus' => 'uploading_to_ipfs',
		);
		$status_arr1 = json_encode($arr1);
		file_put_contents('./converted/' . $video_mp4 . '.json', $status_arr1);
		exec('curl "' . $ipfsapi['protocol'] . '://' . $ipfsapi['host'] . ':' . $ipfsapi['port'] . '/api/v0/add?stream-channels=true&recursive=false&pin=true&wrap-with-directory=false&progress=false" \
			-X POST \
			-H "Content-Type: multipart/form-data" \
			-F file=@"' . realpath(getenv('DOCUMENT_ROOT')) . '/'. $uploaded_file. '"', $output, $ipfs_upload);
		$ipfs = json_decode($output['0'], true);
		$filepath = '/converted/' . $video_mp4;
		$status = ($convert_status['mp4'] === 0) ? 'done' : 'failed';
		$arr2 = array(
			'convertedvideo' => $filepath,
			'convertingstatus' => $status,
			'ipfshash' => $ipfs['Hash'],
			'ipfsname' => $ipfs['Name'],
			'ipfssize' => $ipfs['Size'],
			'ipfs' => $output,
		);
		$status_arr2 = json_encode($arr2);
		file_put_contents('./converted/' . $video_mp4 . '.json', $status_arr2);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($arr2);
	}
	exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	include(realpath(getenv('DOCUMENT_ROOT')) . '/cleanup.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-XSS-Protection" content="1; mode=block">
		<meta charset="utf-8">
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
		<style type="text/css">
			input[type="file"] {width:100%;height:200px;padding:80px 10vw;font-size:28px;background-color:rgba(0,0,0,0.1);border:2px dashed rgba(0,0,0,0.2);border-radius:10px;cursor:pointer;}
			input[type="file"]:hover {background-color:rgba(0,0,0,0.15);}
			input[type="submit"], a.download>button {font-size:28px;border:none;border-radius:5px;background-image:linear-gradient(#7100e2,#58427b,#271212);color:#fff;box-shadow:2px 2px 5px #777;}
			input[type="submit"]:hover {background-image:linear-gradient(#6f00de,#5c4582,#231010);box-shadow:2px 2px 8px #777;}
			#percent {font-size:22px}
			#bararea {width:calc(100% - 40px);height:10px;border:1px solid #7100e2;border-radius:3px;margin-top:20px;background-color:#fff;}
			#bar {width:1%;margin:0px 0;height:8px;background-color:#03d000;transition-duration:0.5s;}
			input[type="submit"], #status, #percent, #percent>svg {width:100%;text-align:center;margin-top:20px;}
			#form, #bararea {margin:20px;}
		</style>
	</head>
	<body>
		<form id="form" action="" method="post" enctype="multipart/form-data">
			<input type="file" name="file" accept="video/*" required>
			<input type="hidden" name="upload_form" value="true" required>
			<input type="submit" name="submit" value="Convert to MP4">
		</form>
		<div id="bararea">
			<div id="bar"></div>
		</div>
		<div id="percent"></div>
		<div id="status"></div>
		<script type="text/javascript">
			let bar = $('#bar');
			let percent = $('#percent');
			let status = $('#status');
			let timeout_;
			let interval;
			
			$('#form').ajaxForm({
				dataType: "json",
				beforeSend: () => {
					status.empty();
					let percentVal = '0%';
					bar.width(percentVal);
					percent.html('<p>' + percentVal + '</p>');
					if ($('#checkvideo')) {
						$('#checkvideo').remove();
					}
					$('input[type=submit]').css('display', 'none');
				},
				uploadProgress: (event, position, total, percentComplete) => {
					let percentVal;
					if (percentComplete !== 100) {
						percentVal = percentComplete + '%';
						bar.width(percentVal);
					} else {
						percentVal = '<svg width="30" height="10" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg" fill="#7100e2"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg><p>Processing Video</p>';
						bar.width('100%');
					}
					percent.html('<p>' + percentVal + '</p>');
				},
				success: (data) => {
					try {
						let response = data;
						$('#percent').css('display', 'none');
						let fileInput = document.querySelector('input[type=file]');
						let path = fileInput.value;
						let fileName = path.split(/(\\|\/)/g).pop();
						if (response.convertingstatus == 'failed') {
							return status.html('<p style="text-align:center;width:100%;font-size:21px;font-weight:600px;">Failed: ' + fileName + ' has failed the conversion :(</p>');
						} else {
							return status.html('<a class="download" href="' + response.convertedvideo + '" download="' + response.ipfsname + '"><button>Download Video</button></a><br/><br/><a class="download" href="https://ipfs.infura.io/ipfs/' + response.ipfshash + '?filename=' + response.ipfsname + '" target="_blank"><button>Open video in a new tab</button></a>');
						}
					} catch(e) {
						interval = setInterval(getConvertingStatus, 5000);
					}
				},
				error: (xhr) => {
					
				},
				statusCode: {
					504: () => {
						interval = setInterval(getConvertingStatus, 5000);
					},
					522: () => {
						interval = setInterval(getConvertingStatus, 5000);
					},
					524: () => {
						interval = setInterval(getConvertingStatus, 5000);
					}
				}
			});
			
			const getConvertingStatus = () => {
				let fileInput = document.querySelector('input[type=file]');
				let path = fileInput.value;
				let fileName = path.split(/(\\|\/)/g).pop();
				let percentVal;
				let downloadurl = document.querySelector('input[type=file]').value.split(/(\\|\/)/g).pop().replace(document.querySelector('input[type=file]').value.split(/(\\|\/)/g).pop().split('.').pop(), 'mp4');
				$.ajax({
					type: 'GET',
					url: '/converted/' + downloadurl + '.json',
					cache: false,
					dataType: 'json',
					complete: (data) => {
						let response = data.responseJSON;
						console.log(response);
						if (response.convertingstatus == 'done') {
							$('#percent').css('display', 'none');
							clearInterval(interval);
							return status.html('<a class="download" href="' + response.convertedvideo + '" download="' + response.ipfsname + '"><button>Download Video</button></a><br/><br/><a class="download" href="https://ipfs.infura.io/ipfs/' + response.ipfshash + '?filename=' + response.ipfsname + '" target="_blank"><button>Open video in a new tab</button></a>');
						} else if (response.convertingstatus == 'converting') {
							console.log('Still converting...');
						} else if (response.convertingstatus == 'uploading_to_ipfs') {
							console.log('Uploading to IPFS...');
							percentVal = '<svg width="30" height="10" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg" fill="#7100e2"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg><p>Processing Video</p>';
							percent.html('<p>' + percentVal + '</p>');
						} else if (response.convertingstatus == 'failed') {
							$('#percent').css('display', 'none');
							clearInterval(interval);
							return status.html('<p style="text-align:center;width:100%;font-size:21px;font-weight:600px;">Failed: ' + fileName + ' has failed the conversion :(</p>');
						} else {
							console.log('no');
							status.html('<p>Something went wrong: UNKOWN ERROR</p>');
						}
					}
				});
			}
			
			$('input[name=file]').on('change', function() {
				clearInterval(interval);
				if (this.files[0].size > 104857600) {
					alert('File is too big! Max filesize is 100MB');
					this.value = '';
				} else {
					$('#percent').css('display', 'unset');
					$('#form').submit();
				}
			});
		</script>
	</body>
</html>

<?php
	exit();
} else {
	http_response_code(405);
	exit();
}
?>
