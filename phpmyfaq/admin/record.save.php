<?php
/**
* $Id: record.save.php,v 1.4 2004-12-11 20:06:54 thorstenr Exp $
*
* Save or update a FAQ record
*
* @author       Thorsten Rinne <thorsten@phpmyfaq.de>
* @since        2003-02-23
* @copyright    (c) 2001-2004 phpMyFAQ Team
*
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License.
*/

$submit = $_REQUEST["submit"];

if (isset($submit[2]) && isset($_REQUEST["thema"]) && $_REQUEST["thema"] != "") {
	// Preview
	$rubrik = $_REQUEST["rubrik"];
    $cat = new Category;
?>
	<h2><?php print $PMF_LANG["ad_entry_preview"]; ?></h2>
	<p><strong><?php print $cat->categoryName[$rubrik]["name"]; ?>:</strong> <?php print stripslashes($_REQUEST["thema"]); ?></p>
<?php
    $content = preg_replace_callback("{(<pre>.*</pre>)}siU", "pre_core", $_REQUEST["content"]);
    $content = preg_replace_callback("{(<pre+.*</pre>)}siU", "pre_core", $content);
    print stripslashes($content);
?>
    <p class="little"><?php print $PMF_LANG["msgLastUpdateArticle"].makeDate(date("YmdHis")); ?><br /><?php print $PMF_LANG["msgAuthor"].$_REQUEST["author"]; ?></p>
    <form action="<?php print $_SERVER["PHP_SELF"].$linkext; ?>&amp;aktion=editpreview" method="post">
    <input type="hidden" name="id" value="<?php print $_REQUEST["id"]; ?>" />
    <input type="hidden" name="thema" value="<?php print htmlspecialchars($_REQUEST["thema"]); ?>" />
    <input type="hidden" name="content" value="<?php print htmlspecialchars($_REQUEST["content"]); ?>" />
    <input type="hidden" name="lang" value="<?php print $_REQUEST["language"]; ?>" />
    <input type="hidden" name="keywords" value="<?php print $_REQUEST["keywords"]; ?>" />
    <input type="hidden" name="author" value="<?php print $_REQUEST["author"]; ?>" />
    <input type="hidden" name="email" value="<?php print $_REQUEST["email"]; ?>" />
    <input type="hidden" name="rubrik" value="<?php print $_REQUEST["rubrik"]; ?>" />
    <input type="hidden" name="active" value="<?php print $_REQUEST["active"]; ?>" />
    <input type="hidden" name="changed" value="<?php print $_REQUEST["changed"]; ?>" />
    <input type="hidden" name="comment" value="<?php print $_REQUEST["comment"]; ?>" />
    <p align="center"><input type="submit" name="submit" value="<?php print $PMF_LANG["ad_entry_back"]; ?>" /></p>
    </form>
<?php
}

if (isset($submit[1]) && isset($_REQUEST["thema"]) && $_REQUEST["thema"] != "") {
	// Wenn auf Speichern geklickt wurde...
	adminlog("Beitragsave", $_REQUEST["id"]);
	$db->query("INSERT INTO ".SQLPREFIX."faqchanges (id, beitrag, usr, datum, what) VALUES (".$db->insert_id(SQLPREFIX."faqchanges", "id").", '".$_REQUEST["id"]."','".$auth_user."','".time()."','".nl2br(addslashes($_REQUEST["changed"]))."')");
	$thema = addslashes($_REQUEST["thema"]);
	$content = addslashes($_REQUEST["content"]);
	$keywords = addslashes($_REQUEST["keywords"]);
	$author = addslashes($_REQUEST["author"]);
    
    if (isset($_REQUEST["comment"]) && $_REQUEST["comment"] != "") {
        $comment = $_REQUEST["comment"];
    } else {
        $comment = "n";
    }
	
    $datum = date("YmdHis");
	
	$result = $db->query("SELECT id, lang FROM ".SQLPREFIX."faqdata WHERE id = '".$_REQUEST["id"]."' AND lang = '".$_REQUEST["language"]."'");
	$num = $db->num_rows($result);
	
    // save or update the FAQ record
	if ($num == "1") {
		$query = "UPDATE ".SQLPREFIX."faqdata SET thema = '".$thema."', content = '".$content."', keywords = '".$keywords."', author = '".$author."', active = '".$_REQUEST["active"]."', datum = '".$datum."', email = '".$_REQUEST["email"]."', comment = '".$comment."' WHERE id = '".$_REQUEST["id"]."' AND lang = '".$_REQUEST["language"]."'";
    } else {
		$query = "INSERT INTO ".SQLPREFIX."faqdata (id, lang, thema, content, keywords, author, active, datum, email, comment) VALUES ('".$_REQUEST["id"]."','".$_REQUEST["language"]."', '".$thema."', '".$content."', '".$keywords."', '".$author."', '".$_REQUEST["active"]."', '".$datum."', '".$_REQUEST["email"]."', '".$comment."')";
    }
    
	// save or update the category relations
    
    
    
	if ($db->query($query)) {
		print $PMF_LANG["ad_entry_savedsuc"];
    } else {
		print $PMF_LANG["ad_entry_savedfail"].$db->error();
    }
}

if (isset($submit[0])) {
	if ($permission["delbt"])	{
        if (isset($_REQUEST["thema"]) && $_REQUEST["thema"] != "") {
            $thema = "<strong>".$_REQUEST["thema"]."</strong>";
        } else {
            $thema = "";
        }
        if (isset($_REQUEST["author"]) && $_REQUEST["author"] != "") {
            $author = $PMF_LANG["ad_entry_del_2"]."<strong>".$_REQUEST["author"]."</strong>";
        } else {
            $author = "";
        }
?>
	<p align="center"><?php print $PMF_LANG["ad_entry_del_1"]." ".$thema." ".$author." ".$PMF_LANG["ad_entry_del_3"]; ?></p>
	<div align="center">
    <form action="<?php print $_SERVER["PHP_SELF"].$linkext; ?>" method="POST">
    <input type="hidden" name="aktion" value="delentry">
    <input type="hidden" name="referer" value="<?php print $_SERVER["HTTP_REFERER"]; ?>">
    <input type="hidden" name="id" value="<?php print $_REQUEST["id"]; ?>">
    <input type="hidden" name="language" value="<?php print $_REQUEST["language"]; ?>">
    <input class="submit" type="submit" value="<?php print $PMF_LANG["ad_gen_yes"] ?>" name="subm">
    <input class="submit" type="submit" value="<?php print $PMF_LANG["ad_gen_no"] ?>" name="subm">
    </form>
    </div>
<?php
    } else {
		print $PMF_LANG["err_NotAuth"];
    }
} elseif (!isset($_REQUEST["thema"]) || $_REQUEST["thema"] == "") {
	print "<p>".$PMF_LANG["ad_entryins_fail"]."</p>";
	print "<p><a href=\"javascript:history.back();\">".$PMF_LANG["ad_entry_back"]."</a></p>";
}
?>
