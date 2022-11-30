<?php
/**
 * DokuWiki Plugin sifas (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class action_plugin_sifas_templatesong extends \dokuwiki\Extension\ActionPlugin
{
    public static $DIFF_IDS = array(
                            "10" => "Beginner",
                            "20" => "Intermediate",
                            "30" => "Advanced",
                            "40" => "Advanced+",
                            "50" => "Challenge"
                        );
    public static $DIFF_SHORT_IDS = array(
                            "10" => "Beg",
                            "20" => "Int",
                            "30" => "Adv",
                            "40" => "Adv+",
                            "50" => "Ch"
                        );
                        
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('COMMON_PAGETPL_LOAD','BEFORE', $this, 'create_song_template');
    }

    function create_song_template(Doku_Event $event , $param) {
        global $INFO, $ID, $conf;
        if($INFO["namespace"] !== "songs") return false;
        $ldid = filter_var(substr($ID, strrpos($ID, ":") + 1), FILTER_SANITIZE_NUMBER_INT);
        if(strlen($ldid) <= 0) return;
        
        $db = new SQLite3($conf["metadir"] . "/lookup.sqlite3");
        $song = $db->query("SELECT * FROM songs WHERE id=" . $ldid)->fetchArray();
        $db->close();
        if (!$song) {
            return;
        }
        
        $title = str_replace("+","plus",str_replace("µ","u",strtolower($song["name"])));
        $title = preg_replace("/[_\\-\\.]+$/","",preg_replace("/^[_\\-\\.]+/","",preg_replace("/[^a-z0-9]+/","_",preg_replace("/\\./","",$title))));
        $title .= "_" . str_replace("+","plus",strtolower(action_plugin_sifas_templatesong::$DIFF_SHORT_IDS[substr($ldid,5,2)]));
        
        if (page_exists("songs:" . $title)) {
            $event->data["tpl"] = "====== Page Title ======\n\nWARNING: You are actually creating the page ".$ID.".\n\nThe page songs:".$title.", which this Live Difficulty ID belongs to, already exists. If you want to edit it instead, go to https://suyo.be/sifas/wiki/songs/" . $title . "\n\nIf this is what you wanted to do, feel free to ignore and remove this warning.";
            return;
        }
        
        $tpl = io_readFile(wikiFN("template:songs"));
        $tpl = str_replace("@@LID@@",substr($ldid,1,4),$tpl);
        $tpl = str_replace("@@LDID@@",$ldid,$tpl);
        $tpl = str_replace("@@ATTRIBUTE@@",strtolower(helper_plugin_sifas_ids::$ATTR_IDS_R[$song["attr"]]),$tpl);
        $tpl = str_replace("@@NAME@@",$song["name"],$tpl);
        $tpl = str_replace("@@DIFFICULTY@@",action_plugin_sifas_templatesong::$DIFF_IDS[substr($ldid,5,2)],$tpl);
        $tpl = str_replace("@@DIFFICULTYS@@",action_plugin_sifas_templatesong::$DIFF_SHORT_IDS[substr($ldid,5,2)],$tpl);
        $tpl = str_replace("@@UNLOCK@@",$song["unlock"],$tpl);
        
        $ID = "songs:" . $title;
        $event->data["tpl"] = $tpl;
    }
}
