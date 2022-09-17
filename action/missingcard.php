<?php
/**
 * DokuWiki Plugin sifas (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class action_plugin_sifas_missingcard extends \dokuwiki\Extension\ActionPlugin
{

    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS','BEFORE', $this, 'check_missing_card');
        $controller->register_hook('TPL_CONTENT_DISPLAY','BEFORE', $this, 'show_missing_card');
    }

    function check_missing_card(Doku_Event $event , $param) {
        global $INFO, $ID;
        $cardid = filter_var(substr($ID, strrpos($ID, ":") + 1), FILTER_SANITIZE_NUMBER_INT);
        if($event->data != "show" && !($event->data == "edit" && strlen($cardid) > 0 && substr_count($ID, "_") < 4)) return false;
        if($INFO["exists"]) return false;
        if($INFO["namespace"] !== "cards") return false;

        $event->data = "missingcard";
        $event->stopPropagation();
        $event->preventDefault();
        return true;
    }

    function show_missing_card(Doku_Event $event, $param) {
        global $ACT, $ID, $conf;
        if($ACT != "missingcard") return false;
        $event->stopPropagation();
        $event->preventDefault();
        
        $cardid = filter_var(substr($ID, strrpos($ID, ":") + 1), FILTER_SANITIZE_NUMBER_INT);
        if (strlen($cardid) <= 0) {
            echo "<h1>This page does not exist yet</h1><p>You've followed a link to a page that doesn't exist yet. If permissions allow, you may create it by clicking on <strong>Create this page</strong>.</p>";
            $ACT = "show";
            return;
        }
        
        $db = new SQLite3($conf["metadir"] . "/lookup.sqlite3");
        $pagename = $db->query("SELECT page FROM wikipages WHERE id=" . $cardid)->fetchArray()["page"];
        $db->close();
        if (!$pagename) {
            echo "<h1>This page does not exist yet</h1><p>You've followed a link to a page that doesn't exist yet. If permissions allow, you may create it by clicking on <strong>Create this page</strong>.</p>";
            $ACT = "show";
            return;
        }
        $ID = "cards:" . $pagename;
        
        if (!page_exists("cards:" . $wiki_page)) {
            $cachefile = $conf["cachedir"] . "/templatecard/" . $cardid;
            if (file_exists($cachefile)) {
                echo file_get_contents($cachefile);
            } else {
                $tpl = action_plugin_sifas_templatecard::get_card_template($cardid);
                $tpl = substr($tpl, 0, strpos($tpl, "======"));
                $res = p_render("xhtml", p_get_instructions($tpl), $info);
                echo $res;
                
                if (!is_dir($conf["cachedir"] . "/templatecard")) {
                    mkdir($conf["cachedir"] . "/templatecard", 0777, true);
                }
                file_put_contents($cachefile, $res);
            }
            
            echo "<h1>Missing card page</h1><p><strong>You can still find out more about this card right now by visiting <a href='https://allstars.kirara.ca/card/" . $cardid . "'>this card's page on the Kirara database</a>.</strong><br><br>However, if you'd like to start creating a strategy page for this card, you can do so by <a href='" . DOKU_BASE . "cards/" . $pagename . "?do=edit'>clicking here</a>.</p>";
            $ACT = "show";
        } else {
            $url = DOKU_BASE . "cards/" . $pagename;
            echo "Redirecting you to <a href='" . $url . "'>cards:" . $pagename . "</a>...<script>window.location = '" . $url . "' </script>";
        }

        return true;
    }
}
