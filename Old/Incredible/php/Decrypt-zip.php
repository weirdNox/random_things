<?
require_once('constants.php');

function decryptSummary($InFileName,$OutFileName,$password){

	//check the file if exists

	if (file_exists($InFileName)){

 

		//get file content as string

		$InFile = file_get_contents($InFileName);

		$InFile = base64_decode($InFile);

			// get string length

			$StrLen = strlen($InFile);

 

			// get string char by char

			for ($i = 0; $i < $StrLen ; $i++){

				//current char

				$chr = substr($InFile,$i,1);

 

				//get password char by char

				$modulus = $i % strlen($password);

				$passwordchr = substr($password,$modulus, 1);

 

				//encryption algorithm

				$OutFile .= chr(ord($chr)-ord($passwordchr));

			}

 

		//write to a new file

		if($newfile = fopen($OutFileName, "c")){

			file_put_contents($OutFileName,$OutFile);

			fclose($newfile);

			return true;

		}else{

			return false;

		}

	}else{
		return false;

	}

}

function create_zip($files = array(),$destination = '',$overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if(file_exists($destination) && !$overwrite) { return false; }
    //vars
    $valid_files = array();
    //if files were passed in...
    if(is_array($files)) {
        //cycle through each file
        foreach($files as $file) {
            //make sure the file exists
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if(count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach($valid_files as $file) {
            $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        
        //close the zip -- done!
        $zip->close();
        
        //check to make sure the file exists
        return file_exists($destination);
    }
    else
    {
        return false;
    }
}

ini_set('max_execution_time', 1000000);
$summaries = array_diff(scandir(HOME.'summaries/'), array('..', '.'));
$number = 0;
$unencryptedFiles = array();
foreach($summaries as $summary)
{
    decryptSummary(HOME.'summaries/'.$summary,HOME.'downloads/'.$number.'.pdf',md5('iNoXRULESFUCKYEAH!sodhf9aeyfq30f'));
    $unencryptedFiles[] = HOME.'downloads/'.$number.'.pdf';
    $number++;
}

if(create_zip($unencryptedFiles, HOME.'downloads/summaries.zip',true))
{
    if ($fd = fopen (HOME.'downloads/summaries.zip', "r"))
    {
        $fsize = filesize(HOME.'downloads/summaries.zip');
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=\"Summaries.zip\"");
        header("Content-length: $fsize");
        header("Cache-control: private");

        while(!feof($fd)) {

            $buffer = fread($fd, 2048);

            echo $buffer;

        }
        fclose($fd);
    }
    else
    {
        echo 'ERROR';
    }
}
else
{
    echo 'Something happened...';
}

