<!doctype html>
<?php
$lang=(isset($_GET["lang"])?$_GET["lang"]:$_SERVER['HTTP_ACCEPT_LANGUAGE']);
$lang=(preg_match("/^(en|fr)($|([^a-z].*$))/",$lang)?substr($lang,0,2):"en");
$i18n=array();
switch($lang)
{
	case "fr":
		$title="Administration des images de puzzle";
		$description="Administration des images de puzzle";
		$i18n["rules"]="Règles";
		$i18n["add"]="Ajouter";
		$i18n["modify"]="Modifier";
		$i18n["remove"]="Supprimer";
		$i18n["image_title"]="Titre";
		$i18n["always"]="Toujours";
		$i18n["visible"]="Visible";
		$i18n["image_title_en"]="Titre en anglais";
		$i18n["image_title_fr"]="Titre en français";
		$i18n["file_name"]="Nom de fichier";
		$i18n["source"]="Source";
		$i18n["thumbnail"]="Vignette";
		$i18n["owner"]="Propriétaire";
		$i18n["license"]="Licence";
		$i18n["category"]="Catégorie";
		$i18n["ratio"]="Ratio";
		$i18n["notes"]="Notes";
		$i18n["animal"]="Animaux";
		$i18n["art"]="Art";
		$i18n["building"]="Constructions";
		$i18n["clothing"]="Vêtements";
		$i18n["flag"]="Drapeaux";
		$i18n["landscape"]="Paysages";
		$i18n["misc"]="Divers";
		$i18n["object"]="Objets";
		$i18n["plant"]="Plantes";
		$i18n["transport"]="Transport";
		$i18n["OK"]="OK";
		$i18n["reset"]="Réinitialiser";
		$i18n["cancel"]="Annuler";
		// msg
		$i18n["can_run_only_on_localhost"]="Ce script ne peut fonctionner que sur localhost";
		$i18n["check_duplicate"]="Vérifiez les doublons";
		$i18n["data_added_successfully"]="Les données ont été ajoutées avec succès";
		$i18n["data_modified_successfully"]="Les données ont été modifiées avec succès";
		$i18n["data_removed_successfully"]="Les données ont été supprimées avec succès";
		$i18n["data_already_exist"]="Les données existent déjà";
		$i18n["unknown_error"]="Erreur inconnue";
		break;
	default:
		$title="Puzzle image administration";
		$description="Puzzle image administration";
		$i18n["rules"]="Rules";
		$i18n["add"]="Add";
		$i18n["modify"]="Modify";
		$i18n["remove"]="Remove";
		$i18n["image_title"]="Title";
		$i18n["always"]="Always";
		$i18n["visible"]="Visible";
		$i18n["image_title_en"]="Title in English";
		$i18n["image_title_fr"]="Title in French";
		$i18n["file_name"]="File name";
		$i18n["source"]="Source";
		$i18n["thumbnail"]="Thumbnail";
		$i18n["owner"]="Owner";
		$i18n["license"]="License";
		$i18n["category"]="Category";
		$i18n["ratio"]="Ratio";
		$i18n["notes"]="Notes";
		$i18n["animal"]="Animals";
		$i18n["art"]="Art";
		$i18n["building"]="Building";
		$i18n["clothing"]="Clothing";
		$i18n["flag"]="Flags";
		$i18n["landscape"]="Landscapes";
		$i18n["misc"]="Miscellaneous";
		$i18n["object"]="Objects";
		$i18n["plant"]="Plants";
		$i18n["transport"]="Transport";
		$i18n["OK"]="OK";
		$i18n["reset"]="Reset";
		$i18n["cancel"]="Cancel";
		// msg
		$i18n["can_run_only_on_localhost"]="This script can run only on localhost";
		$i18n["check_duplicate"]="Check duplicate";
		$i18n["data_added_successfully"]="Data added successfully";
		$i18n["data_modified_successfully"]="Data modified successfully";
		$i18n["data_removed_successfully"]="Data removed successfully";
		$i18n["data_already_exist"]="Data already exist";
		$i18n["unknown_error"]="Unkown error";
}
$origin=json_decode(file_get_contents("images.json"),true);
function isValidData($t,$s)
{
	return true;
}
$msg="";
$found=null;
$new_data=null;
if(isset($_POST["file_name"])&&$_POST["file_name"]&&isValidData("file_name",$_POST["file_name"]))
{
	$image_title=$_POST["image_title"];
	$always=(isset($_POST["always"])?"1":"0");
	$visible=(isset($_POST["visible"])?"1":"0");
	$image_title_en=$_POST["image_title_en"];
	$image_title_fr=$_POST["image_title_fr"];
	$file_name=$_POST["file_name"];
	$source=$_POST["source"];
	$thumbnail=$_POST["thumbnail"];
	$owner=(isset($_POST["owner"])?$_POST["owner"]:"");
	$license=(isset($_POST["license"])?$_POST["license"]:"");
	$category=$_POST["category"];
	$ratio=$_POST["ratio"];
	$notes=$_POST["notes"];
	$k=0;
	foreach($origin as $a)
	{
		if($a["file_name"]==$file_name)
		{
			$found=$a;
			break;
		}
		else $k++;
	}
	if($found)
	{
		if(isset($_POST["OK_add"])) $msg=$i18n["data_already_exist"];
		else $new_data=$found;
	}
	else
	{
		if(isset($_POST["OK_modify"])) $msg=$i18n["check_duplicate"];
		$new_data=array();
	}
	if($new_data!==null)
	{
		$ok=1;
		$new_data["image_title"]=$image_title;
		$new_data["always"]=$always;
		$new_data["visible"]=$visible;
		$new_data["image_title_en"]=$image_title_en;
		$new_data["image_title_fr"]=$image_title_fr;
		$new_data["file_name"]=$file_name;
		$new_data["source"]=$source;
		$new_data["thumbnail"]=$thumbnail;
		$new_data["owner"]=$owner;
		$new_data["license"]=$license;
		$new_data["category"]=$category;
		$new_data["ratio"]=$ratio;
		$new_data["notes"]=$notes;
		if(isset($_POST["OK_add"])) $origin[]=$new_data;
		else if(isset($_POST["OK_modify"]))
		{
			if($found) $origin[$k]=$new_data;
			else $origin[]=$new_data;
		}
		else if(isset($_POST["OK_remove"])) array_splice($origin, $k, 1);
		else $ok=0;
		if($ok) file_put_contents("images.json",json_encode($origin));
		if(isset($_POST["OK_add"])) $msg=($msg?$msg."<br>":"").$i18n["data_added_successfully"];
		else if(isset($_POST["OK_modify"])) $msg=($msg?$msg."<br>":"").$i18n["data_modified_successfully"];
		else if(isset($_POST["OK_remove"])) $msg=($msg?$msg."<br>":"").$i18n["data_removed_successfully"];
		else $msg=($msg?$msg."<br>":"").$i18n["unknown_error"];
	}
}
if(!(isset($_POST["OK_add"])&&($new_data===null)))
{
	$image_title="";
	$always="";
	$visible="";
	$image_title_en="";
	$image_title_fr="";
	$file_name="";
	$source="";
	$thumbnail="";
	$owner="";
	$license="";
	$category="";
	$ratio="";
	$notes="";
}
$licenses_json=json_decode(file_get_contents("licenses.json"),true);
function echo_license_select($selected_license)
{
	global $licenses_json,$i18n;
	echo "<label>".$i18n["license"];
	echo "<select name=\"license\">";
	foreach($licenses_json as $license)
	{
		echo "<option value=\"".$license["license_code"]."\"";
		echo (($selected_license==$license["license_code"])?" selected":"").">";
		echo $license["license_label"]."</option>";
	}
	echo "</select>";
	echo "</label>";
}
?>
<html lang="<?=$lang?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="description" content="<?=$description?>">
<title><?=$title?></title>
<style>
form
{
	background:#eee;
	margin:0.5rem;
	padding:0.5rem;
}
fieldset
{
	margin:0;
	padding:0;
	border:0;
}
label
{
	display:block;
	margin:0.5rem 0;
}
summary
{
	margin:0 0 0.5rem 0;
}
fieldset.content summary label
{
	display:inline-flex;
	margin:0;
	width:calc(100% - 1rem + 2px);
	gap:1rem;
	align-items:center;
}
label input, label select, label textarea
{
	display:block;
	box-sizing:border-box;
	width:100%;
	font-size:1rem;
}
label input[type="checkbox"]
{
	display:inline-block;
	width:auto;
}
fieldset.content summary label span
{
	flex:0;
	padding-left:0.5rem;
}
fieldset.content summary label input
{
	flex:1;
}
select
{
	max-width:max-content;
}
button
{
	font-size:1rem;
}
.msg
{
	color:#f00;
}
</style>
</head>
<body>
<h1><?=$title?></h1>
<?php if(preg_match("/^localhost$/",$_SERVER['SERVER_NAME'])) {?>
<?php if($msg) echo "<p class=\"msg\">".$msg."</p>";?>
<p><a href="#bottom">Bas de page</a></p>
<section>
<h2><?=$i18n["rules"]?></h2>
<p>Title in original language if possible</p>
<p>If artefact, add artist in the title</p>
<p>For animal and plant, title in latin if possible</p>
<p>Largest size (width or height): 1280px</p>
<p>If strange ratio and portrait, prefer 800px or 960px as width and keep height smaller than 1280px</p>
<p>Thumbnail width: 320px</p>
<p>If on wikimedia commons, direct link, else local link</p>
</section>
<section>
<h2><?=$i18n["add"]?></h2>
<form method="post">
<fieldset class="content">
<label><?=$i18n["image_title"]?>
<input name="image_title" value="<?=$image_title?>">
</label>
<label><input name="always" type="checkbox"<?=($always=="1"?" checked":"")?>>
<?=$i18n["always"]?>
</label>
<label><input name="visible" type="checkbox"<?=($visible=="1"?" checked":"")?>>
<?=$i18n["visible"]?>
</label>
<label><?=$i18n["image_title_en"]?>
<input name="image_title_en" value="<?=$image_title_en?>">
</label>
<label><?=$i18n["image_title_fr"]?>
<input name="image_title_fr" value="<?=$image_title_fr?>">
</label>
<label><?=$i18n["file_name"]?>
<input name="file_name" value="<?=$file_name?>">
</label>
<label><?=$i18n["source"]?>
<input name="source" value="<?=$source?>">
</label>
<label><?=$i18n["thumbnail"]?>
<input name="thumbnail" value="<?=$thumbnail?>">
</label>
<label><?=$i18n["owner"]?>
<input name="owner" value="<?=$owner?>">
</label>
<?php echo_license_select($license);?>
<label><?=$i18n["category"]?>
<select name="category">
<option value="archives"<?=($category==""?" selected":"")?>></option>
<option value="animal"<?=($category=="animal"?" selected":"")?>><?=$i18n["animal"]?></option>
<option value="art"<?=($category=="art"?" selected":"")?>><?=$i18n["art"]?></option>
<option value="building"<?=($category=="building"?" selected":"")?>><?=$i18n["building"]?></option>
<option value="clothing"<?=($category=="clothing"?" selected":"")?>><?=$i18n["clothing"]?></option>
<option value="flag"<?=($category=="flag"?" selected":"")?>><?=$i18n["flag"]?></option>
<option value="landscape"<?=($category=="landscape"?" selected":"")?>><?=$i18n["landscape"]?></option>
<option value="misc"<?=($category=="misc"?" selected":"")?>><?=$i18n["misc"]?></option>
<option value="object"<?=($category=="object"?" selected":"")?>><?=$i18n["object"]?></option>
<option value="plant"<?=($category=="plant"?" selected":"")?>><?=$i18n["plant"]?></option>
<option value="transport"<?=($category=="transport"?" selected":"")?>><?=$i18n["transport"]?></option>
</select>
</label>
<label><?=$i18n["ratio"]?>
<input name="ratio" value="<?=$ratio?>">
</label>
<label><?=$i18n["notes"]?>
<textarea name="notes"><?=$notes?></textarea>
</label>
</fieldset>
<fieldset class="content">
<button type="submit" name="OK_add" value="0"><?=$i18n["add"]?></button>
<button type="reset"><?=$i18n["reset"]?></button>
</fieldset>
</form>
</section>
<section>
<h2><?=$i18n["modify"]?></h2>
<?php
function buildOneDataDetails($one,$k)
{
	global $i18n;
	echo '<form method="post">'."\n";
	echo '<fieldset class="content">'."\n";
	echo '<details>'."\n";
	echo '<summary>'."\n";
	echo '<label><span>'.$i18n["image_title"]."</span>\n";
	echo '<input name="image_title" value="'.(array_key_exists("image_title",$one)?$one["image_title"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '</summary>'."\n";
	echo '<label><input name="always" type="checkbox"'.((array_key_exists("always",$one)&&($one["always"]=="1"))?" checked":"").'>'."\n";
	echo '<span>'.$i18n["always"]."</span>\n";
	echo '</label>'."\n";
	echo '<label><input name="visible" type="checkbox"'.((array_key_exists("visible",$one)&&($one["visible"]=="1"))?" checked":"").'>'."\n";
	echo '<span>'.$i18n["visible"]."</span>\n";
	echo '</label>'."\n";
	echo '<label><span>'.$i18n["image_title_en"]."</span>\n";
	echo '<input name="image_title_en" value="'.(array_key_exists("image_title_en",$one)?$one["image_title_en"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '<label><span>'.$i18n["image_title_fr"]."</span>\n";
	echo '<input name="image_title_fr" value="'.(array_key_exists("image_title_fr",$one)?$one["image_title_fr"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '<label>'.$i18n["file_name"]."\n";
	echo '<input name="file_name" value="'.(array_key_exists("file_name",$one)?$one["file_name"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '<label>'.$i18n["source"]."\n";
	echo '<input name="source" value="'.(array_key_exists("source",$one)?$one["source"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '<label>'.$i18n["thumbnail"]."\n";
	echo '<input name="thumbnail" value="'.(array_key_exists("thumbnail",$one)?$one["thumbnail"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '<label>'.$i18n["owner"]."\n";
	echo '<input name="owner" value="'.(array_key_exists("owner",$one)?$one["owner"]:"").'">'."\n";
	echo '</label>'."\n";
	echo_license_select(array_key_exists("license",$one)?$one["license"]:"");
	echo '<label>'.$i18n["category"]."\n";
	echo '<select name="category">'."\n";
	echo '<option value="animal"'.($one["category"]=="animal"?' selected':'').'>'.$i18n["animal"].'</option>'."\n";
	echo '<option value="art"'.($one["category"]=="art"?' selected':'').'>'.$i18n["art"].'</option>'."\n";
	echo '<option value="building"'.($one["category"]=="building"?' selected':'').'>'.$i18n["building"].'</option>'."\n";
	echo '<option value="clothing"'.($one["category"]=="clothing"?' selected':'').'>'.$i18n["clothing"].'</option>'."\n";
	echo '<option value="flag"'.($one["category"]=="flag"?' selected':'').'>'.$i18n["flag"].'</option>'."\n";
	echo '<option value="landscape"'.($one["category"]=="landscape"?' selected':'').'>'.$i18n["landscape"].'</option>'."\n";
	echo '<option value="misc"'.($one["category"]=="misc"?' selected':'').'>'.$i18n["misc"].'</option>'."\n";
	echo '<option value="object"'.($one["category"]=="object"?' selected':'').'>'.$i18n["object"].'</option>'."\n";
	echo '<option value="plant"'.($one["category"]=="plant"?' selected':'').'>'.$i18n["plant"].'</option>'."\n";
	echo '<option value="transport"'.($one["category"]=="transport"?' selected':'').'>'.$i18n["transport"].'</option>'."\n";
	echo '</select>'."\n";
	echo '</label>'."\n";
	echo '<label>'.$i18n["ratio"]."\n";
	echo '<input name="ratio" value="'.(array_key_exists("ratio",$one)?$one["ratio"]:"").'">'."\n";
	echo '</label>'."\n";
	echo '<label>'.$i18n["notes"]."\n";
	echo '<textarea name="notes">'.$one["notes"].'</textarea>'."\n";
	echo '</label>'."\n";
	echo '</details>'."\n";
	echo '</fieldset>'."\n";
	echo '<fieldset class="taskBar">'."\n";
	echo '<button type="submit" name="OK_modify" value="'.$k.'">'.$i18n["modify"].'</button>'."\n";
	echo '<button type="submit" name="OK_remove" value="'.$k.'">'.$i18n["remove"].'</button>'."\n";
	echo '</fieldset>'."\n";
	echo '</form>';
}
$k=0;
foreach($origin as $one) {$k++;buildOneDataDetails($one,$k);}
?>
<section>
<h2>Liste des drapeaux</h2>
<p>
<?php
$out = array();
foreach (glob('_img/_flag/*.svg') as $filename) {
    $p = pathinfo($filename);
    $out[] = $p['filename'];
}
echo json_encode($out);
?>
</section>
<p><a id="bottom" href="#">Haut de page</a></p>
</section>
<?php } else {
	echo "<p class=\"msg\">".$i18n["can_run_only_on_localhost"]."</p>";
}
?>
</body>
</html>