<?PHP 

session_start();
    
if(!$_POST["filepath"] || $_POST["submit"] != "submit")
{
	$_SESSION["ERROR"] = "File rquired to run program";
	header("Location: index.php");
    exit;
}
$input = file_get_contents($_POST["filepath"]);
if (!eregi("(.txt)$",$_POST["filepath"]))
{
    $_SESSION["ERROR"] = "Only text files (\".txt\" file extension) allowed";
    header("Location: index.php");
    exit;
}
else if (!$input)
{
    $_SESSION["ERROR"] = "Empty File or file may not exist...<br/>ensure that you enetered a valid location and double check your spelling!";
    header("Location: index.php");
    exit;
}
$_SESSION["altFacts"] = $_POST["facts"];
$_SESSION["file"] = $_POST["filepath"];
$_SESSION["toValidate"] = "File succesfully loaded.";
header("Location: index.php");
?>
