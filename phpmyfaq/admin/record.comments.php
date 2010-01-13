<?php
/**
 * Shows all comments in the categories and provides a link to delete comments
 * 
 * PHP Version 5.2
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
 *
 * @category  phpMyFAQ
 * @package   Administration
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2007-2010 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/MPL-1.1.html Mozilla Public License Version 1.1
 * @link      http://www.phpmyfaq.de
 * @since     2007-03-04
 */

if (!defined('IS_VALID_PHPMYFAQ_ADMIN')) {
    header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']));
    exit();
}

printf("<h2>%s</h2>\n", $PMF_LANG['ad_comment_administration']);

print '<div id="returnMessage"></div>';

if ($permission['delcomment']) {

    $comment     = new PMF_Comment();
    $faq         = new PMF_Faq();
    $faqcomments = $comment->getAllComments('faq');

    printf("<h3>%s</h3>\n", $PMF_LANG['ad_comment_faqs']);
    if (count($faqcomments)) {
?>
    <form id="commentSelection" name="commentSelection" method="post">
    <input type="hidden" name="ajax" value="comment" />
    <input type="hidden" name="ajaxaction" value="delete" />
    <table class="listrecords">
    <thead>
    <tr>
        <th class="listhead" width="20"></th>
        <th class="listhead">&nbsp;</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="3"><input class="submit" type="submit" value="<?php print $PMF_LANG["ad_entry_delete"]; ?>" name="submit" /></td>
    </tr>    
    </tfoot>
    <tbody>
<?php
        foreach ($faqcomments as $faqcomment) {
?>
    <tr id="comments_<?php print $faqcomment['comment_id']; ?>">
        <td class="list"><input name="faq_comments[<?php print $faqcomment['record_id']; ?>]" value="<?php print $faqcomment['comment_id']; ?>" type="checkbox" /></td>
        <td class="list">
            <span style="font-weight: bold;">
                <a href="mailto:<?php print $faqcomment['email']; ?>"><?php print $faqcomment['user']; ?></a>
                | <?php print date("Y-m-d", $faqcomment['date']); ?>
                | <a href="<?php printf("../?action=artikel&cat=%d&id=%d&artlang=%s", 
                   $faqcomment['category_id'],
                   $faqcomment['record_id'], 
                   $LANGCODE); ?>"><?php print $faq->getRecordTitle($faqcomment['record_id']); ?></a>
            </span><br/>         
            <?php print PMF_String::htmlspecialchars($faqcomment['content']); ?>
        </td>
    </tr>
<?php
        }
?>
    </tbody>
    </table>
<?php
    } else {
        print '<p><strong>0</strong></p>';
    }

    $newscomments = $comment->getAllComments('news');

    printf("<h3>%s</h3>\n", $PMF_LANG['ad_comment_news']);
    if (count($newscomments)) {
?>
    <table class="listrecords">
    <thead>
    <tr>
        <th class="listhead" width="20"></th>
        <th class="listhead">&nbsp;</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="3"><input class="submit" type="submit" value="<?php print $PMF_LANG["ad_entry_delete"]; ?>" name="submit" /></td>
    </tr>    
    </tfoot>
    <tbody>
<?php
        foreach ($newscomments as $newscomment) {
?>
    <tr id="comments_<?php print $newscomment['comment_id']; ?>">
        <td class="list"><input name="news_comments[<?php print $faqcomment['record_id']; ?>]" value="<?php print $faqcomment['comment_id']; ?>" type="checkbox" /></td>
        <td class="list">
            <span style="font-weight: bold;">
                <a href="mailto:<?php print $newscomment['email']; ?>"><?php print $newscomment['user']; ?></a>
            </span><br/>
            <?php print PMF_String::htmlspecialchars($newscomment['content']); ?>
        </td>
    </tr>
<?php
        }
?>
    </tbody>
    </table>
<?php
    } else {
        print '<p><strong>0</strong></p>';
    }
?>
    </form>
    
    <script type="text/javascript">
    /* <![CDATA[ */
    $(document).ready(function() {
      $('.submit').click(function () {
        var comments = $('#commentSelection').serialize();
        $.ajax({
          type: "POST",
          url:  "index.php?action=ajax&ajax=comment",
          data: comments,
          success: function(msg) {
            if (msg == 1) {
                $('#returnMessage').append('<?php print $PMF_LANG['ad_entry_commentdelsuc']; ?>').fadeIn('slow');
                $("tr td input:checked").parent().parent().fadeOut('slow');
            }
          }
        });
        return false;
      });
    });
    
    /* ]]> */
    </script>
<?php 
} else {
    print $PMF_LANG['err_NotAuth'];
}