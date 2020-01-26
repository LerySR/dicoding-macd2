<?php
require_once 'vendor/autoload.php';
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=dicodingmacd;AccountKey=BNXQVFP8pKcmQRV6HIFF81HY0oP/9VMSgdtpXU0MV/PeeFh1mhvQTeKUM1Pk8XycDnP7KIgEzT600sIKPcUlnA==;EndpointSuffix=core.windows.net";
$containerName = "blobstorage-container";

//create blob client
$blobClient = BlobRestProxy::createBlobService($connectionString);
  
if (isset($_POST['submit'])) {
  $fileToUpload = $_FILES["fileToUpload"]["name"];
  $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
  echo fread($content, filesize($fileToUpload));
    
  $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
  header("Location: index.php");
} 
  
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<title>Analisa Gambar</title>
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans&display=swap" rel="stylesheet">
    <style type="text/css">
        .container{
            width: 1200px;
            margin:0 auto;
        }
        .header
        {
            font-family: 'Fira Sans', sans-serif;
            background  : white;
            height      : 10%;
            font-size   : 1.5em;
            text-align  : left;
			color       : white;
            padding-top : 0px;
        }
        .content
        {
            font-family: 'Fira Sans', sans-serif;
            background  : #d9d9e4;  
            float       : left;
            height      : 500px;
            width       : 100%;
            font-size   : 1em;
            text-align  : left;
            color       : black;
            padding-left: 0px;
            padding-right: 0px;
         
        }
		.footer
        {
            font-family: 'Fira Sans', sans-serif;
            background  : #3399ff;
            clear       : both;
            font-size   : 1em;
            text-align  :center;
        }
	</style>

</head>
<body>
    <div class="container">
		<div class="header">
        	<h3 class="text-centre">Analisa Gambar</h3>
		</div>
	<div class="content">
	<h3 class="text-center">Analisa Gambar</h3>
        <div class="form-group mt-3">
            <label for="exampleFormControlFile1">Pilih dan Upload gambar yang hendak dianalisa:</label>
            <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="file" class="form-control-file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
            <button type="submit" class="btn btn-primary mt-2" name="submit"><i class="fas fa-cloud-upload-alt"></i> Upload</button>
            </form>
        </div>

        <table class="table table-hover text-left">
			<thead>
				<tr>
					<th class="text-left">Nama Gambar</th>
					<th class="text-left">URL Gambar</th>
					<th class="text-left">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="image-analyzer.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Start!" class="btn btn-success">
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>
	</div>
	<div class="footer">
        Copyright 2020 Image Analyzer
    </div>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
</body>
</html>